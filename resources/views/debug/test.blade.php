<h3>nonoesp/thinker</h3>
{{ Thinker::title('My Name Is Peter I Have An Ipad') }}
<br>

<h3>grahamcampbell/markdown</h3>
{!! Markdown::convertToHtml('This is a Markdown *test*—go **bold** letters') !!}

<h3>jenssegers/date</h3>
{{ Date::now()->format('M d,   Y') }}

<h3>vinkla/hashids</h3>
{{ Hashids::encode(248) }}

<?php $item = Item::find(200); ?>
<h3>nonoesp/space</h3>
{{ Html::link(Space::path().$item->slug, Space::path().$item->slug) }}

<h3>take {{ $amount }} items</h3>
<?php $items = Item::take($amount)->get(); ?>
  @foreach($items as $item)
    {{ Html::link(Space::path().$item->slug, $item->title) }}
    <br>
  @endforeach

<h3>take {{ $amount }} more items</h3>
<?php $items = Item::orderBy('id','DESC')->take($amount)->get(); ?>
@foreach($items as $item)
  {{ Html::link(Space::path().$item->id, Space::path().$item->id) }} ({{ $item->title }})
  <br>
@endforeach

<h3>take {{ $amount }} more items with hashids</h3>
<?php $items = Item::orderBy('id','DESC')->skip($amount)->take($amount)->get(); ?>
@foreach($items as $item)
  {{ Html::link('e/'.Hashids::encode($item->id), 'e/'.Hashids::encode($item->id)) }} ({{ $item->title }})
  <br>
@endforeach
