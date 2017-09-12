@extends('folio::admin.layout')

<?php
	$site_title = 'Upload — '.config('folio.title');
?>

@section('title', 'New Image')

@section('content')

	
	<div class="admin-form">
	
		<p>
			<a href="/{{ Folio::adminPath() }}upload/list">View All Images</a>
		</p>

	</div>

	<div class="admin-form">

		@if(Request::isMethod('post'))

			@if($img_exists)
				<p>
					@if(!$img_uploaded)
						An image with this name already exists.
						</br>					
						Choose Replace if that is what you want.
					@else
						The image has been replaced.
					@endif
				</p>
			@endif
			
			@if($img_uploaded)
					<p>![]({{ $img_URL }})</p>
					<p><img src="{{ $img_URL }}" style="max-width: 100%"></p>
			@endif

			</br>
		@endif

		{{ Form::open(array('url' => Folio::adminPath().'upload', 'method' => 'POST', 'files' => true)) }}

		<style>
		.grid label {
			margin:0;
			font-size:0.7em;
			color:rgba(0,0,0,0.50);
		}
		</style>

		<div class="grid">

			<div class="[ grid__item ]">{{ Form::label('File') }}</div>
			<div class="[ grid__item ]">{{ Form::file('photo', null) }}</div>
			<div class="[ grid__item ]">{{ Form::label('Name') }}</div>
			<div class="[ grid__item ]">{{ Form::text('name', null, array('placeholder' => 'project-name.jpg')) }}</div>
			<div class="[ grid__item ]">{{ Form::label('Width')}}</div>
			<div class="[ grid__item ]">{{ Form::text('max_width', '1200', array('placeholder' => 'Width')) }}</div>
			<div class="[ grid__item ]">{{ Form::checkbox('shouldReplace', 'Replace') }} {{ Form::label('Replace if exists?') }}</div>
			<div class="[ grid__item ]">{{ Form::label('') }}</div>
			<div class="[ grid__item ]]">{{ Form::submit('Upload') }}</div>

			{{--  <div class="[ grid__item one-fifth   ] [ u-text-align--right ]">{{ Form::label('File') }}</div>
			<div class="[ grid__item four-fifths ]">{{ Form::file('photo', null) }}</div>
			<div class="[ grid__item one-fifth   ] [ u-text-align--right ]">{{ Form::label('Name') }}</div>
			<div class="[ grid__item four-fifths ]">{{ Form::text('name', null, array('placeholder' => 'project-name.jpg')) }}</div>
			<div class="[ grid__item one-fifth   ] [ u-text-align--right ]">{{ Form::label('Width')}}</div>
			<div class="[ grid__item four-fifths ]">{{ Form::text('max_width', '1200', array('placeholder' => 'Width')) }}</div>
			<div class="[ grid__item one-fifth   ] [ u-text-align--right ]">{{ Form::label('Replace') }}</div>
			<div class="[ grid__item four-fifths ]">{{ Form::checkbox('shouldReplace') }}</div>
			<div class="[ grid__item one-fifth   ] [ u-text-align--right ]">{{ Form::label('') }}</div>
			<div class="[ grid__item four-fifths ] ]">{{ Form::submit('Submit') }}</div>  --}}

		</div>

		{{ Form::close() }}

	</div>

@endsection		