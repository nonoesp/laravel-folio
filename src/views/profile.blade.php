@extends('layout.main')


<?php
	$articles = \Nonoesp\Writing\Article::where('user_id', '=', $user->id)->take(5)->get();
?>


@section('content')

  <div class="[ o-band ]  [ u-border-bottom  u-no-padding-bottom ]">
    <div class="[ o-wrap  o-wrap--standard  o-wrap--portable-tiny ]">

    	<article class="[ grid ]  [ c-article ]"><!--

    	--><div class="[ grid__item  one-quarter  portable--one-whole ]  [ -u-border ]">
	   		
	   			<h1>{{ $user->name }}</h1>

	   		</div><!--

         --><div class="[ grid__item  one-half  portable--one-whole ]  [ -u-border ]">

	   			@if($user->bio)
	   				{{ Markdown::string($user->bio) }}
	   			@endif

	   			@if(count($articles))
	   				<h2>Latest Articles</h2>
	   				@foreach($articles as $article)
	   					{{ $article->title }}<br>
	   				@endforeach
	   			@endif

	   		</div><!--

	 --></article>

    </div>
  </div>

@stop



@section('footer')

{{-- Footer --}}
{{ View::make('partial.c-footer') }}

@stop