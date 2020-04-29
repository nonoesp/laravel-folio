@extends('folio::layout-v2')

@php
    $title = 'Properties · '.$item->title;
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

<style>
  pre {
      max-width: 100%;
      background-color: white;
      padding: 20px;
      border-radius: 4px;
      border: 1px solid #dadada;
      overflow: hidden;
  }

  pre code {
      white-space: pre-wrap;
  }

  .property-title {
      margin-bottom: 20px;
  }
</style>

<div class="o-wrap o-wrap--size-650 u-mar-t-6x">

    @if ($item)
        <p class="u-font-size--h f-inter" style="letter-spacing: -0.03em">
            <strong>{{$item->title}}</strong>
        </p>
    @endif

    <div class="u-font-size--b">
        @foreach ($item->properties->sortBy(function ($property) {
            return $property->order_column;
        }) as $p)
            <div @if($p->name[0] === '-' || $p->name[0] === '#') style="opacity:0.3" @endif >
                <div class="property-title">
                    <a href="/property/edit/{{ $p->id }}">
                        <strong>{{ $p->name }} →</strong>
                    </a>
                </div>
                <pre><code>{{ $p->value }}</code></pre>
            </div>
        @endforeach
    </div>

</div>

@endsection