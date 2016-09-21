/**
 * Created by talv on 30/08/16.
 */
var elixir = require('laravel-elixir');
require('laravel-elixir-vue');

elixir(function (mix) {
    mix.sass([
        'animate.min.css',
        'media-manager.scss'
    ], 'public/css/media-manager.css');

    mix.webpack('media-manager.js', 'public/js/media-manager.js');

    mix.copy('resources/assets/fonts', 'public/fonts/');

    //mix.phpUnit();
});