
@extends(config("space.view.layout"))

<?php
  //{{-- @extends('layout.main') --}}
    /*
    / [ space ]
    /
    / $space_type (SINGLE_WRITING_TYPE, MULTIPLE_WRITING_TYPE)
    / $tag
    /
    */

    // 1. Defaults
    $site_title = config('space.title');
    $og_description = config('space.description');
    $header_view = config('space.header.view');
    $header_classes = config('space.header.classes');
    $header_data = config('space.header.data');

    // Footer credits
    $footer_credits = config('space.footer-credits');
    $footer_hidden = false;
    if(isset($footer_credits['hidden'])) {
      $footer_hidden = $footer_credits['hidden'];
    }

    // 2. Defaults Cover
    $cover_subtitle = Thinker::array_rand_value(config('space.cover.subtitles'));
    $cover_classes_title_b = '';
    $cover_image = '';
    $cover_classes = '';
    $cover_active = true;

    // 3. Define Item Type
    if(isset($items)) {
      $space_type = 'MULTIPLE_WRITING_TYPE';
    } else if(isset($item)) {
      $space_type = 'SINGLE_WRITING_TYPE';
    } else {
      $space_type = 'EMPTY_TYPE';
    }

    // 4. Single Item Settings
    if ($space_type == 'SINGLE_WRITING_TYPE') {

        // 4.1. General
        $site_title = $item->title.' — '.config('space.title');
        $og_description = Thinker::limitMarkdownText(Markdown::convertToHtml($item->text), 159, ['sup']);
        $og_type = 'article';
        ?>

        @section('open_object_metadata'){{--
          --}}<meta property="article:author" content="http://facebook.com/nonoesp" />
{{--  --}}    <meta property="article:modified_time" content="{{ $item->updated_at }}" />
{{--  --}}    <meta property="article:published_time" content="{{ $item->published_at }}" />
{{--  --}}    <meta property="article:publisher" content="http://facebook.com/gettingsimple" />{{--
      --}}    @foreach($item->tagNames() as $tag)

{{--  --}}    <meta property="article:tag" content="{{ strtolower($tag) }}" />@endforeach
{{--  --}}
        @stop

        <?php

        if ($item->image_src) {
          $og_image = $item->image_src;
        } else if ($item->image) {
          $og_image = $item->image;
        } else if ($item->video) {
          $og_image = Thinker::getVideoThumb($item->video);
        }

        // 4.2. Cover or not
        if ($item->image == '') {

            // 4.2.1. Item w/o cover
            $header_classes = ['borderless'];
            $cover_active = false;
            $header_data = [
              //'description' => trans('folio.slogan-writing')
                'is_media_hidden' => false,
                //'is_navigation_hidden' => false
            ];

        } else {

            // 4.2.2. Item w/ cover
            $cover_subtitle = $item->title;
            if(strlen($item->title) > 40) {
              $cover_classes_title_b = 'c-cover__title-b--small';
            }
            $cover_image = $item->image;
            $cover_classes .= 'is-faded is-fullscreen';
            //$header_view = 'space::partial.c-header';
            $header_classes = ['absolute', 'borderless'];
            $header_data = [
              'is_media_hidden' => true,
              'is_navigation_hidden' => true
            ];
        }
    }

    // 5. Multiple Item Settings
    if ($space_type == 'MULTIPLE_WRITING_TYPE') {

        // Tags
        if (isset($tag)) {
          $site_title = ucwords($tag).' — '.config('space.title');
          $og_description = 'Items tagged with the category '.ucwords($tag);
        }
    }

    // 1. Defaults
    $og_title = $site_title;

?>

@section('content')

  {{-- Cover --}}

  @if($cover_active)

      {!! view('space::partial.c-cover')
      ->with([
        'title' => config('space.cover.title'),
        'subtitle' => $cover_subtitle,
        'description' => config('space.cover.footline'),
        'image' => $cover_image,
        'classes_title_b' => $cover_classes_title_b,
        'class' => 'is-header u-background-grey '.$cover_classes
        ]) !!}

  @endif

  <div class="[ o-band ]
              [ u-pad-t-5x u-pad-b-1x ]">

      {{-- Items --}}

      @if($space_type == 'MULTIPLE_WRITING_TYPE')

          <div class="[ o-wrap ]" style="max-width: 640px">

          @if(isset($items_expected))
            @foreach($items_expected as $item)
              <div class="[ c-item ] [ u-pad-t-0x u-pad-b-0x ]">
                <p>Expected — {{ $item->title }}</p>
              </div>
            @endforeach
          @endif

          @foreach($items as $item)

            {!! view('space::partial.c-item')->
                     with(['item' => $item,
                           'item_type' => 'SUMMARY_ITEM_TYPE',
                           'isTitleLinked' => 'true',
                           'class' => '']) !!}

          @endforeach

          @if(isset($ids) and count($ids) > 0)
              {!! view('space::partial.c-load-more')->
                       with('ids', $ids) !!}
          @endif

          </div>

      @endif


      {{-- Item --}}

      @if($space_type == 'SINGLE_WRITING_TYPE')

          <div class="[ o-wrap ]" style="max-width: 640px">
              {!! View::make('space::partial.c-item')->
                      with(['item' => $item,
                            'class' => '']) !!}
          </div>

@section('metadata')
    <!-- Article -->
    <meta property="article:published_time" content="{{ $item->published_at }}"/>
@if($item->updated_at)
    <meta property="article:modified_time" content="{{ $item->updated_at }}"/>
@endif
@stop

      @endif

  </div>
@stop

@section('scripts')
    @if(isset($items))
        <script>
          @if ($ids)
            {{ 'ids = '.json_encode($ids).';' }}
          @endif
        </script>
    @endif
@stop

@section('footer')

	{!! view('space::partial.c-footer') !!}

@stop
