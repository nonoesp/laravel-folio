<!DOCTYPE html>
<html lang="{{ Thinker::getLocaleDisplayed() }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimal-ui"/>
	<title>{{ $site_title or 'Admin' }}</title>
	<link rel="shortcut icon" href="/favicon.png" type="image/png">
	<link rel="apple-touch-icon" sizes="144x144" href="/appicon.png">
	<link rel="stylesheet" type="text/css" href="/css/space.css">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<!--TypeKit-->
	<script type="text/javascript" src="//use.typekit.net/{{{ $services_typekit or Config::get('services.typekit.main') }}}.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>

</head>

<body>

  <div class="[ o-band ]  [ u-border-bottom  -u-no-padding-bottom ]">
    <div class="[ o-wrap  o-wrap--tiny ]">

	@if(!isset($shouldHideMenu))
		{!! View::make(Config::get('space.view-admin-menu')) !!}
	@endif
	<div class="admin-title u-borderBottom">@yield('title', 'Admin')</div>

	@yield('content')

	</div>
</div>

<script type="text/javascript" src="/js/vendor/jquery.min.js"></script><!--
-->@yield('scripts')<!--
--></body>
</html>
