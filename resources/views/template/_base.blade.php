
@extends(config("folio.view.layout"))

@section('scripts')
    @if(isset($items))
        <script>
          @if ($ids)
            {{ 'ids = '.json_encode($ids).';' }}
          @endif
        </script>
    @endif
@stop

<?php
    // keep pre-set from child views
    if(isset($header_data)) {
      $keep_header_data = $header_data;
    }
    if(isset($header_classes)) {
      $keep_header_classes = $header_classes;
    }

    // $folio_type (SINGLE_WRITING_TYPE, MULTIPLE_WRITING_TYPE)
    // $tag

    // 1. Defaults
    if(!isset($site_title)) {
      $site_title = config('folio.title');
    }
    $og_description = config('folio.description');
    $header_view = config('folio.header.view');
    $header_classes = config('folio.header.classes');
    $header_data = config('folio.header.data');

    // Footer credits
    $footer_credits = config('folio.footer-credits');
    $footer_hidden = false;
    if(isset($footer_credits['hidden'])) {
      $footer_hidden = $footer_credits['hidden'];
    }

    if(!isset($subscribe_data)) {
      $subscribe_data = [
        'source' => 'folio',
        'campaign' => 'default'
      ];
    }

    // 2. Defaults Cover
    $cover_data = config('folio.cover.data');
    $cover_data['title'] = config('folio.cover.title');
    $cover_data['description'] = config('folio.cover.footline');
    $cover_data['subtitle'] = Thinker::array_rand_value(config('folio.cover.subtitles'));
    $cover_data['class'] = 'is-header u-background-grey ';
    $cover_classes = '';
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
        $site_title = $item->title.' | '.config('folio.title');
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

        // image_src
        if ($item->image_src) {
          $og_image = $item->image_src;
        } else if ($item->image) {
          $og_image = $item->image;
        } else if ($item->video) {
          $og_image = Thinker::getVideoThumb($item->video);
        }

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
            $cover_data['class'] .= 'is-faded is-fullscreen';
            if(strlen($item->title) > 40) {
              $cover_data['classes_title_b'] = 'c-cover__title-b--small';
            }

            $header_classes = ['absolute', 'borderless', 'white'];
            $header_data = [
              'is_media_hidden' => true,
              'is_navigation_hidden' => true,
              'color' => 'white'
            ];
        }
    }

    // ------------------------------------------------------------------------
    // (B) Multiple Item Settings
    else if ($folio_type == 'MULTIPLE_WRITING_TYPE') {

        // Tags
        if (isset($tag)) {
          $site_title = ucwords($tag).' | '.config('folio.title');
          $og_description = 'Items tagged with the category '.ucwords($tag);
          $cover_data['description'] = "Items tagged with $tag";
        }
    }

    $og_title = $site_title;

    if(isset($keep_header_data)) {
      foreach($keep_header_data as $key=>$val) {
        $header_data[$key] = $val;
      }
    }
    if(isset($keep_header_classes)) {
      $header_classes = $keep_header_classes;
    }
?>

      {{----------------------------------------}}
      {{-- (A) ITEMS  --}}

      @if($folio_type == 'MULTIPLE_WRITING_TYPE')

        @section('content')

        <div class="[ o-band ] [ u-pad-t-5x u-pad-b-1x ]">
          <div class="[ o-wrap ]" style="max-width: 640px">

          @if(isset($items_expected))
            @foreach($items_expected as $item)
              <div class="[ c-item ] [ u-pad-t-0x u-pad-b-0x ]">
                <p>Expected — {{ $item->title }}</p>
              </div>
            @endforeach
          @endif

          @foreach($items as $item)

            {!! view('folio::partial.c-item')->
                     with(['item' => $item,
                           'item_type' => 'SUMMARY_ITEM_TYPE',
                           'isTitleLinked' => 'true',
                           'class' => '']) !!}

          @endforeach

          @if(isset($ids) and count($ids) > 0)
              {!! view('folio::partial.c-load-more', ['ids' => $ids]) !!}
          @endif

          </div>
        </div>

        @stop

      @endif

      {{--------------------------------}}
      {{-- (B) ITEM --}}

      @if($folio_type == 'SINGLE_WRITING_TYPE')

        @section('content')

        <div class="[ o-band ] [ u-pad-t-5x u-pad-b-1x ]">
          <div class="[ o-wrap ]" style="max-width: 640px">

              {!! view('folio::partial.c-item', [
                'item' => $item,
                'class' => ''
              ]) !!}

          </div>
        </div>

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
	{!! view('folio::partial.c-footer', ['subscribe_data' => $subscribe_data]) !!}
@stop
