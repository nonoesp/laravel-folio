@php
	$email = $email ?? null;
	$path = $path ?? null;
	$data = $data ?? [];
	$text = $text ?? null;
@endphp

<p>
	@if($text)
	{!! $text !!}
	@else
	A new subscriber to {{ config('folio.title-short') }}!
	@endif
</p>
<p>
	{{ $email }} subscribed at {{ Request::root().$path }}
</p>

@isset($data)
	<p style="color:#999">
		{{ join(" · ", $data) }}
	</p>
@endif