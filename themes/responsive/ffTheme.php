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

if (!defined("FF_THEME_RESTRICTED_RANDOMIZE_COMP_ID"))		define("FF_THEME_RESTRICTED_RANDOMIZE_COMP_ID", false);
if (!defined("FF_THEME_DIR"))		                        define("FF_THEME_DIR", FF_BASE_PATH . "/themes");
if (!defined("FF_THEME_DISK_PATH"))		                define("FF_THEME_DIR", __TOP_DIR__ . "/themes");


class ffTheme {
    const TYPE                                  = "html";
    const RANDOMIZE_COMP_ID                     = FF_THEME_RESTRICTED_RANDOMIZE_COMP_ID;
    const THEME_NAME                            = "responsive";
    const THEME_DIR                             = FF_THEME_DIR;
    const THEME_DISK_PATH                       = FF_THEME_DISK_PATH;
    const FRAMEWORK_CSS                         = "bootstrap4";
    const FONT_ICON                             = "fontawesome";
    const RESOURCES                             = array(
                                                    ffTheme::THEME_DISK_PATH . "/" . ffTheme::THEME_NAME    => array(
                                                        "filter"    => array("css", "js", "html", "jpg", "svg", "png")
                                                        , "rules" => array(
                                                            "/layouts/"             => "layouts"
                                                            , "/common/"            => "common"
                                                            , "/contents/"          => "components"
                                                            , "/widgets/"           => "widgets"
                                                            , "/css/"               => "css"
                                                            , "/javascript/"        => "js"
                                                            , "/images/"            => "images"
                                                            , "/fonts/"             => "fonts"
                                                            , "/ff/"                => "components"
                                                        )
                                                    )
                                                );
    const LIBRARIES                             = array(
                                                    "ff" => array(
                                                        "default" => "latest"
                                                    , "latest" => array(
                                                            "path" => "/themes/library/ff"
                                                        , "file" => "ff.js"
                                                        , "index" => 100
                                                        //, "async" => false
                                                        , "css_defs" => array(
                                                                "core" => array(
                                                                    "path" => "/themes/responsive/css"
                                                                , "file" => "ff.css"
                                                                , "priority" => cm::LAYOUT_PRIORITY_HIGH
                                                                , "index" => 150
                                                                , "css_loads" => array(
                                                                        ".skin" => array(
                                                                            "path" => "/themes/responsive/css"
                                                                        , "file" => "ff-skin.css"
                                                                        )
                                                                    )
                                                                )
                                                            )
                                                        , "js_deps" => array(
                                                                //"jquery" => null
                                                            )
                                                        , "js_loads" => array(
                                                               // "ff.ffEvent" => null
                                                               // , "ff.ffEvents" => null
                                                            )
                                                        , "js_defs" => array(
                                                              /*  "ffEvent" => array(
                                                                    "path" => "/themes/library/ff"
                                                                , "file" => "ffEvent.js"
                                                                , "index" => 100
                                                                //, "async" => false
                                                                )
                                                            , "ffEvents" => array(
                                                                    "path" => "/themes/library/ff"
                                                                , "file" => "ffEvents.js"
                                                                , "index" => 100
                                                                //, "async" => false
                                                                )
                                                            ,*/ "history" => array(
                                                                    "path" => "/themes/library/ff"
                                                                , "file" => "history.js"
                                                                , "index" => 100
                                                                )
                                                            , "ajax" => array(
                                                                    "path" => "/themes/library/ff"
                                                                , "file" => "ajax.js"
                                                                , "index" => 100
                                                                , "js_loads" => array(
                                                                    )
                                                                )
                                                            , "ffPage" => array(
                                                                    "path" => "/themes/responsive/ff/ffPage"
                                                                , "file" => "ffPage.js"
                                                                , "index" => 100
                                                                )
                                                            )
                                                        )
                                                    )
                                                , "jquery" => array(
                                                    "default" => "latest"
                                                    , "latest" => array(
                                                            "path" => "https://code.jquery.com"
                                                        , "file" => "jquery-1.11.2.min.js"
                                                        , "index" => 200
                                                        , "async" => false
                                                        , "js_defs" => array(
                                                                "plugins" => array(
                                                                    "empty" => true
                                                                )
                                                            )
                                                        )
                                                    )
                                                , "jquery-ui" => array(
                                                        "default" => "1.11.3"
                                                    , "1.11.3" => array(
                                                            "path" => "/themes/library/jquery-ui"
                                                        , "file" => null
                                                        , "index" => 200
                                                        , "js_deps" => array(
                                                                "jquery" => null
                                                            )
                                                        , "js_defs" => array(
                                                            )
                                                        , "css_loads" => array(
                                                                "jquery-ui.core" => null,
                                                                "ff-jqueryui" => array(
                                                                    "path" => "/themes/responsive/css"
                                                                , "file" => "ff-jqueryui.css"
                                                                )
                                                            )
                                                        , "css_defs" => array(
                                                                "accordion" => array(
                                                                    "file" => "base/accordion.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "autocomplete" => array(
                                                                    "file" => "base/autocomplete.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "button" => array(
                                                                    "file" => "base/button.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "core" => array(
                                                                    "file" => "base/core.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "datepicker" => array(
                                                                    "file" => "base/datepicker.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "dialog" => array(
                                                                    "file" => "base/dialog.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "draggable" => array(
                                                                    "file" => "base/draggable.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "menu" => array(
                                                                    "file" => "base/menu.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "progressbar" => array(
                                                                    "file" => "base/progressbar.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "resizable" => array(
                                                                    "file" => "base/resizable.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "selectable" => array(
                                                                    "file" => "base/selectable.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "selectmenu" => array(
                                                                    "file" => "base/selectmenu.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "slider" => array(
                                                                    "file" => "base/slider.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "sortable" => array(
                                                                    "file" => "base/sortable.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "spinner" => array(
                                                                    "file" => "base/spinner.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "tabs" => array(
                                                                    "file" => "base/tabs.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            , "tooltip" => array(
                                                                    "file" => "base/tooltip.css"
                                                                , "path" => "/themes/library/jquery-ui.themes"
                                                                , "index" => 200
                                                                )
                                                            )
                                                        )
                                                    )
                                                , "fonticons" => array(
                                                        "default" => "latest"
                                                    , "latest" => array(
                                                            "empty" => true
                                                        , "css_defs" => array(
                                                                "fontawesome" => array(
                                                                    "path" => "https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css"
                                                                , "file" => "font-awesome.min.css"
                                                                , "index" => 175
                                                                , "css_loads" => array(
                                                                        ".ff" => array(
                                                                            "path" => FF_THEME_DIR . "/" . FF_MAIN_THEME . "/css"
                                                                        , "file" => "ff-fontawesome.css"
                                                                        )
                                                                    )
                                                                )
                                                            , "glyphicons" => array(
                                                                    "path" => "/themes/responsive/css"
                                                                , "file" => "bootstrap-glyphicons.min.css"
                                                                , "index" => 175

                                                                )
                                                            )
                                                        )
                                                    )
                                                , "bootstrap" => array(
                                                        "default" => "latest"
                                                    , "latest" => array(
                                                        "empty" => true
                                                        , "js_defs" => array(
                                                                "core" => array(
                                                                    "path" => "https://stackpath.bootstrapcdn.com/bootstrap/3.3.6/js"
                                                                    , "file" => "bootstrap.min.js"
                                                                    , "index" => 150
                                                                    , "js_deps" => array(
                                                                        "jquery" => null
                                                                    )
                                                                )
                                                            )
                                                        , "css_defs" => array(
                                                                "core" => array(
                                                                    "path" => "https://stackpath.bootstrapcdn.com/bootstrap/3.3.6/css"
                                                                    , "file" => "bootstrap.min.css"
                                                                    , "index" => 150
                                                                    , "js_loads" => array(
                                                                        "bootstrap.core" => null
                                                                    )
                                                                , "css_deps" => array(
                                                                        //"fonticons.fontawesome" => null
                                                                    )
                                                                , "css_loads" => array(
                                                                        ".ff" => array(
                                                                            "path" => FF_THEME_DIR . "/" . FF_MAIN_THEME . "/css"
                                                                        , "file" => "ff-bootstrap.css"
                                                                        )
                                                                    )
                                                                )
                                                            )
                                                        )
                                                    )
                                                , "bootstrap4" => array(
                                                        "default" => "latest"
                                                    , "latest" => array(
                                                        "empty" => true
                                                        , "js_defs" => array(
                                                                "core" => array(
                                                                    "path" => "https://stackpath.bootstrapcdn.com/bootstrap/3.3.6/js"
                                                                    , "file" => "bootstrap.min.js"
                                                                    , "index" => 150
                                                                    , "js_deps" => array(
                                                                        "jquery" => null
                                                                    )
                                                                )
                                                            )
                                                        , "css_defs" => array(
                                                                "core" => array(
                                                                    "path" => "https://stackpath.bootstrapcdn.com/bootstrap/3.3.6/css"
                                                                    , "file" => "bootstrap.min.css"
                                                                    , "index" => 150
                                                                    , "js_loads" => array(
                                                                        "bootstrap.core" => null
                                                                    )
                                                                , "css_deps" => array(
                                                                        //"fonticons.fontawesome" => null
                                                                    )
                                                                , "css_loads" => array(
                                                                        ".ff" => array(
                                                                            "path" => FF_THEME_DIR . "/" . FF_MAIN_THEME . "/css"
                                                                        , "file" => "ff-bootstrap.css"
                                                                        )
                                                                    )
                                                                )
                                                            )
                                                        )
                                                    )
                                                , "foundation" => array(
                                                        "default" => "latest"
                                                    , "latest" => array(
                                                            "empty" => true
                                                        , "js_defs" => array(
                                                                "core" => array(
                                                                    "path" => "https://cdn.jsdelivr.net/npm/foundation-sites@6.4.3/dist/js"
                                                                , "file" => "foundation.min.js"
                                                                , "index" => 150
                                                                , "js_deps" => array(
                                                                        "jquery" => null
                                                                    )
                                                                , "js_loads" => array(
                                                                        ".init" => array(
                                                                            "embed" => "
                                                                jQuery(function() {
                                                                    jQuery(document).foundation();
                                                                });
                                                            "
                                                                        )
                                                                    )
                                                                )
                                                            )
                                                        , "css_defs" => array(
                                                                "core" => array(
                                                                    "path" => "https://cdn.jsdelivr.net/npm/foundation-sites@6.4.3/dist/css"
                                                                , "file" => "foundation.min.css"
                                                                , "index" => 150
                                                                , "js_loads" => array(
                                                                        "foundation.core" => null
                                                                    )
                                                                , "css_deps" => array(
                                                                        //"fonticons.fontawesome" => null
                                                                    )
                                                                , "css_loads" => array(
                                                                        ".ff" =>  array(
                                                                            "path" => FF_THEME_DIR . "/" . FF_MAIN_THEME . "/css"
                                                                        , "file" => "ff-foundation.css"
                                                                        )
                                                                    )
                                                                )
                                                            )
                                                        )
                                                    )
                                                , "google" => array(
                                                        "default" => "latest"
                                                    , "latest" => array(
                                                            "empty" => true
                                                        , "js_defs" => array(
                                                                "adsense" => array(
                                                                    "path" => "//pagead2.googlesyndication.com/pagead/js"
                                                                , "file" => "adsbygoogle.js"
                                                                , "priority" => cm::LAYOUT_PRIORITY_LOW
                                                                , "js_loads" => array(
                                                                        ".push" => array(
                                                                            "embed" => '
                                                                                jQuery(function() {
                                                                                    var adsbygoogle = window.adsbygoogle || [];
                                                                                    jQuery("ins.adsbygoogle").each(function(){
                                                                                        if (jQuery(this).attr("data-adsbygoogle-status") !== \"done\") {
                                                                                            adsbygoogle.push({});
                                                                                        }
                                                                                    });
                                                                                });'
                                                                        )
                                                                    )
                                                                )
                                                            , "jsapi" => array(
                                                                    "empty" => true
                                                                , "js_defs" => array(
                                                                        "async" => array(
                                                                            "path" => "https://www.google.com"
                                                                        , "file" => "jsapi?callback=gloadinitcall"
                                                                        , "priority" => cm::LAYOUT_PRIORITY_LOW
                                                                        , "js_deps" => array(
                                                                                ".initcall" => array(
                                                                                    "embed" => "
                                                                            var gloadinit = [];
                                                                            function gloadinitcall(callback) {
                                                                                if (callback === undefined) {
                                                                                    gloadinit.forEach(function(entry) {
                                                                                        entry();
                                                                                    });
                                                                                    gloadinit = false;
                                                                                } else {
                                                                                    if (gloadinit === false)
                                                                                        callback();
                                                                                    else
                                                                                        gloadinit.push(callback);
                                                                                }
                                                                            }
                                                                        "
                                                                                , "exclude_compact" => true
                                                                                )
                                                                            )
                                                                        )
                                                                    , "sync" => array(
                                                                            "path" => "https://www.google.com"
                                                                        , "file" => "jsapi"
                                                                        , "priority" => cm::LAYOUT_PRIORITY_LOW
                                                                        , "js_deps" => array(
                                                                                ".initcall" => array(
                                                                                    "embed" => "
                                                                            var gloadinit = [];
                                                                            function gloadinitcall(callback) {
                                                                                jQuery(window).ready(function(){callback();});
                                                                            }
                                                                        "
                                                                                )
                                                                            )
                                                                        )
                                                                    )
                                                                )
                                                            , "maps" => array(
                                                                    "empty" => true
                                                                , "js_defs" => array(
                                                                        "async" => array(
                                                                            "path" => "https://maps.googleapis.com/maps/api"
                                                                        , "file" => "js?libraries=places&callback=gmapsinitcall"
                                                                        , "priority" => cm::LAYOUT_PRIORITY_LOW
                                                                        , "js_deps" => array(
                                                                                ".initcall" => array(
                                                                                    "embed" => "
                                                                            var gmapsinit = [];
                                                                            function gmapsinitcall(callback) {
                                                                                if (callback === undefined) {
                                                                                    gmapsinit.forEach(function(entry) {
                                                                                        entry();
                                                                                    });
                                                                                    gmapsinit = false;
                                                                                } else {
                                                                                    if (gmapsinit === false)
                                                                                        callback();
                                                                                    else
                                                                                        gmapsinit.push(callback);
                                                                                }
                                                                            }
                                                                        "
                                                                                , "exclude_compact" => true
                                                                                )
                                                                            )
                                                                        )
                                                                    , "sync" => array(
                                                                            "path" => "https://maps.googleapis.com/maps/api"
                                                                        , "file" => "js?libraries=places"
                                                                        , "priority" => cm::LAYOUT_PRIORITY_LOW
                                                                        , "js_deps" => array(
                                                                                ".initcall" => array(
                                                                                    "embed" => "
                                                                            var gmapsinit = [];
                                                                            function gmapsinitcall(callback) {
                                                                                jQuery(window).ready(function(){callback();});
                                                                            }
                                                                        "
                                                                                )
                                                                            )
                                                                        )
                                                                    , "markerclusterer" => array(
                                                                            "path" => FF_THEME_DIR . "/library/plugins/gmap3.markerclusterer",
                                                                            "file" => "markerclusterer.js"
                                                                        )
                                                                    )
                                                                )
                                                            )
                                                        )
                                                    )
                                                /*, "facebook" => array(
                                                        "default" => "latest",
                                                        "latest" => array(
                                                            "path" => "http://connect.facebook.net/" . strtolower(substr(FF_LOCALE, 0, 2)) . "_" . strtoupper(substr(FF_LOCALE, 0, 2))
                                                        , "file" => "all.js"
                                                        )
                                                    )*/
                                                , "library" => array(
                                                        "default" => "latest",
                                                        "latest" => array(
                                                            "empty" => true
                                                        )
                                                    )
                                                );

    protected $js                               = array(
                                                    "jquery"                => null
                                                    , "jquery.nicescroll"   => "https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.3/jquery.nicescroll.min.js"
                                                    , "ff.ffPage"           => null
                                                    , "app"                 => null
                                                );
    protected $css                              = array(
                                                    "[FRAMEWORKCSS].core"   => null
                                                    , "fonticons.[FONTICON]"=> null
                                                    , "app"                 => null
                                                    , "icons"               => null
                                                );
    /**
     * @var $oPage ffPage_html
     */
    private $oPage                              = null;
    protected $resources                        = null;
    protected $excludeDirname                   = null;
    protected $framework_css                    = null;
    protected $font_icon                        = null;

    protected $buttons                          = array();
    protected $components                       = array();

    protected $ffDefaults                       = array(
                                                    "ffPage" => array(
                                                        "favicons" => array(
                                                            "favicon" => array(
                                                                "rel"       => "shortcut icon"
                                                                , "sizes"   => "16x16"
                                                                , "href"    => null
                                                            )
                                                            , "apple-touch-icon-57x57" => array(
                                                                "rel"       => "apple-touch-icon"
                                                                , "sizes"   => "57x57"
                                                                , "href"    => null
                                                            )
                                                            , "apple-touch-icon-60x60" => array(
                                                                "rel"       => "apple-touch-icon"
                                                                , "sizes"   => "60x60"
                                                                , "href"    => null
                                                            )
                                                            , "apple-touch-icon-72x72" => array(
                                                                "rel"       => "apple-touch-icon"
                                                                , "sizes"   => "72x72"
                                                                , "href"    => null
                                                            )
                                                            , "apple-touch-icon-76x76" => array(
                                                                "rel"       => "apple-touch-icon"
                                                                , "sizes"   => "76x76"
                                                                , "href"    => null
                                                            )
                                                            , "apple-touch-icon-114x114" => array(
                                                                "rel"       => "apple-touch-icon"
                                                                , "sizes"   => "114x114"
                                                                , "href"    => null
                                                            )
                                                            , "apple-touch-icon-120x120" => array(
                                                                "rel"       => "apple-touch-icon"
                                                                , "sizes"   => "120x120"
                                                                , "href"    => null
                                                            )
                                                            , "apple-touch-icon-144x144" => array(
                                                                "rel"       => "apple-touch-icon"
                                                                , "sizes"   => "144x144"
                                                                , "href"    => null
                                                            )
                                                            , "apple-touch-icon-152x152" => array(
                                                                "rel"       => "apple-touch-icon"
                                                                , "sizes"   => "152x152"
                                                                , "href"    => null
                                                            )
                                                            , "apple-touch-icon-180x180" => array(
                                                                "rel"       => "apple-touch-icon"
                                                                , "sizes"   => "180x180"
                                                                , "href"    => null
                                                            )
                                                            , "icon-192x192" => array(
                                                                "rel"       => "icon"
                                                                , "type"    => "image/png"
                                                                , "sizes"   => "192x192"
                                                                , "href"    => null
                                                            )
                                                            , "icon-32x32" => array(
                                                                "rel"       => "icon"
                                                                , "type"    => "image/png"
                                                                , "sizes"   => "32x32"
                                                                , "href"    => null
                                                            )
                                                            , "icon-96x96" => array(
                                                                "rel"       => "icon"
                                                                , "type"    => "image/png"
                                                                , "sizes"   => "96x96"
                                                                , "href"    => null
                                                            )
                                                            , "icon-16x16" => array(
                                                                "rel"       => "icon"
                                                                , "type"    => "image/png"
                                                                , "sizes"   => "16x16"
                                                                , "href"    => null
                                                            )
                                                        )
                                                        , "default_js" => array(
                                                            "jquery" => null
                                                            , "ff.page" => null
                                                        )
                                                        , "default_css" => array(
                                                            "ff.core" => null
                                                        )
                                                    )
                                                );

    public static function factory($params = null, $resources = null) {
        if ($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest")
        {
            if (!isset($_REQUEST["XHR_CTX_ID"]))
            {
                $params["page"] = "XHR";
            }

            if (isset($_REQUEST["XHR_THEME"]))
            {
                $params["theme"] = $_REQUEST["XHR_THEME"];
            }
        }

        if($params["theme"] && $params["theme"] != self::THEME_NAME) {
            $class_name = $params["theme"] . "Theme";
            $theme_path = __PRJ_DIR__ . FF_THEME_DIR . "/" . $params["theme"] . "/" . $class_name . "." . FF_PHP_EXT;
        }

        if(is_file($theme_path)) {
            cm::getInstance()->loadConfig(__PRJ_DIR__ . FF_THEME_DIR . "/" . $params["theme"] . "/conf/config.xml");

            require_once $theme_path;

            $theme = new $class_name($params, $resources);
        } else {
            $params["theme"] = null;
            $theme = new ffTheme($params, $resources);
        }

        return $theme->getPage();
    }

    public static function ffGrid_set_events(ffGrid_base $grid)
    {
        $grid->addEvent("onDialog", "ffTheme::ffComponent_onDialog", ffEvent::PRIORITY_HIGH);
    }
    public static function ffRecord_set_events(ffRecord_base $record)
    {
        $record->addEvent("onDialog", "ffTheme::ffComponent_onDialog", ffEvent::PRIORITY_HIGH);
    }
    public static function ffDetails_set_events(ffDetails_base $details)
    {
        $details->addEvent("onDialog", "ffTheme::ffComponent_onDialog", ffEvent::PRIORITY_HIGH);
    }
    public static function ffComponent_onDialog($oComponent, $returnurl, $type, $title, $message, $cancelurl, $confirmurl, $dialog_path)
    {
        return ffDialog($returnurl, $type, $title, $message, $cancelurl, $confirmurl, $oComponent->parent[0]->site_path . $oComponent->parent[0]->page_path . "/dialog?" . $oComponent->parent[0]->get_globals());
    }

    public static function ffRecord_set_events_dialog(ffRecord_base $record)
    {
        $record->addEvent("on_done_action", "ffTheme::ffRecord_dialog_on_done_action_prepare_results", ffEvent::PRIORITY_HIGH);
        $record->addEvent("on_done_action", "ffTheme::ffRecord_dialog_on_done_action_output_results", ffEvent::PRIORITY_FINAL);
        $record->skip_events_on_error = false;
    }
    public static function ffRecord_dialog_on_done_action_prepare_results(ffRecord_base $record, $frmAction)
    {
        switch (isset($record->default_actions[$record->frmAction]) ? $record->default_actions[$record->frmAction] : $record->frmAction)
        {
            case "confirmdelete":
                $record->json_result["close"] = true;
                $record->json_result["refresh"] = true;
                $record->json_result["resources"] = $record->resources;
                break;

            case "insert":
                $record->json_result["close"] = true;
                $record->json_result["refresh"] = true;
                if (is_array($record->key_fields) && count($record->key_fields))
                    $record->json_result["insert_id"] = end($record->key_fields)->value->getValue();
                $record->json_result["resources"] = $record->resources;
                break;

            case "update":
                $record->json_result["close"] = true;
                $record->json_result["refresh"] = true;
                if ($record->db[0]->affectedRows())
                    $record->json_result["insert_id"] = end($record->key_fields)->value->getValue();
                $record->json_result["resources"] = $record->resources;
                break;
        }

        return false;
    }
    public static function ffRecord_dialog_on_done_action_output_results(ffRecord_base $record, $frmAction)
    {
        cm::jsonParse($record->json_result);
        exit;
    }

    public static function tplAddJs_not_found ($page, $tag, $params) {
        static $last_call;
        if ($tag === $last_call) {
            ffErrorHandler::raise("JS: Autoloader recursive inclusion", E_USER_ERROR, $page, get_defined_vars());
        }

        $tag_parts = explode(".", $tag);
        if (strpos($tag, "jquery.plugins.") === 0) {
            $page->loadLibrary(FF_THEME_DISK_PATH . "/library/plugins/jquery." . $tag_parts[2]);
            unset($page->js_loaded[$tag]);
            $page->tplAddJs($tag);
            return true;
        } elseif (strpos($tag, $tag_parts[0] . ".jquery.plugins.") === 0) {
            $page->loadLibrary(FF_THEME_DISK_PATH . "/" . $tag_parts[0] . "/javascript/plugins/jquery." . $tag_parts[3]);
            unset($page->js_loaded[$tag]);
            $page->tplAddJs($tag);
            return true;
        } elseif (strpos($tag, "library.") === 0) {
            $page->loadLibrary(FF_THEME_DISK_PATH . "/library/" . $tag_parts[1]);
            unset($page->js_loaded[$tag]);
            $page->tplAddJs($tag);
            return true;
        }
    }
    public static function tplAddCss_not_found($page, $tag, $params) {
        static $last_call;
        if ($tag === $last_call) {
            ffErrorHandler::raise("CSS: Autoloader recursive inclusion", E_USER_ERROR, $page, get_defined_vars());
        }
        $last_call = $tag;
    }

    public function __construct($params = null, $resources = null)
    {
        $this->load();

        $this->oPage = ffPage::factory(__TOP_DIR__, FF_SITE_PATH, null, ($params["theme"] ? $params["theme"] : ffTheme::THEME_NAME));
        $this->oPage->title                     = $params["title"];
        $this->oPage->class_body                = $params["class_body"];
        $this->oPage->compact_js                = $params["compact_js"];
        $this->oPage->compact_css               = $params["compact_css"];
        $this->oPage->compress                  = $params["compact_html"];

        if(!$this->resources && $this->oPage->getTheme() != ffTheme::THEME_NAME) {
            /**
             * @var $theme_dir String
             */
            $theme_dir                          = $this->oPage->getThemeDir();
            $this->resources                    = array(
                $theme_dir                      => array(
                    "filter"                    => array("css", "js", "html", "tpl", "jpg", "svg", "png")
                    , "rules"                   => array(
                        "/layouts/"             => "layouts"
                        , "/common/"            => "common"
                        , "/contents/"          => "components"
                        , "/widgets/"           => "widgets"
                        , "/css/"               => "css"
                        , "/javascript/"        => "js"
                        , "/images/"            => "images"
                        , "/fonts/"             => "fonts"
                        , "/ff/"                => "components"
                    )
                )
            );
        }

        $this->oPage->loadResources(array_merge(
            (array) $resources
            , $this::RESOURCES
            , (array) $this->resources
        ), $this->excludeDirname);

        $this->oPage->loadLibrary($this::LIBRARIES);
        if($_REQUEST["XHR_FFLIBS"]) {
            $struct = json_decode($_REQUEST["XHR_FFLIBS"]);
            if(is_array($struct) && count($struct)) {
                foreach ($struct AS $lib) {
                    $this->oPage->excludeLib($lib, "js");

                }
            }

        }

        $this->oPage->addEvent("tplAddJs_not_found", "ffTheme::tplAddJs_not_found");
        $this->oPage->addEvent("tplAddCss_not_found", "ffTheme::tplAddCss_not_found");

        $this->oPage->frameworkCSS = frameworkCSS::factory($this->framework_css
                                            ? $this->framework_css
                                            : ($params["framework_css"]
                                                ? $params["framework_css"]
                                                : $this::FRAMEWORK_CSS
                                            )
                                        , $this->font_icon
                                            ? $this->font_icon
                                            : ($params["font_icon"]
                                                ? $params["font_icon"]
                                                : $this::FONT_ICON
                                            )
                                        );
        frameworkCSS::extend($this->buttons, "buttons");
        frameworkCSS::extend($this->components, "override");

        if(is_array($this->js) && count($this->js)) {
            foreach($this->js AS $tag => $js) {
                $this->oPage->tplAddJs(str_replace(
                    array("[FRAMEWORKCSS]", "[FONTICON]")
                    , array(frameworkCSS::getFrameworkName(), frameworkCSS::getFontIconName())
                    , $tag
                ), $js);
            }
        }
        if(is_array($this->css) && count($this->css)) {
            foreach($this->css AS $tag => $css) {
                $this->oPage->tplAddCss(str_replace(
                    array("[FRAMEWORKCSS]", "[FONTICON]")
                    , array(frameworkCSS::getFrameworkName(), frameworkCSS::getFontIconName())
                    , $tag
                ), $css);
            }
        }



        $this->oPage->doEvent("on_layout_init", array($this->oPage, $params));

        if($params["page"]) {
            $this->oPage->template_file = "ffPage_" . $params["page"] . ".html";
        }
        if ($params["layout"]) {
            $this->oPage->layer = $params["layout"];
        }

        if(is_array($params["css"]) && count($params["css"])) {
            if(!isset($params["css"][0])) {
                $params["css"][0] = $params["css"];
            }

            foreach($params["css"] AS $css) {
                $key = ($css["@attributes"]["name"]
                    ? $css["@attributes"]["name"]
                    : basename($css["@attributes"]["path"], ".css")
                );

                if($key) {
                    $this->oPage->tplAddCss($key, $css["@attributes"]["path"]);
                }
            }
        }
        if(is_array($params["js"]) && count($params["js"])) {
            if(!isset($params["js"][0])) {
                $params["js"][0] = $params["js"];
            }

            foreach($params["js"] AS $js) {
                $key = ($js["@attributes"]["name"]
                    ? $js["@attributes"]["name"]
                    : basename($js["@attributes"]["path"], ".js")
                );

                if($key) {
                    $this->oPage->tplAddJs($key, $js["@attributes"]["path"]);
                }

            }
        }
        if(is_array($params["meta"]) && count($params["meta"])) {
            if(!isset($params["meta"][0])) {
                $params["meta"][0] = $params["meta"];
            }

            foreach($params["meta"] AS $meta) {
                $this->oPage->tplAddMeta($meta["@attributes"]);
            }
        }
        if(is_array($params["section"]) && count($params["section"])) {
            if(!isset($params["section"][0])) {
                $params["section"][0] = $params["section"];
            }

            foreach($params["section"] AS $section) {
                $this->oPage->addSection($section["name"], $section);
            }
        }
    }

    public function getPage() {
        return $this->oPage;
    }

    private function load()
    {
        //frameworkCSS::extend($this->font_icon, "fonticon");
        //frameworkCSS::extend($this->framework_css, "framework");

        ffCommon::setDefaults($this->ffDefaults);

        ffGrid::addEvent			("on_factory_done", "ffTheme::ffGrid_set_events"    , ffEvent::PRIORITY_HIGH);
        ffRecord::addEvent			("on_factory_done", "ffTheme::ffRecord_set_events"	, ffEvent::PRIORITY_HIGH);
        ffDetails::addEvent			("on_factory_done", "ffTheme::ffDetails_set_events"	, ffEvent::PRIORITY_HIGH);

        if($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest" && isset($_REQUEST["XHR_CTX_ID"]) && isset($_REQUEST["XHR_CTX_TYPE"])) {
            ffRecord::addEvent("on_factory_done", "ffTheme::ffRecord_set_events_dialog", ffEvent::PRIORITY_HIGH);
        }
    }
}

