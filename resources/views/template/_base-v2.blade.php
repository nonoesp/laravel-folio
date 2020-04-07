
@extends(config("folio.view.layout"))

{{-- Duplicate of _base.blade.php without overloading collection views --}}

@if (isset($ids))
    @push('scripts')
        <script>
            {{ 'ids = '.json_encode($ids).';' }}
        </script>
    @stop
@endif

<?php
    // keep pre-set from child views
    if(isset($header_data)) {
      $keep_header_data = $header_data;
    }
    if(isset($header_classes)) {
      $keep_header_classes = $header_classes;
    }
    if(isset($cover_data)) {
      $keep_cover_data = $cover_data;
    }
    if(isset($cover_classes)) {
      $keep_cover_classes = $cover_classes;
    }    
    if(isset($footer_data)) {
      $keep_footer_data = $footer_data;
    }

    // $folio_type (SINGLE_WRITING_TYPE, MULTIPLE_WRITING_TYPE)
    // $tag

    // 1. Defaults
    // if(!isset($site_title)) {
      // $site_title = config('folio.title');
    // }
    // if(!isset($og_description)) {
    //   $og_description = config('folio.description');
    // }
    if(!isset($header_view)) {
      $header_view = config('folio.header.view');
    }
    $header_classes = config('folio.header.classes');
    $header_data = config('folio.header.data');

    // Footer
    $footer_credits = config('folio.footer-credits');
    $hide_footer = false;
    $footer_data = [
      'hide_subscribe' => false,
      'hide_credits' => false,
      'credits' => config('folio.footer-credits'),
    ];

    // 2. Defaults Cover
    $cover_data = config('folio.cover.data');
    $cover_data['title'] = config('folio.cover.title');
    $cover_data['description'] = config('folio.cover.footline');
    $cover_data['subtitle'] = \Illuminate\Support\Arr::random(config('folio.cover.subtitles'));
    $cover_data['class'] = 'is-header u-background-grey ';
    $cover_data['show_arrow'] = false;
    $cover_active = true;
    if(!isset($cover_hidden)) $cover_hidden = false;

    // 3. Define Item Type
    if(isset($items)) {
      $folio_type = 'MULTIPLE_WRITING_TYPE';
    } else if(isset($item)) {
      $folio_type = 'SINGLE_WRITING_TYPE';
    } else {
      $folio_type = 'EMPTY_TYPE';
    }

    // ------------------------------------------------------------------------
    // (A) Single Item Settings
    if ($folio_type == 'SINGLE_WRITING_TYPE') {

        // 4.1. General
        $site_title = isset($site_title) ? $site_title : $item->title.' · '.config('folio.title');
        $og_title = isset($og_title) ? $og_title : $item->title;        
        if($item->stringProperty('podcast-episode')) {
          $og_title .= ' (#'.$item->stringProperty('podcast-episode').')';
          $site_title = $og_title.' · '.config('folio.title');
        }
        if (!isset($og_description)) {
          $og_description = Thinker::limitMarkdownText($item->htmlText(['stripTags' => ['rss', 'podcast', 'feed']]), 159, ['sup']);
          $og_description = $item->stringProperty('meta-description', $og_description);
        }
        $og_type = 'article';
        $og_url = $item->permalink();
        $apple_touch_icon = $item->stringProperty('apple-touch-icon');
        ?>

        @section('open_object_metadata'){{--
          --}}<meta property="article:author" content="{{ config('folio.social.facebook.author') }}" />
{{--  --}}    <meta property="article:modified_time" content="{{ $item->updated_at }}" />
{{--  --}}    <meta property="article:published_time" content="{{ $item->published_at }}" />
{{--  --}}    <meta property="article:publisher" content="{{ config('folio.social.facebook.publisher') }}" />{{--
      --}}    @foreach($item->tagNames() as $tagName)

{{--  --}}    <meta property="article:tag" content="{{ strtolower($tagName) }}" />@endforeach
{{--  --}}
        @stop

        <?php

        // image_src
        $og_image = $item->thumbnail();

        // 4.2. Cover or not
        if ($item->image == '' || $cover_hidden) {

            // 4.2.1. Item w/o cover
            $cover_active = false;
            $header_classes = ['borderless', 'tight'];
            $header_data['is_media_hidden'] = false;

        } else {

            // 4.2.2. Item w/ cover
            $cover_data['subtitle'] = $item->title;
            $cover_data['image'] = $item->image;
            $cover_data['class'] .= 'is-faded--30 is-fullscreen';
            if(strlen($item->title) > 40) {
              $cover_data['classes_title_b'] = 'c-cover__title-b--small';
            }

            $header_classes = ['absolute', 'borderless', 'white'];
            $header_data['is_media_hidden'] = true;
            $header_data['sis_navigation_hidden'] = true;
            $header_data['color'] = 'white';
        }
        
    }
    // ------------------------------------------------------------------------
    // (B) Multiple Item Settings
    else if ($folio_type == 'MULTIPLE_WRITING_TYPE') {

        // Tags
        if (isset($tag)) {
          $site_title = ucwords($tag).' · '.config('folio.title');
          $og_description = 'Items tagged with the category '.ucwords($tag);
          $cover_data['description'] = "Items tagged with $tag";
        }

        // Master Item
        if (isset($item)) {
            $og_description = $item->stringProperty('meta-description', isset($og_description) ? $og_description : null);
        }
    }

    if(isset($keep_header_data)) {
      foreach($keep_header_data as $key=>$val) {
        $header_data[$key] = $val;
      }
    }
    if(isset($keep_header_classes)) {
      $header_classes = $keep_header_classes;
    }
    if(isset($keep_cover_data)) {
      foreach($keep_cover_data as $key=>$val) {
        $cover_data[$key] = $val;
      }
    }
    if(isset($keep_cover_classes)) {
      $cover_classes = $keep_cover_classes;
    }    
    if(isset($keep_footer_data)) {
      foreach($keep_footer_data as $key=>$val) {
        $footer_data[$key] = $val;
      }
    }
?>

      {{----------------------------------------}}
      {{-- (A) ITEMS  --}}

      @if($folio_type == 'MULTIPLE_WRITING_TYPE')

    

      @endif

      {{--------------------------------}}
      {{-- (B) ITEM --}}

      @if($folio_type == 'SINGLE_WRITING_TYPE')

        @section('floating.menu')
          {!! view('folio::partial.c-floating-menu', ['buttons' => ['<i class="fa fa-pencil"></i>' => $item->editPath()]]) !!}
        @stop

@section('metadata')
    <!-- Article -->
    <meta property="article:published_time" content="{{ $item->published_at }}"/>
@if($item->updated_at)
    <meta property="article:modified_time" content="{{ $item->updated_at }}"/>
@endif
@stop

      @endif {{-- Item endif --}}
      {{-----------------------------------}}

@section('footer')
	{!! view('folio::partial.c-footer', $footer_data) !!}
@stop
