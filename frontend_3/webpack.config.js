const {VueLoaderPlugin} = require('vue-loader');

module.exports = {
    mode: 'development',
    entry: './src/main.js', // Entry file for your Vue app
    output: {
        filename: 'bundle.js',
        path: __dirname + '/public/js', // Output directory
        publicPath: '/js/', // URL prefix for assets
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
            },
            {
                test: /\.css$/i,
                use: ["style-loader", "css-loader"],
            },
        ],
    },
    plugins: [new VueLoaderPlugin()],
    resolve: {
        alias: {
            vue$: 'vue/dist/vue.esm-bundler.js',
        },
        extensions: ['.js', '.vue'],
    },
};
