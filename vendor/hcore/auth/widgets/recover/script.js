if(!hCore) var hCore = {
    auth : {}
};
window.onload= function() {
    jQuery.extend(true, hCore.auth, {
        bExpire: 300000,
        bearer: "",
        expire: "",
        recover: function (url, action, selector, elem) {
            var selectorID = (selector
                    ? "#" + selector
                    : document
            );

            var error = "";
            var domain = jQuery(selectorID).find("INPUT[name='domain']").val() || undefined;
            var email = jQuery(selectorID).find("INPUT[name='user-email']").val() || undefined;
            var token = jQuery(selectorID).find("INPUT[name='csrf']").val() || "";
            var verifyCode = jQuery(selectorID).find("INPUT[name='codice-conferma']").val() || undefined;
            var password = jQuery(selectorID).find("INPUT[name='password']").val() || "";
            var confirmPassword = jQuery(selectorID).find("INPUT[name='confirm-password']").val() || "";

            var bearer = jQuery(selectorID).find("INPUT[name='bearer']").val() || "";
            if (bearer) {
                this.setBearer(bearer);
            }

            var data = {};
            var headers = {};

            if (!action)
                action = (!ff.group ? "login" : "logout");

            if (!url)
                url = '/recover';

            if (this.expire <= new Date().getTime()) {
                this.bearer = "";
                this.expire = "";
            }

            if (this.bearer) {
                if (!password.length) {
                    error = 'Non è stata indicata una nuova password';
                }
                if (!error.length && !confirmPassword.length) {
                    error = 'Non è stato compilato il campo "conferma password"';
                }
                if (!error.length && confirmPassword != password) {
                    error = 'I campi "password" e "conferma password" non coincidono';
                }

                if (!error.length) {
                    headers = {
                        "Bearer": this.bearer
                        , "domain": domain
                        , "csrf": token
                    };
                    data = {
                        "t": $.trim(verifyCode)
                        , "key": password
                        , "scopes": "password"
                    };
                } else {
                    jQuery(selectorID + " .error-container").html('<div class="callout alert">' + error + '</div>');
                }
            } else {
                headers = {
                    "domain": domain
                    , "csrf": token
                };
                data = {
                    "username": email
                    , "scopes": "password"
                };
            }
            if (!jQuery.isEmptyObject(headers) && !jQuery.isEmptyObject(data)) {
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
                                $(selectorID + " .conferma-email-title").html("Modifica Password");
                                if (window.location.hostname == "pro.paginemediche.it") {
                                    $(selectorID + " .conferma-email-subtitle").html('<div style="font-size:14px">Controlla la tua casella di posta. Ti abbiamo appena inviato una mail con un codice che dovrai inserire nel campo sottostante per impostare una nuova password. Ricordati che la tua password è personale e che grazie ad essa è possibile accedere a informazioni sensibili. Tienila al sicuro.<br><br>Per rispondere ai requisiti di sicurezza la tua password deve:<ul style="list-style-type: square;"><li>essere di almeno 8 caratteri</li><li>contenere almeno una lettera</li><li>contenere almeno un numero</li></ul></div>');
                                } else {
                                    $(selectorID + " .conferma-email-subtitle").html('<div style="font-size:14px">Controlla la tua casella di posta. Ti abbiamo appena inviato una mail con un codice che dovrai inserire nel campo sottostante per impostare una nuova password. Ricordati che la tua password è personale e che grazie ad essa è possibile accedere ai tuoi dati salute nella tua Area Personale!<br><br>Per rispondere ai requisiti di sicurezza la tua password deve:<ul style="list-style-type: square;"><li>essere di almeno 8 caratteri</li><li>contenere almeno una lettera</li><li>contenere almeno un numero</li></ul></div>');
                                }
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
                            jQuery(elem).find(".disabled").css({
                                'opacity': '',
                                'pointer-events': ''
                            }).removeClass("disabled");
                            if (jQuery(selectorID + " .error-container").length) {
                                jQuery(selectorID + " .error-container").html('<div class="callout alert">' + data.error + '</div>');
                            }
                        }
                    }
                });
            }

            return false;
        },
        submitProcessKey: function (e, button) {
            if (null == e)
                e = window.event;
            if (e.keyCode === 13) {
                document.getElementById(button).focus();
                document.getElementById(button).click();
                return false;
            }
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