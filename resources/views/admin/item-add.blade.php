@extends('folio::admin.layout')

<?php
	$settings_title = Config::get('settings.title');
	if($settings_title == '') {
		$settings_title = "Folio";
	}
	$site_title = 'New Item · '.$settings_title;
?>

@section('title', 'New Item')

@section('content')

	<div class="[ c-admin ] [ u-pad-b-12x ]">

		{{ Form::open(['url' => Folio::adminPath().'item/add', 'method' => 'POST']) }}

			<p>{{ Form::text('title', '', ['placeholder' => 'An Awesome Title']) }}</p>

			<p>{{ Form::submit('Create') }}</p>

		{{ Form::close() }}

	</div>

@endsection
