@php
  $class = $class ?? 'c-social__wrapper';
  $classes = Folio::expandClassesAsString($classes ?? [], $class);
  $media = $media ?? null;
@endphp

@if($media && count($media))

  <div class="[ {{ $classes }} ]">

        <span class="[ c-social m-fa ]">

          @foreach($media as $media=>$link)
            <a href="{{ $link }}" target="_blank" class="fa--social fa__link fa__link--{{ $media }}">
              <i class="fa fa-{{ $media }}"></i>
            </a>
          @endforeach

        </span>

  </div>

@endif
