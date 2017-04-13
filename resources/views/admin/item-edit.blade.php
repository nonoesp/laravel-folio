
@extends('space::admin.layout')

<?php
$settings_title = config('space.title');
if($settings_title == '') {
	$settings_title = "Space";
}
	$site_title = 'Editing Item '.$item->id.' | '. $settings_title;
?>

@section('title', 'Items')

@section('scripts')

    <script type="text/javascript" src="/nonoesp/space/js/manifest.js"></script>
    <script type="text/javascript" src="/nonoesp/space/js/vendor.js"></script>
    <script type="text/javascript" src="/nonoesp/space/js/space.js"></script>

<script type="text/javascript">
VueResource.Http.headers.common['X-CSRF-TOKEN'] = $('meta[name="csrf-token"]').attr('content');

var debounced_property_sync = _.debounce(
	function(property, model) {
		var data = property;
		property.is_updating = true;
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
	properties: '',
	timers: {},
	properties_changed: false,
	styleObject: {
		color: 'red'
	}
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
computed: {
	reversedMessage: function () {
		return this.message.split('').reverse().join('');
	}
},
methods: {
	set_updating: function(property) {
		property.is_updating = true;
	},
	sync_properties: function(property) {
		if(this.properties_changed == false) {
			return;
		}
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
	}
}
});

</script>

		<script>
			admin.item = {!! $item !!};
			admin.properties = {!! $item->properties !!};
			admin.message = "{!! $item->title !!}";
		</script>
@stop

<?php
	$inputs = [
		['name' => 'published_at', 'placeholder' => 'Publishing Date (yyyy-mm-dd hh:mm:ss)', 'label' => 'Date'],
		['name' => 'image', 'placeholder' => 'Image', 'label' => 'Image'],
		['name' => 'image_src', 'placeholder' => 'Thumbnail', 'label' => 'Thumbnail'],
		['name' => 'video', 'placeholder' => 'Video', 'label' => 'Video URL'],
		['name' => 'link', 'placeholder' => 'External Link', 'label' => 'External Link'],
		['name' => 'slug_title', 'placeholder' => 'Explicit URL slug (prefix with / to make absolute, i.e. \'/terms\')', 'label' => 'URL slug'],
		['name' => 'tags_str', 'placeholder' => 'Tags (e.g. writing, project)', 'label' => 'Tags'],
		['name' => 'recipients_str', 'placeholder' => 'Recipients (Twitter handles)', 'label' => 'Recipients'],
	];
?>

@section('content')

<style media="screen">
	.grid {
		letter-spacing: inherit;
	}
</style>

{{-- Vue Component --}}

<div class="[ c-admin ] [ u-pad-b-12x ]">

	<p>
		Editing Item {{ $item->id }}
		<a href="/e/{{ Hashids::encode($item->id) }}">
		<i class="[ fa fa-link fa--social ]"></i></a>
		<a href="{{ '/'.$item->path() }}">Preview</a>
	</p>

	<div class="[ c-admin__form ] [ grid ]">

		@if( Request::isMethod('post') )
			<div class="[ grid__item ] [ one-whole ]">
				<p>Changes saved.</p>
			</div>
		@endif

		{{ Form::model($item, array('route' => array('item.edit', $item->id))) }}

		<div class="[ grid__item ] [ one-whole ]">
			<p>{{ Form::text('title', null, array('placeholder' => 'Title')) }}</p>
		</div>

		<div class="[ grid__item ] [ one-whole ]">
			<p>{{ Form::textarea('text', null, array('placeholder' => 'Text')) }}</p>
		</div>

			@foreach($inputs as $input)
				<div v-if="item.{{ $input['name'] }}" class="[ grid__item ]
				[ c-admin-form__label u-text-align--right c-admin--font-light ]
				[ one-half portable--one-whole ]">
					<span>{{ $input['label'] }}</span>
				</div><!--
		 --><div class="[ grid__item ] [ one-whole ]">
		 			<p>{{ Form::text($input['name'], null, array('v-model' => 'item.'.$input['name'],'placeholder' => $input['placeholder'])) }}</p>
				</div>
			@endforeach


			{{-- Template Drop-down --}}

			<div class="[ grid__item ] [ one-whole ]">
				<p>
					{{ Form::select('template', $templates, $item->template) }}
				</p>
				@if($item->templateView() != null && !view()->exists($item->templateView()))
					<p>View <i>{{$item->templateView()}}</i> is missing!</p>
				@endif
			</div>


			{{-- RSS --}}

			<div class="[ grid__item ] [ one-whole ]">
				<p><label for="rss">{{ Form::checkbox('rss', null, null, array('id' => 'rss')) }} RSS</label></p>
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
					--><div class="[ grid__item four-twelfths  ] [ u-text-align--right ]">
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
						--><div class="[ grid__item four-twelfths ]">
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
					{{--<span class="[ fa fa-plus fa--social ]"></span>--}}
					<span @click="add_property" class="[ u-cursor-pointer ]">Add Custom Property</span>
			</div>


			<div class="[ grid__item ] [ one-whole ]">
				<p>{{ Form::submit('Save') }}</p>
			</div>

		{{ Form::close() }}

	</div>

</div>

@endsection
