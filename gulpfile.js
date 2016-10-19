/**
 * Created by talv on 30/08/16.
 */
const elixir = require('laravel-elixir');
require('laravel-elixir-vue-2');

elixir(function (mix) {

    // Styles and resources...
    mix.copy('resources/assets/fonts', 'public/fonts/')
        .sass(['animate.min.css',
            'media-manager.scss'
        ], 'public/css/media-manager.css')
        .webpack('media-manager.js', 'public/js/media-manager.js');

    // Run unit tests...
    mix.phpUnit();
});