<?php
	$space_typekit = Config::get('space.typekit');
	$space_css = Config::get('space.css');
	if($space_typekit == '') $space_typekit = null;
	if($space_css == '') $space_css = null;
	$header_view = 'space::partial.c-header';
	$header_classes = ['relative'];
?>

<!DOCTYPE html>
<html lang="{{ Thinker::getLocaleDisplayed() }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimal-ui"/>
	<title>{{ $site_title or 'Admin' }}</title>
	<link rel="shortcut icon" href="/favicon.png" type="image/png">
	<link rel="apple-touch-icon" sizes="144x144" href="/appicon.png">
	<link rel="stylesheet" type="text/css" href="{{ $space_css or '/nonoesp/space/css/space.css?default' }}">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	@if($space_typekit)
	<!--TypeKit-->
	<script type="text/javascript" src="//use.typekit.net/{{ $space_typekit }}.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	@endif

</head>

<body>

	{{-- Header --}}
		<?php if(!isset($header_hidden)){ $header_hidden = false; } ?>
		<?php if(!isset($header_view)){ $header_view = Config::get('space.header.view'); } ?>
		<?php if(!isset($header_classes)){ $header_classes = Config::get('space.header.classes'); } ?>
		<?php if(!isset($header_data)){ $header_data = []; } ?>
		@if(!$header_hidden)
		{!! view($header_view)->with(['classes' => $header_classes,
																  'data' => $header_data
																	]) !!}
	  @endif

  <div class="[ o-band ]">
    <div class="[ o-wrap  o-wrap--size-small ]">

	@if(!isset($shouldHideMenu))
		{!! view('space::admin.c-menu') !!}
	@endif
	<div class="admin-title u-borderBottom">@yield('title', 'Admin')</div>

	@yield('content')

	</div>
</div>

{{--<script type="text/javascript" src="/js/vendor/jquery.min.js"></script>--}}<!--
-->@yield('scripts')<!--
--></body>
</html>
