/**
 * Created by talv on 30/08/16.
 */
var elixir = require('laravel-elixir');

elixir(function (mix) {
    mix.sass([
        'easel-media-manager.scss'
    ], 'public/css/easel-media-manager.css');
    mix.scripts([
        '../../../node_modules/jquery/dist/jquery.min.js',
        '../../../node_modules/bootstrap-growl/bootstrap-notify.min.js',
        '../../../node_modules/vue/dist/vue.js',
        '../../../node_modules/vue-resource/dist/vue-resource.js',
        '../../../node_modules/vue-touch/vue-touch.js',
        'easel-media-manager.js'
    ], 'public/js/easel-media-manager.js');

    mix.phpUnit();
});