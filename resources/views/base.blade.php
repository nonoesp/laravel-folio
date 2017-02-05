
@extends(Config::get("space.template-view"))

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
    $site_title = Config::get('space.title');
    $og_description = 'Description of the blog.';
    $services_typekit = Config::get('services.typekit.space');
    $header_view = 'space::partial.c-header-simple';
    $header_classes = 'c-header--white -c-header--relative';
    $is_header_static = true;

    // 2. Defaults Cover
    $cover_subtitle = Thinker::array_rand_value(['Subtitle 01', 'Subtitle 02']);
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
        $site_title = $item->title.' — '.Config::get('space.title');
        $og_description = Thinker::limitMarkdownText(Markdown::convertToHtml($item->text), 159, ['sup']);
        $og_type = 'item';
        if ($item->image) {
          $og_image = $item->image;
        } else if ($item->video) {
          $og_image = Thinker::getVideoThumb($item->video);
        }

        // 4.2. Cover or not
        if ($item->image == '') {

            // 4.2.1. Item w/o cover
            $header_classes = ['relative', 'borderless'];
            $cover_active = false;
            $header_data = [
              //'description' => trans('folio.slogan-writing')
                'is_media_hidden' => true,
                'is_navigation_hidden' => true
            ];

        } else {

            // 4.2.2. Item w/ cover
            $cover_subtitle = $item->title;
            if(strlen($item->title) > 40) {
              $cover_classes_title_b = 'c-cover__title-b--small';
            }
            $cover_image = $item->image;
            $cover_classes .= 'is-faded is-fullscreen';
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
          $site_title = ucwords($tag).' — '.Config::get('space.title');
          $og_description = 'Items tagged with the category '.ucwords($tag);
        }
    }

    // 1. Defaults
    $og_title = $site_title;

?>

@section('content')

  {{-- Cover --}}

  @if($cover_active)

      {!! View::make('space::partial.c-cover')
              ->with(array('title' => Config::get('space.title'),//$item->title,
                          'subtitle' => $cover_subtitle,
                          'classes_title_b' => $cover_classes_title_b,
                          'image' => $cover_image,
                          'description' => Config::get('space.description'),//trans('base.description'),
                          'class' => 'is-header u-background-grey '.$cover_classes)) !!}

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
        <meta property="article:modified_time" content="{{ $item->modified_at }}"/>
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

    <script type="text/javascript" src="/nonoesp/space/js/space.js"></script>

@stop

@section('footer')

{{-- Footer --}}
{{-- View::make('partial.c-footer') --}}

@stop
