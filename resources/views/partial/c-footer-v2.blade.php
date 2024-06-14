@php
  $class = 'c-footer-v2';
  $classes = Folio::expandClassesAsString($classes ?? config('folio.footer.classes'), $class);
  $credits = $credits ?? config('folio.footer.credits');

  $credits_view = $credits_view ?? config('folio.credits.view');
  $credits_data = $credits_data ?? config('folio.credits');
  $credits_hidden = $credits_hidden ?? config('folio.credits.hidden');

  $subscribe_view = $subscribe_view ?? config('folio.subscribe.view');
  $subscribe_data = $subscribe_data ?? config('folio.subscribe');
  $subscribe_hidden = $subscribe_hidden ?? config('folio.subscribe.hidden');
@endphp

<footer class="{{ $class}} {{ $classes }} [ u-pad-b-3x u-mar-t-4x ]">

  @if(!$subscribe_hidden)
    <div class="[ o-wrap o-wrap--size-400 o-wrap--palm-size-full u-mar-b-5x ]">
        {!! view($subscribe_view, $subscribe_data) !!}
    </div>
  @endif

  @if(!$credits_hidden)
      {!! view($credits_view, $credits_data) !!}
  @endif

</div>