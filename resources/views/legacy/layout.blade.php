<?php

	$item = $item ?? null;

	// Open graph
	$og_author = $og_author ?? config('folio.meta.author');
	$og_description = ($og_description ?? config('folio.meta.description')) ?? config('folio.description');
	$site_title = $site_title ?? config('folio.title');
	$title = $title ?? $site_title;
	$og_title = $og_title ?? $title;
	$og_image = $og_image ?? config('folio.image-src');
	$og_url = $og_url ?? Request::root().'/'.Request::path();
	$fb_app_id = $fb_app_id ?? config('folio.social.facebook.app_id');

	// Icons
	$apple_touch_icon_default = '/apple-touch-icon.png';

	// Apple App id
	$apple_app_id = $apple_app_id ?? config('folio.apple-app-id');

	// Google Analytics
	$google_analytics = $google_analytics ?? Folio::googleAnalytics();

	// Sitemap
	$sitemap = $sitemap ?? config('folio.sitemap');

	// Assets
	$folio_css = ($folio_css ?? config('folio.css')) ?? '/folio/css/folio.css';
	$folio_typekit = $folio_typekit ?? config('folio.typekit');

	// Try to pass CSS through Laravel mix to bust the cache
	try {
		$folio_css = mix($folio_css);
	}
	catch (Exception $e) {
		// Graceful fallback to CSS without cache busting
		$folio_css = '/folio/css/folio.css?mix-busting-failed';
	}
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"{!! isset($html_theme_class) ? ' class="'.$html_theme_class.'"' : null !!}>

<!-- This site is built with github.com/nonoesp/laravel-folio -->

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1"/>
	<title>{{ $title }}</title>
	<link rel="shortcut icon" href="/favicon.png" type="image/png">
	<link rel="stylesheet" type="text/css" href="{{ $folio_css }}">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

@if($folio_typekit)
	<!-- TypeKit -->
	<link rel="dns-prefetch" href="http://use.typekit.net">
	<script type="text/javascript" src="//use.typekit.net/{{ $folio_typekit }}.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
@endif

	<!-- Icon -->
	<link rel="apple-touch-icon" sizes="180x180" href="{{ $apple_touch_icon ?? $apple_touch_icon_default }}" />
	<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/manifest.json">
	<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="theme-color" content="#ffffff">

	<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png" />
	<meta name="apple-mobile-web-app-title" content="{{ config('folio.title-short') }}" />
	<link rel="apple-touch-icon-precomposed" href="{{ $apple_touch_icon ?? $apple_touch_icon_default }}" />
	<link rel="apple-touch-icon" href="{{ $apple_touch_icon ?? $apple_touch_icon_default }}" />
@if($apple_app_id)

	<!-- Apple Podcast id -->
	<meta name="apple-itunes-app" content="app-id={{ $apple_app_id }}">
@endif

	<!-- Tags -->
	<meta name="description" content="{{ $og_description }}" />
	<link rel="image_src" href="{{ $og_image }}" />
@if($og_author)
	<meta name="author" content="{{ $og_author }}" />
@endif

	<!-- Open Graph meta data -->
	<meta property="fb:app_id" content="{{ $fb_app_id ?? $fb_app_id_default }}" />
	<meta property="og:url" content="{{ $og_url ?? $og_url_default }}" />
	<meta property="og:image" content="{{ $og_image ?? $og_image_default }}" />
	<meta property="og:title" content="{{ $og_title }}" />
	<meta property="og:description" content="{{ $og_description ?? $og_description_default }}" />
	<meta property="og:type" content="{{ $og_type ?? 'profile' }}" />
	<meta property="og:image:width" content="1200" />
	<meta property="og:image:height" content="900" />
	@yield('open_object_metadata')

	<!-- Twitter Card -->
	<meta name="twitter:site" content="{{ config('folio.social.twitter.handle') }}" />
	<meta name="twitter:creator" content="{{ config('folio.social.twitter.handle') }}" />
	<meta name="twitter:title" content="{{ $og_title }}" />
	<meta name="twitter:description" content="{{ $og_description ?? $og_description_default }}" />
	<meta name="twitter:image" content="{{ $og_image ?? $og_image_default }}" />
	<meta name="twitter:card" content="summary_large_image" />

	<!-- RSS -->
	<link rel="alternate" type="application/atom+xml" href="/{{ config('folio.feed.route') }}" />

@if(!Auth::check() && $google_analytics)
	<!-- DNS Prefetch · Google Fonts -->
	<link rel="dns-prefetch" href="//fonts.googleapis.com">
	<link rel="dns-prefetch" href="//fonts.gstatic.com">
@endif

@if(config('folio.imgix') && config('imgix.domain'))
	<!-- DNS Prefetch · Imgix -->
	<link rel="dns-prefetch" href="//{{ config('imgix.domain') }}">
@endif

@if(config('folio.prefetch'))
	<!-- DNS Prefetch · Folio -->
@foreach(config('folio.prefetch') as $prefetchURL)
	<link rel="dns-prefetch" href="//{{ $prefetchURL }}">
@endforeach
@endif

@if($sitemap)
	<!-- Sitemap -->
	<meta name="sitemap" content="{{ $sitemap }}" />
@endif
@if(!Auth::check() && $google_analytics)
	<!-- Google Analytics -->
	@if(Str::of($google_analytics)->startsWith('UA-'))
	<link rel="dns-prefetch" href="//www.google-analytics.com">
	<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '{{ $google_analytics }}', 'auto');ga('send', 'pageview');</script>
	@else
	<link rel="dns-prefetch" href="//www.googletagmanager.com">
	<script async src="https://www.googletagmanager.com/gtag/js?id={{ $google_analytics }}"></script>
	<script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '{{ $google_analytics }}');</script>	
	@endif
@endif
@yield('metadata')
@stack('metadata')

</head>

<body>

	@if(Auth::check() && config('folio.debug.load-time'))
		{!! view('folio::partial.o-notification', [
			'notification' => 'This page took '. (microtime(true) - LARAVEL_START) .' seconds to render',
			'classes' => ['o-notification--light'],
		]) !!}
	@endif

	@if($notification = Request::session()->get('notification'))
		{!! view('folio::partial.o-notification', [
			'notification' => $notification,
			'classes' => ['o-notification--light'],
		]) !!}
	@endif

	{{-- Header --}}
	<?php if(!isset($header_hidden)){ $header_hidden = false; }
				if(!isset($header_view)){ $header_view = config('folio.header.view'); }
				if(!isset($header_classes)){ $header_classes = config('folio.header.classes');; }
				if(!isset($header_data)){ $header_data = []; } ?>
	@if(!$header_hidden)
				{!! view($header_view, [
					'classes' => $header_classes,
					'data' => $header_data,
					'item' => $item,
				]) !!}
	@endif

	{{-- Cover --}}
	<?php if(!isset($cover_active)) $cover_active = true;
		  if(!isset($cover_hidden)) $cover_hidden = true; ?>
  @if($cover_active and $cover_hidden != true)
      	{!! view('folio::legacy.partial.c-cover', $cover_data) !!}
  @endif

@yield('floating.menu', view('folio::partial.c-floating-menu'))

@yield('content')

@yield('footer')

<script src="{{ mix('/folio/js/manifest.js') }}"></script>
<script src="{{ mix('/folio/js/vendor.js') }}"></script>
<script src="{{ mix('/folio/js/folio.js') }}"></script>
<script>
	var trans = {!! json_encode(trans('folio::base')) !!};
</script>
@yield('scripts')
@stack('scripts')

</body>
</html>
