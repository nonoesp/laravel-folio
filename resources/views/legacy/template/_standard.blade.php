@extends('folio::template._base')

@section('content')

  <div class="[ o-band ] [ u-pad-t-10x u-pad-b-1x ]">
    <div class="[ o-wrap ]" style="max-width: 640px">

      {!! view('folio::partial.c-item', ['item' => $item]) !!}

      @if($user = Auth::user() and $user->is_admin)
        {!! Html::link('/'.Folio::adminPath().'item/edit/'.$item->id, 'Edit') !!}
      @endif

    </div>
  </div>

@stop
