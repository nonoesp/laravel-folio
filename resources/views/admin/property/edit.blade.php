@extends('folio::layout-v2')

@php
    $property = $property ?? null;
    if ($property) {
        $item = Item::withTrashed()->find($property->item_id);
    }

    $title = $property->name.' · '.$item->title;
    $footer_hidden = true;
    $header_view = 'folio::partial.c-header-simple-v2';
    $header_data = array_merge($header_data ?? config('folio.header'),
    [
        'image' => null,
        'is_media_hidden' => true,
    ]);

    $menu_data = array_merge($menu_data ?? config('folio.menu'),
    [
        'items' => [
          '<i class="fa fa-eye"></i>' => $item->path(),
		  '<i class="fa fa-share"></i>' => $item->sharePath(),
		  '<i class="fa fa-pencil"></i>' => $item->editPath()
        ]
    ]);  


@endphp

@push('scripts')
	<!-- Mousetrap for handling keyboard shortcuts -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.6.1/mousetrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mousetrap/1.6.1/plugins/global-bind/mousetrap-global-bind.min.js"></script>
    <script>

        window.onload = function () {
            const value = $("#value").val();
            const name = $("#name").val();
            const updateSaveButton = () => {
                const currentValue = $("#value").val();
                const currentName = $("#name").val();
                $('.js--save').prop('disabled', value === currentValue && name === currentName);
            };
            $("#value").on('keyup', function () { updateSaveButton(); });
            $("#name").on('keyup', function () { updateSaveButton(); });
            updateSaveButton();
        };

        /*
        * CTRL+S & COMMAND+S
        * Keyboard shortcut to save edits by submitting the form.
        */
        Mousetrap.bindGlobal(['ctrl+s', 'command+s'], function(e) {
            $('.js--save').click();
            e.preventDefault();
            return false;
        });

    </script>
@endpush

@section('content')

<div class="o-wrap u-mar-t-8x">

    <p><a href="/property/{{$property->item_id}}">
        <strong>← item properties</strong>
    </a></p>

    @if ($item)
        <p class="u-font-size--g">{{$item->title}}</p>
    @endif

    <form action="/property/edit" method="POST">
        @csrf
        <input type="text" name="name" id="name" value="{{$property->name}}" style="font-size:1.1rem;font-family: Menlo, monospace;border:none;margin-bottom:20px;">
        <textarea name="value" id="value" cols="30" rows="10"
        style="height:400px;font-family:Menlo, monospace;">{{$property->value}}</textarea>
        <input type="hidden" name="id" value="{{$property->id}}" />
        <input type="submit" disabled="true" class="js--save" value="Save">
    </form>
</div>

@endsection