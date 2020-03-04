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
?>

<article class="[ grid ] [ c-item ] {{ $class_is_tagged }} {{ $class_specified }} {{ $class_categories }}"><!--

--><div class="[ grid__item  one-whole ]  [ c-item__header ]  [ u-text-align--center ]">


	  {{-- Title --}}
	  @if (isset($isTitleLinked))
	  	<h1><a href="/{{$item->path()}}">{{ Thinker::title($item->title) }}</a></h1>
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

				{!! $item->htmlText(['stripTags' => ['rss', 'podcast', 'feed']]) !!}

			@endif

			@if ($item_type == 'SUMMARY_ITEM_TYPE')
				<p>
					{{ Thinker::limitMarkdownText($item->htmlText(['stripTags' => ['rss', 'podcast', 'feed']]), 275, ['figcaption']) }}
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
