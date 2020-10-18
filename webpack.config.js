const webpack = require("webpack");

module.exports = {
    entry: {
        contactView: __dirname + "/js/ContactView/init.js",
        editAppView: __dirname + "/js/EditAppView/init.js",
        loginView: __dirname + "/js/LoginView/init.js",
        mainView: __dirname + "/js/MainView/init.js",
        materialView: __dirname + "/js/MaterialView/init.js",
        newAppView: __dirname + "/js/NewAppView/init.js",
        newUserView: __dirname + "/js/NewUserView/init.js",
        partnerView: __dirname + "/js/PartnerView/init.js",
        passwordLostView: __dirname + "/js/PasswordLostView/init.js",
        resetPasswordView: __dirname + "/js/ResetPasswordView/init.js",
        profileView: __dirname + "/js/ProfileView/init.js"
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
    plugins: [
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery"
        })
    ]
};
