
@extends('space::admin.layout')

<?php
$settings_title = Config::get('space.title');
if($settings_title == '') {
	$settings_title = "Space";
}
	$site_title = 'Subscribers — '. $settings_title;
?>

@section('title', 'Subscribers')

@section('scripts')
    <script type="text/javascript" src="/nonoesp/space/js/space.js"></script>
@stop

@section('content')

<style media="screen">
	.grid {
		letter-spacing: inherit;
	}
</style>

<div class="c-admin">

  @if(count($subscribers))

    @if(count($subscribers) == 1)
      <p>There is a subscriber.</p>
    @else
      <p>There are {{ count($subscribers) }} subscribers.</p>
    @endif

    @foreach($subscribers as $subscriber)
      <p>{{ Html::link('mailto:'.$subscriber->email, $subscriber->email) }}</p>
    @endforeach

  @else
    <p>
      There are no subscribers yet.
    </p>
  @endif

</div>

@endsection
