<?php
  if(!isset($text)) {
      $footer = config('folio.footer');
      $text = null;
      if ($footer && array_key_exists('credits_text', $footer)) {
        $text = $footer['credits_text'];
      }
  }
?>

<footer class="[ c-footer ] [ u-text-align--center u-font-size--a u-opacity--half ]">
  <div class="[ grid ]">
      <div class="[ c-footer__item ] [ grid__item one-whole ]">

        <p>
          @if($text)

            @php
            $text = str_replace(
                [
                    '{year}',
                    '{footer-text}',
                ],
                [
                    Item::formatDate(Date::now(), 'Y'),
                    trans('folio.footer-text'),
                ],
                $text);
            @endphp          

            {!! $text !!}

          @else

            Designed and built with
            <i class="[ fa fa-heart ]"></i>
            by
            {!! Html::link('https://nono.ma', 'Nono.MA', ['target' => '_blank']) !!}.

          @endif
        </p>

      </div>
  </div>
</footer>
