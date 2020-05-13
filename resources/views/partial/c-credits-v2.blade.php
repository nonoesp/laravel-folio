@php
    $text = $text ?? null;
    $class = $class ?? 'c-credits-v2';
	$classes = Folio::expandClassesAsString($classes ?? [], $class ?? 'c-credits-v2');
@endphp

<div class="{{ $class }} {{ $classes }}">
    {!! 
    preg_replace(
        '/<p>(.*?)<\/p>/is',
        '$1',
        Item::convertToHtml($text) ?? ''
    );
    !!}
</div>