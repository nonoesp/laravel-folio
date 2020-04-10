
@extends('folio::admin.layout')

<?php
$settings_title = config('folio.title');
if($settings_title == '') {
	$settings_title = "Folio";
}
	$site_title = 'Redirections · '. $settings_title;
	function trimURL($url, $length = 40) {
		if (strlen($url) > $length) {
			return substr($url, 0, $length - 1).'..';
		}
		return $url;
	}
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

	<h1>Item redirections</h1>

  @isset($redirections)

		<ul class="c-archive__list">
			@foreach($redirections as $redirection)
			<?php $item = Item::withTrashed()->find($redirection->item_id); ?>
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

	<br/>
  <h1>Redirections from redirects.php</h1>

  @if(config('redirects') != null)

  <ul class="c-archive__list">

	@foreach(config('redirects') as $from => $to)

		@if(is_array($to))

			<li>
				<br/>
				<p><strong>{{ $from }}</strong></p>
			</li>

			@foreach($to as $subFrom => $subTo)

				@if(is_array($subTo))
					@continue
				@endif

				<li>
					<a href="{{ $subTo }}" target="_blank">
		
						<b class="c-archive__list__title">
							{{ $subFrom }}
							<span class="u-opacity--low"><br/>→ {{ trimURL($subTo, 55) }}</span>
						</b>
		
						<em class="c-archive__list__date u-font-size--a">
							redirects.php
						</em>
		
					</a>
				</li>

			@endforeach

		@else

		<li>
			<a href="{{ $to }}" target="_blank">

				<b class="c-archive__list__title">
					{{ $from }}
					<span class="u-opacity--low"><br/>→ {{ trimURL($to, 55) }}</span>
				</b>

				<em class="c-archive__list__date u-font-size--a">
					redirects.php
				</em>

			</a>
		</li>

		@endif

	@endforeach
  </ul>
  @endif

</div>

@endsection
