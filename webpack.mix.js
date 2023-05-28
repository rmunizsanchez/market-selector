let mix = require('laravel-mix')

mix
  .setPublicPath('dist')
  .js('resources/js/MarketSelector.js', 'js')
  .sass('resources/sass/MarketSelector.scss', 'css')
