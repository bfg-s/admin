const mix = require('laravel-mix');

mix.options({
    processCssUrls: false
});

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

mix.js('resources/js/admin.js', 'public/js').vue();
mix.js('resources/theme/theme.js', 'public/theme/default/theme.js').vue();
//mix.js('resources/lte_theme/theme.js', 'public/theme/lte/theme.js').vue();

mix.sass('resources/theme/theme.scss', 'public/theme/default/theme.css');
//mix.sass('resources/lte_theme/theme.scss', 'public/theme/lte/theme.css');
