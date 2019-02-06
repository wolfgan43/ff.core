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

define("FF_ONLY_INIT", true);
require_once (dirname(__DIR__) . "/ff/main.php");

if(strpos($_SERVER["PATH_INFO"], FF_SITE_PATH . "/media/") === 0) {
    $site_path = FF_SITE_PATH . "/media";
}
if(strpos($_SERVER["PATH_INFO"], FF_SITE_PATH . "/static/") === 0) {
    $site_path = FF_SITE_PATH . "/static";
}
if(strpos($_SERVER["PATH_INFO"],  "/media/") === 0) {
    $site_path = "/media";
}
if(strpos($_SERVER["PATH_INFO"], "/static/") === 0) {
    $site_path = "/static";
}
if(strpos($_SERVER["HTTP_HOST"], "media.") === 0) {
    $site_path = "";
}
if(strpos($_SERVER["HTTP_HOST"], "static.") === 0) {
    $site_path = "";
}

$path_info = ($site_path
    ? substr($_SERVER["PATH_INFO"], strlen($site_path))
    : $_SERVER["PATH_INFO"]
);

ffMedia::getInstance($path_info)->render();
exit;



