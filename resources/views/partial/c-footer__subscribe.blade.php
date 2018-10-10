<?php
if(!isset($source) && request()->has('utm_source')) {
      $source = request()->input('utm_source');
}
if(!isset($campaign) && request()->has('utm_campaign')) {
      $campaign = request()->input('utm_campaign');
}
if(!isset($medium) && request()->has('utm_medium')) {
      $medium = request()->input('utm_medium');
}
?>

<footer class="[ c-footer ] [ c-subscribe ] [ js--subscribe ] ">

<div class="[ grid ]">

    <div class="[ c-footer__item ] [ grid__item one-whole ]">

    {{ Form::open(array('class' => '[ s-form-subscribe ] [ js--subscribe__form ]', 'url' => config('services.mailchimp.long'))) }}

    <div class="[ grid ] [ grid--narrow ]">

           @if(isset($source))
               {{ Form::hidden('source', $source, ['class' => 'js--subscribe__source']) }}
           @endif

           @if(isset($medium))
               {{ Form::hidden('medium', $medium, ['class' => 'js--subscribe__medium']) }}
           @endif

           @if(isset($campaign))
               {{ Form::hidden('campaign', $campaign, ['class' => 'js--subscribe__campaign']) }}
           @endif

           <div class="[ grid__item desk--two-thirds ]">
               {{ Form::email('email', null, ['placeholder' => trans('folio::base.your-email-address'), 'class' => '[ js--subscribe__email ] [ u-case-input-lower ]', 'name' => 'EMAIL']) }}
           </div>
           <div class="[ grid__item desk--one-third ]">
                @if(isset($button_text))
                    {{ Form::submit($button_text, ['class' => '[ js--subscribe__submit ] [ button--background-white ]']) }}
                @else
                    {{ Form::submit(trans('folio::base.subscribe_button_text'), ['class' => '[ js--subscribe__submit ] [ button--background-white ]']) }}
                @endif
           </div>

    </div>

    {{ Form::close() }}

       <p class="[ js--subscribe__label ]{{--
             --}}[ u-font-size--a u-opacity--half u-select-none ]{{--
             --}}[ u-text-align--portable-center ]">

                @if(isset($text))
                    {!! $text !!}
                @else
                    {!! trans('folio::base.receive-our-posts') !!}
                @endif
           
       </p>


    @if(!isset($hide_terms))
       <p class="[ js--subscribe__label-privacy ]{{--
             --}}[ u-font-size--70 u-opacity--half u-select-none ]{{--
             --}}[ u-text-align--portable-center ]">

                @if(isset($text_small))
                    {!! $text_small !!}
                @else
                    {!! trans('folio::base.newsletter-privacy') !!}
                @endif
           
       </p>       
    @endif

    </div>

</div>
</footer>