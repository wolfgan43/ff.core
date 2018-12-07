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

$config_default = array(
    "libs" => array(
        "handlebars" => array(
            "mime" => "text/x-handlebars-template"
            , "js" => "https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.11/handlebars.js"
            , "css" => null
        )
        , "default" => array(
            "mime" => "text/template"
            , "js" => null
            , "css" => null
        )
    )
    , "tpl" => "default"
);


if(Auth::isLogged()) {
    $config = array_replace_recursive($config_default, (array) $config);

    $path = Auth::getDiskPath("tpl") . ($config["tpl_path"]
            ? $config["tpl_path"]
            : "/notifier"
        );
    $html_name = "/index.html";
    $item_name = "/item.html";
    $css_name = "/style.css";
    $script_name = "/script.js";

    if(!is_file($file)) {
        $file = __DIR__ . $html_name;
    }

    $filename_item = (is_file($path . $item_name)
        ? $path . $item_name
        : __DIR__ . $item_name
    );

    $head = '<script id="notification-item" type="' . $config["libs"][$config["tpl"]]["mime"] . '" >' . file_get_contents($filename_item) . '</script>';

    $filename = (is_file($path . $html_name)
        ? $path . $html_name
        : __DIR__ . $html_name
    );

    $tpl = new ffTemplate(dirname($filename));
    $tpl->load_file(basename($filename), "main");
    $html = $tpl->rpparse("main", false);

    $css = ($config["libs"][$config["tpl"]]["css"]
            ? file_get_contents($config["libs"][$config["tpl"]]["css"])
            : ""
        )
        . file_get_contents(ffMedia::getFileOptimized(is_file($path . $css_name)
            ? $path . $css_name
            : __DIR__ . $css_name
        ));
    $js = ($config["libs"][$config["tpl"]]["js"]
            ? file_get_contents($config["libs"][$config["tpl"]]["js"])
            : ""
        )
        . file_get_contents(ffMedia::getFileOptimized(is_file($path . $script_name)
            ? $path . $script_name
            : __DIR__ . $script_name
        ));

    $output = array(
        "html"  => $head . $html
        , "css" => $css
        , "js"  => $js
    );


    if(Auth::isXHR()) {
        Api::send($output);
    }
}

return $output;
