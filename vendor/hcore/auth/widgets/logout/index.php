<?php
/**
 *   VGallery: CMS based on FormsFramework
Copyright (C) 2004-2015 Alessandro Stucchi <wolfgan@gmail.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

 * @package VGallery
 * @subpackage core
 * @author Alessandro Stucchi <wolfgan@gmail.com>
 * @copyright Copyright (c) 2004, Alessandro Stucchi
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link https://github.com/wolfgan43/vgallery
 */

    $config_default = array(
        "api"                   => array(
            "login"             => Auth::API_PATH . "/login"
            , "logout"          => Auth::API_PATH . "/logout"
            , "recover"         => Auth::API_PATH . "/recover"
            , "registration"    => Auth::API_PATH . "/registrazione"
            , "activation"      => Auth::API_PATH . "/activation"
        )
    );

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $response = Auth::logout();

        Api::send($response);
    } else {
        $config = array_replace_recursive($config_default, (array) $config);

        $path = Auth::getDiskPath("tpl") . ($config["tpl_path"]
                ? $config["tpl_path"]
                : "/logout"
            );
        
        $html_name = "/index.html";
        $css_name = "/style.css";
        $script_name = "/script.js";

        $filename = (is_file($path . $html_name)
            ? $path . $html_name
            : __DIR__ . $html_name
        );
        $tpl =  new ffTemplate(dirname($filename));
        $tpl->load_file(basename($filename), "main");

        $tpl->set_var("logout_url", Auth::SITE_PATH . $config["api"]["logout"]);

        $anagraph = Auth::get();
        if(strlen($anagraph["name"])) {
            $nome = $anagraph["name"];
        } elseif(strlen($anagraph["person"]["name"]) || strlen($anagraph["person"]["surname"])) {
            $nome = $anagraph["person"]["name"] . " " . $anagraph["person"]["surname"];
        } else {
            $nome = $anagraph["user"]["username"];
        }
        $tpl->set_var("user_name", $nome);
        $tpl->set_var("user_email", $anagraph["email"]);
        $tpl->set_var("user_avatar", Auth::getUserAvatar($config["avatar"], $anagraph["avatar"]));

        $output = array(
            "html"  => $tpl->rpparse("main", false)
            , "css" => str_replace(AUTH::$disk_path, "", (is_file($path . $css_name)
                ? $path . $css_name
                : __DIR__ . $css_name
            ))
            , "js"  => str_replace(AUTH::$disk_path, "", (is_file($path . $script_name)
                ? $path . $script_name
                : __DIR__ . $script_name
            ))
        );

        if(Auth::isXHR()) {
            Api::send($output);
        }
    }

return $output;
