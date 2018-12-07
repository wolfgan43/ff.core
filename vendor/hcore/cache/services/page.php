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
if(!defined("FF_DISK_PATH"))                            define("FF_DISK_PATH", substr(__DIR__, 0, strpos("/vendor/", __DIR__)));
if(!defined("DEBUG_MODE"))                              define("DEBUG_MODE", false);

class cachePage {
    const DISK_PATH                                         = FF_DISK_PATH;
    const DEBUG                                             = DEBUG_MODE;
    const ENGINE                                            = "dynamic";
    const COMPRESS_TYPE                                     = "gz";
    const VALID_CACHE_START_AT                              = null;
    const TYPE                                              = "page";

    private $cache                                          = null;

    public function __construct($cache, $params = null)
    {
        //require_once(Cache::$disk_path . "/library/gallery/system/cache.php");

        /**
         * Cache System
         */

        //$this->cache = check_static_cache_page();
        //print_r($this->cache);
    }

    public function get() {
        return $this->cache;
    }

    public function run($page, $request = null, $session = null) {
        //require_once(Cache::$disk_path . "/library/gallery/system/cache.php");

        /**
         * Cache System
         */

       // $this->cache = check_static_cache_page();
       // return;


        $path_info = $_SERVER["PATH_INFO"];

        $cache = $this->find($page, $request, $session);
        if(is_array($cache)) {
            //check __nocache__
            if($this::DEBUG && isset($_REQUEST["__nocache__"])) {
                $_REQUEST["__CLEARCACHE__"] = true;
                define("DISABLE_CACHE", true);

                if($cache["error_path"]) {
                    $this->delete_error_document($cache["error_path"], $page);
                }
                if(is_file(Cache::$disk_path . $cache["storing_path"] . "/" . $cache["primary"])) {
                    if($cache["primary"] != $cache["gzip"])
                        @unlink(Cache::$disk_path . $cache["storing_path"] . "/" . $cache["primary"]);

                    @unlink(Cache::$disk_path . $cache["storing_path"] . "/" . $cache["gzip"]);
                }
            }

            if(defined("DISABLE_CACHE")) {
                return false;
            }

            //check is valid page
            if($cache["invalid"]) {
                /**
                 * @var cacheSem $sem
                 */
                $sem = Cache::getInstance("sem");

                if ($cache["is_error_document"] && !$cache["noexistfileerror"] && $cache["noexistfile"]) {

                } elseif ($cache["noexistfile"]) {
                    $sem->acquire($cache["storing_path"]);
                    $sem->acquire("create");

                    /*$arrSem[] = cache_sem($cache["storing_path"]);
                    if (is_file(Cache::$disk_path . $cache["storing_path"] . "/" . $cache["primary"])) {
                        $cache["invalid"] = false;
                    } else {
                        $arrSem[] = cache_sem("create");
                    }*/
                } elseif ($cache["noexistfileerror"]) {
                    $sem->acquire($cache["error_path"]);
                    //$arrSem[] = cache_sem($cache["error_path"]);
                } else {
                    //todo:: da ruprendere le pagine priority dallo schema
                    if(0 &&is_array($schema["priority"]) && array_search($path_info, $schema["priority"]) !== false) {
                        @touch( Cache::$disk_path . $cache["storing_path"] . "/" . $cache["primary"], time() + 10); //evita il multi loading di pagine in cache
                        //Logs::write($cache["storing_path"] . "/" . $cache["primary"] . "  " . filemtime(Cache::$disk_path . $cache["storing_path"] . "/" . $cache["primary"]) . " => " . (time() + 10), "sem_update_primary");
                    } else {
                        $res = $sem->acquire("update", true);
                        if($res["acquired"]) {
                            @touch($cache["storing_path"] . "/" . $cache["primary"], time() + 10); //evita il multi loading di pagine in cache
                            //Logs::write($cache["storing_path"] . "/" . $cache["primary"] . "  " . filemtime(Cache::$disk_path . $cache["storing_path"] . "/" . $cache["primary"]) . " => " . (time() + 10), "sem_update");
                        } else {
                            $cache["invalid"] = false;
                            //Logs::write($cache["storing_path"] . "/" . $cache["primary"] . "  " . filemtime(Cache::$disk_path . $cache["storing_path"] . "/" . $cache["primary"]) . " => " . (time() + 10), "sem_update_queue");
                        }
                    }
                }
            }

            if(!$cache["invalid"]) {
                //send output cache
                $this->sender($cache);
            }

            //register set_cache_page
            ob_start();
            register_shutdown_function(function($cache)  {
                $this->write($cache);
            }, $cache);
        }

       // $ff_contents = cache_check_ff_contents($path_info, $cache_file["last_update"]);

        //Gestisce gli errori delle pagine provenienti da apachee con errorDocument nell'.htaccess
       // if($cache_params === true) {
       //     $this->sender("error");
       // }


    }

    private function find($page, $request = null, $user = null) {
        $engine = "engine_" . $this::ENGINE;

        $res = $this->$engine($page, $request, $user);

        return ($res
            ? $res
            : $page["exit"]
        );
    }


    private function sender($signal) {
        switch ($signal) {
            case "error":
                $this->sender_error();
            default:
                $this->sender_parse($signal);
        }
        exit;
    }


    private function sender_error() {
        ffMedia::sendHeaders(array(
            "cache" => "must-revalidate"
        ));

        if(Cache::isXHR()) {
            cache_http_response_code(500);
        } else {
            cache_http_response_code(404);
        }
        Logs::write(array(
            "RULE"          => null
            , "ACTION"      => null
            , "URL"         => $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]
            , "REFERER"     => $_SERVER["HTTP_REFERER"]
        ), "error_badpath");
        exit;
    }

    private function sender_parse($cache) {
        $target_file = "";
        $compress = false;
        $cache_path = ($cache["is_error_document"]
            ? $cache["error_path"]
            : $cache["storing_path"]
        );

        //$cache_file["compress"] = true;
        if(strpos($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") === false) {
            $target_file = $cache_path . "/" . $cache["primary"];
        } elseif($cache["compress"] && $cache["gzip"]) {
            $compress = true;
            $target_file = $cache_path . "/" . $cache["gzip"];
        } else {
            $target_file = $cache_path . "/" . $cache["primary"];
        }

        if(strlen($target_file)) {
            $target_file = Cache::$disk_path . $target_file;
            // header_remove();
            //clearstatcache();

            /* if($cache_file["last_update"]) {
                 if(filemtime($target_file) != $cache_file["last_update"])
                     return false;
             } else {
                 if(filemtime($target_file) >= filectime($target_file))
                     return false;
             }*/
            //define("CACHE_PAGE_STORING_PATH", $cache_file["cache_path"] . "/" . $cache_file["filename"]);
            /*if(!$cache_file["is_error_document"] && $_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest" && TRACE_VISITOR === true) {
                Stats::getInstance("trace")->write();
            }*/

            $etag = md5($target_file . filemtime($target_file) . "_" . $cache["lang"] . "_" . $cache["group"]);
            if($cache["client"] === false
                || ($cache["client"] == "noxhr" && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest")
                || ($cache["client"] == "nohttp" && $_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest")
            ) {
                $expires = false;
                $max_age = false;
                $etag = false;
            } elseif($cache["group"] != "guests") {
                $expires = null;
                $max_age = 3;
            } else {
                $expires = null;
                $max_age = 7;
            }

            ffMedia::sendHeaders(array(
                "compress" => $cache["compress"]
                , "max_age" => $max_age
                , "expires" => $expires
                , "etag" => $etag
                , "size" => filesize($target_file)
                , "mimetype" => "text/html"
            ));

            if (strlen($_SERVER["HTTP_IF_NONE_MATCH"]) && substr($_SERVER["HTTP_IF_NONE_MATCH"], 0, strlen($etag)) == $etag)
            {
                cache_http_response_code(304);
                exit;
            }

            readfile($target_file);
            exit;
        }
    }

    /**
     * @param $page
     * @param null $user
     * @return array|bool
     */
    private function engine_dynamic($page, $request = null, $user = null) {
        $cache                          = false;

        if($page["cache"]) {
            $cache["invalid"]           = true;
            $cache["noexistfile"]       = true;
            $cache["noexistfileerror"]  = null;

            $cache["compress"]          = ($page["compress"] && isset($_SERVER["HTTP_ACCEPT_ENCODING"]) && strpos($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") === false
                                            ? false
                                            : true
                                        );
            $cache["base_path"]         = $this->engine_dynamic_get_path(
                                            $page["cache"]
                                            , $page["lang"]
                                            , ($page["session"] === false
                                                ? null
                                                : $user
                                            )
                                            , $page["cache_path"]
                                        );

            $cache["storing_path"]      = $cache["base_path"]
                                            . $this->engine_dynamic_rule($page["rule"], $page["user_path"])
                                            . ($page["user_path"] == "/"
                                                ? ""
                                                : $page["user_path"]
                                            );
            $cache["filename"]          = $this->engine_dynamic_filename($request, $page["cache_rnd"]);
            $cache["ext"]               = $this->engine_dynamic_extension($cache["compress"], $page["type"]);
            $cache["primary"]           = $cache["filename"] . "." . $cache["ext"];
            $cache["gzip"]              = $cache["filename"] . "." . $this::COMPRESS_TYPE;

            $cache_final                = Cache::$disk_path . $cache["storing_path"] . "/" . $cache["primary"];
            if(is_file($cache_final)) {
                $cache["noexistfile"]   = false;

                $cache["last_update"]   = filemtime($cache_final);
                $cache["created"]       = filectime($cache_final);

                if($cache["last_update"] >= $cache["created"]) {
                    $cache["invalid"]   = false;
                }

                if($this::VALID_CACHE_START_AT && $this::VALID_CACHE_START_AT > $cache["created"]) {
                    $cache["invalid"]   = true;
                }
            }

            $cache["is_error_document"] = false;
            if($cache["invalid"] && $cache["noexistfile"] && $page["user_path"] != "/") {
                $arrUserPath = explode("/", $page["user_path"]);
                $cache["error_path"]    = $this->engine_dynamic_get_path(
                                            $page["cache"]
                                            , $page["lang"]
                                            , ($page["session"] === false
                                                ? null
                                                : $user
                                            )
                                            , "/error-document"
                                        );
                $cache_error_final = Cache::$disk_path . $cache["error_path"] . "/". $arrUserPath[1] . ".php";


                $cache["noexistfileerror"] = !is_file($cache_error_final);
                $cache["is_error_document"]= $this->get_error_document($cache_error_final, $cache["storing_path"] . "/" . $cache["filename"]);
            }

            $cache["client"] = $page["cache_client"];
            $cache["type"] = $page["type"];
            $cache["lang"] = $page["lang"];
            $cache["group"] = ($page["session"] === false || !$user["acl_primary"]
                ? "guests"
                : $user["acl_primary"]
            );
        }

        return $cache;
    }

    private function engine_dynamic_get_path($type, $lang, $user = null, $base_path = null) {
        if($type === "guest") {
            $cache_path             = "/global";
        } else {
            if($user) {
                if($type === "user") {
                    $auth_path      = ($user["username_slug"]
                        ? $user["username_slug"]
                        : preg_replace("/[^a-z\-0-9]/i", "", $user["username"])
                    );
                    $cache_path     = "/private";
                } else {
                    $auth_path      = $user["acl_primary"];
                }
            }

            if(!$auth_path) {
                $auth_path          = "guests";
            }
            if(!$cache_path) {
                $cache_path         = "/public";
            }
        }

        if(strpos(strtolower($_SERVER["HTTP_HOST"]), "www.") === 0) {
            $domain_name            = substr($_SERVER["HTTP_HOST"], strpos($_SERVER["HTTP_HOST"], ".") + 1);
        } else {
            $domain_name            = $_SERVER["HTTP_HOST"];
        }

        if(strpos($domain_name, ":") !== false) {
            $domain_name            = substr($domain_name, 0, strpos($domain_name, ":"));
        }
        //cache_get_locale($page["user_path"], $domain_name, $user_permission);


        $cache_base                 = Cache::CACHE_PATH . "/" . $domain_name . "/" . $lang
            . ($base_path
                ? $base_path
                : $cache_path
            )
            . (!$auth_path || ($auth_path == "guests" && $base_path)
                ? ""
                : "/" . $auth_path
            );

        return $cache_base;
    }

    private function engine_dynamic_rule($rule, $path_info) {
        $cache_base_rule = "";
        if(is_array($rule) && count($rule)) {
            foreach($rule AS $compare_path => $precision) {
                if(strpos($path_info, $compare_path) !== false) {
                    $arrCacheSplit = explode($compare_path, $path_info, 2);
                    if($arrCacheSplit[1]) {
                        $cache_base_rule = "/" . substr(ltrim($arrCacheSplit[1], "/"), 0, $precision);
                        break;
                    }
                }
            }
        }

        return $cache_base_rule;
    }

    private function engine_dynamic_filename($request, $random = false) {
        $cache_filename = "index";

        if($random) {
            $cache_filename .= rand(1, $random);
        }

        if(is_array($request["valid"]) && count($request["valid"])) {
            $cache_filename .= "_" . str_replace(array("&", "="), array("_", "-"), http_build_query($request["valid"]));
        }

        $cache_filename = preg_replace("/[^A-Za-z0-9\-_]/", '', $cache_filename);
        if(Cache::isXHR()) {
            $cache_filename .= "_XHR";
        }

        return $cache_filename;
    }

    private function engine_dynamic_extension($compress = false, $page_type = null) {
        if($compress) {
            $cache_ext = $this::COMPRESS_TYPE;
        } else {
            $cache_file_type = $page_type;
            if($cache_file_type == "mixed") {
                if (Cache::isXHR()) {
                    $cache_file_type = "json";
                } else {
                    $cache_file_type = "html";
                }
            } elseif(!$cache_file_type) {
                $cache_file_type = "html";
            }
            $cache_ext = $cache_file_type;
        }

        return $cache_ext;
    }

    private function engine_static($page, $request = null, $user = null) {
        $cache                          = false;

        return $cache;
    }

    private function engine_eternal($page, $request = null, $user = null) {
        $cache                          = false;

        return $cache;
    }

    private function get_error_document($file, $key)
    {
        $fs = new Filemanager("php");

        $page = $fs->read($file, $key);

        return (is_array($page) && count($page)
            ? true
            : false
        );
    }

    private function delete_error_document($cache_error_path, $params)
    {
        if($params["user_path"] && $params["user_path"] != "/") {
            $arrUserPath = explode("/", $params["user_path"]);
            $errorDocumentFile = Cache::$disk_path . $cache_error_path . "/" . $arrUserPath[1]; // . ".php";
            $key = $params["user_path"];

            //require_once (Cache::$disk_path . "/library/gallery/models/filemanager/Filemanager.php");
            $fs = new Filemanager("php");

            return $fs->delete($key, $errorDocumentFile, Filemanager::SEARCH_IN_VALUE);
        }
    }

    private function set_error_document($cache, $user_path) {
        $arrUserPath = explode("/", $user_path);

        $errorDocumentFile = $cache["error_path"] . "/" . $arrUserPath[1];

        $fs = new Filemanager("php", $errorDocumentFile);
        $fs->update(array(
            $cache["storing_path"] . "/" . $cache["primary"] => $user_path
        ));
    }

    public function write($cache) {

        return;
        $buffer = ob_get_contents();
        if($diable_cache) {
            $cache = check_static_cache_page($globals->page["strip_path"] . $globals->page["user_path"], 200);

            if(strpos($cache["file"]["cache_path"], FF_DISK_PATH) === 0 && is_file($cache["file"]["cache_path"] . "/" . $cache["file"]["primary"]))
                @unlink($cache["file"]["cache_path"] . "/" . $cache["file"]["primary"]);
            if(strpos($cache["file"]["cache_path"], FF_DISK_PATH) === 0 && is_file($cache["file"]["cache_path"] . "/" . $cache["file"]["gzip"]))
                @unlink($cache["file"]["cache_path"] . "/" . $cache["file"]["gzip"]);
            if(strpos($cache["file"]["cache_path"], FF_DISK_PATH) === 0 && is_file($cache["file"]["cache_path"] . "/" . $cache["file"]["filename"] . "." . $cache["file"]["type"]))
                @unlink($cache["file"]["cache_path"] . "/" . $cache["file"]["filename"] . "." . $cache["file"]["type"]);
        } else {
            $is_error_document = !($buffer && http_response_code() == 200);
            if (!$is_error_document) {
                $page = Kernel::stats($cache);

                //            Stats::getInstance("page")->write();
                //            if (TRACE_VISITOR === true) {
                //                Stats::getInstance("trace")->write("pageview", "nocache");
                //            }

                if (!is_dir($cache["storing_path"])) {
                    @mkdir($cache["storing_path"], 0777, true);
                }

                if ($cache["primary"] != $cache["gzip"]) {
                    $fs = Filemanager::getInstance("html", Cache::$disk_path . $cache["storing_path"] . "/" . $cache["primary"]);
                    $fs->write($buffer);
                }
                $fs = Filemanager::getInstance("html", Cache::$disk_path . $cache["storing_path"] . "/" . $cache["gzip"]);
                $fs->write($buffer);

            } else {
                $this->set_error_document($cache, $page["user_path"]);
            }
        }


/*
        if(defined("DISABLE_CACHE"))
            cache_send_header_content(false, false, false, false);
        else
            cache_send_header_content(false, false);
*/

        //ffErrorHandler::raise("DEBUG CM Process End", E_USER_WARNING, null, get_defined_vars());



    }
}

