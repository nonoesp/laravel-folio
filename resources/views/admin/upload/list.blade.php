@extends('folio::admin.layout')

@php
	$site_title = 'Upload · '.config('folio.title');
	$remove_wrap = true;
@endphp

@section('title', 'Existing Images')

@section('content')

	<div class="admin-form o-wrap o-wrap--size-1000">
	
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
            $images = [];
            $videos = [];
            $animations = [];
            $otherFiles = [];
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
                
                $isVideo = in_array($extension, ['mp4', 'mov', 'webm']);
                $isAnimation = in_array($extension, ['gif']);
                $isImage = in_array($extension, ['jpeg', 'jpg', 'svg', 'png', 'tiff', 'bmp', 'webp']);

                if ($isVideo) {
                    // Videos
                    array_push($videos, $filename);
                } else if ($isAnimation) {
                    // Animations
                    array_push($animations, $filename);
                } else if ($isImage) {
                    // Images
                    array_push($images, $filename);
                } else {
                    // Other files
                    array_push($otherFiles, $filename);
                }

            @endphp
        @endforeach

        {{-- Images --}}

        @if(count($images))

            <div class="admin-form grid">

                <div class="grid__item one-whole u-mar-b-3x u-mar-t-3x u-font-size--g">
                    <strong>Images</strong>
                </div>        

                @foreach($images as $filename)
                    @php
                        $basename = basename($filename);
                        
                        // Construct file path
                        $filePath = $uploaderPublicFolder.'/'.$basename;

                        // Veil
                        $veil = Folio::asset('images/veil.gif');

                        // Construct image path
                        $imageHighRes = $filePath;
                        if (config('folio.imgix')) {
                            $veil = imgix($veil);
                            $imageHighRes = imgix($filePath);
                            $image = imgix($filePath, ['w' => 200, 'q' => 50, 'auto' => 'format,compress']);
                        } else {
                            $image = Folio::mediaUrl($filePath);
                        }
                    @endphp

                        <div class="[ grid__item one-sixth lap--one-quarter palm--one-third ]">
                                <a href="{{ $imageHighRes }}" target="_blank">
                                <img src="{{ $veil }}" data-src="{{ $image }}" style="width:100%">
                                </a>
                                <br/>
                                {{ $basename }}
                                ·
                                <a class="o-image-upload__delete js--delete-image u-cursor-pointer" data-url="/{{ Folio::adminPath().'upload/delete/'.$basename }}">╳</a>
                            </p>
                        </div>
                @endforeach
            
            </div>
        
        @endif

        
        {{-- Animations --}}

        @if(count($animations))

            <div class="admin-form grid">

                <div class="grid__item one-whole u-mar-b-3x u-mar-t-3x u-font-size--g">
                    <strong>Gifs</strong>
                </div>        

                @foreach($animations as $filename)
                    @php
                        $basename = basename($filename);
                        
                        // Construct file path
                        $filePath = $uploaderPublicFolder.'/'.$basename;

                        // Veil
                        $veil = Folio::asset('images/veil.gif');

                        // Construct image path
                        $imageHighRes = $filePath;
                        if (config('folio.imgix')) {
                            $veil = imgix($veil);
                            $imageHighRes = imgix($filePath);
                            $image = imgix($filePath, ['w' => 150, 'q' => 40, 'auto' => 'format,compress']);
                        } else {
                            $image = Folio::mediaUrl($filePath);
                        }
                    @endphp

                        <div class="[ grid__item one-sixth lap--one-quarter palm--one-third ]">
                                <a href="{{ $imageHighRes }}" target="_blank">
                                <img src="{{ $veil }}" data-src="{{ $image }}" style="width:100%">
                                </a>
                                <br/>
                                {{ $basename }}
                                ·
                                <a class="o-image-upload__delete js--delete-image" data-url="/{{ Folio::adminPath().'upload/delete/'.$basename }}">╳</a>
                            </p>
                        </div>
                @endforeach
            
            </div>
        
        @endif


        {{-- Videos --}}        

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
                        // $filePath = Folio::upload($basename);

                        $video = Folio::uploadUrl($basename);
                        // $image = config('folio.imgix') ? imgix($filePath) : Folio::mediaUrl($filePath);
                    @endphp

                        <div class="[ grid__item one-whole ]">
                            <p>
                                <a href="{{ $video }}" target="_blank">{{ $basename }}</a>
                                ·
                                <a
                                    class="js--delete-image u-cursor-pointer"
                                    data-url="/{{ Folio::adminPath("upload/delete/$basename") }}"
                                >
                                    ╳
                                </a>
                            </p>
                        </div>
                @endforeach        

            </div>

        @endif

        {{-- Other files --}}

        @if(count($otherFiles))

            <div class="admin-form grid">

                <div class="grid__item one-whole u-mar-b-3x u-mar-t-3x u-font-size--g">
                    <strong>Files</strong>
                </div>        

                @foreach($otherFiles as $filename)
                    @php
                        $basename = basename($filename);
                        $extension = explode('.', $basename);
                        $extension = $extension[count($extension) - 1];
                        $file = Folio::uploadUrl($basename);
                    @endphp

                        <div class="[ grid__item one-whole ]">
                            <p>
                                <a href="{{ $file }}" target="_blank">{{ $basename }}</a>
                                ·
                                <a
                                    class="js--delete-image u-cursor-pointer"
                                    data-url="/{{ Folio::adminPath("upload/delete/$basename") }}"
                                >
                                    ╳
                                </a>
                            </p>
                        </div>
                @endforeach    
            
            </div>
        
        @endif        

	</div>

    @if(!count($images) && !count($videos))
        <div class="o-wrap o-wrap--size-1000">
            No images or videos have been uploaded yet.
        </div>
    @endif

@endsection

@section('scripts')

<script type="text/javascript" src="{{ mix('/folio/js/manifest.js') }}"></script>
<script type="text/javascript" src="{{ mix('/folio/js/vendor.js') }}"></script>
<script type="text/javascript" src="{{ mix('/folio/js/folio.js') }}"></script>

<script>
	$(document).on('click', '.js--delete-image', function(event) {

		event.preventDefault();

    	if(confirm("Are you sure? This can't be undone.")) {
			window.location = $(this).attr('data-url');
		}
	});
</script>

@endsection