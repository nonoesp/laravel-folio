@extends('folio::admin.layout')

<?php
	$site_title = 'Upload — '.config('folio.title');
?>

@section('title', 'Existing Images')

@section('content')

	<div class="admin-form">
	
		<p>
			<a href="/{{ Folio::adminPath() }}upload">Upload Image</a>
		</p>

	</div>

	<div class="admin-form grid">

		<?php $filenames = glob(public_path(config('folio.media-upload-path').'*')); ?>

		@foreach($filenames as $filename)
			<?php $basename = basename($filename); ?>

				<div class="[ grid__item one-quarter portable--one-half ]">
					<p>
						<img src="{{ config('folio.media-upload-path').$basename }}" style="width:100%">
						<br>{{ $basename }} (<a href="/{{ Folio::adminPath().'upload/delete/'.$basename }}">X</a>)
					</p>
				</div>
		@endforeach

	</div>

@endsection