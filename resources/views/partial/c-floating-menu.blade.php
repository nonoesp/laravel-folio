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
          <a href="{{ $path }}" class="[ c-floating-menu__item ]">
            <div class="[ c-floating-menu__item-button c-floating-menu__item-button ]">
              {!! $label !!}
            </div>
          </a>
        @endforeach
      </div>
  
    @endif

  @endif
@endif