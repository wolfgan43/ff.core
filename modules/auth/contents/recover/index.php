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
        , "logout"          => $cm->router->getRuleById("mod_auth_login")->reverse
        , "recover"         => $cm->router->getRuleById("mod_auth_recover")->reverse
        , "registration"    => $cm->router->getRuleById("mod_auth_registration")->reverse
        , "activation"      => $cm->router->getRuleById("mod_auth_activation")->reverse
    )
    , "domain"              => cm::env("MOD_AUTH_MULTIDOMAIN")
    , "stay_connect"        => cm::env("MOD_AUTH_SESSION_PERMANENT")
    , "referer"             => $_SERVER["HTTP_REFERER"]
    , "ret_url"             => $_SERVER["HTTP_REFERER"]
    , "registration"        => array(
        "enable"            => cm::env("MOD_AUTH_REGISTRATION")
        , "path"            => $cm->router->getRuleById("mod_auth_registration")->reverse
    )
);

$cm->modules["auth"]["events"]->doEvent("on_before_login", array($config));

$widget = Auth::widget("recover", $config);

if($widget["js"]) {
    $cm->oPage->tplAddJs("ff.modules.auth.recover", $widget["js"]);
}
if($widget["css"]) {
    $cm->oPage->tplAddCss("ff.modules.auth.recover", $widget["css"]);
}

$cm->oPage->addContent($widget["html"]);