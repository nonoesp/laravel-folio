<style media="screen">
html,body{
}
  .o-wrap {
    max-width:640px;
      margin:auto;
  }
  img {width:100%;}
</style>

<div class="o-wrap">

<article class="[ grid ] [ c-item ]">

  <div class="[ grid__item  one-whole ]  [ c-item__header ]  [ u-text-align--center ]">

	  {{-- Title --}}
    <h1>{{ Thinker::title($item->title) }}</h1>
    <h1>{{ Thinker::title($item->title) }}</h1>

    </div>

    <div class="[ grid__item  one-whole ]  [ c-item__body ]">

      <p>
        This dummy template renders the item as plain HTML without using any layouts or stylesheets, and repeats both title and image (if existing) to display that the system is working properly.
      </p>

      @if ($item->image)
        <p class="c-item__cover-media"><img src="{{ $item->image }}"></p>
  		@endif

      <p>
        This dummy template renders the item as plain HTML without using any layouts or stylesheets, and repeats both title and image (if existing) to display that the system is working properly.
      </p>

      @if ($item->image)
        <p><img src="{{ $item->image }}"></p>
  		@endif

      {!! Markdown::convertToHtml($item->text) !!}

    </div>

</article>

</div>
