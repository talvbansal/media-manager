/**
 * Created by talv on 30/08/16.
 */
var elixir = require('laravel-elixir');

elixir(function (mix) {
    mix.sass([
        'animate.min.css',
        'media-manager.scss'
    ], 'public/css/media-manager.css');

    mix.copy('node_modules/jquery/dist/jquery.min.js', 'public/js/jquery.min.js');
    mix.copy('node_modules/hammerjs/hammer.min.js', 'public/js/hammer.min.js');
    mix.copy('node_modules/momentjs/build/moment.min.js', 'public/js/moment.min.js');
    mix.copy('node_modules/vue/dist/vue.js', 'public/js/vue.js');
    mix.copy('node_modules/vue-resource/dist/vue-resource.js', 'public/js/vue-resource.js');
    mix.copy('node_modules/vue-touch/vue-touch.js', 'public/js/vue-touch.js');

    mix.copy('resources/assets/fonts', 'public/fonts/');

    mix.scripts(['bootstrap-growl.min.js', 'media-manager.js'], 'public/js/media-manager.js')

    mix.phpUnit();
});