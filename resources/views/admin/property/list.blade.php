@extends('folio::layout-v2')

@php
    $footer_hidden = true;
    $header_view = 'folio::partial.c-header-simple-v2';
    $header_data = array_merge($header_data ?? config('folio.header'),
    [
        'image' => null,
        'is_media_hidden' => true,
    ]);

    $menu_data = array_merge($menu_data ?? config('folio.menu'),
    [
        'items' => [
          '<i class="fa fa-eye"></i>' => $item->path(),
		  '<i class="fa fa-share"></i>' => $item->sharePath(),
		  '<i class="fa fa-pencil"></i>' => $item->editPath()
        ]
    ]);    
@endphp

@section('content')

<div class="o-wrap u-mar-t-8x">

    @if ($item)
        <p class="u-font-size--g">{{$item->title}}</p>
    @endif

    @foreach ($item->properties as $p)
        <a href="/property/edit/{{ $p->id }}">
            <strong>{{ $p->name }} →</strong>
        </a>
        <pre><code>{{ $p->value }}</code></pre>
    @endforeach

</div>

@endsection