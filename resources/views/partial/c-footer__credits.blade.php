<?php
  if(!isset($text)) {
      $footer = config('folio.footer');
      $text = null;
      if($footer['credits_text']) {
        $text = $footer['credits_text'];
      }
  }
?>

<footer class="[ c-footer ] [ u-text-align--center u-font-size--a u-opacity--half ]">
  <div class="[ grid ]">
      <div class="[ c-footer__item ] [ grid__item one-whole ]">

        <p>
          @if($text)

            {!! $text !!}

          @else

            Developed with
            <i class="[ fa fa-heart ]"></i>
            by
            {!! Html::link('https://nono.ma', 'Nono.MA', ['target' => '_blank']) !!}

          @endif
        </p>

      </div>
  </div>
</footer>
