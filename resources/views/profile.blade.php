@extends('layout.main')

<?php
	use Arma\Article;
	// Settings
	$header_classes = 'c-header--relative';

	// User Articles
	$articles = Article::where('user_id', '=', $user->id)->orderBy('id', 'DESC')->take(5)->get();

	$user_thumbnail = \View::make('partial.c-user-picture')->with(["user" => $user,
	         													   "size" => 75,
	         													   "margin_top" => "-15",
	         													   "margin_bottom" => "15",
	         													   "shouldLink" => false]);
?>


@section('content')

  <div class="[ o-band ]  [ u-border-bottom  -u-no-padding-bottom ]">
    <div class="[ o-wrap  o-wrap--standard  o-wrap--portable-tiny ]">

    	<article class="[ grid ]  [ c-article ]"><!--

    	--><div class="[ grid__item  one-whole ]  [ -u-border  u-text-align--center ]">
	   		


	   		</div><!--

    	--><div class="[ grid__item  one-quarter  portable--one-whole ]  [ -u-border  u-text-align--desk-right ]">
	   		
	   			{{ $user_thumbnail }}

	   		</div><!--

         --><div class="[ grid__item  one-half  portable--one-whole ]  [ -u-border ]">

         		<h1>{{ $user->name }}</h1>

         		@if($user->title)
	         		<div class=" [ c-article__meta  c-article__meta--closer ]">
	         			<div class="c-article__inline-container">
	         				<div class="c-article__inline-detail--medium">
	         					{{ $user->title }}
	         				</div>
	         			</div>
	     			</div>
	     		@endif

	   			@if($user->bio)
	   				{{ Markdown::string($user->bio) }}
	   				<br>
	   			@endif

	   			@if(count($articles))
	   				<h2>Latest Writings</h2>
	   				@foreach($articles as $article)

   						<?php
   					    	// Date
							$date = new Date($article->published_at);
							$date = ucWords(substr($date->format('F'), 0, 3).$date->format(' j, Y'));
						?>

	   					{{ HTML::link(Config::get('writing::path').'/'.$article->slug, $article->title, ['class' => 'u-font-size--b']) }}

   						<div class="c-article__inline-container">
   							<div class="c-article__inline-detail">
   								{{ $date }}
   							</div>
   						</div>

   						<br>

	   				@endforeach
	   			@endif

	   		</div><!--

    	--><div class="[ grid__item  one-quarter  portable--one-whole ]  [ -u-border ]">
	   		
	   			

	   		</div><!--

	 --></article>

    </div>
  </div>

@stop



@section('footer')

{{-- Footer --}}
{{ View::make('partial.c-footer') }}

@stop