@php
    $text = $text ?? null;
    $class = $class ?? 'c-credits-v2';
	$classes = Folio::expandClassesAsString($classes ?? [], $class);
@endphp

@if ($text)

@php
    $text = str_replace(
        [
            '{year}',
            '{footer-text}',
        ],
        [
            Item::formatDate(Date::now(), 'Y'),
            trans('folio.footer-text'),
        ],
        $text);
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

@endif