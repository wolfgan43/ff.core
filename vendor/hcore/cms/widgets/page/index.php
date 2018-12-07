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
        "head" => array(
            "properties" => array(
                "lang" => FF_LOCALE
            )
            , "title" => ffTranslator::get_word_by_code("page_title")
            , "meta" => array(
                "content-type" => array(
                    "http-equiv" => "Content-Type"
                    , "content" => "text/html; charset=utf8"
                )
            )
            , "links" => array(

            )
        )
        , "body" => array(
            "properties" => null
            , "class" => null
            , "content" => ""
        )
        , "css" => array(

        )
        , "js" => array(

        )
    );

    $config = array_replace_recursive($config_default, (array) $config);

    $path = Auth::getDiskPath("tpl") . ($config["tpl_path"]
            ? $config["tpl_path"]
            : "/page"
        );
    $html_name = "/index.html";
    $css_name = "/style.css";
    $script_name = "/script.js";

    $filename = (is_file($path . $html_name)
        ? $path . $html_name
        : __DIR__ . $html_name
    );

    /**
     * Html Properties
     */
    $html_properties = array();
    if(is_array($config["head"]["properties"]) && count($config["head"]["properties"])) {
        foreach ($config["head"]["properties"] as $key => $value) {
            $html_properties[] = $key . '="' . $value . '"';
        }
    }
    $vars["{html_properties}"] = (count($html_properties) ? " " . implode(" ", $html_properties) : "");

    /**
     * Title
     */
    $vars["{title}"] = $config["head"]["title"];


    /**
     * head Tags
     */
    $html_tags = array();

        /**
         * head Meta Tags
         */
        if(is_array($config["head"]["meta"]) && count($config["head"]["meta"]))
        {
            foreach($config["head"]["meta"] AS $key => $attr)
            {
                $tag_properties = "";
                foreach($attr AS $attr_name => $attr_value)
                {
                    $tag_properties .= ' ' . $attr_name . '="' . $attr_value . '"';
                }
            }
            $html_tags[] = '<meta' . $tag_properties . ' />';
        }
        /**
         * head Meta Links
         */
        if(is_array($config["head"]["links"]) && count($config["head"]["links"]))
        {
            foreach($config["head"]["links"] AS $key => $attr)
            {
                $tag_properties = "";
                foreach($attr AS $attr_name => $attr_value)
                {
                    $tag_properties .= ' ' . $attr_name . '="' . $attr_value . '"';
                }
            }
            $html_tags[] = '<link' . $tag_properties . ' />';
        }

    /**
     * Body Properties
     */
    $body_properties = array();
    if(is_array($config["body"]["class"]) && count($config["body"]["class"])) {
        $config["body"]["properties"]["class"] = implode(" ", $config["body"]["class"]);
    }

    if (is_array($config["body"]["properties"]) && count($config["body"]["properties"]))
    {
        $properties = array();
        foreach ($config["body"]["properties"] as $key => $value)
        {
            $body_properties[] = $key . '="' . $value . '"';
        }
    }
    $vars["{body_properties}"] = (count($body_properties) ? implode(" ", $body_properties) : "");


    $vars["{content}"] = implode("", (array) $config["body"]["content"]);

    /**
     * CSS
     */
    if(is_array($config["css"]) && count($config["css"])) {
        foreach($config["css"] AS $config_css) {
            if(Util::isUrl($config_css)) {
                $css[] = file_get_contents($config_css);
            } else {
                $css[] = $config_css;
            }
        }
    } elseif($config["css"]) {
        $css[] = $config["css"];
    }

    /**
     * JS
     */
    if(is_array($config["js"]) && count($config["js"])) {
        foreach($config["js"] AS $config_js) {
            if(Util::isUrl($config_js)) {
                $js[] = file_get_contents($config_js);
            } else {
                $js[] = $config_js;
            }
        }
    } elseif($config["js"]) {
        $js[] = $config["js"];
    }

    $css[] = file_get_contents(is_file($path . $css_name)
        ? $path . $css_name
        : __DIR__ . $css_name
    );
    $js[] = file_get_contents(is_file($path . $script_name)
        ? $path . $script_name
        : __DIR__ . $script_name
    );

    $html_tags[] = '<style>' . implode("", $css) . '</style>';
    $html_tags[] = '<script>' . implode("", $js) . '</script>';

    $vars["{tags}"] = implode("", $html_tags);

    $output = str_replace(array_keys($vars), array_values($vars), file_get_contents($filename));

    if(Auth::isXHR()) {
        Api::send($output);
    }

    return $output;

