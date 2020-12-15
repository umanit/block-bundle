const Encore = require('@symfony/webpack-encore');

Encore
  .setOutputPath('./src/Resources/public/')
  .setPublicPath('./')
  .setManifestKeyPrefix('bundles/umanitblock')

  .cleanupOutputBeforeBuild()
  .enableSassLoader()
  .enableSourceMaps(false)
  .enableVersioning(false)
  .disableSingleRuntimeChunk()
  .configureBabel(() => {
  }, {
    useBuiltIns: 'usage',
    corejs: 3,
  })

  .addEntry('sonata/panel', './assets/sonata/js/panel.js')
  .addEntry('sylius/panel', './assets/sylius/js/panel.js')
;

module.exports = Encore.getWebpackConfig();
