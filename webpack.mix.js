// webpack.mix.js
const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .postCss('resources/css/app.css', 'public/css', [
       require('postcss-import'),
       require('tailwindcss'),
   ]);

// Copiar arquivos JS dos relatórios
mix.copyDirectory('public/js/relatorios', 'public/js/relatorios');

// Se preferir minificar
if (mix.inProduction()) {
    mix.minify(['public/js/relatorios/base.js']);
    mix.minify(['public/js/relatorios/admin-tabela.js']);
    mix.minify(['public/js/relatorios/user-cards.js']);
}