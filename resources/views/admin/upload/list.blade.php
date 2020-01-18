@extends('folio::admin.layout')

<?php
	$site_title = 'Upload — '.config('folio.title');
	$remove_wrap = true;
?>

@section('title', 'Existing Images')

@section('content')

	<style>
	.o-image-upload__delete {
		cursor: pointer;
	}
	</style>

	<div class="admin-form o-wrap o-wrap--size-550">
	
		<p>
			<a href="/{{ Folio::adminPath() }}upload">↑ Upload Image</a>
		</p>

	</div>

	<div class="o-wrap o-wrap--size-1000">

	<div class="admin-form grid">

		<?php $filenames = glob(public_path(config('folio.media-upload-path').'*')); ?>

		@foreach($filenames as $filename)
			<?php
				$basename = basename($filename);
				$image = config('folio.media-upload-path').$basename;
				$imageHighRes = $image;
				if (config('folio.imgix')) {
					$imageHighRes = imgix($image);
					$image = imgix($image, ['w' => 300, 'q' => 90]);
				}
			?>

				<div class="[ grid__item one-sixth lap--one-quarter palm--one-third ]">
					<p>
						<a href="{{ $imageHighRes }}" target="_blank">
							<img src="{{ $image }}" style="width:100%">
						</a>
						<br>{{ $basename }} · <a class="o-image-upload__delete js--delete-image" data-url="/{{ Folio::adminPath().'upload/delete/'.$basename }}">╳</a>
					</p>
				</div>
		@endforeach

	</div>

	</div>

@endsection

@section('scripts')

<script type="text/javascript" src="{{ mix('/nonoesp/folio/js/manifest.js') }}"></script>
<script type="text/javascript" src="{{ mix('/nonoesp/folio/js/vendor.js') }}"></script>
<script type="text/javascript" src="{{ mix('/nonoesp/folio/js/folio.js') }}"></script>

<script>
	$(document).on('click', '.js--delete-image', function(event) {

		event.preventDefault();

    	if(confirm("Are you sure? This can't be undone.")) {
			window.location = $(this).attr('data-url');
		}
	});
</script>

@endsection