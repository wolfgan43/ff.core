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

//define("FF_ONLY_INIT", true);
require_once (dirname(__DIR__) . "/ff/main.php");

if(strpos($_SERVER["PATH_INFO"], FF_SITE_PATH . "/media/") === 0) {
    $site_path = FF_SITE_PATH . "/media";
    $path_info = substr($_SERVER["PATH_INFO"], strlen(FF_SITE_PATH . "/media"));
}

if(strpos($_SERVER["HTTP_HOST"], "media.") === 0) {
    $site_path = "";
    $path_info = $_SERVER["PATH_INFO"];
}

if($path_info) {
    $redirect = ffMedia::getInstance($path_info)->process();

    if ($redirect) {
        $status = ($redirect == $path_info
            ? 302
            : 301
        );
        header("Location:" . $site_path . $redirect, null, $status);
        exit;
    }
}

http_response_code(404);
exit;