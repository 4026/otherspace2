require('es6-promise').polyfill();
var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function (mix) {
    mix
        .styles(
            ['app.css'],
            'public/css/app.css'
        )
        .babel(
            [
                'utils.js',
                'PlayerLocation.js',
                'app.js',
                'SetStateInDepthMixin.jsx',
                'app.jsx'
            ],
            'public/js/app.js'
        )
        .version(['css/app.css', 'js/app.js'])
    ;
});
