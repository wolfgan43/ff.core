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
class ffTheme {
    //private $oPage                              = null;
    private $config                             = array();
    private $fs                                 = array(
                                                    "/themes/responsive"            => array(
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
    private $framework_css                      = array();
    private $font_icons                         = array();
    private $buttons                            = array();
    private $components                         = array();

    private $ffDefaults                          = array(
                                                    "ffPage" => array(
                                                        "default_js" => array(
                                                            "jquery" => null
                                                            , "ff.page" => null
                                                        )
                                                        , "default_css" => array(
                                                            "ff.core" => null
                                                        )
                                                    )
    );

    public function __construct($framework_css, $font_icon)
    {
        $this->load();

        frameworkCSS::factory($framework_css, $font_icon);
       // $this->oPage =& $oPage;

    }

    private function load()
    {
        frameworkCSS::extend($this->font_icons, "fonticon");
        frameworkCSS::extend($this->framework_css, "framework");
        frameworkCSS::extend($this->buttons, "buttons");
        frameworkCSS::extend($this->components, "override");

        ffCommon::setDefaults($this->ffDefaults);
    }
}

