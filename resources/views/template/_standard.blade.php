@extends('space::template._base')

@section('content')

  <div class="[ o-band ] [ u-pad-t-10x u-pad-b-1x ]">
    <div class="[ o-wrap ]" style="max-width: 640px">

      {!! view('space::partial.c-item', ['item' => $item]) !!}

    </div>
  </div>

@stop
