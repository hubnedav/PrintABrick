var Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
// directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    // .setManifestKeyPrefix('build/')

    .addPlugin(new CopyWebpackPlugin([
        { from: './assets/images/noimage_large.png', to: 'images', flatten: true },
        { from: './assets/images/noimage_min.png', to: 'images', flatten: true },
    ]))

    .addEntry('app', './assets/js/app.js')

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .disableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .autoProvidejQuery()
    .autoProvideVariables({
        THREE: 'three',
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
    })
    // enables Sass/SCSS support
    .enableSassLoader()
    .enablePostCssLoader((options) => {
        options.config = {
            path: 'postcss.config.js'
        };
    })
;

module.exports = Encore.getWebpackConfig();
