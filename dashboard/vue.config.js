module.exports = {
    indexPath: 'index.php',
    css: {
        extract: {
            filename: '[name].css',
            chunkFilename: '[name].css',
        },
    },
    configureWebpack: {
        output: {
            filename: '[name].js',
            chunkFilename: '[name].js',
        }
    },
    chainWebpack: config => {
        config.module
            .rule('raw')
            .test(/\.ini$/)
            .use('raw-loader')
            .loader('raw-loader')
            .end()
    },
};