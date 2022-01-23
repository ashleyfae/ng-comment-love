let mix = require('laravel-mix');

mix
    .js('assets/src/js/admin.js', 'assets/build/js')
    .js('assets/src/js/frontend.js', 'assets/build/js')
    .sass('assets/src/sass/frontend.scss', 'assets/build/css');
