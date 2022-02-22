<style>
* {
  font-family: 'Inter', system-ui, sans-serif;
  font-size: 0.95rem;
}

.comment {
  color: rgba(0,0,0,0.4);
}
</style>

<h2>Folio debug</h2>

<h3>Thinker</h3>
{{ Thinker::title('My Name Is Peter I Have An Ipad') }}
<br>

<h3>grahamcampbell/markdown</h3>
{!! Item::convertToHtml('This is a Markdown *test*—go **bold** letters') !!}

<h3>jenssegers/date</h3>
{{ Date::now()->format('M d,   Y') }}

<h3>hashids/hashids</h3>
{{ Folio::hashids()->encode(248) }}

<?php $item = Item::find(200); ?>
<h3>nonoesp/folio</h3>
{{ Html::link(Folio::path().$item->slug, Folio::path().$item->slug) }}

<h3>take {{ $amount }} items</h3>
<?php $items = Item::take($amount)->get(); ?>
  @foreach($items as $item)
    {{ Html::link($item->path(), $item->title) }}
    <br>
  @endforeach

<h3>take {{ $amount }} more items · permalinks</h3>
<?php $items = Item::orderBy('id','DESC')->take($amount)->get(); ?>
@foreach($items as $item)
  @php
      $link = $item->permalink();
  @endphp
  {{ Html::link($link, $link) }} · {{ $item->title }}
  <br>
@endforeach

<h3>take {{ $amount }} more items with hashids</h3>
<?php $items = Item::orderBy('id','DESC')->skip($amount)->take($amount)->get(); ?>
@foreach($items as $item)
  @php
      $link = 'e/'.Folio::hashids()->encode($item->id);
  @endphp
  {{ Html::link($link, $link) }} · {{ $item->title }}
  <br>
@endforeach

<h3>spatie/laravel-translatable</h3>

<?php
  $item = new Item();
  $item->setTranslation('title', 'en', 'This weird item');
  $item->setTranslation('title', 'es', 'Este raro objeto');
?>

@php App::setLocale('en') @endphp
English [en] · {{ $item->title }}
<span class="comment">// This weird item</span>
<br>
@php App::setLocale('es') @endphp
Spanish [es] · {{ $item->title }}
<span class="comment">// Este raro objeto</span>
<br>
@php App::setLocale('it') @endphp
Italian [it] · {{ $item->title }}
<span class="comment">// fallback to 'en'</span>

@php App::setLocale('en') @endphp

<h3>Item</h3>

<?php
  $item = Item::withTrashed()->orderBy('published_at', 'DESC')->first();
?>

title · {{ $item->text }}
<br>
<br>
edit path · {{ $item->editPath() }}
<br>
url · {{ $item->URL() }}
<br>
share path · {{ $item->sharePath() }}
<br>
encoded path · {{ $item->encodedPath() }}