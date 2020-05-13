@extends('folio::template._item')

@php
    $collection = $collection ?? null;
    $cover_hidden = $cover_hidden ?? true;
    $tag = $tag ?? null;

    if ($tag) {
        $title = config('folio.title').' · '.$tag;
        $og_description = 'Publications tagged as '.$tag.'.';
    }

@endphp