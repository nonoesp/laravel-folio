@extends('folio::admin.layout')

<?php
	$site_title = 'Upload — '.Config::get('settings.title');
?>

@section('title', 'Upload')

@section('content')

	<div class="admin-form grid">

	<?php $filenames = glob(public_path('/img/u/'.'*')); ?>

	@foreach($filenames as $filename)
		<?php $basename = basename($filename); ?>

			<div class="[ grid__item one-quarter portable--one-half ]">
				<p>
					<img src="/img/u/{{ $basename }}" style="width:100%">
					<br>{{ $basename }} (<a href="/upload/delete/{{ $basename }}">X</a>)
				</p>
			</div>

	@endforeach

	</div>

@endsection