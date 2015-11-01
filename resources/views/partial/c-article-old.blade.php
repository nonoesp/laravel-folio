
{{-- Article Component --}}

<article class="c-article {{ Thinker::articleCategoryClass($article->tagNames(), 'c-article') }}{{ (count($article->tagNames()) > 0) ? 'is-tagged' : '' }}">

		{{-- Title --}}
		@if (isset($isTitleLinked))
			<h1>{{ HTML::link('/writing/'.$article->slug, Thinker::title($article->title)) }}</h1>
		@else
			<h1>{{ Thinker::title($article->title) }}</h1>
		@endif

		{{-- Date --}}
		<?php $date = new Date($article->published_at); ?>
		<p class="c-article__date">{{ ucWords($date->format('l j, F Y')); }}</p>

		{{-- Cover Image --}}
		@if ($article->image)
			<p class="c-article__cover-media"><img src="{{ $article->image }}"></p>
		@endif

		{{-- Cover Video --}}
		@if ($article->video)
			<p class="c-article__cover-media">{{ Thinker::videoWithURL($article->video) }}</p>
		@endif
		
		{{-- Text --}}
			<?php echo Markdown::string( $article->text ); ?>
		
		{{-- Tags --}}
			<p class="c-article__tags">{{ Thinker::displayArticleTags($article->tagNames(), 'c-article__tag u-case-upper') }}</p>

		{{-- Share --}}
		{{-- <p>Share on Twitter</p> --}}

</article>