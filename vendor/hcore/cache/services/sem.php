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

if (!defined("APPID"))                              define("APPID", $_SERVER["HTTP_HOST"]);

class cacheSem {
    private $sem                                            = null;

    const APPID                                             = APPID;
    const DEBUG                                             = DEBUG_MODE;
    const PROFILING                                         = DEBUG_PROFILING;

    const TYPE                                              = "sem";

    //private $cache                                        	= null;
    public function __construct($cache, $params = null)
    {
        //$this->cache = $cache;
        //$this->remove();
        register_shutdown_function(function()  {
            $this->release();
        });

    }
    public function acquire($namespace = null, $nowait = false, $max = null) {
        $acquired = true;
        if(1 || $_SERVER["SERVER_ADDR"] == $_SERVER["REMOTE_ADDR"] || defined("DISABLE_CACHE") /*|| $this::DEBUG === true*/) {
            return array("acquired" => true); //nn funziona
        }

        if(function_exists("sem_get")) {
            if(/*defined("DEBUG_MODE") &&*/ isset($_REQUEST["__nocache__"])) {
                $this->remove($namespace);
            } else {
                $params = $this->getParams($namespace);
                if($max === null)
                    $max = $params["max"];

                $sem = @sem_get($params["key"], $max, 0666, false);
                if($sem !== false) {
                    if(version_compare(phpversion(), "5.6.1", "<"))
                        $acquired = @sem_acquire($sem);
                    else
                        $acquired = @sem_acquire($sem, $nowait);

                    Logs::write(array(
                        "res" => $sem
                        , "acquired" => $acquired
                        , "namespace" => $namespace
                        , "max" => $max
                        , "key" => $params["key"]
                        , "remove" => $params["remove"]
                    ), "sem");
                } else {
                    $this->remove($namespace);
                    Logs::write($namespace . " ERROR: " . print_r(error_get_last(), true), "error_sem");
                }
            }
        }

        $this->sem[] = array(
            "res"           => $sem
            , "acquired"    => $acquired
            , "namespace"   => $namespace
            , "key"         => $params["key"]
            , "remove"      => $params["remove"]
        );
    }

    public function release($message = null) {
        if(function_exists("sem_release")) {
            if(is_array($this->sem) && $this->sem) {
                foreach($this->sem AS $key => $sem) {
                    if($sem["res"] && $sem["acquired"]) {
                        $released = @sem_release($sem["res"]);
                        if($sem["remove"] && $released !== false) {
                            $removed = @sem_remove($sem["res"]);
                        }

                        if($this::PROFILING) {
                            Logs::write("Release:" . $released . " " . ($sem["remove"] && $released !== false ? "Removed: " . $removed . " " : "") . $message . " of: " . print_r($sem, true) . ($released === false ? " ERROR: " . print_r(error_get_last(), true) : ""), "sem");
                        }
                        unset($this->sem[$key]);
                    }
                }
            }
        }
    }

    public function remove($namespace = null) {
        if(function_exists("sem_get")) {
            $params = $this->getParams($namespace);
            $sem = @sem_get($params["key"]);
            if($sem) {
                $is_removed = @sem_remove($sem);
                Logs::write("ID: " . $params["key"] . " namespace: " . $namespace . " " . ($is_removed ? "REMOVED" : "NO EXIST"), "sem");
            }
            if($namespace != "create") {
                $params = $this->getParams("create");
                $sem = @sem_get($params["key"]);
                if($sem) {
                    $is_removed = @sem_remove($sem);
                    Logs::write("ID: " . $params["key"] . " namespace: " . "create" . " " . ($is_removed ? "REMOVED" : "NO EXIST"), "sem");
                }
            }
            if($namespace != "update") {
                $params = $this->getParams("update");
                $sem = @sem_get($params["key"]);
                if($sem) {
                    $is_removed = @sem_remove($sem);
                    Logs::write("ID: " . $params["key"] . " namespace: " . "update" . " " . ($is_removed ? "REMOVED" : "NO EXIST"), "sem");
                }
            }
            if($namespace) {
                $params = $this->getParams();
                $sem = @sem_get($params["key"]);
                if($sem) {
                    $is_removed = @sem_remove($sem);
                    Logs::write("ID: " . $params["key"] . " namespace: " . "default" . " " . ($is_removed ? "REMOVED" : "NO EXIST"), "sem");
                }

                $params = $this->getParams(null, true);
                $sem = @sem_get($params["key"]);
                if($sem) {
                    $is_removed = @sem_remove($sem);
                    Logs::write("ID: " . $params["key"] . " namespace: " . "default XHR" . " " . ($is_removed ? "REMOVED" : "NO EXIST"), "sem");
                }
            }
        }
    }

    private function getParams($namespace = null, $xhr = null) {
        $max                                    = 1;
        $remove                                 = false;
        $appid                                  = substr(crc32($this::APPID
                                                    ? $this::APPID
                                                    : $_SERVER["HTTP_HOST"]
                                                ), 0, 4);
        if($xhr === null) {
            $xhr = $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest";
        }

        if(strlen($namespace) && is_numeric($namespace)) {
            $key = $namespace;
            $max = 10;
            $remove = true;
        } elseif($namespace == "create") {
            $key = 1;
            $max = 4;
        } elseif($namespace == "update") {
            $key = 2;
            $max = 3;
        } elseif(strlen($namespace) && is_string($namespace)) {
            $key = substr(crc32($namespace), 0, 6);
            $remove = true;
        } else {
            if($xhr) {
                $key = 4;
                $max = 25;
            } else {
                $key = 0;
                $max = 15;
            }
        }

        return array(
            "key"       => $appid . $key
            , "max"     => $max
            , "remove"  => $remove
        );
    }
}
