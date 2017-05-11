@extends('folio::admin.layout')

<?php
	$settings_title = Config::get('settings.title');
	if($settings_title == '') {
		$settings_title = "Folio";
	}
	$site_title = 'New Item | '.$settings_title;
?>

@section('title', 'New Item')

@section('content')

	<div class="[ c-admin ] [ u-pad-b-12x ]">

		{{ Form::open(['url' => Folio::adminPath().'item/add', 'method' => 'post']) }}

			<p>{{ Form::text('title', '', ['placeholder' => 'Title']) }}</p>

			<p>{{ Form::text('image', '', ['placeholder' => 'Image']) }}</p>

			<p>{{ Form::text('image_str', '', ['placeholder' => 'Image (Thumbnail)']) }}</p>

			<p>{{ Form::text('video', '', ['placeholder' => 'Video']) }}</p>

			<p>{{ Form::text('link', '', ['placeholder' => 'External Link']) }}</p>

			<p>{{ Form::textarea('text', '', ['placeholder' => 'Text']) }}</p>

			<p>{{ Form::text('published_at', '', ['placeholder' => 'Publishing Date']) }}</p>

			<p>{{ Form::text('slug_title', '', ['placeholder' => 'Explicit Slug Title']) }}</p>

			<p>{{ Form::text('tags_str', '', ['placeholder' => 'Tags']) }}</p>

			<p>{{ Form::text('recipients_str', '', ['placeholder' => '@recipients']) }}</p>

			<p><label for="rss">{{ Form::checkbox('rss', null, null, array('id' => 'rss')) }} RSS</label></p>

			<p>{{ Form::submit('Create') }}</p>

		{{ Form::close() }}

	</div>

@endsection
