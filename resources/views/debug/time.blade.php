@extends('folio::template._base')

<?php
  $site_title = 'Time · '.config('folio.title');
  $og_title = $site_title;
  // header
  $header_view = config('folio.header.view');
  $header_data = [
    'image' => null,
    'is_media_hidden' => true,
  ];
  $header_classes = ['borderless'];
  $cover_hidden = true;

  $timezone = config('app.timezone');
  $date = Date::now()->format('F j, Y H:i:s');
?>

@section('content')
	
	<div class="[ o-wrap o-wrap--full ]" style="position:fixed;left:0;right:0;top:0;bottom:0;margin:auto;height:300px;">

    	<article class="[ c-item-v2 ]" style="font-size:1.4rem">

			<p class="[ u-text-align--center statement tk-myriad-pro ]">
        What's the time at {!! str_replace(' ', '&nbsp;', config('folio.title-short')) !!}?
			  <br/>
			  <br/>
			  {{ $date }}
			  <span class="u-opacity--high">
          {{ $timezone }}
        </span>
			</p>

		</article>

	</div>

@stop

@section('footer')
@stop