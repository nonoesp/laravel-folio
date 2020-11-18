@extends('folio::layout-v2')

@php
    $item = $item ?? null;
    $collection = $collection ?? [];
    $tag = $tag ?? null;
    $css = $css ?? Folio::asset('css/folio.css');

    $header_view = $header_view ?? config('folio.header.view');
    
    if ($item) {
        
        $menu_data = $menu_data ?? ['items' => ['<i class="fa fa-pencil"></i>' => $item->editPath()]];
        $title = $title ?? $item->title.' · '.config('folio.title');
        $og_type = $og_type ?? 'article';
        $og_url = $og_url ?? $item->permalink();
        $og_description = $og_description ?? $item->description();
        $og_image = $og_image ?? $item->ogImage();
        $collection = $collection ?? $item->collection();
        $google_fonts = $google_fonts ?? $item->propertyArray('google-font');
        $scripts = $scripts ?? $item->propertyArray('js');        
        $stylesheets = $stylesheets ?? $item->propertyArray('css');

    }
   
    if ($tag) {

        $title = config('folio.title').' · '.$tag;
        $og_description = 'Publications tagged as '.$tag.'.';

    }

    $menu_data = $menu_data ?? config('folio.menu');
@endphp