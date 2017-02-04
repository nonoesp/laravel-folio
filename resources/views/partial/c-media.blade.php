@if(isset($media) && count($media))

<div class="[ c-header-simple__media ]">

      <span class="[ c-social m-fa ]">

        @foreach($media as $media=>$link)
          <a href="{{ $link }}" target="_blank" class="fa--social">
            <i class="fa fa-{{ $media }}"></i>
          </a>
        @endforeach

      </span>

</div>

@endif
