

<?php

	/* [ c-article ]
	/
	/  [ $article ]
	/  [ $article_type ]
	/
	/	 DEFAULT_ARTICLE_TYPE
	/    SUMMARY_ARTICLE_TYPE
	/    EXPECTED_ARTICLE_TYPE (TODO)
	/    RECOMMENDED_ARTICLE_TYPE (TODO)
	/	
	*/

	// Article Type
	!isset($article_type) ? $article_type = 'DEFAULT_ARTICLE_TYPE' : '' ;

    // Date
    $date = new Date($article->published_at);
    $date = ucWords(substr($date->format('F'), 0, 3).$date->format(' j, Y'));

    // Author
    $user_thumbnail = NULL;
    $user = NULL;
    if($article->user_id) {
 	   $user = User::find($article->user_id);
 	   $user_thumbnail = View::make('partial.c-user-picture')->with(["user" => $user,
	  										     		   			 "size" => 36]);
	}
?>

<article class="[ grid ]  [ c-article @if(isset($class)) {{ $class }} @endif{{ Writing::articleCategoryClass($article->tagNames(), 'c-article') }}{{ (count($article->tagNames()) > 0) ? 'is-tagged' : '' }} ]"><!--

 --><div class="[ grid__item  one-quarter  portable--one-whole ]  [ -u-border ]">


	  {{-- Title --}}
	  @if (isset($isTitleLinked))
	    <h1>{{ HTML::link(Config::get('writing::path').'/'.$article->slug, Thinker::title($article->title)) }}</h1>
	  @else
	    <h1>{{ Thinker::title($article->title) }}</h1>
	  @endif

	  {{-- Meta --}}

	  @if(isset($user))

		  <div class="c-article__meta">
			  {{ $user_thumbnail }}
			  <div class="c-article__meta__inline">
			  	<div class="c-article__meta__inline--user">
			  		<a href="{{ Writing::userURL($user) }}" class="c-article__link--accent">@if($user){{ $user->name }}@endif</a>
			  	</div>
			  	<br>
			  	<div class="c-article__meta__inline--date">
			  		{{ $date }}
			  	</div>
			  </div>
		  </div>	

	  @else

		  <div class="c-article__meta">
			  	<div class="c-article__meta__inline--date-big">
			  		{{ $date }}
			  	</div>
		  </div>
  
	  @endif

    </div><!--

 --><div class="[ grid__item  one-half  portable--one-whole ]  [ -u-border ]">

 		@if ($article_type == 'DEFAULT_ARTICLE_TYPE')
			{{-- Cover Image --}}
			@if ($article->image)
				<p class="c-article__cover-media"><img src="{{ $article->image }}"></p>
			@endif

			{{-- Cover Video --}}
			@if ($article->video)
				{{ Thinker::videoWithURL($article->video, 'c-article__cover-media') }}
			@endif
		@endif

		{{-- Text --}}
			@if ($article_type == 'DEFAULT_ARTICLE_TYPE')
				{{ Markdown::string($article->text) }}
			@endif

			@if ($article_type == 'SUMMARY_ARTICLE_TYPE')
				<p>
					{{ Thinker::limitMarkdownText(Markdown::string($article->text), 275, array('figcaption')) }}
					{{ HTML::link(Config::get('writing::path').'/'.$article->slug, trans('writing::base.continue-reading')) }}
				</p>
			@endif
		
		{{-- Tags --}}
			@if (count($article->tagNames()) > 0)
				<p class="c-article__tags">{{ Writing::displayArticleTags($article->tagNames(), 'c-article__tag u-case-upper') }}</p>
			@endif

    </div><!--

 -->{{--<div class="[ grid__item  one-quarter  lap--one-whole ]  [ -u-border ]">
 	<div class="-u-border">a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f a b c d e f </div>
    </div>--}}

</article>