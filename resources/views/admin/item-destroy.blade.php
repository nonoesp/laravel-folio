@extends('folio::admin.layout')

<?php
	$settings_title = config('settings.title');
	if($settings_title == '') {
		$settings_title = "Folio";
	}
    $site_title = 'Remove Item '.$item->id.' | '. $settings_title;
?>

@section('title', 'Permanently delete Item '.$item->id)

@section('floating.menu')
  	{!! view('folio::partial.c-floating-menu', ['buttons' => [
		  '·' => '/'.Folio::path(),
		  '<i class="fa fa-check"></i>' => $item->editPath(),
		  ]]) !!}
@stop

@section('content')

	<div class="[ c-admin ] [ u-pad-b-12x ]">
        
        <p>
            You are about to permanently delete this Item from your site.
        </p>

        <p>
            This action can't be undone!
        </p>		

        <p class="u-font-size--f">
            <a href="{{ $item->forceDeletePath() }}" style="text-decoration:underline">
				<strong>Permanently delete "{{$item->title}}"</strong>
			</a>
        </p>

	</div>

@endsection
