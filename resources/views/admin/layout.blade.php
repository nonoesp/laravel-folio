<!DOCTYPE html>
<html lang="{{ Thinker::getLocaleDisplayed() }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimal-ui"/>
	<title>{{ $site_title or 'Admin' }}</title>
	<link rel="shortcut icon" href="/favicon.png" type="image/png">
	<link rel="apple-touch-icon" sizes="144x144" href="/appicon.png">
	<link rel="stylesheet" type="text/css" href="/css/normalize.min.css">
	<link rel="stylesheet" type="text/css" href="/css/admin.css">

	<!--TypeKit-->
	<script type="text/javascript" src="//use.typekit.net/{{{ Config::get('services.typekit.nonoma') }}}.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>	
		
</head>

<body>

<div class="admin">
	<div class="admin-wrap">

	@if(!isset($shouldHideMenu))
		{{ View::make('writing::admin.c-menu') }}
	@endif
	<div class="admin-title u-borderBottom">@yield('title', 'Admin')</div>

	@yield('content')

	</div>
</div>

</body>
</html>