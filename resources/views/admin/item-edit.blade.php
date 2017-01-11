
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
    <script type="text/javascript" src="/js/helpers.min.js"></script>
    <script type="text/javascript" src="/js/space.admin.js"></script>
    <script>
      app.item = {!! $item !!};
      app.properties = {!! $item->properties !!};
    </script>
@stop

@section('content')

<div class="admin-form">

	<p>
		Editing Item {{ $item->id }}
		<a href="/e/{{ Hashids::encode($item->id) }}">
		<i class="[ fa fa-link fa--social ]"></i></a>
		<a href="{{ '/'.Writing::path().$item->slug }}">Preview</a>
	</p>

	<?php if( Request::isMethod('post') ) { echo '<p>Changes saved.</p>'; } ?>

	{{ Form::model($item, array('route' => array('item.edit', $item->id))) }}

		<p>{{ Form::text('title', null, array('placeholder' => 'Title')) }}</p>

		<p>{{ Form::textarea('text', null, array('placeholder' => 'Text')) }}</p>

		<p>{{ Form::text('published_at', null, array('placeholder' => 'Publishing Date')) }}</p>

		<p>{{ Form::text('image', null, array('placeholder' => 'Image')) }}</p>

		<p>{{ Form::text('image_src', null, array('placeholder' => 'Image (Thumbnail)')) }}</p>

		<p>{{ Form::text('video', null, array('placeholder' => 'Video')) }}</p>

		<p>{{ Form::text('slug_title', null, array('placeholder' => 'Explicit Slug Title')) }}</p>

		<p>{{ Form::text('tags_str', null, array('placeholder' => 'Tags')) }}</p>

		<p>{{ Form::text('recipients_str', null, array('placeholder' => '@recipients')) }}</p>

		<p><label for="rss">{{ Form::checkbox('rss', null, null, array('id' => 'rss')) }} RSS</label></p>

		<p>{{ Form::submit('Save') }}</p>

	{{ Form::close() }}

</div>

@endsection
