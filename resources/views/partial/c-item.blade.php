<?php

	/* [ c-item ]
	/
	/  [ $item ]
	/  [ $item_type ]
	/
	/	 DEFAULT_ITEM_TYPE
	/    SUMMARY_ITEM_TYPE
	/    LISTING_ITEM_TYPE (TODO)
	/    EXPECTED_ITEM_TYPE (TODO)
	/    RECOMMENDED_ITEM_TYPE (TODO)
	/
	*/

	// Item Type
	!isset($item_type) ? $item_type = 'DEFAULT_ITEM_TYPE' : '' ;

    // Date
    $date = new Date($item->published_at);
    $date = ucWords(substr($date->format('F'), 0, 3).$date->format(' j, Y'));

    // Author
    $user_thumbnail = NULL;
    $user = NULL;
    if($item->user_id) {
 	   $user = User::find($item->user_id);
 	//   $user_thumbnail = View::make('partial.c-user-picture')->with(["user" => $user,
	//										     		   			 "size" => 36]);
	}
?>

<item class="[ grid ] [ c-item @if(isset($class)){{ $class }}@endif{{ Space::itemCategoryClass($item->tagNames(), 'c-item') }}{{ (count($item->tagNames()) > 0) ? 'is-tagged' : '' }}]"><!--

--><div class="grid__item">
	@if($property = $item->property('location'))

	<div class="[ m-fa  m-fa--black-static ]">
		<i class="[ fa fa-map-marker fa--social ]">s</i>
	</div>

		<p>{{ $property['label'].': '.$property['value'] }}</p>
	@endif
</div><!--  --><div class="[ grid__item  one-quarter  portable--one-whole ]  [ c-item__header ]">


	  {{-- Title --}}
	  @if (isset($isTitleLinked))
	    <h1>{!! Html::link(Space::path().$item->slug, Thinker::title($item->title)) !!}</h1>
	  @else
	    <h1>{!! Thinker::title($item->title) !!}</h1>
	  @endif

	  {{-- Meta --}}

	  @if(isset($user))

		  <div class="c-item__meta">
			  {{ $user_thumbnail }}
			  <div class="[ c-item__inline-container ]">
			  	<div class="[ c-item__inline-detail  c-item__inline-detail--user ]">
			  		<a href="{{ Space::userURL($user) }}" class="c-item__link--accent">@if($user){{ $user->name }}@endif</a>
			  	</div>
			  	<br>
			  	<div class="[ c-item__inline-detail ]">
			  		{{ $date }}
			  	</div>
			  </div>
		  </div>

	  @else

		  <div class="c-item__meta">
			  	<div class="[ c-item__inline-detail  c-item__inline-detail--medium ]">
			  		{{ $date }}
			  	</div>
		  </div>

	  @endif

    </div><!--

 --><div class="[ grid__item  one-half  portable--one-whole ]  [ c-item__body ]  [ -u-border ]">

 		@if ($item_type == 'DEFAULT_ITEM_TYPE')
			{{-- Cover Image --}}
			@if ($item->image)
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
					{!! Thinker::limitMarkdownText(Markdown::convertToHtml($item->text), 275, array('figcaption')) !!}
					{!! Html::link(Space::path().$item->slug, trans('space::base.continue-reading')) !!}
				</p>
			@endif

		{{-- Tags --}}
			@if (count($item->tagNames()) > 0)
				<p class="c-item__tags">{!! Space::tagListWithItemAndClass($item, 'c-item__tag u-case-upper') !!}</p>
			@endif

    </div><!--

 -->{{--<div class="[ grid__item  one-quarter  lap--one-whole ]  [ -u-border ]">
 	<div class="-u-border">Text</div>
    </div>--}}

</item>
