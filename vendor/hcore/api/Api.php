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

/**
 * Created by PhpStorm.
 * User: wolfgan
 * Date: 25/04/2018
 * Time: 14:10
 */

class Api extends vgCommon {
    private static $singleton = null;

    public function __construct() {

    }

    public static function getInstance() {
        if(self::$singleton) {
            self::$singleton = new Api();
        }

        return self::$singleton;
    }

    public static function send($data = null, $type = "json") {
        if($data) {
            if($data["error"])                                              { $data["error"] = ffTranslator::get_word_by_code($data["error"]); }
            switch($type) {
                case "json":
                    header("Content-type: application/json");
                    echo json_encode($data);
                    break;
                case "xml":
                    header("Content-type: application/xml");
                    echo $data;
                    break;
                case "soap":
                    header("Content-type: application/soap+xml");
                    //echo self::soap_client($data["url"], $data["headers"], $data["action"], $data["data"], $data["auth"]);

                    break;
                default:
            }
        }

        exit;
    }

    public function request($target, $scope, $method) {
        if ($target && $scope && $method) {
            if(Auth::getInstance("oauth2")->grantAccess($target . "/" . $scope . "/" . $method)) {
                $class_name = $target . "/" . $scope;
                if (self::$singleton[$class_name] === null) {
                    self::$singleton[$class_name] = $this->autoload($target, $scope);
                }

                if (self::$singleton[$class_name] && method_exists(self::$singleton[$class_name], $method)) {
                    $this->response($class_name, $method);
                }
            } else {
                Cms::errorDocument(401);
            }
        }

        $this->load($target, $scope, $method);

        Cms::errorDocument();
    }
    private function response($class_name, $method) {
        if(self::DEBUG)                                                 { Debug::startWatch(); }

        $data                                                           = self::$singleton[$class_name]->$method(self::getRequest());
        $res["status"]                                                  = ($data["status"]
                                                                            ? $data["status"]
                                                                            : "0"
                                                                        );
        $res["error"]                                                   = ($data["error"]
                                                                            ? $data["error"]
                                                                            : ""
                                                                        );

        if(self::DEBUG && is_array($res)) {
            $res["exTime"]                                              = Debug::stopWatch();
            if($data["exTime"] && $data["exTime"] != $res["exTime"])    { $res["exTime"] .= " (" . $data["exTime"] . ")"; }
        }

        unset($data["error"]);
        unset($data["status"]);
        unset($data["exTime"]);

        if(count($data))                                                { $res["data"] = $data; }

        Api::send($res);

    }

    private function getQuery() {

    }

    private function autoload($target, $scope) {
        $file                                                           = self::getClassPath(ucfirst($target)) . "/api/" . $scope . "." . self::PHP_EXT;

        if (is_file($file)) {
            require($file);

            $controller                                                 = $target . "Api" . ucfirst($scope);
            return new $controller();
        }

        return false;
    }

    private function load($target, $scope = null, $path = null) {
        $return                                                         = null;

        if($path) {
            $file                                                       = $this::getDiskPath("api") . "/" . $target . "/" . $scope . "/" . $path . "." . $this::PHP_EXT;
            if(!is_file($file))                                         { $file = $this::getDiskPath("modules") . "/" . $scope . "/api/" . $target . "/" . $path . "." . $this::PHP_EXT; }
        } elseif($scope) {
            $file                                                       = $this::getDiskPath("api") . "/" . $target . "/" . $scope . "." . $this::PHP_EXT;
        } else {
            $file                                                       = $this::getDiskPath("api") . "/" . $target . "." . $this::PHP_EXT;
        }

        if(is_file($file)) {
            require($file);

            Api::send($return);
        }
    }


    public static function request2sql($sql, $get = null) {
        $db = ffDB_Sql::factory();

        $limit = "";
        $order = "";
        $where = "";

        if(!$get)
            $get = $_REQUEST;

        $request = self::getRequest();

        if($request["navigation"]) {
            if(!$request["navigation"]["count"])
                $request["navigation"]["count"] = 50;

            $page = $request["navigation"]["page"] - 1;
            if($page < 0)
                $page = 0;

            $limit = (int) $page * $request["navigation"]["rec_per_page"] . ", " . (int) $request["navigation"]["count"];
        }

        if($request["sort"]) {
            $order = "`" . $request["sort"]["name"] . "` " . $request["sort"]["dir"];
        }


        if($request["search"]) {
            $sql = str_replace(array("\r", "\n", "\t"), " ", $sql);
            $tick1 = strpos($sql,'FROM ') + 5;
            $tick2 = strpos($sql,' ', $tick1);
            $table = substr($sql, $tick1,$tick2 - $tick1);

            $sSQL = "SELECT *
				FROM " . $table . "
				WHERE 1
				LIMIT 1";
            $db->query($sSQL);
            $fields = $db->fields;

            if($request["search"]["term"]) {
                if(is_array($fields) && count($fields)) {
                    $sub_where = array();
                    foreach($fields AS $field => $params) {
                        $sub_where[] = "`" . $field . "` LIKE '%" . $db->toSql($request["search"]["term"], "Text", false) . "%'";
                    }
                    $where[] = " (" . implode(" OR ", $sub_where) . ")";
                }
            }

            if(is_array($request["search"]["available_terms"]) && count($request["search"]["available_terms"])) {
                $param_where 		= null;
                foreach($request["search"]["available_terms"] AS $keys => $value) {
                    $arrValue 		= null;
                    $op 			= "OR";
                    $type_op 		= "eq";
                    $operations 	= array(
                        "eq" 		=> "`[NAME]` = '[VALUE]'"
                    , "in" 		=> "FIND_IN_SET('[VALUE]', `[NAME]`)"
                    , "like" 	=> "`[NAME]` LIKE '%[VALUE]%'"
                    );

                    if(substr($keys, -1, 1) == "+") {
                        $type_op 	= "in";
                        $keys 		= substr($keys, 0, -1);
                    } elseif(substr($keys, -1, 1) == "-") {
                        $type_op 	= "like";
                        $keys 		= substr($keys, 0, -1);
                    }

                    if(!$fields[$keys])
                        continue;

                    if(strpos($value, ",") !== false) {
                        $arrValue 	= array_filter(explode(",", $value));
                        $op 		= "OR";
                    } elseif(strpos($value, "-") !== false) {
                        $arrValue 	= array_filter(explode("-", $value));
                        $op 		= "AND";
                    }

                    if (is_array($arrValue) && count($arrValue)) {
                        $sub_where = array();
                        foreach ($arrValue AS $item) {
                            $sub_where[] = str_replace(
                                array(
                                    "[NAME]"
                                , "[VALUE]"
                                )
                                , array(
                                    $keys
                                , $db->toSql($item, "Text", false)
                                )
                                , $operations[$type_op]
                            );
                        }
                        $where[] = " (" . implode(" " . $op . " ", $sub_where) . ")";
                    } else {
                        $param_where[] = str_replace(
                            array(
                                "[NAME]"
                            , "[VALUE]"
                            )
                            , array(
                                $keys
                            , $db->toSql($value, "Text", false)
                            )
                            , $operations[$type_op]
                        );
                    }
                }
                if($param_where)
                    $where[] = implode(" AND ", $param_where);
            }
        }

        return str_replace(array(
                "[LIMIT]"
            , "[COLON] [ORDER]"
            , "[ORDER] [COLON]"
            , "[ORDER]"
            , "[AND] [WHERE]"
            , "[OR] [WHERE]"
            , "[WHERE] [AND]"
            , "[WHERE] [OR]"
            , "[WHERE]"
            )
            , array(
                ($limit 	? 	" LIMIT " . $limit 		: "")
            , ($order 	? 	", " . $order 			: "")
            , ($order 	? 	$order . ", " 			: "")
            , ($order 	? 	" ORDER BY " . $order 	: "")
            , ($where 	? 	" AND " . implode(" AND ", $where) 		: "")
            , ($where 	? 	" OR (" . implode(" AND ", $where) . ")" 	: "")
            , ($where 	? 	implode(" AND ", $where) . " AND " 		: "")
            , ($where 	? 	"(" . implode(" AND ", $where) . ") OR " 	: "")
            , ($where 	? 	" WHERE " . implode(" AND ", $where) 		: "")
            ), $sql);
    }
}