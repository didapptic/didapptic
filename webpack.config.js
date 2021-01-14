const webpack = require("webpack");

module.exports = {
    entry: {
        aboutView: __dirname + "/js/AboutView/init.js",
        contactView: __dirname + "/js/ContactView/init.js",
        editAppView: __dirname + "/js/EditAppView/init.js",
        hintsView: __dirname + "/js/HintsView/init.js",
        loginView: __dirname + "/js/LoginView/init.js",
        mainView: __dirname + "/js/MainView/init.js",
        materialView: __dirname + "/js/MaterialView/init.js",
        newAppView: __dirname + "/js/NewAppView/init.js",
        newUserView: __dirname + "/js/NewUserView/init.js",
        partnerView: __dirname + "/js/PartnerView/init.js",
        passwordLostView: __dirname + "/js/PasswordLostView/init.js",
        profileView: __dirname + "/js/ProfileView/init.js",
        resetPasswordView: __dirname + "/js/ResetPasswordView/init.js",
        settingsView: __dirname + "/js/SettingsView/init.js",
    },
    output: {
        filename: './[name].bundle.js',
        path: __dirname + "/src/dist/js"
    },
    module: {
        rules: [{
            exclude: /node_modules/,
            loader: 'babel-loader',
        }]
    },
    resolve: {
        fallback: {
            "crypto": require.resolve("crypto-browserify"),
            "buffer": require.resolve("buffer/"),
            "util": require.resolve("util/"),
            "stream": require.resolve("stream-browserify")
        }
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery"
        })
    ]
};
