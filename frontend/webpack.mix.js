const mix = require("laravel-mix");
const glob = require("glob");
const ESLintPlugin = require("eslint-webpack-plugin");
const StyleLintPlugin = require("stylelint-webpack-plugin");

const webPath = "build";
mix.setPublicPath(webPath);

mix.js("src/js/common.js", `${webPath}/js/common.js`);

mix
    .sass("src/scss/styles.scss", `${webPath}/css/styles.css`)
    .options({
        processCssUrls: false
    })
    .sourceMaps(true, "inline-source-map");

mix.copyDirectory("src/img", `${webPath}/img`);
mix.webpackConfig({
    plugins: [new ESLintPlugin(), new StyleLintPlugin()]
});
