ff.modules.auth = (function () {
    var social_window = undefined;
    var that = {
        social: {
            "requestLogin": function (title, url) {
                social_window = window.open(
                    url
                    , title
                    , "menubar=no, status=no, height=500, width= 500"
                );


            }
        },
        login: function (url, selector, elem, redirect) {
            ff.pluginLoad("hCore.auth.login", "/vendor/wolfgan/auth/widgets/login/script.js", function() {
                hCore.auth.login(url, selector, elem, redirect);
            });

            return false;
        },
        recover: function (url, selector, elem, redirect) {
            ff.pluginLoad("hCore.auth.recover", "/vendor/wolfgan/auth/widgets/recover/script.js", function() {
                hCore.auth.recover(url, selector, elem, redirect);
            });

            return false;
        },
        activation: function (url, selector, elem, redirect) {
            ff.pluginLoad("hCore.auth.activation", "/vendor/wolfgan/auth/widgets/activation/script.js", function() {
                hCore.auth.activation(url, selector, elem, redirect);
            });

            return false;
        },
        "submitProcessKey": function (e, button) {
            ff.pluginLoad("hCore.auth.login", "/vendor/wolfgan/auth/widgets/login/script.js", function() {
                hCore.auth.submitProcessKey(e, button);
            });

            return false;
        },
        submit: function(action, selector, elem, redirect) {
            switch(action) {
                case "login":
                    this.login(undefined, selector, elem, redirect);
                    break;
                case "recover":
                    this.recover(undefined, selector, elem, redirect);
                    break;
                case "activation":
                    this.activation(undefined, selector, elem, redirect);
                    break;
                default:
            }

        }

    };
    return that;
})();