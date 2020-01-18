@extends('folio::admin.layout')

<?php
	$site_title = 'Upload — '.config('folio.title');
?>

@section('title', 'Upload an Image')

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
			<a href="/{{ Folio::adminPath() }}upload/list">View All Images</a>
		</p>

	</div>

	<div class="[ c-admin ] [ admin-form ]">

		@if(Request::isMethod('post'))

			@if(isset($message))
				<p>{!! $message !!}</p>
			@endif

			@if(isset($img_exists) && $img_exists == true)
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
			
			@if(isset($img_uploaded) && $img_uploaded == true)
				<p>
				<input
					type="text" 
					value="{{$img_URL}}" 
					class="js--image-path" 
					data-clipboard-text="{{$img_URL}}"
					style="height:50px;font-size:1.2em;padding:0.7em;cursor:context-menu;"
					spellcheck="false"
				/>
				</p>
				<p>
					<img src="{{ $img_URL }}" style="max-width: 100%">
				</p>
			@endif

			</br>
		@endif

		{{ Form::open(array('url' => Folio::adminPath().'upload', 'method' => 'POST', 'files' => true)) }}

		<div class="grid">

			<div class="[ grid__item ]">{{ Form::label('Image file') }}</div>
			<div class="[ grid__item ]">{{ Form::file('photo', [
				'@change' => 'fileSelected',
				'v-model' => 'inputFile',
			]) }}</div>
			<div v-if="file">
			<div class="[ grid__item ]">{{ Form::label('Name') }}</div>
			<div class="[ grid__item ]">{{ Form::text('name', $img_name, ['v-bind:placeholder' => "placeholderName", 'v-model' => 'inputName']) }}</div>
			<br />
			<div class="[ grid__item ]">{{ Form::label('Maximum width (image will be resized if larger)', 'Maximum width in pixels (image will be resized if larger)')}}</div>
			<div class="[ grid__item ]">{{ Form::text('max_width', config('folio.uploader.max_width'), ['placeholder' => 'Width']) }}</div>
			<div class="[ grid__item ]">
				{{ Form::checkbox('shouldReplace', 'Replace', false, ['id' => 'shouldReplace']) }}
				{{ Form::label('shouldReplace', 'Overwrite (replace image if an image with the same name already exists)') }}</div>
			</div>
			<div class="[ grid__item ]">{{ Form::label('') }}</div>
			<div class="[ grid__item ]]">{{ Form::submit('Upload', [':disabled'=>'!file']) }}</div>

		</div>

		{{ Form::close() }}

	</div>

@endsection		