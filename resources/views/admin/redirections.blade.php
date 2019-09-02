
@extends('folio::admin.layout')

<?php
$settings_title = config('folio.title');
if($settings_title == '') {
	$settings_title = "Folio";
}
	$site_title = 'Redirections · '. $settings_title;
?>

@section('title', 'Redirections')

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

  @isset($redirections)

		<ul class="c-archive__list">
			@foreach($redirections as $redirection)
			<?php $item = Item::find($redirection->item_id); ?>
					@empty($redirection->value)
						@continue
					@endempty
					<li>
						<a href="/{{ $redirection->value }}" target="_blank">

							<b class="c-archive__list__title">
								{{ $redirection->value }}
								<span class="u-opacity--low">→ {{ $item->path() }}</span>
							</b>

							<em class="c-archive__list__date u-font-size--a">

								{{ $item->id }}

							</em>

						</a>

						<p class="u-font-size--a u-opacity--low -u-hidden-palm -u-text-align--right" style="margin-top:-0.8em">

						{{ $item->title }}

						</p>

					</li>


			@endforeach
		</ul>

  @else
    <p>
      There are no items with redirections to show.
    </p>
  @endisset

</div>

@endsection
