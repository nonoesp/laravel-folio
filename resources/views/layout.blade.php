<?php
	//$folio_typekit = config('folio.typekit');
	//$folio_css = config('folio.css');
	if(!isset($folio_typekit)) { $folio_typekit = config('folio.typekit'); }
	$og_title_default = config('folio.title');
	$og_description_default = config('folio.description');
	$og_image_default = config('folio.image-src');
	$og_url_default = Request::root().'/'.Request::path();
	$fb_app_id_default = config('folio.social.facebook.app_id');
?>

<!DOCTYPE html>
<html lang="en">

<!-- This site is built with github.com/nonoesp/laravel-folio -->

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimal-ui"/>
	<title>{{ $site_title or config('folio.title') }}</title>
	<link rel="shortcut icon" href="/favicon.png" type="image/png">
	<link rel="apple-touch-icon" sizes="144x144" href="/appicon.png">
	<link rel="stylesheet" type="text/css" href="{{ $folio_css or config('folio.css') }}">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

@if($folio_typekit)
	<!-- TypeKit -->
	<script type="text/javascript" src="//use.typekit.net/{{ $folio_typekit }}.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
@endif

	<!-- Icon -->
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/manifest.json">
	<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="theme-color" content="#ffffff">

	<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png" />
	<meta name="apple-mobile-web-app-title" content="{{ config('folio.title-short') }}" />
	<link rel="apple-touch-icon-precomposed" href="/apple-touch-icon.png" />
	<link rel="apple-touch-icon" href="/apple-touch-icon.png" />

	<!-- Tags -->
	<meta name="description" content="{{ $og_description or $og_description_default }}" />
	<link rel="image_src" href="{{ $og_image or $og_image_default }}" />

	<!-- Open Graph meta data -->
	<meta property="fb:app_id" content="{{ $fb_app_id or $fb_app_id_default }}" />
	<meta property="og:url" content="{{ $og_url or $og_url_default }}" />
	<meta property="og:image" content="{{ $og_image or $og_image_default }}" />
	<meta property="og:title" content="{{ $og_title or $og_title_default }}" />
	<meta property="og:description" content="{{ $og_description or $og_description_default }}" />
	<meta property="og:type" content="{{ $og_type or 'profile' }}" />
	<meta property="og:image:width" content="1200" />
	<meta property="og:image:height" content="900" />
	@yield('open_object_metadata')

	<!-- Twitter Card -->
	<meta name="twitter:site" content="{{ config('folio.social.twitter.handle') }}" />
	<meta name="twitter:creator" content="{{ config('folio.social.twitter.handle') }}" />
	<meta name="twitter:title" content="{{ $og_title or $og_title_default }}" />
	<meta name="twitter:description" content="{{ $og_description or $og_description_default }}" />
	<meta name="twitter:image" content="{{ $og_image or $og_image_default }}" />
	<meta name="twitter:card" content="summary_large_image" />

	<!-- RSS -->
	<link rel="alternate" type="application/atom+xml" href="/{{ config('folio.feed.route') }}" />

@if(!Auth::check() && config('folio.google-analytics'))
	<!-- Google Analytics -->
	<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '{{ $google_analytics or config('folio.google-analytics') }}', 'auto');ga('send', 'pageview');</script>
@endif
@yield('metadata')

</head>

<body>

	@if($notification = Request::session()->get('notification'))
		{!! view('folio::partial.o-notification', ['notification' => $notification]) !!}
	@endif

	{{-- Header --}}
	<?php if(!isset($header_hidden)){ $header_hidden = false; }
				if(!isset($header_view)){ $header_view = config('folio.header.view'); }
				if(!isset($header_classes)){ $header_classes = config('folio.header.classes');; }
				if(!isset($header_data)){ $header_data = []; } ?>
	@if(!$header_hidden)
				{!! view($header_view, [
					'classes' => $header_classes,
					'data' => $header_data
				]) !!}
	@endif

	{{-- Cover --}}
	<?php if(!isset($cover_active)) $cover_active = true;
		  if(!isset($cover_hidden)) $cover_hidden = true; ?>
  @if($cover_active and $cover_hidden != true)
      	{!! view('folio::partial.c-cover', $cover_data) !!}
  @endif

@section('floating.menu')
  	{!! view('folio::partial.c-floating-menu', ['buttons' => ['<i class="fa fa-gear"></i>' => '/admin']]) !!}
@show

@yield('content')

@yield('footer')

<script src="{{ mix('/nonoesp/folio/js/manifest.js') }}"></script>
<script src="{{ mix('/nonoesp/folio/js/vendor.js') }}"></script>
<script src="{{ mix('/nonoesp/folio/js/folio.js') }}"></script>
<script>
	var trans = {!! json_encode(trans('folio::base')) !!};
</script>
@yield('scripts')

</body>
</html>
