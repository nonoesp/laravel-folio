const mix = require('laravel-mix');

mix.setPublicPath('resources/build');

mix.sass('resources/sass/folio.scss', 'folio/css')
    .js('resources/js/folio.js', 'folio/js')
    .extract([
        'vue',
        'vue-resource',
        'vue-focus',
        'jquery',
        'jquery-lazy',
        'jquery-unveil',
        'validate-js',
        'lodash',
        'axios'
    ]);

// ..

if (mix.inProduction()) {
    mix.version();
}

// mix.copy('node_modules/folio-scss/vendor/icons-links-gwern', 'img/icons');