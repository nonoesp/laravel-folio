<?php

  if(!isset($buttons)) {
    $buttons = ['·' => '/'.Folio::path()];
  }

?>

@if($user = Auth::user())
  @if($user->is_admin)

    @if(isset($buttons))

      <div class="[ c-floating-menu ]">
        @foreach($buttons as $label=>$path)
          @if(is_array($path))
            <a href="{{ $path[0] }}"
               class="[ c-floating-menu__item {{$path[1] or ''}} ]"
               {!! $path[2] or '' !!}>
          @else
            <a href="{{ $path }}" class="[ c-floating-menu__item ]">
          @endif
            <div class="[ c-floating-menu__item-button c-floating-menu__item-button u-text-align--center ]">
              {!! $label !!}
            </div>
          </a>
        @endforeach
      </div>
  
    @endif

  @endif
@endif