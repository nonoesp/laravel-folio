@extends('folio::layout-v2')

@php
    $footer_hidden = true;
    $header_view = 'folio::partial.c-header-simple-v2';
    $header_data = array_merge($header_data ?? config('folio.header'),
    [
        'image' => null,
        'is_media_hidden' => true,
    ]);

    $property = $property ?? null;
    if ($property) {
        $item = Item::find($property->item_id);
    }
@endphp

@push('scripts')
    <script>

        window.onload = function () {
            const value = $("#value").val();
            const updateSaveButton = () => {
                const newValue = $("#value").val();
                $('.js--save').prop('disabled', value === newValue);
            };
            $("#value").on('keyup', function () { updateSaveButton(); });
            updateSaveButton();
        };

    </script>
@endpush

@section('content')

<div class="o-wrap u-mar-t-8x">

    <p><a href="/property/{{$property->item_id}}">
        <strong>← item</strong>
    </a></p>

    @if ($item)
        <p class="u-font-size--g">{{$item->title}}</p>
    @endif

    <p>
        <strong><code>{{$property->name}}</code></strong>
    </p>

    <form action="/property/edit" method="POST">
        @csrf
        <textarea name="value" id="value" cols="30" rows="10">{{$property->value}}</textarea>
        <input type="hidden" name="id" value="{{$property->id}}" />
        <input type="submit" disabled="true" class="js--save" value="Save">
    </form>
</div>

@endsection