const mix = require('laravel-mix');

// ...

mix.sass('resources/sass/folio.scss', 'public/folio/css');
    //.sourceMaps();

mix.js('resources/js/folio.js', 'public/folio/js')
    .extract([
        'vue', 
        'vue-resource', 
        'jquery',
        'jquery-lazy',
        'jquery-unveil',
        'validate-js', 
        'lodash', 
        'axios'
    ]);

if (mix.inProduction()) {
    mix.version();
}

//mix.copy('node_modules/folio-scss/vendor/icons-links-gwern', 'public/folio/icons');

//mix.browserSync('app.test');