@extends('folio::admin.layout')

<?php
    $site_title = 'Upload — '.config('folio.title');
    $supportedFileTypes = config('folio.uploader.allowed-file-types');
?>

@section('title', 'Upload a File')

@section('scripts')

	<script type="text/javascript" src="{{ mix('/nonoesp/folio/js/manifest.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/nonoesp/folio/js/vendor.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/nonoesp/folio/js/folio.js') }}"></script>
	<!-- Mousetrap for handling keyboard shortcuts -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.6.1/mousetrap.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.6.1/plugins/global-bind/mousetrap-global-bind.min.js"></script>
	<!-- Clipboard -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.0/clipboard.min.js"></script>

<script type="text/javascript">

    /*
    * CTRL+S & COMMAND+S
    * Keyboard shortcut to save edits by submitting the form.
    */
    Mousetrap.bindGlobal(['ctrl+c', 'command+c'], function(e) {
        $(".js--image-path").focus();
        e.preventDefault();
        return false;
    });

    /*
    * esc (escape key)
    * Keyboard shortcut to escape text editing mode.
    */
    Mousetrap.bindGlobal('esc', function(e) {
        admin.inputFile = null;
        admin.file = null;
        e.preventDefault();
        return false;
    });

    var admin = new Vue({
    el: '.c-admin',
    data: {
        inputName: '',
        placeholderName: 'your-file-name.jpg',
        file: null,
        inputFile: '',
        supportedFileTypes: {!! json_encode($supportedFileTypes) !!},
    },
    watch: {
        file: {
            handler: function(value, old) {
                admin.placeholderName = this.file != null ? this.file.name : 'your-file-name.jpg';
            }
        }
    },
    mounted() {

    },
    methods: {
        fileSelected: (e) => {
            admin.file = e.target.files[0];
            admin.inputName = admin.file.name;
        },
        deactivateUploadFilter: (e) => {
            const nameParts = admin.inputName.split('.');
            const extension = nameParts.reverse()[0];

            if (
                // File has no extension
                nameParts.length < 2 ||
                // File name ends in dot
                extension == '' ||
                // Extension is not supported
                !admin.supportedFileTypes.includes(extension.toLowerCase())
            ) {
                return true;
            }

            return false;
        }
    }
    });

    </script>

    <script>
        new ClipboardJS('.js--image-path');
        $(document).on('click', '.js--image-path', function(e) {
            e.preventDefault();
            e.target.select();
            return false;
        });
    </script>
@stop

@section('content')


	<style>
	.grid label {
		margin:0;
			padding:0;
			font-size:0.7em;
			color:rgba(0,0,0,0.50);
		}
		</style>

	<div class="[ admin-form ]">
	
		<p>
			<a href="/{{ Folio::adminPath() }}upload/list">View All Files</a>
		</p>

	</div>

	<div class="[ c-admin ] [ admin-form ]">

		@if(Request::isMethod('post'))

			@if(isset($message))
				<p>{!! $message !!}</p>
			@endif

            @if(isset($errors))
                @foreach($errors as $error)
                    <p style="color:#d63e3c">{!! $error !!}</p>
                @endforeach
            @endif

            @if(isset($messages))
                @foreach($messages as $message)
                    <p>{!! $message !!}</p>
                @endforeach
                </br>
            @endif            
			
			@if(isset($imgUploaded) && $imgUploaded == true)
				<p>
				<input
					type="text" 
					value="{{ $imgURL }}" 
					class="js--image-path" 
					data-clipboard-text="{{$imgURL}}"
					style="height:50px;font-size:1.2em;padding:0.7em;cursor:context-menu;"
					spellcheck="false"
				/>
				</p>
				<p>
                    @isset($fileType)
                        @if(in_array($fileType, ['mp4']))
                            <video width="100%" controls>
                                <source src="{{ $imgURL }}" type="video/{{ $fileType }}">
                            Your browser does not support the video tag.
                            </video>
                        @else
                            <img src="{{ $imgURL }}" style="max-width: 100%">
                        @endif
                    @endisset
				</p>
			@endif

			</br>
		@endif

		{{ Form::open([
            'url' => Folio::adminPath('upload'),
            'method' => 'POST',
            'files' => true])
        }}

		<div class="grid">

			<div class="[ grid__item ]">{{ Form::label('File') }}</div>
			<div class="[ grid__item ]">{{ Form::file('photo', [
				'@change' => 'fileSelected',
			]) }}</div>

            <div v-if="file">

                <div class="[ grid__item ]">{{ Form::label('Name') }}</div>
                <div class="[ grid__item ]">{{ Form::text('name', $filename, ['v-bind:placeholder' => "placeholderName", 'v-model' => 'inputName']) }}</div>
                
                <div class="[ grid__item ]" v-show="!file || this.deactivateUploadFilter()">
                    <p class="u-opacity--high">
                        Please specify a supported file extension
                        ·
                        
                        @foreach($supportedFileTypes as $fileType){{--
                        --}}@php
                            if(!$loop->first && !$loop->last) {
                                echo ", ";
                            }
                            if($loop->last) {
                                echo ", or ";
                            }
                            echo $fileType;
                            if($loop->last) {
                                echo ".";
                            }                            
                        @endphp{{--
                        --}}@endforeach
                    </p>
                </div>

                <div class="[ grid__item ]">
                    {{ Form::checkbox('shouldReplace', 'Replace', false, ['id' => 'shouldReplace']) }}
                    {{ Form::label('shouldReplace', 'Overwrite (replace image if an image with the same name already exists)') }}
                </div>
                
            </div>

            <div class="[ grid__item ]">{{ Form::label('') }}</div>
			<div class="[ grid__item ]]">{{ Form::submit('Upload', [':disabled'=>'!file || this.deactivateUploadFilter()']) }}</div>

		</div>

		{{ Form::close() }}

	</div>

@endsection		