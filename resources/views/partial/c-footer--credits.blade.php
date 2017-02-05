<?php
  $footer_text = null;
  $footer_credits = Config::get('space.footer-credits');
  if($footer_credits['text']) $footer_text = $footer_credits['text'];
?>

<footer class="[ c-footer ] [ u-text-align--center u-font-size--a u-opacity--half ]">

<div class="[ grid ]">

    <div class="[ c-footer__item ] [ grid__item one-whole ]">

      <p>
        @if($footer_text)

          {{ $footer_text }}

        @else

          Developed with
          <i class="[ fa fa-heart ]"></i>
          by
          {!! Html::link('http://nono.ma', 'nono.ma', ['target' => '_blank']) !!}

        @endif
      </p>

    </div>

</div>
</footer>
