<?php
// hide credits?
if(!isset($hide_credits)) {
  $footer_credits = Config::get('space.footer');
  $hide_credits = false;
  if(isset($footer_credits['hide_credits'])) {
    $hide_credits = $footer_credits['hide_credits'];
  }
}

$source = '';
$campaign = '';

if(!isset($subscribe_data)) {
  $subscribe_data = null;
} else {
  if(isset($subscribe_data['source'])) {
    $source = $subscribe_data['source'];
  }
  if(isset($subscribe_data['campaign'])) {
    $campaign = $subscribe_data['campaign'];
  }
}

  if(isset($data['classes'])) { $classes = $data['classes']; }
?>

<div class="[ u-pad-b-1x u-pad-t-1x {{ $classes or '' }} ]">

  <div class="[ o-wrap o-wrap--size-tiny o-wrap--portable-size-minuscule u-pad-b-2x ]">
    {!! view('folio::partial.c-footer__subscribe', ['source' => $source, 'campaign' => $campaign]) !!}
  </div>

  @if($hide_credits == false)
  <div class="[ o-wrap o-wrap--size-medium ]">
    @if(isset($credits_text))
      {!! view('folio::partial.c-footer__credits')->with(['text' => $credits_text]) !!}
    @else
      {!! view('folio::partial.c-footer__credits') !!}
    @endif
  </div>
  @endif

</div>
