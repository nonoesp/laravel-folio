@extends('folio::template._base')

<?php
$site_title = 'Time · '.config('folio.title');
$og_title = $site_title;
// header
$header_view = 'folio::partial.c-header-simple';
$header_data = [
  'image' => null,
  'is_media_hidden' => true,
];
$header_classes = ['borderless'];
$cover_hidden = true;

$timezone = config('app.timezone');
$date = Date::now();
$date = ucWords($date->format('F').' '.$date->format('j, Y').$date->format(' H:i:s'));
?>

@section('content')
	
	<div class="[ o-wrap o-wrap--size-500 ]">

    	<article class="[ c-item-v2 ]" style="font-size:1.1em">

			<p class="[ u-text-align--center statement tk-myriad-pro ]">
			What's the time at Nono.MA?</strong>
			<br>
			<br>
			{{ $date }}
			<span class="u-opacity--high">{{ $timezone }}</span>
			</p>

		</article>

	</div>

@stop
