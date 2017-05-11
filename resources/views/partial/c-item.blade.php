<?php

	/* [ c-item ]
	/
	/  [ $item ]
	/  [ $item_type ]
	/
	/	 DEFAULT_ITEM_TYPE
	/    SUMMARY_ITEM_TYPE
	/    EXPECTED_ITEM_TYPE (TODO)
	/    RECOMMENDED_ITEM_TYPE (TODO)
	/
	*/

	// Article Type
	!isset($item_type) ? $item_type = 'DEFAULT_ITEM_TYPE' : '' ;

    // Date
    $date = new Date($item->published_at);
    $date = ucWords($date->format('F').' '.$date->format('j, Y'));

		// Class
		$class_specified = '';
		if(isset($class) && $class != '') $class_specified = '[ '.$class.' ]';
		$class_is_tagged = '';
		if($item->tagNames()) $class_is_tagged = '[ is-tagged ]';
		$class_categories = '';
		if($categories = Folio::itemCategoryClass($item, 'c-item')) $class_categories = '[ '.$categories.']';

		// Author
		// $user_thumbnail = NULL;
		// $user = NULL;
		// if($item->user_id) {
		// 	$user = User::find($item->user_id);
		// 	$user_thumbnail = view('partial.c-user-picture')->with(["user" => $user, "size" => 36]);
		// }
?>

<article class="[ grid ] [ c-item ] {{ $class_is_tagged }} {{ $class_specified }} {{ $class_categories }}"><!--

--><div class="[ grid__item  one-whole ]  [ c-item__header ]  [ u-text-align--center ]">


	  {{-- Title --}}
	  @if (isset($isTitleLinked))
	    <h1>{{ Html::link(Folio::path().$item->slug, Thinker::title($item->title)) }}</h1>
	  @else
	    <h1>{{ Thinker::title($item->title) }}</h1>
	  @endif

    </div><!--

 --><div class="[ grid__item  one-whole ]  [ c-item__body ]  [ -u-border ]">

 		@if ($item_type == 'DEFAULT_ITEM_TYPE')
			{{-- Cover Image --}}
			@if ($item->image && false)
				<p class="c-item__cover-media"><img src="{{ $item->image }}"></p>
			@endif

			{{-- Cover Video --}}
			@if ($item->video)
				{!! Thinker::videoWithURL($item->video, 'c-item__cover-media') !!}
			@endif
		@endif

		{{-- Text --}}
			@if ($item_type == 'DEFAULT_ITEM_TYPE')

				@if ($item->isPublic())
					{!! Markdown::convertToHtml($item->text) !!}
				@else
					@if($twitter_handle = Authenticate::isUserLoggedInTwitter())
						<?php /*@if($item->visibleFor($twitter_handle) OR Auth::user()->is_admin)*/ ?>
						@if($item->visibleFor($twitter_handle))
							{{--Visible for @twitter_handle--}}
							{!! Markdown::convertToHtml($item->text) !!}
						@else
							{{--Not visible for this @twitter_handle--}}
							<p>Oh, this content doesn't seem to be visible for {{ "@".$twitter_handle }}.</p>
						@endif
					@else
						{{--Need to log in in Twitter to access content--}}
						<p class="u-text-align--center">
							Access to see this content.
							<br><br>
							<a href="/twitter/login" class="u-a--box-shadow-reset">{{ Form::button('Sign in with Twitter', array('class' => 'button--twitter-hero')) }}</a>
						</p>
					@endif

				@endif

			@endif

			@if ($item_type == 'SUMMARY_ITEM_TYPE')
				<p>
					{{ Thinker::limitMarkdownText(Markdown::convertToHtml($item->text), 275, array('figcaption')) }}
					{{ Html::link(Folio::path().$item->slug, trans('folio::base.continue-reading')) }}
				</p>
			@endif

	  {{-- Meta --}}

		  <div class="c-item__meta">
			  		{{ $date }}
		  </p>

		{{-- Tags --}}
			@if (count($item->tagNames()) > 0)
				<p class="c-item__tags">{!! Folio::tagListWithItemAndClass($item, 'c-item__tag u-case-upper') !!}</p>
			@endif

    </div><!--

 --></article>
