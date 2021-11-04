@extends('folio::admin.layout')

<?php
	$settings_title = Config::get('settings.title');
	if($settings_title == '') {
		$settings_title = "Folio";
	}
	$site_title = 'New Item · '.$settings_title;
?>

@section('title', 'New Item')

@section('scripts')

	<script type="text/javascript" src="{{ mix('/folio/js/manifest.js') }}"></script>
	<script type="text/javascript" src="{{ mix('/folio/js/vendor.js') }}"></script>
	<script type="text/javascript" src="{{ mix('/folio/js/folio.js') }}"></script>
	<!-- Mousetrap for handling keyboard shortcuts -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.6.1/mousetrap.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.6.1/plugins/global-bind/mousetrap-global-bind.min.js"></script>
	<script>

		$(window).ready(() => {
			$(document).on('keyup', '.js--title', () => {

				const title = $('.js--title').val();
				// $('.js--submit').attr('disabled', !title);
				if (title) {
					$('.js--submit').removeClass('off');
				} else {
					$('.js--submit').addClass('off');
				}
			
			});
		});

		/*
		* CTRL+S & COMMAND+S
		* Keyboard shortcut to save edits by submitting the form.
		*/
		Mousetrap.bindGlobal(['ctrl+s', 'command+s'], function(e) {
			const title = $('.js--title').val();
			if (title != '') {
				$('.js--submit').click();
			} else {
				alert('no title');
			}
			e.preventDefault();
			return false;
		});
	</script>
@endsection

@section('content')

	<style>
		.js--submit {
			transition: all 0.25s;
		}

		.js--submit.off {
			opacity: 0;
			user-select: none;
			cursor: none;
		}

		.js--title {
			background-color: #fafafa !important;
		}

		.js--title::placeholder {
			font-weight: 400;
			color: #787E97;
			opacity: 0.4;
		}
	</style>

	<div class="[ c-admin ] [ u-pad-b-12x ]">

		@php
			$titles = [
				'Time flies like an arrow',
				'It begins with a word',
				'Ready and repeat',
			];
		@endphp

		{{ Form::open(['url' => Folio::adminPath().'item/add', 'method' => 'POST']) }}

			<p>{{ Form::text('title', '', [
				'placeholder' => Illuminate\Support\Arr::random($titles),
				'class' => 'js--title',
			]) }}</p>

			<div class="o-wrap o-wrap--size-100" style="margin-right:-17px">

				<p>{{ Form::submit('Create →', [
					'class' => 'js--submit off',
				]) }}</p>
			
			</div>

		{{ Form::close() }}

	</div>

@endsection
