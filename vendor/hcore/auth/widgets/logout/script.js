if(!hCore) var hCore = {
    auth : {}
};
window.onload= function() {
    jQuery.extend(true, hCore.auth, {
        logout: function (url, selector) {
            var selectorID = (selector
                    ? "#" + selector
                    : document
            );

            if (!url)
                url = '/logout';

            $.ajax({
                url: url,
                headers: {},
                method: 'POST',
                dataType: 'json',
                data: {}
            }).done(function (data) {
                if (data.status === "0") {
                    window.location.href = "/";
                } else {
                    jQuery(selectorID).find(".disabled").css({
                        'opacity': '',
                        'pointer-events': ''
                    }).removeClass("disabled");
                    if (jQuery(selectorID + " .error-container").length) {
                        jQuery(selectorID + " .error-container").html('<div class="callout alert">' + data.error + '</div>');
                    }
                }
            })
                .fail(function (data) {
                    if (jQuery(selectorID + " .error-container").length) {
                        jQuery(selectorID + " .error-container").html('<div class="callout alert">' + data.error + '</div>');
                    }
                });

            return false;
        }
    });
}