@php
    $items = $items ?? ($buttons ?? ['<i class="fa fa-gear"></i>' => '/admin']);
@endphp

@if($user = Auth::user())
  @if($user->is_admin)

    @if(isset($items))

      <div class="[ c-floating-menu ]">

        <div class="c-floating-menu__buttons js--floating-menu__buttons">
            @foreach($items as $label=>$path)
            @if(is_array($path))
              <a href="{{ $path[0] }}"
                 class="[ c-floating-menu__item {{$path[1] ?? ''}} ]"
                 {!! $path[2] ?? '' !!}>
            @else
              <a href="{{ $path }}" class="[ c-floating-menu__item ]">
            @endif
              <div class="[ c-floating-menu__item-button c-floating-menu__item-button u-text-align--center ]
                          [ js--floating-menu-item ]">
                {!! $label !!}
              </div>
            </a>
          @endforeach
        </div>

        <div class="c-floating-menu__status js--floating-menu__status"
        style="display: none; padding: 9px; background-color: white;">
            Status..
        </div>
      </div>
  
    @endif

  @endif
@endif