
@extends(Config::get("writing.template-view"))

<?php
  //{{-- @extends('layout.main') --}}
    /*
    / [ writing ]
    /
    / $writing_type (SINGLE_WRITING_TYPE, MULTIPLE_WRITING_TYPE)
    / $tag
    /
    */

    // 1. Defaults
    $site_title = 'Writing — '.Config::get('settings.title');
    $og_description = 'Description of the blog.';
    $services_typekit = Config::get('services.typekit.writing');
    $header_classes = 'c-header--white';
    $is_header_static = true;

    // 2. Defaults Cover
    $cover_subtitle = Thinker::array_rand_value(['Subtitle 01', 'Subtitle 02']);
    $cover_classes_title_b = '';
    $cover_image = '';
    $cover_classes = '';
    $cover_active = true;

    // 3. Define Item Type
    if(isset($items)) {
      $writing_type = 'MULTIPLE_WRITING_TYPE';
    } else if(isset($item)) {
      $writing_type = 'SINGLE_WRITING_TYPE';
    } else {
      $writing_type = 'EMPTY_TYPE';
    }

    // 4. Single Item Settings
    if ($writing_type == 'SINGLE_WRITING_TYPE') {

        // 4.1. General
        $site_title = $item->title.' — '.Config::get('settings.title');
        $og_description = Thinker::limitMarkdownText(Markdown::string($item->text), 159, ['sup']);
        $og_type = 'item';
        if ($item->image) {
          $og_image = $item->image;
        } else if ($item->video) {
          $og_image = Thinker::getVideoThumb($item->video);
        }

        // 4.2. Cover or not
        if ($item->image == '') {

            // 4.2.1. Item w/o cover
            $header_classes = 'c-header--relative';
            $cover_active = false;

        } else {

            // 4.2.2. Item w/ cover
            $cover_subtitle = $item->title;
            if(strlen($item->title) > 40) {
              $cover_classes_title_b = 'c-cover__title-b--small';
            }
            $cover_image = $item->image;
            $cover_classes .= 'is-faded is-fullscreen';
        }
    }

    // 5. Multiple Item Settings
    if ($writing_type == 'MULTIPLE_WRITING_TYPE') {

        // Tags
        if (isset($tag)) {
          $site_title = ucwords($tag).' — '.Config::get('settings.title');
          $og_description = 'Items tagged with the category '.ucwords($tag);
        }
    }

    // 1. Defaults
    $og_title = $site_title;

?>

@section('content')

  {{-- Cover --}}

  @if($cover_active)

      {{-- View::make('partial.c-cover')
             ->with(array('title' => '<div class="[ o-icon-container  o-icon-container--small  o-icon--white ]">'.Config::get('svg.logo-arma').'</div>',
                          'subtitle' => $cover_subtitle,
                          'classes_title_b' => $cover_classes_title_b,
                          'image' => $cover_image,
                          'description' => trans('base.description'),
                          'class' => 'is-header u-background-grey '.$cover_classes)) --}}
  @endif

  <div class="[ o-band ]  [ u-border-bottom  u-no-padding-bottom ]">
    <div class="[ o-wrap  o-wrap--standard  o-wrap--portable-tiny ]">

      {{-- Items --}}

      @if($writing_type == 'MULTIPLE_WRITING_TYPE')

          @if(isset($items_expected))
            @foreach($items_expected as $item)
              <div class="c-item">
                <p>Expected — {{ $item->title }}</p>
              </div>
            @endforeach
          @endif

          @foreach($items as $item)

            {!! View::make('writing::partial.c-item')->
                     with(['item' => $item,
                           'item_type' => 'SUMMARY_ITEM_TYPE',
                           'isTitleLinked' => 'true']) !!}

          @endforeach

          @if(isset($ids) and count($ids) > 0)
              {!! View::make('writing::partial.c-load-more')->
                       with('ids', $ids) !!}
          @endif

      @endif


      {{-- Item --}}

      @if($writing_type == 'SINGLE_WRITING_TYPE')

            {!! View::make('writing::partial.c-item')->
                     with(['item' => $item,
                           'class' => '-u-no-margin-bottom  -u-no-border-bottom']) !!}

      @section('metadata')
        <!-- Item -->
        <meta property="item:published_time" content="{{ $item->published_at }}"/>
        <meta property="item:modified_time" content="{{ $item->modified_at }}"/>
      @stop

      @endif

    </div>
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

    <script type="text/javascript" src="/nonoesp/writing/js/writing.js"></script>

@stop

@section('footer')

{{-- Footer --}}
{{-- View::make('partial.c-footer') --}}

@stop
