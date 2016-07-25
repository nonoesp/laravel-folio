

@extends('writing::layout')


@section('content')

  <div class="[ c-article o-band ]  [ u-border-bottom  u-no-padding-bottom ]">
    <div class="[ o-wrap  o-wrap--tiny  o-wrap--portable-tiny ]">
	
	  <div class="grid">
	  	@foreach($items as $item)
	  	    <div class="grid__item">
	  			<p>{{ $item->title }}</p>
			</div>
	  	@endforeach
	  </div>

    </div>
  </div>

@stop