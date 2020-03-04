@extends('folio::admin.layout')

@php
	$site_title = 'Upload · '.config('folio.title');
	$remove_wrap = true;
@endphp

@section('title', 'Existing Images')

@section('content')

	<style>
	.o-image-upload__delete {
		cursor: pointer;
	}
	</style>

	<div class="admin-form o-wrap o-wrap--size-550">
	
		<p>
			<a href="/{{ Folio::adminPath() }}upload">↑ Upload File</a>
		</p>

	</div>

	<div class="o-wrap o-wrap--size-1000">

        @php
            $uploaderPublicFolder = config('folio.uploader.public-folder');
            $uploaderDisk = config('folio.uploader.disk');
            $uploaderUploadsFolder = config('folio.uploader.uploads-folder');
            $uploaderAllowedFileTypes = config('folio.uploader.allowed-file-types');

            $filenames = Storage::disk($uploaderDisk)->files($uploaderUploadsFolder);
            $videos = [];
            $images = [];
        @endphp

        @foreach($filenames as $filename)
            @php
                $basename = basename($filename);
                // Omit dot files
                if (substr($basename, 0, 1) == ".") {
                    continue;
                }
                // Determine if it's a video or an image
                $extension = explode('.', $basename);
                $extension = $extension[count($extension) - 1];
                
                $isVideo = in_array($extension, ['mp4', 'mov']);
                if ($isVideo) {
                    array_push($videos, $filename);
                    continue;
                }
                array_push($images, $filename);
            @endphp
        @endforeach

        @if(count($images))

            <div class="admin-form grid">

                <div class="grid__item one-whole u-mar-b-3x u-mar-t-3x u-font-size--g">
                    <strong>Images</strong>
                </div>        

                @foreach($images as $filename)
                    @php
                        $basename = basename($filename);
                        
                        // Construct file path
                        $filePath = $uploaderPublicFolder.$basename;

                        // Construct image path
                        $imageHighRes = $filePath;
                        if (config('folio.imgix')) {
                            $imageHighRes = imgix($filePath);
                            $image = imgix($filePath, ['w' => 300, 'q' => 90]);
                        } else {
                            $image = $filePath;
                        }
                    @endphp

                        <div class="[ grid__item one-sixth lap--one-quarter palm--one-third ]">
                            <p>
                                @if($isVideo)
                                    <video width="100%" controls>
                                        <source src="{{ $filePath }}" type="video/{{ $extension }}">
                                    Your browser does not support the video tag.
                                    </video>
                                @else
                                    <a href="{{ $imageHighRes }}" target="_blank">
                                        <img src="{{ $image }}" style="width:100%">
                                    </a>
                                @endif
                                <br>{{ $basename }} · <a class="o-image-upload__delete js--delete-image" data-url="/{{ Folio::adminPath().'upload/delete/'.$basename }}">╳</a>
                            </p>
                        </div>
                @endforeach
            
            </div>
        
        @endif


        @if(count($videos))

            <div class="admin-form grid">

                <div class="grid__item one-whole u-mar-b-3x u-mar-t-3x u-font-size--g">
                    <strong>Videos</strong>
                </div>

                @foreach($videos as $filename)
                    @php
                        $basename = basename($filename);
                        // Determine if it's a video or an image
                        $extension = explode('.', $basename);
                        $extension = $extension[count($extension) - 1];

                        // Construct file path
                        $filePath = $uploaderPublicFolder.$basename;
                    @endphp

                        <div class="[ grid__item one-whole lap--one-quarter palm--one-third ]">
                            <p>
                                <a href="{{ $filePath }}" target="_blank">
                                    {{ $basename }}
                                </a>
                                · <a class="o-image-upload__delete js--delete-image" data-url="/{{ Folio::adminPath("upload/delete/$basename") }}">╳</a>
                            </p>
                        </div>
                @endforeach        

            </div>

        @endif

	</div>

    @if(!count($images) && !count($videos))
        <div class="o-wrap o-wrap--size-550">
            No images or videos has been uploaded yet.
        </div>
    @endif

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