@extends('writing::layout')

<?php
	$site_title = $layer['title'].' — '.Config::get('settings.title');
?>

@section('content')

  <div class="[ c-article o-band ]  [ u-border-bottom  u-no-padding-bottom ]">
    <div class="[ o-wrap  o-wrap--standard  o-wrap--portable-tiny ]">

	  <div class="grid">
	  	@foreach($items as $item)
	  	    <div class="grid__item one-third  lap--one-half  palm--one-whole">
	  			<p>{{ $item->title }}</p>
				<p><img src="{{ $item->image }}"></p>
			</div>
	  	@endforeach
	  </div>

    </div>
  </div>

@stop
