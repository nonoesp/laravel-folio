
@extends('writing::admin.layout')

<?php
$settings_title = Config::get('settings.title');
if($settings_title == '') {
	$settings_title = "Space";
}
	$site_title = 'Edit Item '.$item->id.' — '. $settings_title;
	$services_typekit = 'fgm7qov';
?>

@section('title', 'Items')

@section('scripts')
    <script type="text/javascript" src="/js/vendor/vue.js"></script>
    <script type="text/javascript" src="/js/vendor/vue-resource.js"></script>
		<script type="text/javascript" src="/js/vendor/lodash.min.js"></script>
    {{--<script type="text/javascript" src="/js/helpers.min.js"></script>--}}
		<script type="text/javascript" src="/js/space.main.js"></script>
    <script type="text/javascript" src="/js/space.admin.js"></script>
    <script>
      app.item = {!! $item !!};
      app.properties = {!! $item->properties !!};
    </script>
@stop

<?php
	$inputs = [
		['name' => 'published_at', 'placeholder' => 'Publishing Date (yyyy-mm-dd hh:mm:ss)', 'label' => 'Date'],
		['name' => 'image', 'placeholder' => 'Image', 'label' => 'Image'],
		['name' => 'image_src', 'placeholder' => 'Thumbnail', 'label' => 'Thumbnail'],
		['name' => 'video', 'placeholder' => 'Video', 'label' => 'Video URL'],
		['name' => 'slug_title', 'placeholder' => 'URL (defaults to /'.Writing::path().$item->slug.')', 'label' => 'URL Slug'],
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

<div id="app" class="c-admin">

	<p>
		Editing Item {{ $item->id }}
		<a href="/e/{{ Hashids::encode($item->id) }}">
		<i class="[ fa fa-link fa--social ]"></i></a>
		<a href="{{ '/'.Writing::path().$item->slug }}">Preview</a>
	</p>

	<div class="[ c-admin__form ] [ grid ]">

		<?php if( Request::isMethod('post') ) { echo '<p>Changes saved.</p>'; } ?>

		{{ Form::model($item, array('route' => array('item.edit', $item->id))) }}

		<div class="[ grid__item ] [ one-whole ]">
			<p>{{ Form::text('title', null, array('placeholder' => 'Title')) }}</p>
		</div>

		<div class="[ grid__item ] [ one-whole ]">
			<p>{{ Form::textarea('text', null, array('placeholder' => 'Text')) }}</p>
		</div>

			@foreach($inputs as $input)
				<div v-if="item.{{ $input['name'] }}" class="[ grid__item ]
				[ c-admin-form__label u-text-align--right ]
				[ one-sixth portable--one-whole ]">
					<p>{{ $input['label'] }}</p>
				</div><!--
		 --><div class="[ grid__item ] [ one-whole ]">
		 			<p>{{ Form::text($input['name'], null, array('v-model' => 'item.'.$input['name'],'placeholder' => $input['placeholder'])) }}</p>
				</div>
			@endforeach

			<div class="[ grid__item ] [ one-whole ]">
				<p><label for="rss">{{ Form::checkbox('rss', null, null, array('id' => 'rss')) }} RSS</label></p>
			</div>


			<div class="[ grid__item ]">
				<div @click="add_property" class="[ u-cursor-pointer ]">Add Property</div>
			</div>


				<div v-for="property in properties" class="[ grid__item one-whole ]">
					<div class="[ grid ]"><!--
					--><div class="[ grid__item two-sixths ] [ u-text-align--right ]">
							<input type="text"
							v-model="property.label"
							@keyup="sync_properties(property)"
							v-bind:data-id="property.id" data-field="label"
							class="u-text-align--right">
						</div><!--
						--><div class="[ grid__item one-sixth ]">
							<input type="text"
							v-model="property.name"
							@keyup="sync_properties(property)"
							v-bind:data-id="property.id" data-field="name"
							class="u-text-align--right">
								{{--<span v-bind:data-id="property.id" data-field="name">@{{ property.name }}</span>--}}
						</div><!--
						--><div class="[ grid__item two-sixths ]">
								<input type="text" v-model="property.value"
								@keyup="sync_properties(property)"
								v-bind:data-id="property.id" data-field="value">
						</div><!--
						--><div @click="delete_property(property)"
						v-bind:data-id="property.id"
						class="[ grid__item one-sixth ] [ u-text-align--left ]">
							(@{{ property.id }})
							<span class="[ u-cursor-pointer ]">X</span>
							<span v-if="property.is_updating">...</span>
						</div><!--
			 --></div>

			</div>

			<div class="[ grid__item ] [ one-whole ]">
				<p>{{ Form::submit('Save') }}</p>
			</div>

		{{ Form::close() }}

	</div>

</div>

@endsection
