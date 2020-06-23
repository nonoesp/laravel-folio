@php
  $class = $class ?? config('folio.subscribe.class');
  $classes = Folio::expandClassesAsString($classes ?? config('folio.footer.classes'), $class);
  
  $button_text = Folio::trans($button_text ?? '{folio::base.subscribe_button_text}');
  $text = Folio::trans($text ?? '{folio::base.receive-our-posts}');
  $detail_text = Folio::trans($detail_text ?? '{folio::base.newsletter-privacy}');

  $source = $source ?? request()->has('utm_source');
  $campaign = $campaign ?? request()->has('utm_campaign');
  $medium = $medium ?? request()->has('utm_medium');
  $newsletter_list = $newsletter_list ?? 
  (request()->has('list') ? request()->input('list') : config('folio.subscribers.default-list'));

  // Remove wrapping <p>s
  $text = $text ? preg_replace('/<p>(.*?)<\/p>/is', '$1', Item::convertToHtml($text) ?? '') : null;
  $detail_text = $detail_text ? preg_replace('/<p>(.*?)<\/p>/is', '$1', Item::convertToHtml($detail_text) ?? '') : null;
@endphp

<div class="js--subscribe {{$class}} {{$classes}}">

<div class="[ grid ]">

    <div class="grid__item one-whole">

    <p class="{{$class}}__text js--subscribe__label">
        {!! $text !!}
    </p>

    {{ Form::open([
      'class' => $class.'__form ] [ js--subscribe__form ]',
      'url' => '/subscriber/create',
      'method' => 'POST',
    ]) }}
    @honeypot
    <div class="[ grid ] [ grid--narrow ] folio-inputs">

           @if($source)
               {{ Form::hidden('source', $source, ['class' => 'js--subscribe__source']) }}
           @endif

           @if($medium)
               {{ Form::hidden('medium', $medium, ['class' => 'js--subscribe__medium']) }}
           @endif

           @if($campaign)
               {{ Form::hidden('campaign', $campaign, ['class' => 'js--subscribe__campaign']) }}
           @endif

           @if($newsletter_list)
               {{ Form::hidden('newsletter_list', $newsletter_list, ['class' => 'js--subscribe__newsletter-list']) }}
           @endif           

           <div class="[ grid__item two-thirds palm--one-whole ]">
               {{ Form::email('email', null, ['placeholder' => trans('folio::base.your-email-address'), 'class' => '[ js--subscribe__email ] [ u-case-input-lower ]', 'name' => 'EMAIL']) }}
           </div>
           <div class="[ grid__item one-third palm--one-whole ]">
                @if($button_text)
                    {{ Form::submit($button_text, ['class' => '[ js--subscribe__submit ] [ button--background-white ]']) }}
                @endif
           </div>

    </div>

    {{ Form::close() }}

    @if($detail_text)
    <p class="{{$class}}__detail-text js--subscribe__label-privacy">
        {!! $detail_text !!}            
    </p>
    @endif

    </div>

</div>
</div>