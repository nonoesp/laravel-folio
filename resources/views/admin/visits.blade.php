
@extends('folio::admin.layout')

<?php
$settings_title = config('folio.title');
if($settings_title == '') {
	$settings_title = "Folio";
}
	$site_title = 'Visits · '. $settings_title;
?>

@section('title', 'Visits')

@section('content')

<style media="screen">
	.grid {
		letter-spacing: inherit;
	}
	.o-hide-button {
		cursor:pointer;
	}
</style>

<div class="[ c-admin ]">

  @isset($items)

		<ul class="c-archive__list">
			@foreach($items as $item)

					<li>
						<a href="{{ $item->path() }}" target="_blank">

							<b class="c-archive__list__title">{{ $item->title }}</b>

							<em class="c-archive__list__date u-font-size--a">

								{{ $item->visits }}

							</em>

						</a>

						<p class="u-font-size--a u-opacity--low -u-hidden-palm -u-text-align--right" style="margin-top:-0.8em">
						
							{{ $item->date() }}
						</p>

					</li>


			@endforeach
		</ul>

  @else
    <p>
      There are no items to show.
    </p>
  @endisset

</div>

@endsection
