@php
    $locale = str_replace('_', '-', app()->getLocale());
    $theme = $theme ?? null;
    $html_classes = $html_classes ?? config('folio.html.classes', []);

    if ($theme) {
        array_push($html_classes, $theme);
    }

    $html_classes_string = count($html_classes) ? ' class="'.join(' ', $html_classes).'"' : null;
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}"{!! $html_classes_string !!}>

    <!--
    Hey. It's Nono! 👋🏻
    I craft user experiences.
    
    This site is built with Folio.
    github.com/nonoesp/laravel-folio
    -->

    <head>

    @stack('metadata')

    @stack('scripts')

    </head>

    <body>

    @stack('notifications')

    @yield('header')

    @yield('menu')

    @yield('cover')

    @yield('content')

    @yield('footer')

    </body>

</html>