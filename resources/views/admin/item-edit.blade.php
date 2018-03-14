
@extends('folio::admin.layout')

<?php
$settings_title = config('folio.title');
if($settings_title == '') {
	$settings_title = "Folio";
}
$site_title = 'Editing Item '.$item->id.' | '. $settings_title;
$remove_wrap = true;
?>

@section('title', 'Editing Item '.$item->id)

@section('scripts')

    <script type="text/javascript" src="/nonoesp/folio/js/manifest.js"></script>
    <script type="text/javascript" src="/nonoesp/folio/js/vendor.js"></script>
    <script type="text/javascript" src="/nonoesp/folio/js/folio.js"></script>
	<!-- Mousetrap for handling keyboard shortcuts -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.6.1/mousetrap.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.6.1/plugins/global-bind/mousetrap-global-bind.min.js"></script>
	<!-- Clipboard -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.0/clipboard.min.js"></script>

<script type="text/javascript">

/*
 * Prevent the user from walking away without saving changes.
 */
window.onbeforeunload = function() {
	if(isSaving) {
		return null;
	}
    return admin.isDirty() ? "If you leave this page you will lose your unsaved changes." : null;
}

/*
 * Wether the user requested to save.
 * (User to not warn the user the file has unsaved changes.)
 */
var isSaving = false; 

/*
 * CTRL+S & COMMAND+S
 * Keyboard shortcut to save edits by submitting the form.
 */
Mousetrap.bindGlobal(['ctrl+s', 'command+s'], function(e) {
	save();
	e.preventDefault();
	return false;
});

/*
 * command+i
 * Keyboard shortcut to log if the Item is "dirty."
 */
Mousetrap.bindGlobal('command+i', function(e) {
	console.log('admin.isDirty(): ' + admin.isDirty());
	e.preventDefault();
	return false;
});

var save = function() {
	$("form").submit();
}

$(document).on('submit', 'form', function() {
	isSaving = true;
});

VueResource.Http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

var debounced_property_sync = _.debounce(
	function(property, model) {

		if(property.value != property.old_value ||
		property.label != property.old_label ||
		property.name != property.old_name) {
			// update
		} else {
			// no changes, abort update
			return;
		}

		var data = property;
		property.is_updating = true;
		
		property.old_value = property.value;
		property.old_label = property.label;
		property.old_name = property.name;
		
		model.$forceUpdate();
		VueResource.Http.post('/api/property/update', data).then((response) => {
				// success
				property.is_updating = false;
				model.$forceUpdate();
			}, (response) => {
				// error
			});
	}, 500);

var editing_property = -1;

var admin = new Vue({
el: '.c-admin',
data: {
	item: '',
	originalItem: '',
	properties: '',
	timers: {},
	properties_changed: false,
	isTextareaFocused: false
},
watch: {
	properties: {
		handler: function(value, old) {
			if (old != "") {
				this.properties_changed = true;
			}
		},
		deep: true
	}
},
mounted() {
	//this.adjustTextareaHeight(this.$refs.editor, 0);
},
methods: {
	initProperties: function() {
		for(var i in this.properties) {
			var p = this.properties[i];
			p.old_name = p.name;
			p.old_label = p.label;
			p.old_value = p.value;
		}
	},
	sync_properties: function(property) {
		if(editing_property != -1 && property.id != editing_property) {
			debounced_property_sync.flush();
		}
		editing_property = property.id;
		debounced_property_sync(property, this);
		this.properties_changed = false;
	},
	delete_property: function(property) {
		VueResource.Http.post('/api/property/delete', {id: property.id}).then((response) => {
				// success
				this.properties.splice(this.properties.indexOf(property), 1);
			}, (response) => {
				// error
			});
	},
	add_property: function(event) {
		var data = { item_id: this.item.id }
		VueResource.Http.post('/api/property/create', data).then((response) => {
				// success
				console.log(response);
				this.properties.push({id: response.body.property_id});
			}, (response) => {
				// error
			});
	},
	textareaKeyupHandler: function (event) {
		let target = this.targetElement(event);
		this.adjustTextareaHeight(target, 0);
	},
	adjustTextareaHeight: function(textarea, height) {
		if(height == 0) {
			var scrollTop = document.documentElement.scrollTop;
			textarea.style.height = "1px";
			textarea.style.height = (25+textarea.scrollHeight)+"px";
			document.documentElement.scrollTop = scrollTop;
		} else {
			textarea.style.height = '150px';
		}
	},
	updateTextareaFocus: function(event) {

		let target = this.targetElement(event);

		switch(event.type) {
			case 'focus':
				this.isTextareaFocused = true;
				this.adjustTextareaHeight(target, 0);
			break;
			case 'blur':
				this.isTextareaFocused = false;
				this.adjustTextareaHeight(target, '150px');
			break;
		}
	},
	targetElement(event) {
		if(event.srcElement == undefined) {
			return event.target;
		}
		return event.srcElement;
	},
	isDirty() {
		var trackedFields = [
			'title',
			'text',
			'video',
			'published_at',
			'image',
			'image_src',
			'link',
			'slug_title',
			'tags_str',
			'recipients_str',
			'template',
			'deleted_at',
			'rss',
			'is_blog',
		];

		for(var i in this.item) {

			if(trackedFields.includes(i)) {
			
				let itemValue = this.item[i];
				let originalValue = this.originalItem[i];

				if((itemValue == "" || itemValue == "null") && originalValue == null) {
					// ignore this as is not really "dirty"
				} else if(itemValue != originalValue) {
					return true;
				}
			}
		}
		return false;
	}
}
});

</script>

		<script>
			admin.item = {!! $item !!};
			admin.originalItem = {!! $item !!};
			admin.properties = {!! $item->properties !!};
			admin.message = "{!! $item->title !!}";
			admin.initProperties();
			// parse deleted_at date to true if existing for isDirty to detect property
			if(admin.item.deleted_at != null) admin.item.deleted_at = true;
			if(admin.originalItem.deleted_at != null) admin.originalItem.deleted_at = true;
			new ClipboardJS('.js--encoded-path');
			$(document).on('click', '.js--encoded-path', function(e) {
				e.preventDefault();
				return false;
			});
		</script>
@stop

<?php
	$inputs = [
		['name' => 'published_at', 'placeholder' => 'Publication Date (yyyy-mm-dd hh:mm:ss)', 'label' => 'Publication Date'],
		['name' => 'image', 'placeholder' => 'Image', 'label' => 'Image'],
		['name' => 'image_src', 'placeholder' => 'Thumbnail', 'label' => 'Thumbnail'],
		['name' => 'video', 'placeholder' => 'Video', 'label' => 'Video URL'],
		['name' => 'link', 'placeholder' => 'External Link', 'label' => 'External Link'],
		['name' => 'slug_title', 'placeholder' => 'Explicit URL slug (prefix with / to make absolute, i.e. \'/terms\')', 'label' => 'URL slug'],
		['name' => 'tags_str', 'placeholder' => 'Tags (e.g. writing, project)', 'label' => 'Tags'],
		['name' => 'recipients_str', 'placeholder' => 'Recipients (Twitter handles)', 'label' => 'Recipients'],
	];
?>

@section('floating.menu')
  	{!! view('folio::partial.c-floating-menu', ['buttons' => [
		  '<i class="fa fa-home"></i>' => '/'.Folio::path(),
		  '<i class="fa fa-times"></i>' => $item->destroyPath(),
		  '<i class="fa fa-history"></i>' => $item->versionsPath(),
		  '<i class="fa fa-link"></i>' => [$item->encodedPath(), 'js--encoded-path', 'data-clipboard-text="blah"'],
		  '<i class="fa fa-eye"></i>' => '/'.$item->path()
		  ]]) !!}
@stop

@section('content')

<style media="screen">
	.grid {
		letter-spacing: inherit;
	}
</style>

{{-- Vue Component --}}

<div class="[ c-admin ] [ u-pad-b-12x ]">

	<div class="[ c-admin-form-v2 ] [ grid ]">

		@if( Request::isMethod('post') )
		<div class="[ o-wrap o-wrap--size-small ]">		
			<div class="[ grid__item ] [ one-whole ]">
				<p>Changes saved.</p>
			</div>
		</div>
		@endif

		{{ Form::model($item, array('route' => array('item.edit', $item->id))) }}

		<div class="[ o-wrap o-wrap--size-small ]">
			<div class="[ grid__item ] [ one-whole ]">
				<p>{{ Form::text('title', null, ['placeholder' => 'Title', 'v-model' => 'item.title']) }}</p>
			</div>
		</div>

		<div class="[ grid__item ] [ one-whole ]">
			<p class="[ unwrapped wide ]">{{ Form::textarea('text', null, [
				'placeholder' => 'Text',
				'ref' => 'editor',
				'v-on:keyup' => 'textareaKeyupHandler',
				'v-model' => 'item.text',
				'@focus' => 'updateTextareaFocus',
				'@blur' => 'updateTextareaFocus',
				'v-bind:class' => '{ "u-opacity--high" : !isTextareaFocused }'
				]) }}</p>
		</div>


		<div class="[ o-wrap o-wrap--size-600 ]">

			@foreach($inputs as $input)
				<div v-if="item.{{ $input['name'] }}" class="[ grid__item ]
				[ c-admin-form__label u-text-align--right c-admin--font-light ]
				[ one-half portable--one-whole ]">
					<span>{{ $input['label'] }}</span>
				</div><!--
		 --><div class="[ grid__item ] [ one-whole ]">
		 			<p>{{ Form::text($input['name'], null, ['v-model' => 'item.'.$input['name'], 'placeholder' => $input['placeholder']]) }}</p>
				</div>
			@endforeach

			{{-- Template Drop-down --}}

			<div class="[ grid__item ] [ one-whole ]">
				<p>
					{{ Form::select('template', $templates, $item->template, ['v-model' => 'item.template']) }}
				</p>
				@if($item->templateView() != null && !view()->exists($item->templateView()))
					<p>View <i>{{$item->templateView()}}</i> is missing!</p>
				@endif
			</div>

			<div class="[ grid__item ] [ one-whole ]">
				<p><label for="hidden">{{ Form::checkbox('is_hidden', null, $item->trashed(), ['id' => 'is_hidden', 'v-model' => 'item.deleted_at']) }} Hidden</label></p>
			</div>			

			{{-- Blog Feed --}}

			<div class="[ grid__item ] [ one-whole ]">
				<p><label for="blog">{{ Form::checkbox('is_blog', null, null, ['id' => 'is_blog', 'v-model' => 'item.is_blog']) }} Blog Feed</label></p>
			</div>

			{{-- RSS --}}

			<div class="[ grid__item ] [ one-whole ]">
				<p><label for="rss">{{ Form::checkbox('rss', null, null, ['id' => 'rss', 'v-model' => 'item.rss']) }} RSS Feed</label></p>
			</div>			

			{{-- Properties --}}
				
				<div v-if="properties.length" class="[ grid__item ] [ u-pad-b-1x ]">
					<strong>Properties</strong>
				</div>

				<div v-for="property in properties" class="[ grid__item one-whole ] [ c-admin__property ]">

						<div class="[ grid grid--narrow ]">
							<div class="[ grid__item ]
							[ c-admin-form__label u-text-align--right c-admin--font-light ]
							[ one-half portable--one-whole ]
							[ u-hidden-portable ]">
								<span>@{{ property.id }}</span>
							</div>
							<!--
						--><div class="[ grid__item three-twelfths  ] [ u-text-align--right ]">
								<input type="text"
								placeholder="Label"
								v-model="property.label"
								@keyup="sync_properties(property)"
								v-bind:data-id="property.id" data-field="label"
								class="u-text-align--right">
							</div><!--
							--><div class="[ grid__item three-twelfths ]">
								<input type="text"
								placeholder="identifier"
								v-model="property.name"
								@keyup="sync_properties(property)"
								v-bind:data-id="property.id" data-field="name"
								class="u-text-align--right">
									{{--<span v-bind:data-id="property.id" data-field="name">@{{ property.name }}</span>--}}
							</div><!--
							--><div class="[ grid__item five-twelfths ]">
									<input type="text" v-model="property.value"
									placeholder="Value"
									@keyup="sync_properties(property)"
									v-bind:data-id="property.id" data-field="value">
							</div><!--
							--><div @click="delete_property(property)"
							v-bind:data-id="property.id"
							class="[ grid__item one-twelfth ] [ u-opacity--low ]">
								<span class="[ c-admin__property-trash ] [ u-cursor-pointer ]">
									<i class="fa fa-trash-o"></i>
								</span>
								<span v-if="property.is_updating">
									<i class="fa fa-refresh fa-spin fa-fw"></i>
									<span class="sr-only">Loading...</span>
								</span>
							</div><!--
				--></div>

				</div>


			<div class="[ grid__item ] [ u-pad-b-2x u-pad-t-0x ] [ c-admin--font-light ] ">
					<p @click="add_property" class="[ u-cursor-pointer ]">Add Custom Property</p>
			</div>

			<div class="[ grid__item ] [ one-whole ]">
				<p>{{ Form::submit('Save') }}</p>
			</div>

		{{ Form::close() }}

	</div>

</div>

@endsection
