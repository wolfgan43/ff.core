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
class anagraphLogs
{
	const TYPE                                              = "logs";
	const MAIN_TABLE                                        = "logs";

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
																	, "prefix"				    => "ANAGRAPH_LOGS_DATABASE_"
																	, "table"                   => null
																	, "key"                     => "ID"
																)
																, "nosql"                       => array(
																	"host"          		    => null
																	, "username"    		    => null
																	, "password"    		    => null
																	, "name"       			    => null
																	, "prefix"				    => "ANAGRAPH_LOGS_MONGO_DATABASE_"
																	, "table"                   => null
																	, "key"                     => "ID"
																	)
																, "fs"                          => array(
																	"service"				    => "php"
																	, "path"                    => "/cache/anagraph/logs"
																	, "name"                    => array("url")
                                                                )
															);
    private static $struct								    = array(
	                                                            "logs" => array(
	                                                                "ID"                        => "primary"
                                                                    , "ID_src"                  => "number"
                                                                    , "tbl_src"                 => "string"
                                                                    , "hash"                    => "string"
                                                                    , "referer"                 => "string"
                                                                    , "created"                 => "number"
                                                                )
                                                                , "privacy" => array(
                                                                    "ID"                        => "primary"
                                                                    , "description"             => "string"
                                                                    , "ID_user"                 => "number"
                                                                    , "ID_privacy"              => "number"
                                                                    , "action"                  => "string"
                                                                    , "created"                 => "number"
                                                                )
                                                                , "device" => array(
                                                                    "ID"                        => "primary"
                                                                    , "description"             => "string"
                                                                    , "ID_user"                 => "number"
                                                                    , "ID_device"               => "number"
                                                                    , "browser"                 => "string"
                                                                    , "os"                      => "string"
                                                                    , "ip"                      => "string"
                                                                    , "model"                   => "string"
                                                                    , "created"                 => "number"
                                                                )
															);
    private static $relationship							= array(
                                                                "logs"                          => array(
                                                                    /*"acl"                       => array(
                                                                        "tbl"                       => "groups"
                                                                        , "key"                     => "ID"
                                                                    )
                                                                    , Anagraph::TYPE              => array(
                                                                        "external"                  => "ID_user"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                    , "tokens"                  => array(
                                                                        "external"                  => "ID_user"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                    , "user_groups"                  => array(
                                                                        "external"                  => "ID_user"
                                                                        , "primary"                 => "ID"
                                                                    )*/
                                                                )
                                                                , "privacy"                     => array(
                                                                    "users"                     => array(
                                                                        "external"                  => "ID_user"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                )
                                                                , "device"                      => array(
                                                                    "users"                     => array(
                                                                        "external"                  => "ID_user"
                                                                        , "primary"                 => "ID"
                                                                    )
                                                                )
                                                            );
    private static $indexes                                 = array(
                                                                "logs"                          => array(
                                                                    "ID_src"                    => "hardindex"
                                                                    , "acl"                     => "hardindex"
                                                                )
                                                                , "privacy"                     => array(
                                                                    "ID_user"                   => "hardindex"
                                                                    , "ID_privacy"              => "hardindex"
                                                                )
                                                                , "device"                      => array(
                                                                    "ID_user"                   => "hardindex"
                                                                    , "ID_device"               => "hardindex"
                                                                )
                                                            );
    private static $tables                                  = array(
                                                                "logs"                          => array(
                                                                    "name"                      => "logs"
                                                                    , "alias"                   => "log"
                                                                    , "engine"                  => "InnoDB"
                                                                    , "crypt"                   => false
                                                                    , "pairing"                 => false
                                                                    , "transfert"               => false
                                                                    , "charset"                 => "utf8"
                                                                )
                                                                , "privacy"                     => array(
                                                                    "name"                      => "logs_privacy"
                                                                    , "alias"                   => "privacy"
                                                                    , "engine"                  => "InnoDB"
                                                                    , "crypt"                   => false
                                                                    , "pairing"                 => false
                                                                    , "transfert"               => false
                                                                    , "charset"                 => "utf8"
                                                                )
                                                                , "device"                      => array(
                                                                    "name"                      => "logs_device"
                                                                    , "alias"                   => "device"
                                                                    , "engine"                  => "InnoDB"
                                                                    , "crypt"                   => false
                                                                    , "pairing"                 => false
                                                                    , "transfert"               => false
                                                                    , "charset"                 => "utf8"
                                                                )
                                                            );
    private static $alias                                   = array(
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