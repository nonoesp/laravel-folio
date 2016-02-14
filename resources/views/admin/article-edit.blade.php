@extends('writing::admin.layout')

<?php
	$site_title = 'Article Edit — '. Config::get('settings.title');
?>

@section('title', 'Articles')

@section('content')

<div class="admin-form">

	<p>Editing Article {{ $article->id }} <a href="{{ '/'.Writing::path().$article->slug }}">Preview</a></p>

	<?php if( Request::isMethod('post') ) { echo '<p>Changes saved.</p>'; } ?>

	{{ Form::model($article, array('route' => array('article.edit', $article->id))) }}

		<p>{{ Form::text('title', null, array('placeholder' => 'Title')) }}</p>

		<p>{{ Form::textarea('text', null, array('placeholder' => 'Text')) }}</p>

		<p>{{ Form::text('published_at', null, array('placeholder' => 'Publishing Date')) }}</p>

		<p>{{ Form::text('image', null, array('placeholder' => 'Image')) }}</p>

		<p>{{ Form::text('video', null, array('placeholder' => 'Video')) }}</p>

		<p>{{ Form::text('tags_str', null, array('placeholder' => 'Tags')) }}</p>
		
		<p>{{ Form::text('recipients_str', null, array('placeholder' => '@recipients')) }}</p>

		<p><label for="rss">{{ Form::checkbox('rss', null, null, array('id' => 'rss')) }} RSS</label></p>

		<p>{{ Form::submit('Save') }}</p>

	{{ Form::close() }}

</div>

<br>

{{-- View::make('blog.partials.article')->with(array('article' => $article, 'isTitleLinked' => true)) --}}

@endsection