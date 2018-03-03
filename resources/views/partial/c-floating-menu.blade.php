@if($user = Auth::user())
  @if($user->is_admin)

    @if(isset($item))
      <div class="[ c-floating-menu ]">
        <a href="/admin/item/edit/{{ $item->id }}" class="[ c-floating-menu__item ]">
          <div class="[ c-floating-menu__item-button c-floating-menu__item-button ]">
            edit
          </div>
        </a>
      </div>
    @else
      <div class="[ c-floating-menu c-floating-menu ]">

        <a href="/{{ Folio::path() }}" class="[ c-floating-menu__item ]">
          <div class="[ c-floating-menu__item-button c-floating-menu__item-button ]">
            home
          </div>
        </a>
        
        {{-- <a href="/logout" class="[ c-floating-menu__item ]">
          <div class="[ c-floating-menu__item-button c-floating-menu__item-button ]">
            logout
          </div>
        </a>         --}}

      </div>
    @endif

  @endif
@endif