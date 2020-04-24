@php
    $locale = str_replace('_', '-', app()->getLocale());
    $theme = $theme ?? null;
@endphp

<!DOCTYPE html>
<html lang="{{ $locale }}" @if($theme)class="{{ $theme }}"@endif>

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