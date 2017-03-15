<article class="[ grid ] [ c-item ]">

  <div class="[ grid__item  one-whole ]  [ c-item__header ]  [ u-text-align--center ]">

	  {{-- Title --}}
	  @if (isset($isTitleLinked))
	    <h1>{{ Html::link(Space::path().$item->slug, Thinker::title($item->title)) }}</h1>
	  @else
	    <h1>{{ Thinker::title($item->title) }}</h1>
	  @endif

    </div>

    <div class="[ grid__item  one-whole ]  [ c-item__body ]">

 		@if ($item_type == 'DEFAULT_ITEM_TYPE')
			{{-- Cover Image --}}
			@if ($item->image)
				<p class="c-item__cover-media"><img src="{{ $item->image }}"></p>
			@endif

    </div>

</article>
