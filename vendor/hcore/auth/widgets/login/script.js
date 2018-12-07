/*
 * VGallery: CMS based on FormsFramework
 * Copyright (C) 2004-2015 Alessandro Stucchi <wolfgan@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @package VGallery
 *  @subpackage core
 *  @author Alessandro Stucchi <wolfgan@gmail.com>
 *  @copyright Copyright (c) 2004, Alessandro Stucchi
 *  @license http://opensource.org/licenses/gpl-3.0.html
 *  @link https://github.com/wolfgan43/vgallery
 */

if(!hCore) var hCore = {
    auth : {}
};
window.onload= function() {
    jQuery.extend(true, hCore.auth, {
        login: function (url, selector, redirect) {
            var selectorID = (selector
                    ? "#" + selector
                    : document
            );

            var domain = jQuery(selectorID).find("INPUT[name='domain']").val() || window.location.host;
            var username = jQuery(selectorID).find("INPUT[name='username']").val() || undefined;
            var password = jQuery(selectorID).find("INPUT[name='password']").val() || undefined;
            var token = jQuery(selectorID).find("INPUT[name='csrf']").val() || "";
            var stayConnect = jQuery(selectorID).find("INPUT[name='stayconnected-new']").is(':checked') || false;
            var redirect = redirect || ff.getURLParameter("redirect") || "/";
            if (redirect == 'null')
                redirect = "/";

            if (!url)
                url = '/login';

            if (!jQuery(selectorID + " .error-container").length) {
                jQuery(selectorID).prepend('<div class="error-container" />');
            }
            jQuery(selectorID + " .error-container").html("");

            $.ajax({
                url: url,
                headers: {
                    "domain": domain
                    , "csrf": token
                    , "refresh": stayConnect
                },
                method: 'POST',
                dataType: 'json',
                data: {
                    "username": username
                    , "password": password
                    , "redirect": redirect
                }
            })
                .done(function (data) {
                    if (data.status == "0") {
                        if (data.welcome) {
                            if (data.welcome.css.length) {
                                var style = document.createElement("style");
                                style.innerHTML = data.welcome.css;
                                document.head.appendChild(style);
                            }
                            if (data.welcome.js.length) {
                                var script = document.createElement("script");
                                script.innerHTML = data.welcome.js;
                                document.head.appendChild(script);
                            }
                            if (data.welcome.html.length) {
                                window.location.href = data.redirect

                                jQuery(".login-page-doctor.doctor-action").remove();
                                jQuery(selectorID + " .login-inner-container").html(data.welcome.html);
                            }
                        } else {
                            window.location.href = redirect;
                        }
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
                    jQuery(selectorID).find(".disabled").css({
                        'opacity': '',
                        'pointer-events': ''
                    }).removeClass("disabled");
                    if (jQuery(selectorID + " .error-container").length) {
                        jQuery(selectorID + " .error-container").html('<div class="callout alert">' + 'Service Temporary not Available. Try Later.' + '</div>');
                    }
                });

            return false;
        },
        social: function (title, url) {
            var social_window = undefined;
            social_window = window.open(
                url
                , title
                , "menubar=no, status=no, height=500, width= 500"
            );
        },
        submitProcessKey: function (e, button) {
            if (null == e)
                e = window.event;
            if (e.keyCode === 13) {
                document.getElementById(button).focus();
                document.getElementById(button).click();
                return false;
            }
        }
    });
}