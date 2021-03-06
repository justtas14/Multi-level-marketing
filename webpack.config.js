var Encore = require('@symfony/webpack-encore');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    .addEntry('fileGallery', './assets/js/GalleryApp/app.js')
    .addEntry('modal', './assets/js/GalleryApp/modalApp.js')
    .addEntry('quill', './assets/js/GalleryApp/quillApp.js')
    .addEntry('dragNdrop', './assets/js/DragNDrop/app.js')
    .addEntry('confirmation', './assets/js/Confirmation/app.js')
    .addEntry('clipboard', './assets/js/Clipboard/app.js')
    .addEntry('userSearch', './assets/js/UserSearch/app.js')
    .addEntry('logging', './assets/js/Logging/app.js')

    //.addEntry('page1', './assets/js/page1.js')
    //.addEntry('page2', './assets/js/page2.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    // .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    // .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())
    .addLoader({ test: /\.vue$/, loader: "vue-svg-inline-loader" })
    .enableVueLoader()

    // enables Sass/SCSS support
    // .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    .enableReactPreset()
    .configureBabel(function (babelConfig) {
        babelConfig.plugins.push('@babel/plugin-transform-runtime');
    })
    //.addEntry('admin', './assets/js/admin.js')
    .enableSassLoader()
;
const config = Encore.getWebpackConfig();

config.output = {
    ...config.output,
    library: ["Widgets", "[name]"],
    libraryTarget: 'var',
};

module.exports  = config;

