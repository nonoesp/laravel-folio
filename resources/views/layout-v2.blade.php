@extends('folio::layout-skeleton')

@php
    use Illuminate\Support\Str;

    $title = $title ?? config('folio.title');

    $author = $author ?? config('folio.og.author');
    $og_author = $og_author ?? config('folio.social.facebook.author');
    $og_title = $og_title ?? ($title ?? config('folio.og.title'));
    $og_description = $og_description ?? config('folio.og.description');
    $og_image = $og_image ?? config('folio.og.image');
    $og_url = $og_url ?? request()->root().'/'.request()->path();
    $og_type = $og_type ?? 'website';
    
    $fb_app_id = $fb_app_id ?? config('folio.social.facebook.app_id');
    $apple_app_id = $apple_app_id ?? config('folio.social.apple.app-id');
    $google_analytics = $google_analytics ?? Folio::googleAnalytics();
    $google_fonts = $google_fonts ?? null;
    $typekit = $typekit ?? config('folio.typekit');
    $sitemap = $sitemap ?? config('folio.sitemap');
    $prefetch = $prefetch ?? config('folio.prefetch');
    $imgix = $imgix ?? config('folio.imgix');
    $imgix_domain = $imgix_domain ?? config('imgix.domain');
    $scripts = $scripts ?? null;
    $stylesheets = $stylesheets ?? null;

    $admin = $admin ?? Auth::check();
    $debug_load_time = $debug_load_time ?? config('folio.debug.load-time');
    $notification_data = $notification_data ?? ['classes' => ['--light']];

    // Folio
    $rss = $rss ?? config('folio.feed.route');
    $css = $css ?? config('folio.css');
    $favicon = $favicon ?? mix(Folio::asset('images/favicon-48x48.png'));
    $canonical = $canonical ?? request()->url();

    // Try to pass CSS through Laravel mix to bust the cache
    try {
        $css = mix($css);
    }
    catch (Exception $e) {
        // Graceful fallback to CSS without cache busting
        $css = $css.'?mix-cache-busting-failed';
    }    

@endphp

@prepend('metadata')

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <title>{{ $title }}</title>
    <link rel="stylesheet" type="text/css" href="{{ $css }}">
    <link rel="icon" type="image/png" href="{{ $favicon }}" />
    <link rel="alternate" type="application/atom+xml" href="/{{ $rss }}" />
    <link rel="canonical" href="{{ $canonical }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

@if($typekit)
	<!-- TypeKit -->
    <link rel="dns-prefetch" href="http://use.typekit.net">
    <link rel="stylesheet" href="https://use.typekit.net/{{ $typekit }}.css">
@endif

    <!-- Tags -->
	<link rel="image_src" href="{{ $og_image }}" />
	<meta name="description" content="{{ $og_description }}" />
@if($og_author)
	<meta name="author" content="{{ $author }}" />
@endif

	<!-- Open graph -->
	<meta property="fb:app_id" content="{{ $fb_app_id }}" />
	<meta property="og:url" content="{{ $og_url }}" />
	<meta property="og:title" content="{{ $og_title }}" />
	<meta property="og:image" content="{{ $og_image }}" />
	<meta property="og:description" content="{{ $og_description }}" />
	<meta property="og:type" content="{{ $og_type }}" />
	{{-- <meta property="og:image:width" content="1200" /> --}}
    {{-- <meta property="og:image:height" content="900" /> --}}
@if($og_author && $og_type == 'article')
    <meta property="article:author" content="{{ $og_author }}" />
@endif
@isset($item)
    <meta property="article:publisher" content="{{ config('folio.social.facebook.publisher') }}" />
    <meta property="article:modified_time" content="{{ $item->updated_at }}" />
    <meta property="article:published_time" content="{{ $item->published_at }}" />
@foreach($item->tagNames() as $tagName)
    <meta property="article:tag" content="{{ strtolower($tagName) }}" />
@endforeach
@endisset

	<!-- Twitter Card -->
	<meta name="twitter:site" content="{{ config('folio.social.twitter.handle') }}" />
	<meta name="twitter:creator" content="{{ config('folio.social.twitter.handle') }}" />
	<meta name="twitter:title" content="{{ $og_title }}" />
	<meta name="twitter:description" content="{{ $og_description }}" />
	<meta name="twitter:image" content="{{ $og_image }}" />
	<meta name="twitter:card" content="summary_large_image" />

@if($apple_app_id)
	<!-- Apple Podcast id -->
	<meta name="apple-itunes-app" content="app-id={{ $apple_app_id }}">
@endif

@if($sitemap)
	<!-- Sitemap -->
	<meta name="sitemap" content="{{ $sitemap }}" />
@endif

@if($prefetch)
	<!-- DNS Prefetch · Folio -->
@foreach($prefetch as $prefetch_url)
	<link rel="dns-prefetch" href="//{{ $prefetch_url }}">
@endforeach
@endif

@if($imgix && $imgix_domain)
	<!-- DNS Prefetch · Imgix -->
	<link rel="preconnect" href="//{{ $imgix_domain }}">
	<link rel="dns-prefetch" href="//{{ $imgix_domain }}">
@endif

@if(!$admin && $google_analytics)
    <!-- Google Analytics -->
	<link rel="dns-prefetch" href="//www.google-analytics.com">
	<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '{{ $google_analytics }}', 'auto');ga('send', 'pageview');</script>
@endif

@if($google_fonts)
    <!-- Google Fonts -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
@foreach($google_fonts as $font)
    <link href="https://fonts.googleapis.com/css2?family={{ $font->value }}" rel="stylesheet">
@endforeach
@endif

@if($scripts)
    <!-- Scripts -->
@foreach($scripts as $script)
@if(Str::of($script->value)->contains('.js') && Str::of($script->value)->contains('/'))
	<script language="javascript" type="text/javascript" src="{{ $script->value }}"></script>
@else
    <script>{!! $script->value !!}</script>
@endif
@endforeach
@endif

@if($stylesheets)
    <!-- Stylesheets -->
@foreach($stylesheets as $stylesheet)
@if(Str::of($stylesheet->value)->contains('.css') && Str::of($stylesheet->value)->contains('/'))
    <link rel="stylesheet" href="{{ $stylesheet->value }}" type="text/css">
@else
    <style>{!! $stylesheet->value !!}</style>
@endif    
@endforeach
@endif

@endprepend

@if($admin && $debug_load_time)
@prepend('notifications')
{!! view('folio::partial.o-notification', array_merge($notification_data, [
    'notification' => 'This page took '. (microtime(true) - LARAVEL_START) .' seconds to render',
])) !!}
@endprepend
@endif

@if($notification = session()->get('notification'))
@prepend('notifications')
{!! view('folio::partial.o-notification', array_merge($notification_data, [
    'notification' => $notification,
])) !!}
@endprepend
@endif

@prepend('scripts')
    
    <script src="{{ mix('/folio/js/manifest.js') }}" defer></script>
    <script src="{{ mix('/folio/js/vendor.js') }}" defer></script>
    <script src="{{ mix('/folio/js/folio.js') }}" defer></script>
    <script>
        var trans = {!! json_encode(trans('folio::base')) !!};
    </script>
@endprepend

@php

    /* 
    # View fallbacks
    Tip: Hide any of these subviews by setting them to hidden.
    For instance, you'd hide the header with $header_hidden = true;
    */

    $menu_view = $menu_view ?? config('folio.menu.view');
    $menu_data = $menu_data ?? config('folio.menu');
    $menu_hidden = $menu_hidden ?? config('folio.menu.hidden');

    $cover_view = $cover_view ?? config('folio.cover.view');
    $cover_data = $cover_data ?? config('folio.cover');
    $cover_hidden = $cover_hidden ?? config('folio.cover.hidden');

    $header_view = $header_view ?? config('folio.header.view');
    $header_data = $header_data ?? config('folio.header');
    $header_hidden = $header_hidden ?? config('folio.header.hidden');

    $footer_view = $footer_view ?? config('folio.footer.view');
    $footer_data = $footer_data ?? config('folio.footer');
    $footer_hidden = $footer_hidden ?? config('folio.footer.hidden');

@endphp

@unless($menu_hidden)
@section('menu', view($menu_view , $menu_data))
@endunless

@unless($header_hidden)
@section('header', view($header_view, $header_data))
@endunless

@unless($cover_hidden)
@section('cover', view($cover_view, $cover_data))
@endunless

@unless($footer_hidden)
@section('footer', view($footer_view, $footer_data))
@endunless