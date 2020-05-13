<?php
// hide credits?
if(!isset($hide_credits)) {
  $footer_credits = config('folio.footer');
  $hide_credits = false;
  if(isset($footer_credits['hide_credits'])) {
    $hide_credits = $footer_credits['hide_credits'];
  }
}
// hide subscribe?
if(!isset($hide_subscribe)) {
  $hide_subscribe = false;
}

$source = '';
$medium = '';
$campaign = '';
$button_text = trans('folio::base.subscribe_button_text');
$text = trans('folio::base.receive-our-posts');

if(!isset($subscribe_data)) {
  $subscribe_data = null;
} else {
  if(isset($subscribe_data['source'])) {
    $source = $subscribe_data['source'];
  }
  if(isset($subscribe_data['medium'])) {
    $medium = $subscribe_data['medium'];
  }
  if(isset($subscribe_data['campaign'])) {
    $campaign = $subscribe_data['campaign'];
  }
  if(isset($subscribe_data['button_text'])) {
    $button_text = $subscribe_data['button_text'];
  }
  if(isset($subscribe_data['text'])) {
    $text = $subscribe_data['text'];
  }  
}

  if(isset($data['classes'])) { $classes = $data['classes']; }
?>

<div class="[ u-pad-b-1x u-pad-t-1x {{ $classes ?? '' }} ] [ folio-inputs ]">

  @if($hide_subscribe == false)

    <div class="[ o-wrap o-wrap--size-tiny o-wrap--portable-size-minuscule u-pad-b-2x ]">
      {!! view('folio::partial.c-subscribe-v2', [
        'source' => $source,
        'medium' => $medium,
        'campaign' => $campaign,
        'button_text' => $button_text,
        'text' => $text
        ]) !!}
    </div>

  @endif

  @if($hide_credits == false)

    <div class="[ o-wrap o-wrap--size-medium ]">
      @if(isset($credits_text))
        {!! view('folio::legacy.partial.c-footer__credits')->with(['text' => $credits_text]) !!}
      @else
        {!! view('folio::legacy.partial.c-footer__credits') !!}
      @endif
    </div>

  @endif

</div>
