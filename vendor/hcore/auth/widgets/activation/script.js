if(!hCore) var hCore = {
    auth : {}
};
window.onload= function() {
    jQuery.extend(true, hCore.auth, {
        bExpire: 300000,
        bearer: "",
        expire: "",
        activation: function (url, selector, elem, ret_url) {
            if (!selector)
                selector = dialog_opened;

            var selectorID = "#" + selector;

            var domain = jQuery(selectorID).find("INPUT[name='domain']").val() || undefined;
            var email = jQuery(selectorID).find("INPUT[name='user-email']").val() || undefined;
            var verifyCode = jQuery(selectorID).find("INPUT[name='codice-conferma']").val() || undefined;
            var token = jQuery(selectorID).find("INPUT[name='csrf']").val() || "";

            var bearer = jQuery(selectorID).find("INPUT[name='bearer']").val() || "";
            if (bearer) {
                this.setBearer(bearer);
            }

            var data = {};
            var headers = {};

            if (!url)
                url = '/activation';

            if (ret_url)
                url = ff.urlAddParam(url, "ret_url", ret_url);

            if (this.expire <= new Date().getTime()) {
                this.bearer = "";
                this.expire = "";
            }

            if (this.bearer) {
                headers = {
                    "Bearer": this.bearer
                    , "domain": domain
                    , "csrf": token
                };
                data = {
                    "t": $.trim(verifyCode)
                    //, "key" : value
                    //, "scopes" : "activation"
                };
            } else {
                headers = {
                    "domain": domain
                    , "csrf": token
                };
                data = {
                    "username": email
                    //, "scopes" : "activation"
                };
            }

            $(selectorID + " .error-container").html('');
            $.ajax({
                url: url,
                headers: headers,
                method: 'POST',
                dataType: 'json',

                data: data,
                success: function (data) {
                    if (data.status === "0") {
                        hCore.auth.setBearer(data["t"]);

                        if (data["sender"]) {
                            $(selectorID + " .error-container").html('<div class="callout success">' + 'Check your ' + data["sender"] + '</div>');
                            jQuery(selectorID).find(".hide-code-string").removeClass("hide-code-string");
                        }
                        if (!data["t"]) {
                            window.location.href = "/user";
                        }
                    } else {
                        if (data.status != "404") {
                            hCore.auth.setBearer();
                        }

                        if ($(selectorID + " .error-container").length) {
                            $(selectorID + " .error-container").html('<div class="callout alert">' + data.error + '</div>');
                        }
                    }
                    $(elem).find(".disabled").css({'opacity': '', 'pointer-events': ''}).removeClass("disabled");
                }
            });

            return false;
        },
        "submitProcessKey": function (e, button) {
            if (null == e)
                e = window.event;
            if (e.keyCode == 13) {
                document.getElementById(button).focus();
                document.getElementById(button).click();
                return false;
            }
        },
        "getURLParameter": function (name) {
            return decodeURIComponent(
                (RegExp(name.replace(/\[/g, "\\[").replace(/\]/g, "\\]") + '=' + '(.+?)(&|$)').exec(location.search) || [, null])[1]
            );
        },
        "setBearer": function (t) {
            if (t) {
                this.bearer = t;
                this.expire = new Date().getTime() + this.bExpire;
            } else {
                this.bearer = "";
                this.expire = "";
            }
        }

    });
}