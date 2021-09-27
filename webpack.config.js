const path = require('path')

const postCSSPlugins = [
    require('postcss-import'),
    require("postcss-mixins"),
    require('postcss-simple-vars'),
    require('postcss-nested'),
    require('autoprefixer')
]

module.exports = {
    entry: './app/assets/scripts/App.js',
    output: {
        filename: 'bundled.js',
        path: path.resolve(__dirname, 'app'),
    },

    mode: 'development',
    watch: true, // "False" if devServer is used.
    module: { // By modul property we’ll tell webpack what to do if it runs into certain files
        rules: [
            {
                test: /\.css$/i,
                use: ['style-loader', 'css-loader?url=false', { loader: 'postcss-loader', options: { postcssOptions: { plugins: postCSSPlugins } } }]
                // Note: To not allow webpack to handle images we needed to change "css-loader" to "css-loader?url=false".
            }
        ]
    }
}