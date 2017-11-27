let mix = require('laravel-mix');

mix.sass('resources/assets-dev/sass/folio.scss', 'resources/assets/css')
    .js('resources/assets-dev/js/folio.js', 'resources/assets/js')
    .extract(['vue', 'vue-resource', 'vue-focus', 'jquery', 'validate-js', 'lodash', 'axios']);