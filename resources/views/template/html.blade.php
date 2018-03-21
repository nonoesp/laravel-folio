@extends('folio::layout.plain')

<?php
  $site_title = $item->title.' | '.config('folio.title');
  $header_hidden = true;
  $folio_css = '';
?>

<head>
<title>{{ $site_title }}</title>
</head>

<style media="screen">
html,body{
    font-size:1em;
}
  .o-wrap {
    max-width:640px;
      //margin:auto;
  }
  img {width:100%;}

      .c-floating-menu {
      position:fixed;
      top:0;
      right:0;
      width:150px;
      height:80px;
      padding:1.5em 1.5em;
      z-index:300;
      cursor:pointer;
    }

    .c-float-menu__item {
      float:right;
      font-size:80%;
      color:rgba(0,0,0,0.50);
      font-weight:600;
      text-transform:uppercase;
      background-color:white;
      padding:0 1em;
      border-radius:25px;
      text-decoration:none;
    }
</style>

@if($user = Auth::user())
  @if($user->is_admin)
    <div class="c-floating-menu">
      <a href="/admin/item/edit/{{ $item->id }}" class="c-float-menu__item">edit</a>
    </div>
    @endif
@endif

{{--  Header  --}}
<a href="/">{{ config('folio.title-short') }}</a>

<br/><br/>

@section('content')

  <div class="[ o-wrap o-wrap--size-650 o-wrap--align-left ]">

  <article class="[ grid ] [ c-item ]">

    <div class="[ grid__item  one-whole ]  [ c-item__header ]  [ u-text-align--center ]">

      {{-- Title --}}
      <h1>{{ Thinker::title($item->title) }}</h1>

      </div>

      <div class="[ grid__item  one-whole ]  [ c-item__body ]">

        @if ($item->image)
          <p><img src="{{ $item->image }}"></p>
        @endif

        {!! $item->htmlText() !!}

      </div>

  </article>

  </div>

@stop