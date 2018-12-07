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

    );

    $config = array_replace_recursive($config_default, (array) $config);

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $response = Auth::write(null, null, array("scopes" => "activation", "email_path" => $config["email_path"]));

        if(isset($response["status"]) && $response["status"] === "0") {
            if(is_callable($config["callback"]["activation"])) {
                $response = array_replace($response, (array) call_user_func_array($config["callback"]["activation"], array($response)));
            }

            if($_REQUEST["redirect"]) {
                $response["redirect"]   = $_REQUEST["redirect"];
            }
        }
        Api::send($response);
    } else {


        $path = Auth::getDiskPath("tpl") . ($config["tpl_path"]
                ? $config["tpl_path"]
                : "/activation"
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

        if(isset($_REQUEST["email"]) && strlen($_REQUEST["email"])) {
            $response = Auth::request($_REQUEST["email"], array("scopes" => "activation"));

            $tpl->set_var("email_conferma", $_REQUEST["email"]);
            $tpl->set_var("email_class", "");

            $tpl->set_var("bearer_code", $response["t"]);
            $tpl->parse("SezBearerContainer", false);
        } else {
            $tpl->set_var("email_class", "hide-code-string");
        }

        $token = Auth::password();
        $tpl->set_var("csrf_token", $token);

        $tpl->set_var("activation_url", $config["api"]["activation"]);

        if($config["domain"]) {
            $tpl->parse("SezDomain", false);
        } else {
            $tpl->set_var("domain_name", $_SERVER["HTTP_HOST"]);
            $tpl->parse("SezDomainHidden", false);
        }

        if(isset($_REQUEST["box-type"]) && strlen($_REQUEST["box-type"])) {
            $tpl->set_var("box_type", $_REQUEST["box-type"]);
            $tpl->parse("SezBoxType", false);
        }

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
