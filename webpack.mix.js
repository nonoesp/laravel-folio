const { mix } = require('laravel-mix');

mix.sass('resources/assets-dev/sass/space.scss', 'resources/assets/css')
   .sourceMaps();

mix.js('resources/assets-dev/js/space.js', 'resources/assets/js');
