let mix = require('laravel-mix').setPublicPath('resources/assets');

mix.sass('resources/assets-dev/sass/folio.scss', 'nonoesp/folio/css')
    .js('resources/assets-dev/js/folio.js', 'nonoesp/folio/js')
    .extract(['vue', 'vue-resource', 'vue-focus', 'jquery', 'validate-js', 'lodash', 'axios']);