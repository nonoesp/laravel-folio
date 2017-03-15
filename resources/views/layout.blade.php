<?php
	//$space_typekit = config('space.typekit');
	//$space_css = config('space.css');
	if(!isset($space_typekit)) { $space_typekit = config('space.typekit'); }
	$og_title_default = config('space.title');
	$og_description_default = config('space.description');
	$og_image_default = config('space.image-src');
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimal-ui"/>
	<title>{{ $site_title or config('space.title') }}</title>
	<link rel="shortcut icon" href="/favicon.png" type="image/png">
	<link rel="apple-touch-icon" sizes="144x144" href="/appicon.png">
	<link rel="stylesheet" type="text/css" href="{{ $space_css or config('space.css') }}">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

@if($space_typekit)
	<!--TypeKit-->
	<script type="text/javascript" src="//use.typekit.net/{{ $space_typekit }}.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
@endif

	<!--Icon-->
	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="/manifest.json">
	<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="theme-color" content="#ffffff">

	<link rel="apple-touch-icon" sizes="72x72" href="apple-touch-icon-ipad.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="apple-touch-icon-@2x.png" />
	<meta name="apple-mobile-web-app-title" content="{{ config('space.title-short') }}" />
	<link rel="apple-touch-icon-precomposed" href="img/apple-touch-icon.png" />
	<link rel="apple-touch-icon" href="img/apple-touch-icon.png" />

	<!--Tags-->
	<meta name="description" content="{{ $og_description or $og_description_default }}" />
	<link rel="image_src" href="{{ $og_image or $og_image_default }}" />

	<!--Open Object-->
	<meta property="og:image" content="{{ $og_image or $og_image_default }}" />
	<meta property="og:title" content="{{ $og_title or $og_title_default }}" />
	<meta property="og:description" content="{{ $og_description or $og_description_default }}" />
	<meta property="og:type" content="{{ $og_type or 'profile' }}" />
	@yield('open_object_metadata')

	<!--RSS Feed-->
	<link rel="alternate" type="application/atom+xml" href="/{{ config('space.feed.route') }}" />

@if(!Auth::check() && config('space.google-analytics'))
	<!--Google Analytics-->
	<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');ga('create', '{{ config('space.google-analytics') }}', 'auto');ga('send', 'pageview');</script>
@endif
@yield('metadata')

</head>

<body>

	{{-- Header --}}
	<?php if(!isset($header_hidden)){ $header_hidden = false; }
				if(!isset($header_view)){ $header_view = config('space.header.view'); }
				if(!isset($header_classes)){ $header_classes = config('space.header.classes');; }
				if(!isset($header_data)){ $header_data = []; } ?>
	@if(!$header_hidden)
				{!! view($header_view, [
					'classes' => $header_classes,
					'data' => $header_data
				]) !!}
	@endif

	<?php if(!isset($cover_active)){ $cover_active = true; }
	 			if(!isset($cover_hidden)){ $cover_hidden = true; } ?>
	{{-- Cover --}}
  @if($cover_active and $cover_hidden != true)
      	{!! view('space::partial.c-cover', $cover_data) !!}
  @endif

@yield('content')

@yield('footer')

<script src="{{ mix('/nonoesp/space/js/manifest.js') }}"></script>
<script src="{{ mix('/nonoesp/space/js/vendor.js') }}"></script>
<script src="{{ mix('/nonoesp/space/js/space.js') }}"></script>
<script>
	var trans = {!! json_encode(trans('space::base')) !!};
</script>
@yield('scripts')

</body>
</html>
