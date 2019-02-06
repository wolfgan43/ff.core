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

if(!is_file(FF_DISK_UPDIR . "/docs/example.jpg")) {
    @mkdir(FF_DISK_UPDIR . "/docs", 0777, true);
    @copy(__DIR__ . "/example.jpg", FF_DISK_UPDIR . "/docs/example.jpg");
}

    $cm->oPage->addContent(__DIR__ . "/media.html", "tabs", array(
    "vars" => array(
        "{site_path}" => FF_SITE_PATH
    )
    , "title" => "Cache/Media"
));
$cm->oPage->addContent(__DIR__ . "/media.html", "tabs", array(
    "vars" => array(
        "{site_path}" => FF_SITE_PATH
        , "/media" => "/static"
    )
    , "title" => "Static"
));
/*
$cm->oPage->addContent(__DIR__ . "/media.html", "tabs", array(
    "vars" => array(
        "{site_path}" => FF_SITE_PATH
        , "/media" => "/cm/showfiles.php"
    )
    , "title" => "Showfiles"
));*/


