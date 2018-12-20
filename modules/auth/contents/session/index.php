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
if(!Auth::check()) {
    $access_denied = ffTemplate::_get_word_by_code("mod_auth_login_required");
} else {
    $auth_settings = $cm->router->getMatchedRules("auth");

    if ($auth_settings) {
        $user = Auth::get("user");

        if ($auth_settings["profiles"] && array_search($user->acl_profile, explode(",", $auth_settings["profiles"])) === false) {
            $access_denied = ffTemplate::_get_word_by_code("mod_auth_profile_not_authorized");
        }

        if ($auth_settings["groups"] && array_search($user->acl_primary, explode(",", $auth_settings["groups"])) === false) {
            $access_denied = ffTemplate::_get_word_by_code("mod_auth_group_not_authorized");
        }

        if ($auth_settings["acl"] && $user->acl > $auth_settings["acl"]) {
            $access_denied = ffTemplate::_get_word_by_code("mod_auth_access_denied");
        }

    }
}

if($access_denied) {
    ffRedirect(Auth::SITE_PATH . "/login?error=" . $access_denied . "&redirect=" . Auth::SITE_PATH . $cm->path_info);
}

