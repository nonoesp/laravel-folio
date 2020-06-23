@php
	$email = $email ?? null;
	$path = $path ?? null;
	$data = $data ?? [];	
@endphp

<p>A new subscriber to {{ config('folio.title-short') }}!</p>

<p>{{ $email }} subscribed at {{ Request::root().$path }}</p>

@isset($data)
	<p style="color:#999">{{ join(" · ", $data) }}</p>
@endif