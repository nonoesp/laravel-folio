@extends('folio::admin.layout')

<?php
	$site_title = config('folio.title');
?>

@if(isset($title))
    <?php $site_title = $title." — ".config('folio.title'); ?>
    @section('title', $title)
@endif

@section('content')

	<div class="admin-form">

        @if(isset($message))
            {!! $message !!}
        @endif

    </div>

@endsection		