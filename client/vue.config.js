module.exports = {
    devServer: {
        proxy: {
            '^/api': {
                target: 'http://prelaunchbuilder.local',
                secure: true,
                pathRewrite: {
                    '^/api': '/api',
                },
            },
        },
    },
    configureWebpack: (config) => {
        config.devtool = 'source-map';
    },
};
