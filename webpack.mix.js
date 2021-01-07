const mix = require('laravel-mix');
require("laravel-mix-vue3");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.vue3('js/bootstrap.vue.ui.js', 'assets')
    .postCss('css/bootstrap.ui.css', 'assets');