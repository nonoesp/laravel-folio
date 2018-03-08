@extends('folio::admin.layout')

<?php
	$settings_title = config('settings.title');
	if($settings_title == '') {
		$settings_title = "Folio";
	}
    $site_title = 'Versions of Item '.$item->id.' | '. $settings_title;
?>

@section('title', 'Versions of Item '.$item->id)

@section('floating.menu')
  	{!! view('folio::partial.c-floating-menu', ['buttons' => [
		  '·' => '/'.Folio::path(),
		  '<i class="fa fa-check"></i>' => $item->editPath(),
		  ]]) !!}
@stop

@section('content')

	<div class="[ c-admin ] [ u-pad-b-12x ]">
        
        <p>
            <a href="/admin/item/edit/{{ $item->id }}">Return to Item</a>
        </p>

        @foreach($item->versions->reverse() as $key=>$version)
            <?php
            $date = new Date($version->updated_at);
            $date = ucWords($date->format('F').' '.$date->format('j, Y').$date->format(' H:i:s'));
            ?>
            <div class="[ u-pad-b-1x u-pad-t-2x ] [ c-admin--font-light ] ">
                {{ $date }}
			</div>
            
            <textarea>{!! $version->getModel()->text; !!}</textarea>
            <br/>
        @endforeach

	</div>

@endsection
