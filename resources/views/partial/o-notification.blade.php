@php
    $notification_class = 'o-notification';
    $classes = $classes ?? [];
    // Expand modifier classes
    foreach($classes as $key => $class) {
      if (Illuminate\Support\Str::of($class)->startsWith('--')) {
        $classes[$key] = $notification_class.$class;
      }
    }
@endphp

<div class="[ o-notification {{ $classes ? join(" ", $classes) : null }} ]">
  {!! $notification !!}
</div>
