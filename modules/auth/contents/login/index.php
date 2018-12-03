<?php
/**
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

$config = array(
    "api"                   => array(
        "login"             => $cm->router->getRuleById("mod_auth_login")->reverse
        , "logout"          => "/logout"
        , "recover"         => $cm->router->getRuleById("mod_auth_recover")->reverse
        , "registration"    => $cm->router->getRuleById("mod_auth_registration")->reverse
        , "activation"      => $cm->router->getRuleById("mod_auth_activation")->reverse
    )
    , "callback"            => array(
        "login"             => null
        , "logout"          => null
        , "recover"         => null
        , "registration"    => null
        , "activation"      => null
    )
    , "title"               => array(
        "enable"            => false
        , "value"           => "login_title"
    )
    , "subtitle"               => array(
        "enable"            => false
        , "value"           => "login_subtitle"
    )
    , "domain"              => cm::env("MOD_SEC_MULTIDOMAIN")
    , "stay_connect"        => cm::env("MOD_SECURITY_SESSION_PERMANENT")
    , "referer"             => $_SERVER["HTTP_REFERER"]
    , "redirect"            => $_SERVER["HTTP_REFERER"]
    , "tpl_path"            => null
    , "registration"        => array(
        "enable"            => cm::env("MOD_SEC_REGISTRATION")
        , "path"            => $cm->router->getRuleById("mod_auth_registration")->reverse
    )
    , "recover"             => array(
        "username"          => array(
            "enable"        => cm::env("MOD_SEC_USERNAME_RECOVER_USERNAME")
            , "path"        => $cm->router->getRuleById("mod_auth_recover")->reverse . "/username"
        )
        , "password"        => array(
            "enable"        => cm::env("MOD_SEC_PASSWORD_RECOVER")
            , "path"        => $cm->router->getRuleById("mod_auth_recover")->reverse . "/password"
        )
    )
    , "social"              => array(
        "facebook"          => array(
            "enable"        => cm::env("MOD_SEC_SOCIAL_FACEBOOK")
            , "path"        => $cm->router->getRuleById("mod_auth_social")->reverse . "/facebook"
            , "icon"        => $cm->oPage->frameworkCSS->get("facebook", "icon")
            , "name"        => "Facebook"
            , "title"       => "mod_auth_social_facebook"
        )
        , "gplus"           => array(
            "enable"        => cm::env("MOD_SEC_SOCIAL_GPLUS")
            , "path"        => $cm->router->getRuleById("mod_auth_social")->reverse . "/gplus"
            , "icon"        => $cm->oPage->frameworkCSS->get("gplus", "icon")
            , "name"        => "GooglePlus"
            , "title"       => "mod_auth_social_gplus"
        )
        , "twitter"         => array(
            "enable"        => cm::env("MOD_SEC_SOCIAL_TWITTER")
            , "path"        => $cm->router->getRuleById("mod_auth_social")->reverse . "/twitter"
            , "icon"        => $cm->oPage->frameworkCSS->get("twitter", "icon")
            , "name"        => "Twitter"
            , "title"       => "mod_auth_social_twitter"
        )
        , "linkedin"        => array(
            "enable"        => cm::env("MOD_SEC_SOCIAL_LINKEDIN")
            , "path"        => $cm->router->getRuleById("mod_auth_social")->reverse . "/linkedin"
            , "icon"        => $cm->oPage->frameworkCSS->get("linkedin", "icon")
            , "name"        => "Linkedin"
            , "title"       => "mod_auth_social_linkedin"
        )
        , "dribble"         => array(
            "enable"        => cm::env("MOD_SEC_SOCIAL_DRIBBLE")
            , "path"        => $cm->router->getRuleById("mod_auth_social")->reverse . "/dribble"
            , "icon"        => $cm->oPage->frameworkCSS->get("dribble", "icon")
            , "name"        => "Dribble"
            , "title"       => "mod_auth_social_dribble"
        )
        , "ff"              => array(
            "enable"        => cm::env("MOD_SEC_SOCIAL_FF")
            , "path"        => $cm->router->getRuleById("mod_auth_social")->reverse . "/ff"
            , "icon"        => $cm->oPage->frameworkCSS->get("cube", "icon")
            , "name"        => "FormsFramework"
            , "title"       => "mod_auth_social_ff"
        )
    )
);

$res = $cm->modules["auth"]["events"]->doEvent("on_before_login", array($config));
$rc = end($res);
if (is_array($rc))
{
    $config = $rc;
}

if(Auth::islogged()) {
    $widget = Auth::widget("logout", $config, str_replace($cm->router->getRuleById("mod_auth_login")->reverse, "/logout", $cm->path_info));
}else {
    $widget = Auth::widget("login", $config);
}

if ($widget["js"]) {
    $cm->oPage->tplAddJs("ff.modules.auth.login", array(
        "path" => dirname($widget["js"])
        , "file" => basename($widget["js"])
    ));
}

if ($widget["css"]) {
    $cm->oPage->tplAddCss("ff.modules.auth.login", array(
        "path" => dirname($widget["css"])
        , "file" => basename($widget["css"])
    ));
}


$cm->oPage->addContent($widget["html"]);