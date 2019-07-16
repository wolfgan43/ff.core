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
spl_autoload_register(function ($class) {
    switch ($class) {
        case "ffPage_html":
            require("ff/ffPage/ffPage_html.php");
            break;
        case "ffGrid_html":
            require("ff/ffGrid/ffGrid_html.php");
            break;
        case "ffGrid_json":
            require("ff/ffGrid/ffGrid_json.php");
            break;
        case "ffGrid_xls":
            require("ff/ffGrid/ffGrid_xls.php");
            break;
        case "ffRecord_html":
            require("ff/ffRecord/ffRecord_html.php");
            break;
        case "ffRecord_dialog":
            require("ff/ffRecord/ffRecord_dialog.php");
            break;
        case "ffRecord_xhr":
            require("ff/ffRecord/ffRecord_xhr.php");
            break;
        case "ffPageNavigator_html":
            require("ff/ffPageNavigator/ffPageNavigator_html.php");
            break;
        case "ffDetails_html":
            require("ff/ffDetails/ffDetails_html.php");
            break;
        case "ffDetails_drag":
            require("ff/ffDetails/ffDetails_drag.php");
            break;
        case "ffDetails_sort":
            require("ff/ffDetails/ffDetails_sort.php");
            break;
        case "ffDetails_horiz":
            require("ff/ffDetails/ffDetails_horiz.php");
            break;
        case "ffButton_html":
            require("ff/ffButton/ffButton_html.php");
            break;
        case "ffField_html":
            require("ff/ffField/ffField_html.php");
            break;
        case "ffTheme":
            require("ffTheme.php");
            break;
        default:
    }
});


