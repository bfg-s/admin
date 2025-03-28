const mix = require('laravel-mix');
require('laravel-mix-tailwind');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('javascript/app.js', 'assets/js')
    .sass('javascript/sass/app.scss', 'assets/css')
    .sass('javascript/sass/tailwind.scss', 'assets/css', [
        require('tailwindcss'),
        require('autoprefixer'),
    ])
    .tailwind();
