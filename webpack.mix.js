const webpack = require('webpack');
let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Custom Mix setup
 |--------------------------------------------------------------------------
 |
 */

mix.webpackConfig({
    plugins: [
        new webpack.ContextReplacementPlugin(
            // The path to directory which should be handled by this plugin
            /moment[\/\\]locale/,
            // A regular expression matching files that should be included
            /(en-gb)\.js/
        )
    ]
});
mix.sass('resources/assets/sass/media-manager.scss', 'public/css/')
    .js('resources/assets/js/media-manager.js', 'public/js/')
    .copy('resources/assets/fonts', 'public/fonts/')