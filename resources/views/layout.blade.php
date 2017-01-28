<?php
	$space_typekit = Config::get('space.typekit');
	$space_css = Config::get('space.css');
	if($space_typekit == '') $space_typekit = null;
	if($space_css == '') $space_css = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimal-ui"/>
	<title>{{ $site_title or Config::get('settings.title') }}</title>
	<link rel="shortcut icon" href="/favicon.png" type="image/png">
	<link rel="apple-touch-icon" sizes="144x144" href="/appicon.png">
	<link rel="stylesheet" type="text/css" href="{{ $space_css or '/nonoesp/space/css/space.css?default' }}">

	@if($space_typekit)
	<!--TypeKit-->
	<script type="text/javascript" src="//use.typekit.net/{{ $space_typekit }}.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	@endif

</head>

<body>

  <div class="[ o-band ]  [ u-border-bottom  u-no-padding-bottom ]">
    <div class="[ o-wrap  o-wrap--standard  o-wrap--portable-tiny ]">

	{!! Html::link("/".Config::get("space.path-prefix"), "Home") !!}
	&nbsp;&nbsp;
	{!! Html::link("/".Config::get("space.admin-path-prefix"), "Admin") !!}
</div></div>

@yield('content')

{{--<script type="text/javascript" src="/js/vendor/jquery.min.js"></script>--}}<!--
-->@yield('scripts')<!--
--></body>
</html>
