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

class anagraphApiSupport
{
    public function __construct()
    {

    }

    public function state($request = null)
    {
        $res = Anagraph::getInstance("support")->read(
            array(
                "state.ID"
                , "state.name"
            )
            , true
        );

        return array("state" => $res);
    }

    public function region($request = null)
    {
        $res = Anagraph::getInstance("support")->read(
            array(
                "region.ID"
                , "region.ID_state"
                , "region.name"
            )
            , true
        );

        return array("region" => $res);
    }

    public function province($request = null)
    {
        $res = Anagraph::getInstance("support")->read(
            array(
                "province.ID"
                , "province.ID_state"
                , "province.ID_region"
                , "province.name"
            )
            , true
        );

        return array("province" => $res);
    }

    public function city($request = null)
    {
        $res = Anagraph::getInstance("support")->read(
            array(
                "city.ID"
                , "city.ID_state"
                , "city.ID_region"
                , "city.ID_province"
                , "city.name"
            )
            , true
        );

        return array("city" => $res);
    }
}