<?php
	$date = new Date($item->published_at);
	$href = '/'.$item->path();
	$target = "";
	$class_is_external = '';
	$source = "";
	if($item->link != "") {
		$href = $item->link;
		$target = ' target="_"';
		$class_is_external = '[ is-external ]';
		$source = " · ".explode("/", $item->link)[2];
	}
  $class_is_tagged = '';
  if($item->tagNames()) $class_is_tagged = '[ is-tagged ]';
	$is_expected = false;
	$class_is_expected = '';
	if(isset($expected)) {
		$is_expected = true;
		$class_is_expected = '[ is-expected ]';
	}
?>

<div class="[ c-item-li__container ]">

@if(!$is_expected)
<a href="{{ $href }}" {{ $target }}>
@endif

  <article class="[ c-item-li ] {{ $class_is_tagged }} {{ $class_is_expected }}">

  		{{-- Title --}}
  		<h1 class="{{$class_is_external}}">
        {{ Thinker::title($item->title) }}
      </h1>

  		{{-- Date --}}
  		<p class="[ c-item-li__date ]">
				@if(isset($expected))
					@if($date < Date::now()->add('20 days'))
						{{ trans('space::base.expected') }}
						<span class="u-hidden-palm">{{ ucWords($date->format('l j, F Y')) }}.</span>
						<span class="u-visible-palm">{{ ucWords($date->format('F j')) }}.</span>
					@else
						{{ trans('space::base.expected') }}.
					@endif
					{{ Html::link('', trans('space::base.subscribe'), ['class' => 'js--subscribe-link']) }}
					{{ trans('space::base.to-be-notified') }}
				@else
        	<span>{{ ucWords($date->format('F Y')).$source }}</span>
				@endif
  		</p>

  </article>

@if(!$is_expected)
</a>
@endif

</div>
