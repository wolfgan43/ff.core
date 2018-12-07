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
class anagraphSupport
{
	const TYPE                                              = "support";
	const MAIN_TABLE                                        = "place";

    /*private static $services								= array(
																"sql" 					        => null
																, "nosql"						=> null
																, "fs" 						    => null
															);*/
    private static $connectors								= array(
																"sql"                           => array(
																	"host"          		    => null
																	, "username"    		    => null
																	, "password"   			    => null
																	, "name"       			    => null
																	, "prefix"				    => "ANAGRAPH_SUPPORT_DATABASE_"
																	, "table"                   => null
																	, "key"                     => "ID"
																)
																, "nosql"                       => array(
																	"host"          		    => null
																	, "username"    		    => null
																	, "password"    		    => null
																	, "name"       			    => null
																	, "prefix"				    => "ANAGRAPH_SUPPORT_MONGO_DATABASE_"
																	, "table"                   => null
																	, "key"                     => "ID"
																	)
																, "fs"                          => array(
																	"service"				    => "php"
																	, "path"                    => "/cache/support"
																	, "name"                    => array("name")
                                                                )
															);
    private static $struct								    = array(
	                                                            "place" => array(
	                                                                "ID"                        => "primary"
                                                                    , "ID_src"                  => "number"
                                                                    , "tbl_src"                 => "number"
                                                                    , "ID_state"                => "number"
                                                                    , "ID_region"               => "number"
                                                                    , "ID_province"             => "number"
                                                                    , "ID_city"                 => "number"
                                                                    , "coord_title"             => "string"
                                                                    , "coord_lat"               => "double"
                                                                    , "coord_lng"               => "double"
                                                                    , "coord_zoom"              => "number"
                                                                    , "address"                 => "string"
                                                                    , "cap"                     => "string"
                                                                    , "tel"                     => "string"
                                                                    , "title"                   => "string"
                                                                    , "description"             => "string"
                                                                    , "cover"                   => "string"
                                                                    , "media"                   => "string"
                                                                )
                                                                , "currency" => array(
                                                                    "ID"                        => "primary"
                                                                    , "name"                    => "string"
                                                                    , "code"                    => "string"
                                                                    , "symbol"                  => "string"
                                                                )
                                                                , "zone" => array(
                                                                    "ID"                        => "primary"
                                                                    , "name"                    => "string"
                                                                    , "code"                    => "string"
                                                                )
                                                                , "state" => array(
                                                                    "ID"                        => "primary"
                                                                    , "ID_currency"             => "number"
                                                                    , "ID_lang"                 => "number"
                                                                    , "ID_zone"                 => "number"
                                                                    , "name"                    => "string"
                                                                    , "abbreviation"            => "string"
                                                                    , "code"                    => "string"
                                                                    , "prefix"                  => "string"
                                                                    , "coord_title"             => "string"
                                                                    , "coord_lat"               => "double"
                                                                    , "coord_lng"               => "double"
                                                                    , "coord_zoom"              => "number"
                                                                    , "vat_enable"              => "boolean"
                                                                    , "vat"                     => "number"
                                                                )
                                                                , "region" => array(
                                                                    "ID"                        => "primary"
                                                                    , "ID_state"                => "number"
                                                                    , "name"                    => "string"
                                                                    , "coord_title"             => "string"
                                                                    , "coord_lat"               => "double"
                                                                    , "coord_lng"               => "double"
                                                                    , "coord_zoom"              => "number"
                                                                    , "code"                    => "string"
                                                                )
                                                                , "province" => array(
                                                                    "ID"                        => "primary"
                                                                    , "ID_state"                => "number"
                                                                    , "ID_region"                => "number"
                                                                    , "name"                    => "string"
                                                                    , "coord_title"             => "string"
                                                                    , "coord_lat"               => "double"
                                                                    , "coord_lng"               => "double"
                                                                    , "coord_zoom"              => "number"
                                                                    , "code"                    => "string"
                                                                )
                                                                , "city" => array(
                                                                    "ID"                        => "primary"
                                                                    , "ID_state"                => "number"
                                                                    , "ID_region"               => "number"
                                                                    , "ID_province"             => "number"
                                                                    , "name"                    => "string"
                                                                    , "chief_town"              => "boolean"
                                                                    , "cap"                     => "string"
                                                                    , "coord_title"             => "string"
                                                                    , "coord_lat"               => "double"
                                                                    , "coord_lng"               => "double"
                                                                    , "coord_zoom"              => "number"
                                                                    , "code"                    => "string"
                                                                )
															);
    private static $relationship							= array(
                                                                "place"                         => array(
                                                                    "ID_city"                       => array(
                                                                        "tbl"                       => "city"
                                                                        , "key"                     => "ID"
                                                                    )
                                                                    , "ID_province"             => array(
                                                                        "tbl"                       => "province"
                                                                        , "key"                     => "ID"
                                                                    )
                                                                    , "ID_region"               => array(
                                                                        "tbl"                       => "region"
                                                                        , "key"                     => "ID"
                                                                    )
                                                                    , "ID_state"                => array(
                                                                        "tbl"                       => "state"
                                                                        , "key"                     => "ID"
                                                                    )

                                                                    , "city"                    => array(
                                                                        "external"                  => "ID_city"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                    , "province"                => array(
                                                                        "external"                  => "ID_province"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                    , "region"                  => array(
                                                                        "external"                  => "ID_region"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                    , "state"                   => array(
                                                                        "external"                  => "ID_state"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                )
                                                                , "state"                       => array(
                                                                    "currency"                  => array(
                                                                        "external"                  => "ID_currency"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                    , "language"                => array(
                                                                        "external"                  => "ID_lang"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                    , "zone"                    => array(
                                                                        "external"                  => "ID_zone"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                )
                                                                , "region"                      => array(
                                                                    "state"                     => array(
                                                                        "external"                  => "ID_state"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                )
                                                                , "province"                    => array(
                                                                    "state"                     => array(
                                                                        "external"                  => "ID_state"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                    , "region"                  => array(
                                                                        "external"                  => "ID_region"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                )
                                                                , "city"                        => array(
                                                                    "state"                     => array(
                                                                        "external"                  => "ID_state"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                    , "region"                  => array(
                                                                        "external"                  => "ID_region"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                    , "city"                     => array(
                                                                        "external"                  => "ID_region"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                )

                                                            );
    private static $indexes                                 = array(
                                                                "place"                         => array(
                                                                    "ID_src"                    => "hardindex"
                                                                    , "tbl_src"                 => "hardindex"
                                                                    , "ID_state"                => "hardindex"
                                                                    , "ID_region"               => "hardindex"
                                                                    , "ID_province"             => "hardindex"
                                                                    , "ID_city"                 => "hardindex"
                                                                )
                                                                , "state"                       => array(
                                                                    "ID_currency"               => "hardindex"
                                                                    , "ID_lang"                 => "hardindex"
                                                                    , "ID_zone"                 => "hardindex"
                                                                    , "code"                    => "hardindex"
                                                                )
                                                                , "region"                      => array(
                                                                    "ID_state"                  => "hardindex"
                                                                    , "code"                    => "hardindex"
                                                                )
                                                                , "province"                    => array(
                                                                    "ID_state"                  => "hardindex"
                                                                    , "ID_region"               => "hardindex"
                                                                    , "code"                    => "hardindex"
                                                                )
                                                                , "city"                        => array(
                                                                    "ID_state"                  => "hardindex"
                                                                    , "ID_region"               => "hardindex"
                                                                    , "ID_province"             => "hardindex"
                                                                    , "code"                    => "hardindex"
                                                                )

                                                            );
    private static $tables                                  = array(
                                                                "place"                         => array(
                                                                    "name"                      => "support_place"
                                                                    , "alias"                   => "place"
                                                                    , "engine"                  => "InnoDB"
                                                                    , "crypt"                   => false
                                                                    , "pairing"                 => false
                                                                    , "transfert"               => false
                                                                    , "charset"                 => "utf8"
                                                                )
                                                                , "zone"                        => array(
                                                                    "name"                      => "support_zone"
                                                                    , "alias"                   => "zone"
                                                                    , "engine"                  => "InnoDB"
                                                                    , "crypt"                   => false
                                                                    , "pairing"                 => false
                                                                    , "transfert"               => false
                                                                    , "charset"                 => "utf8"
                                                                )
                                                                , "currency"                    => array(
                                                                    "name"                      => "support_currency"
                                                                    , "alias"                   => "currency"
                                                                    , "engine"                  => "InnoDB"
                                                                    , "crypt"                   => false
                                                                    , "pairing"                 => false
                                                                    , "transfert"               => false
                                                                    , "charset"                 => "utf8"
                                                                )
                                                                , "state"                       => array(
                                                                    "name"                      => "support_state"
                                                                    , "alias"                   => "state"
                                                                    , "engine"                  => "InnoDB"
                                                                    , "crypt"                   => false
                                                                    , "pairing"                 => false
                                                                    , "transfert"               => false
                                                                    , "charset"                 => "utf8"
                                                                )
                                                                , "region"                      => array(
                                                                    "name"                      => "support_region"
                                                                    , "alias"                   => "region"
                                                                    , "engine"                  => "InnoDB"
                                                                    , "crypt"                   => false
                                                                    , "pairing"                 => false
                                                                    , "transfert"               => false
                                                                    , "charset"                 => "utf8"
                                                                )
                                                                , "province"                    => array(
                                                                    "name"                      => "support_province"
                                                                    , "alias"                   => "province"
                                                                    , "engine"                  => "InnoDB"
                                                                    , "crypt"                   => false
                                                                    , "pairing"                 => false
                                                                    , "transfert"               => false
                                                                    , "charset"                 => "utf8"
                                                                )
                                                                , "city"                        => array(
                                                                    "name"                      => "support_city"
                                                                    , "alias"                   => "city"
                                                                    , "engine"                  => "InnoDB"
                                                                    , "crypt"                   => false
                                                                    , "pairing"                 => false
                                                                    , "transfert"               => false
                                                                    , "charset"                 => "utf8"
                                                                )
                                                            );
    private static $alias                                   = array(
                                                                /*
                                                                "users"                         => array(
                                                                    "ID_languages"              => "ID_lang"
                                                                    , "ID_domains"              => "ID_domain"
                                                                    , "primary_gid"             => "acl"
                                                                    , "expiration"              => "expire"
                                                                    , "activation_code"         => "SID"
                                                                    , "lastlogin"               => "last_login"
                                                                )
                                                                , "groups"                      => array(
                                                                    "gid"                       => "ID"
                                                                )
                                                                */
                                                            );

    /**
     * anagraphAccess constructor.
     * @param $anagraph
     */
    public function __construct($anagraph)
	{
		//$this->anagraph                                     = $anagraph;
        $anagraph->setConfig($this->connectors, $this->services, $this::TYPE);
	}

    /**
     * @param $type
     * @return array
     */
    public static function getStruct($type) {
        return array(
            "struct"                                        => self::$struct[$type]
            , "indexes"                                     => self::$indexes[$type]
            , "relationship"                                => self::$relationship[$type]
            , "table"                                       => self::$tables[$type]
            , "alias"                                       => self::$alias[$type]
            , "connectors"                                  => false
            , "mainTable"                                   => self::MAIN_TABLE
        );
    }

    /**
     * @param $anagraph
     * @return array
     */
    public static function getConfig($anagraph, $services) {
        $res                                                = null;
        $connectors                                         = self::$connectors;

        $res                                                = array_fill_keys(array_keys($services), null);

        $anagraph->setConfig($connectors, $res, self::TYPE);

        return $res;
    }
}

