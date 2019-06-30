@extends('folio::admin.layout')

<?php
	$settings_title = config('settings.title');
	if($settings_title == '') {
		$settings_title = "Folio";
	}
    $site_title = 'Remove Item '.$item->id.' | '. $settings_title;
?>

@section('title', 'DANGER ZONE · Permanently delete Item '.$item->id)

@section('floating.menu')
  	{!! view('folio::partial.c-floating-menu', ['buttons' => [
		  '<i class="fa fa-chevron-left"></i>' => $item->editPath(),
		  ]]) !!}
@stop

@section('content')

	<script>
	function confirmDelete() {
		const shouldDelete = confirm("Are you sure? This can't be undone.");
    	if(shouldDelete) {
			window.location = '{{ $item->forceDeletePath() }}';
		} else {
			// don't delete
		}
	}
	</script>

	<div class="[ c-admin ] [ u-pad-b-12x ]">
        
		<p>
            You are about to permanently delete this Item from your site.
        </p>

        <p>
            If all you want to do is deactivate this item you should <i>hide</i> it.
        </p>		

        <p>
            Remember. This action can't be undone!
        </p>		

        <p class="u-font-size--f">
            <a href="#" onClick="confirmDelete()" style="text-decoration:underline">
				<strong>Permanently delete "{{$item->title}}"</strong>
			</a>
        </p>

	</div>

@endsection
