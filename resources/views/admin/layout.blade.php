<?php
	$folio_typekit = config('folio.typekit');
	if($folio_typekit == '') $folio_typekit = null;

	$folio_css = config('folio.css');
	if ($folio_css == '') {
		$folio_css = '/nonoesp/folio/css/folio.css';
	}
	
	if(isset($remove_wrap) && $remove_wrap == true) {
		$remove_wrap = true;
	} else {
		$remove_wrap = false;
	}
?>

<!DOCTYPE html>
<html lang="{{ Thinker::getLocaleDisplayed() }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale=1, maximum-scale=1, minimal-ui"/>
	<title>{{ $site_title ?? 'Admin' }}</title>
	<link rel="shortcut icon" href="/favicon.png" type="image/png">
	<link rel="apple-touch-icon" sizes="144x144" href="/appicon.png">
	<link rel="stylesheet" type="text/css" href="{{ mix($folio_css) }}">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	@if($folio_typekit)
	<!--TypeKit-->
	<script type="text/javascript" src="//use.typekit.net/{{ $folio_typekit }}.js"></script>
	<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
	@endif

</head>

<body>

@section('floating.menu')
  	{!! view('folio::partial.c-floating-menu', ['buttons' => ['<i class="fa fa-home"></i>' => '/'.Folio::path()]]) !!}
@show

  	<div class="[ o-band ] [ u-pad-t-4x u-pad-b-4x ]">

		<div class="[ o-wrap o-wrap--size-600 ]">
			@if(!isset($shouldHideMenu))
				{!! view('folio::admin.c-menu') !!}
			@endif
			<div class="admin-title u-borderBottom">@yield('title', 'Admin')</div>
		</div>

		@if(!$remove_wrap)
			<div class="[ o-wrap o-wrap--size-600 ]">
		@endif

			@yield('content')

		@if(!$remove_wrap)
			</div>
		@endif

	</div>

<!--
-->@yield('scripts')<!--
--></body>
</html>
