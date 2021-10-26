let mix = require('laravel-mix');

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

mix

// --------------------------------------------------------------
//  App
// --------------------------------------------------------------
.js(
    "./resources/js/app.js",
    "./public/js/app.js", "."
).vue()

.postCss(
    "resources/css/app.css",
    "public/css/app.css", [
        //require('postcss-import'),
        require("tailwindcss"),
]);

// --------------------------------------------------------------
//  Version
// --------------------------------------------------------------
mix.version();

