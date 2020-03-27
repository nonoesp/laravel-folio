<?php
	$folio_typekit = config('folio.typekit');
	$folio_css = config('folio.css');
	if($folio_typekit == '') $folio_typekit = null;
	if($folio_css == '') $folio_css = null;
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimal-ui"/>
	<title>{{ $site_title ?? 'Admin' }}</title>

	<!--Icon-->
	<link rel="shortcut icon" href="/favicon.png" type="image/png" />
	<link rel="apple-touch-icon-precomposed" href="/apple-touch-icon.png" />
	<link rel="apple-touch-icon" href="/apple-touch-icon.png" />
	<link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-ipad.png" />
	<link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-@2x.png" />
	<meta name="apple-mobile-web-app-title" content="{{ config('folio.title') }}" />

	<!--Stylesheets-->
	<link rel="stylesheet" type="text/css" href="{{ $folio_css ?? Folio::asset('css/folio.css?default') }}">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	@if($folio_typekit)
	<!--TypeKit-->
	<script type="text/javascript" src="//use.typekit.net/{{ $folio_typekit }}.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	@endif

</head>

<body>

<div class="[ o-band ] [ u-pad-t-5x ] ">
	<div class="[ o-wrap  o-wrap--size-400 ]">

	<h3 class="[ c-admin__title ] [ u-border-bottom ]">{{ $headline ?? 'Admin' }}</h3>

	@yield('content')

	</div>
</div>

</body>
</html>