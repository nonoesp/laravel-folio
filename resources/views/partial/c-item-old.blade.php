
{{-- Item Component --}}

<item class="c-item {{ Thinker::itemCategoryClass($item->tagNames(), 'c-item') }}{{ (count($item->tagNames()) > 0) ? 'is-tagged' : '' }}">

		{{-- Title --}}
		@if (isset($isTitleLinked))
			<h1>{{ HTML::link('/writing/'.$item->slug, Thinker::title($item->title)) }}</h1>
		@else
			<h1>{{ Thinker::title($item->title) }}</h1>
		@endif

		{{-- Date --}}
		<?php $date = new Date($item->published_at); ?>
		<p class="c-item__date">{{ ucWords($date->format('l j, F Y')); }}</p>

		{{-- Cover Image --}}
		@if ($item->image)
			<p class="c-item__cover-media"><img src="{{ $item->image }}"></p>
		@endif

		{{-- Cover Video --}}
		@if ($item->video)
			<p class="c-item__cover-media">{{ Thinker::videoWithURL($item->video) }}</p>
		@endif

		{{-- Text --}}
			<?php echo Markdown::convertToHtml( $item->text ); ?>

		{{-- Tags --}}
			<p class="c-item__tags">{{ Thinker::displayItemTags($item->tagNames(), 'c-item__tag u-case-upper') }}</p>

		{{-- Share --}}
		{{-- <p>Share on Twitter</p> --}}

</item>
