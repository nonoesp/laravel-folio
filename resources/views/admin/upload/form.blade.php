@extends('folio::admin.layout')

<?php
	$site_title = 'Upload — '.config('folio.title');
?>

@section('title', 'Upload')

@section('content')

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
					<p>![](/{{ $img_URL }})</p>
					<p><img src="{{ $img_URL }}" style="max-width: 100%"></p>
			@endif

			</br>
		@endif

		{{ Form::open(array('url' => '/upload', 'method' => 'POST', 'files' => true)) }}

			<p>{{ Form::file('photo', null) }}</p>
			<p>{{ Form::label('Name') }}</p>
			<p>{{ Form::text('name', null, array('placeholder' => 'project-name.jpg')) }}</p>
			<p>{{ Form::label('Width')}}</p>
			<p>{{ Form::text('max_width', '1200', array('placeholder' => 'Width')) }}</p>      
			<p>{{ Form::label('Replace') }}</p>
			<p>{{ Form::checkbox('shouldReplace') }}</p>            
			<p>{{ Form::submit('Submit') }}</p>

		{{ Form::close() }}

	</div>

@endsection		