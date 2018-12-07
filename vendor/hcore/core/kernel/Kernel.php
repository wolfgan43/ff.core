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

class Kernel extends vgCommon
{
    //const LANG_DEFAULT_ID                                   = "1";
    const LANG_DEFAULT_CODE                                 = "ITA";
    //const LANG_DEFAULT_TINY_CODE                            = "it";
    //const BADPATH_DELAY                                     = 10;

    private $router                                         = null;
    private $path_info                                      = null;
    private $orig_path_info                                 = null;
    private $root_path                                      = null;
    private $redirect                                       = null;

    private static $page                                    = null;
    private static $struct                                  = null;
    private static $lang                                    = null;
    private static $locale                                  = null;
    private static $session                                 = null;
    private static $config                                  = null;
   // private static $controllers                             = null;

    public static function getPage($key = null) {
        return ($key
            ? self::$page[$key]
            : self::$page
        );
    }
    public static function getLang($key = null) {
        return ($key
            ? self::$lang[$key]
            : self::$lang
        );
    }
    public static function getLocale($key = null) {
        return ($key
            ? self::$locale[$key]
            : self::$locale
        );
    }
    public static function getSession($key = null) {
        return ($key
            ? self::$session[$key]
            : self::$session
        );
    }
    public static function set($key = null, $bucket = null) {
        if(!$bucket) {
            $bucket = "unknown";
        }
        self::$struct[$bucket][$key] = str_replace(",", "", $key);
    }
    public static function get($bucket = null, $key = null) {
        return ($bucket
            ? ($key
                ? self::$struct[$bucket][$key]
                : self::$struct[$bucket]
            )
            : self::$struct
        );
    }

    public static function stats($cache) {
        $cm = cm::getInstance();
		$globals = ffGlobals::getInstance("gallery");

		$now 							    = time();
		$res                                = self::getRequest();
		$get                                = $res["valid"];

		if($globals->author) {
			$author = $globals->author;
			unset($author["token"]);
			unset($author["user_vars"]);
		}
		if(is_array($cm->oPage->page_js) && count($cm->oPage->page_js)) {
			$page_js 					= $cm->oPage->page_js;
			foreach ($page_js AS $key => $js) {
				if($js["embed"]) {
					$page_js[$key]["embed"] = true;
				}
			}
		}

		if(is_array($cm->oPage->page_css) && count($cm->oPage->page_css)) {
			$page_css 					= array_diff_key($cm->oPage->page_css, $globals->links);
			foreach ($page_css AS $key => $css) {
				if($css["embed"]) {
					$page_css[$key]["embed"] = true;
				}
			}
		}

        foreach(self::get() AS $bucket => $keys) {
            $struct[$bucket] = array_values($keys);
        }

		return array(
			"url"						=> $globals->user_path
			, "get"						=> $get
			, "domain"					=> self::DOMAIN
			, "type"					=> $globals->seo["current"]
			, "event"					=> null
			, "title" 					=> $cm->oPage->title
			, "description" 			=> $cm->oPage->page_meta["description"]["content"]
			, "cover"					=> array_filter($globals->cover)
			, "author" 					=> $author
			, "tags"					=> $globals->tags
			, "owner"					=> $globals->author["id"]
			, "meta"					=> array() //$cm->oPage->page_meta
			, "links"					=> $globals->links
			, "microdata"				=> $globals->microdata
			, "js"						=> array() /*array(
					                        "url" => (is_array($cm->oPage->page_defer["js"]) && count($cm->oPage->page_defer["js"])
                                                ? $cm->oPage->page_defer["js"][0]
                                                : ""
                                            )
				                            , "keys" => $page_js
				                        )*/
			, "css"						=> array() /*array(
                                            "url" => (is_array($cm->oPage->page_defer["css"]) && count($cm->oPage->page_defer["css"])
                                                ? $cm->oPage->page_defer["css"][0]
                                                : ""
                                            )
                                            , "keys" => $page_css
                                        )*/
			, "international"			=> array() //ffTranslator::dump()
			, "settings"				=> self::getPage()
            , "lang"                    => self::getLang("tiny_code")
            , "session"                 => self::getSession()
			, "http_status"				=> $globals->http_status
			, "created"					=> $now
			, "last_update"				=> $now
			, "cache_last_update"		=> $now
            , "struct"                  => $struct
            , "cache"					=> ($cache["storing_path"]
                                            ? str_replace(self::getDiskPath("cache"), "", $cache["storing_path"]) . "/" . $cache["primary"]
                                            : array()
                                        )
			, "user_vars"				=> $globals->user_vars
            , "exTime"                  => number_format(microtime(true) - CMS_START, 2, '.', '')
        );
    }

    public static function &config() {
        return self::$config;
    }


    public function __construct()
    {
        $this->loadConfig();

        $this->loadEnv();
        $this->loadLocale();
        $this->loadSchema();

        $schema = self::getSchema();

        //Auth::getInstance("oauth2")->grantAccess("/pippo");

        //$this->router = $config["router"];

        $this->rewritePathInfo($schema["alias"]);

        //todo:: da spostare nello shield
        $this->checkLoadAvg();
        $this->checkAllowedPath($schema["badpath"]);

        $this->router                                       = new Router();
        $this->router->addRules($schema["router"]);

        $page                                               = $this->router->check($this->orig_path_info);

        //$schema                                             = $cms->getSchema();

        self::$page                                         = $this->get_page_properties($schema, $_SERVER["PATH_INFO"]);
        if(self::$page["root_path"] && self::$page["root_path"] == $this->root_path)    {
            $_SERVER["PATH_INFO"] = $this->orig_path_info;
        }

        // if(self::$page["router"])                           { $this->router = array_replace($this->router , self::$page["router"]); }

        /**
         * Resolve Request
         */
        $request_rules                                      = $this->get_request_rules($schema["request"]);
        $request                                            = self::getRequest($request_rules);

        if($request_rules["log"]) {
            Logs::write(array(
                    "URL"               => $_SERVER["PATH_INFO"]
                , "REFERER"         => $_SERVER["HTTP_REFERER"]
                ) + $request, "request");
        }

        //necessario XHR perche le request a servizi esterni path del domain alias non triggerano piu
        if($_SERVER["REQUEST_METHOD"] == "GET" && $_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
            if(count($request["rawdata"]) != count($request["valid"])) { $query = "?" . http_build_query($request["valid"]); }

            // Evita pagine duplicate quando i link vengono gestiti dagli alias o altro
            if(self::$page["redirect"])                     { $this->redirect = self::$page["redirect"] . $query; }//self::$page["redirect"] comprende http_host

            if(count($request["unknown"]) && !self::$page["restricted"]) {
                $this->redirect = $_SERVER["HTTP_HOST"] . $this->path_info . $query;
            }
        }

        if(is_array($request_rules["nocache"])) {
            if(is_array($request["rawdata"]) && count($request["rawdata"])) {
                $valid_request = array_intersect_key($request["rawdata"], array_flip($request_rules["nocache"]));
                if (is_array($valid_request) && count($valid_request)) {
                    define("DISABLE_CACHE", true);
                }
            }
        } elseif($request_rules["nocache"]) {
            define("DISABLE_CACHE", true);
        }
    }
    private function loadConfig() {
        if(!self::$config) {
            self::$config = array();

            Filemanager::scan(array(
                self::getDiskPath("libs")               => array("filter" => array("xml"))
                , self::getDiskPath("configuration")    => array("filter" => array("xml"), "flag" => Filemanager::SCAN_FILE)
            ), function ($file) {
                $config = Filemanager::getInstance("xml")->read($file);

                if(is_array($config["locale"]) && count($config["locale"])) {
                    self::$config["locale"] = array_replace((array) self::$config["locale"], $config["locale"]);
                }
                if(is_array($config["snippet"]) && count($config["snippet"])) {
                    self::$config["snippet"] = array_replace((array) self::$config["snippet"], $config["snippet"]);
                }
                if(is_array($config["engine"]) && count($config["engine"])) {
                    self::$config["engine"] = array_replace((array) self::$config["engine"], $config["engine"]);
                }
                if(is_array($config["router"]["rule"]) && count($config["router"]["rule"])) {
                    self::$config["router"]["rule"] = array_merge((array) self::$config["router"]["rule"], $config["router"]["rule"]);
                }
                if(is_array($config["pages"]["page"]) && count($config["pages"]["page"])) {
                    self::$config["pages"]["page"] = array_merge((array) self::$config["pages"]["page"], $config["pages"]["page"]);
                }
                if(is_array($config["request"]["page"]) && count($config["request"]["page"])) {
                    self::$config["request"]["page"] = array_merge((array) self::$config["request"]["page"], $config["request"]["page"]);
                }
                if(is_array($config["alias"]["domain"]) && count($config["alias"]["domain"])) {
                    self::$config["alias"]["domain"] = array_merge((array) self::$config["alias"]["domain"], $config["alias"]["domain"]);
                }
                if(is_array($config["mirror"]["domain"]) && count($config["mirror"]["domain"])) {
                    self::$config["mirror"]["domain"] = array_merge((array) self::$config["mirror"]["domain"], $config["mirror"]["domain"]);
                }
                if(is_array($config["badpath"]["rule"]) && count($config["badpath"]["rule"])) {
                    self::$config["badpath"]["rule"] = array_merge((array) self::$config["badpath"]["rule"], $config["badpath"]["rule"]);
                }
                if(is_array($config["cache"]["rule"]) && count($config["cache"]["rule"])) {
                    self::$config["cache"]["rule"] = array_merge((array) self::$config["cache"]["rule"], $config["cache"]["rule"]);
                }
                if(is_array($config["cache"]["priority"]) && count($config["cache"]["priority"])) {
                    self::$config["cache"]["priority"] = array_merge((array) self::$config["cache"]["priority"], $config["cache"]["priority"]);
                }
            });

            $this->loadSettings();
        }
    }
    private function loadSettings() {
        if(self::DEBUG) {
            register_shutdown_function(function() {
                $time = microtime(true) - CMS_START;
                if($time > 10000) {
                    Logs::write($_SERVER["REQUEST_URI"] . " - " . $time . "ms", "error_timeout");
                }
            });
        }
        /**
         * Performance Profiling
         */
        if(self::PROFILING === true) {
            define("FF_DB_MYSQLI_PROFILE", true);
            Debug::benchmark();
            register_shutdown_function(function() {
                Debug::benchmark(true);
                Debug::page(self::$page);
            });
        }

//        if(self::DEBUG === true) {
//          Debug::registerErrors();
//        }

    }
    private function get_page_properties($schema, $user_path = null) {
        $user_path                                          = ($user_path
            ? $user_path
            : $this->path_info
        );

        if(is_array($schema["pages"]) && count($schema["pages"])) {
            $settings_user_path                             = $user_path;
            if(isset($schema["pages"][$settings_user_path])) {
                $res                                        = $schema["pages"][$settings_user_path];
                $res["source"]                              = $settings_user_path;
            } elseif($settings_user_path == "/") {
                $res                                        = $schema["pages"]["*"];
                $res["source"]                              = $settings_user_path;
            } else {
                foreach($schema["pages"] AS $key => $page) {
                    if($page["router"] && preg_match("#" . $page["router"]["source"] . "#i", $settings_user_path)) {
                        $res                                = $page;
                        $res["source"]                      = $key;
                        break;
                    }
                }
                if(!$res) {
                    /*$arrSettings_path                       = explode("/", trim($settings_user_path, "/"));
                    if(isset($schema["pages"]["/" . $arrSettings_path[0]] )) {
                        $res                                = $schema["pages"]["/" . $arrSettings_path[0]];
                        $res["source"]                      = "/" . $arrSettings_path[0];
                    } elseif(isset($schema["pages"][$arrSettings_path[count($arrSettings_path) - 1]])) {
                        $res                                = $schema["pages"][$arrSettings_path[count($arrSettings_path) - 1]];
                    } else {*/
                    do {
                        if (isset($schema["pages"][$settings_user_path])) {
                            $res                        = $schema["pages"][$settings_user_path];
                            $res["source"]              = $settings_user_path;
                            break;
                        }
                    } while ($settings_user_path != DIRECTORY_SEPARATOR && ($settings_user_path = dirname($settings_user_path))); //todo: DS check
                    //}
                }

                if(!$res) {
                    $res                                        = $schema["pages"]["*"];
                    $res["source"]                              = $user_path;
                }
            }
        }

        /*
                if(strpos($user_path, $res["strip_path"]) === 0) {
                    $user_path                                      = substr($user_path, strlen($res["strip_path"]));
                    if(!$user_path)
                        $user_path                                  = "/";
                }*/

        /*if($resAlias) {
            $res["alias"]                                   = $resAlias["alias"];
            if($resAlias["redirect"] === false && $_SERVER["SERVER_ADDR"] != $_SERVER["REMOTE_ADDR"] && strpos($_SERVER["HTTP_HOST"], "www.") === 0) {
                $alias_flip                                 = array_flip($schema["alias"]); //fa redirect al dominio alias se il percorso e riservato ad un dominio alias
                if($alias_flip["/" . $arrSettings_path[0]]) {
                    $resAlias["redirect"]                   = $alias_flip["/" . $arrSettings_path[0]] . substr($user_path, strlen("/" . $arrSettings_path[0]));
                }
            }

            $res["redirect"]                                = $resAlias["redirect"];
        }*/

        $res["user_path"]                                   = (strpos($this->path_info, $res["strip_path"]) === 0
            ? substr($this->path_info, strlen($res["strip_path"]))
            : $this->path_info
        );
        if(!$res["user_path"])                              { $res["user_path"] = "/"; }

        $res["db_path"]                                     = $res["user_path"];
        $res["lang"]                                        = strtolower(self::$lang["code"]);
        $res["type"]                                        = pathinfo($res["user_path"], PATHINFO_EXTENSION);

        if(!$res["framework_css"])                          { $res["framework_css"] = $schema["page"]["/"]["framework_css"]; }
        if(!$res["font_icon"])                              { $res["font_icon"] = $schema["page"]["/"]["font_icon"]; }

        if(!$res["layer"])                                  { $res["layer"] = $schema["page"]["/"]["layer"]; }
        if(!$res["group"])                                  { $res["group"] = $res["name"]; }
        if($schema["rule"])                                 { $page["rule"] = $schema["rule"]; }

        return $res;
    }

    private function get_request_rules($rules, $page = null) {
        $matches                                            = null;
        $page                                               = ($page
            ? $page
            : self::$page
        );

        if($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest")
            $page["user_path"]                              = $page["strip_path"] . $page["user_path"];

        $request_path                                       = rtrim($page["alias"] . $page["user_path"], "/");
        if(!$request_path)                                  $request_path = "/";

        $last_split_path                                    = basename($request_path);
        if(isset($rules[$request_path])) {
            $matches                                        = $rules[$request_path];
        } elseif(isset($rules[$last_split_path])) {
            $matches                                        = $rules[$last_split_path];
        } else {
            do {
                $request_path                               = dirname($request_path);
                if(isset($rules[$request_path])) {
                    $matches                                = $rules[$request_path];
                    break;
                }
            } while($request_path != DIRECTORY_SEPARATOR);
        }

        if($matches["ext"] && is_array($rules[$matches["ext"]]))
            $matches                                        = array_merge_recursive($rules[$matches["ext"]], $matches);

        $matches["request_method"]                          = $_SERVER["REQUEST_METHOD"];
        if($page["primary"] && !$page["restricted"] && !$page["api"]) {
            $matches["exts"]                                = array(
                "ffl"   => '["filter"]["first_letter"]'
            , "pci" => '["filter"]["place"]["city"]["ID"]'
            , "ppi" => '["filter"]["place"]["city"]["ID_province"]'
            , "pri" => '["filter"]["place"]["city"]["ID_region"]'
            , "psi" => '["filter"]["place"]["city"]["ID_state"]'
            , "pcn" => '["filter"]["place"]["city"]["smart_url"]'
            , "ppn" => '["filter"]["place"]["city"]["province_smart_url"]'
            , "prn" => '["filter"]["place"]["region"]["smart_url"]'
            , "psn" => '["filter"]["place"]["state"]["smart_url"]'
            , "pps" => '["filter"]["place"]["city"]["province_sigle"]'
            , "pss" => '["filter"]["place"]["state"]["sigle"]'
            );
        }



        return $matches;
    }


    private function rewritePathInfo($alias = null) {
        $aliasname                                          = $alias[$_SERVER["HTTP_HOST"]];
        $this->orig_path_info                               = rtrim(rtrim($_SERVER["QUERY_STRING"]
            ? rtrim($_SERVER["REQUEST_URI"],  $_SERVER["QUERY_STRING"])
            : $_SERVER["REQUEST_URI"]
            , "?"), "/");
        if(!$this->orig_path_info)                          { $this->orig_path_info = "/"; }

        /*$path_info                                          = ($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest" && $_SERVER["HTTP_REFERER"]
                                                                ? $this->orig_path_info // parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH)
                                                                : $this->orig_path_info
                                                            );*/

        $arrPathInfo                                        = explode("/", trim($this->orig_path_info, "/"), "2");
        $locale                                             = $this->getLocale();
        if($locale["rev"]["lang"][$arrPathInfo[0]]) {
            $this->orig_path_info                           = "/" . $arrPathInfo[1];
        }

        $this->setLang($locale["rev"]["lang"][$arrPathInfo[0]]);

        if($aliasname) {
            if(strpos($this->orig_path_info, $aliasname . "/") === 0
                || $this->orig_path_info == $aliasname
            ) {
                if(is_array($_GET) && count($_GET))         { $query = "?" . http_build_query($_GET); }
                Cms::redirect($_SERVER["HTTP_HOST"] . substr($this->orig_path_info, strlen($aliasname)) . $query);
            }

            $this->root_path                                = $aliasname;
        }


        $path_info                                          = rtrim($this->root_path . $this->orig_path_info, "/");

        /*$path_info                                          = rtrim($this->root_path . ($path_info == "/index" || $path_info == "/"
                                                                ? ""
                                                                : $path_info
                                                            ), "/");*/
        if(!$path_info)                                     { $path_info = "/"; }

        $_SERVER["XHR_PATH_INFO"]                           = null;
        $_SERVER["ORIG_PATH_INFO"]                          = $this->orig_path_info;
        $_SERVER["PATH_INFO"]                               = $path_info;


        if($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest") {
            $_SERVER["XHR_PATH_INFO"]                       = rtrim($this->root_path . parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH), "/");
        }

        if($_SERVER["REMOTE_ADDR"] == $_SERVER["SERVER_ADDR"]) {
            if($_POST["pathinfo"]) {
                $_SERVER["PATH_INFO"]                       = rtrim($_POST["pathinfo"], "/");
                if(!$_SERVER["PATH_INFO"])                  { $_SERVER["PATH_INFO"] = "/"; }

                unset($_POST["pathinfo"]);
            }
            if($_POST["referer"]) {
                $_SERVER["HTTP_REFERER"]                    = $_POST["referer"];
                unset($_POST["referer"]);
            }
            if($_POST["agent"]) {
                $_SERVER["HTTP_USER_AGENT"]                 = $_POST["agent"];
                unset($_POST["agent"]);
            }
            if($_POST["cookie"]) {
                $_COOKIE                                    = $_POST["cookie"];
                unset($_POST["cookie"]);
            }

            if(vgCommon::DEBUG) {
                register_shutdown_function(function() {
                    $data["pathinfo"] = $_SERVER["PATH_INFO"];
                    $data["error"] = error_get_last();
                    $data["pid"] = getmypid();
                    $data["exTime"] = Debug::stopwatch();

                    Logs::write($data, "request_async_end");
                });
            }
        }

        $this->path_info                                    = $path_info;
    }

    private function checkLoadAvg() {
        $load = sys_getloadavg();
        if ($load[0] > 80) {
            Cms::errorDocument(503);
            Logs::write($_SERVER, "error_server_busy");
            exit;
        }
    }
    private function checkAllowedPath($rules = null, $path_info = null, $do_redirect = true)
    {
        $path_info                                          = ($path_info
            ? $path_info
            : $this->path_info
        );
        $matches                                            = array();
        if(is_array($rules) && count($rules)) {
            foreach($rules AS $source => $rule) {
                $src                                        = $this->regexp($source);
                if(preg_match($src, $path_info, $matches)) {
                    if(is_numeric($rule["destination"]) || ctype_digit($rule["destination"])) {
                        //sleep(mt_rand(floor($this::BADPATH_DELAY / 2), $this::BADPATH_DELAY));

                        http_response_code($rule["destination"]);

                        if($rule["log"]) {
                            Logs::write(array(
                                "RULE"          => $source
                                , "ACTION"      => $rule["destination"]
                                , "URL"         => $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]
                                , "REFERER"     => $_SERVER["HTTP_REFERER"]
                            ), "error_badpath");
                        }
                        exit;
                    } elseif($do_redirect && $rule["destination"]) {
                        $redirect                           = $rule["destination"];
                        if(strpos($src, "(") !== false && strpos($rule["destination"], "$") !== false) {
                            $redirect                       = preg_replace($src, $rule["destination"], $path_info);
                        }

                        Cms::redirect($_SERVER["HTTP_HOST"] . $redirect);
                    }
                }
            }
        }

        return $path_info;
    }

    private function regexp($rule) {
        return "#" . (strpos($rule, "[") === false && strpos($rule, "(") === false && strpos($rule, '$') === false
                ? str_replace("\*", "(.*)", preg_quote($rule, "#"))
                : $rule
            ) . "#i";
    }

    private function changeLang($code = null) {
        if(!$code)                                          $code = strtoupper($_GET["lang"]);

        if ($code) { //todo: da completare il redirect alla pagina attuale della lingua scelta
            //Cms::redirect(normalize_url($res["url"], HIDE_EXT, true, $lang, $prefix));
        }
    }

    private function setLang($code = null) {
        if(!$code) {
            $code                                           = Kernel::LANG_DEFAULT_CODE;
        }

        self::$lang                                         = self::$locale["lang"][$code];
        self::$lang["code"]                                 = $code;

        self::$locale["lang"]["current"] = self::$lang;

        self::$locale["country"]["current"] = self::$locale["country"][self::$lang["country"]];
        self::$locale["country"]["current"]["code"] = self::$lang["country"];

        //todo: trovare alternativa (tipo Cms::lang) per semplificare la programmazione
        define("LANGUAGE_INSET_TINY", self::$lang["tiny_code"]);
        define("LANGUAGE_INSET", self::$lang["code"]);
        define("LANGUAGE_INSET_ID", self::$lang["id"]);
        define("FF_LOCALE", self::$lang["code"]);
        define("FF_LOCALE_ID", self::$lang["id"]);

    }
    private function loadLocale() {
        if(is_array(self::$config["locale"])) {
            /**
             * Lang
             */
            if(is_array(self::$config["locale"]["lang"]) && count(self::$config["locale"]["lang"])) {
                foreach (self::$config["locale"]["lang"] AS $code => $lang) {
                    $attr                                               = Filemanager::getAttr($lang);
                    self::$locale["lang"][$code]                        = $attr;

                    self::$locale["rev"]["lang"][$attr["tiny_code"]]    = $code;
                    self::$locale["rev"]["key"][$attr["id"]]            = $code;
                }
            }
            /**
             * Country
             */
            if(is_array(self::$config["locale"]["country"]) && count(self::$config["locale"]["country"])) {
                foreach (self::$config["locale"]["country"] AS $code => $country) {
                    $attr                                               = Filemanager::getAttr($country);
                    self::$locale["country"][$code]                     = $attr;

                    self::$locale["rev"]["country"][$code]              = $attr["lang"];
                }
            }
        }

        unset(self::$config["locale"]);
    }
    public function run() {
        if($this->redirect) {
            //   Cms::redirect($this->redirect);
        }

        self::$session = Auth::check();
        if($_SERVER["REMOTE_ADDR"] != $_SERVER["SERVER_ADDR"]) {
            if(self::$page["primary"]
                && self::$page["trace"]
                && $_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest"
                && TRACE_VISITOR === true
            ) {
                //Jobs::async("/api/stats/trace/pageview", array("pathinfo" => $_SERVER["PATH_INFO"]));
                if(!function_exists('pcntl_fork')) {
                    Stats::getInstance("trace")->write();
                } else {
                    $pid = pcntl_fork();
                    if ($pid == -1) {
                        //die('could not fork');
                    } else if ($pid) {
                        // we are the parent, do nothing
                    } else {
                        // we are the child
                        Stats::getInstance("trace")->write();
                    }
                }
            }

            if(self::$page["cache"]) {
                // Cache::getInstance("page");
                Cache::getInstance("page")->run(self::$page, self::getRequest(), self::$session);
            }
        }

        /*$router = Cms::getInstance("router");
        $router->addRules($this->router);*/

        $response = $this->router->run($this->orig_path_info);

        //  if($response) {
       // $this->router->run("/restricted");
        //  }

        Cms::errorDocument(404);

        /*
        switch(self::$page["group"]) {
            case "public":
            case "service":
                $session = Auth::check();

                if(self::$page["cache"]) {
                   // Cache::getInstance("page");
                    Cache::getInstance("page")->run(self::$page, $this->getRequest(), $session);
                }

                //Cms::getInstance("process")->parse();
                $router->run("/restricted");
                break;
            case "restricted":
                $router->run("/restricted");
                break;
            case "api":
                Api::getInstance($this->path_info);
                break;
            case "login":

                //in teoria da mandare tutto ad un connettore esterno /auth

            case "activation":
                //break;
            case "recover":
                //break;
            case "registration":
                //break;
            default:
            $session = Auth::check();

            if(self::$page["cache"]) {
                Cache::getInstance("page")->run(self::$page, $this->getRequest(), $session);
            }

                $router->run("/restricted");
        }
*/
        exit;
    }

     private function loadEnv() {
        /**
         * ENV
         */
        if(is_array(self::$config["env"]) && count(self::$config["env"])) {
            $class_name                                             = strtolower(get_called_class());
            foreach (self::$config["env"] as $key => $params) {
                $attr                                               = Filemanager::getAttr($params);
                self::$env[$class_name][$key]                       = $attr["value"];
            }
        }
        unset(self::$config["env"]);
    }

    private function loadSchema() {
        /**
         * Snippet
         */
        if(is_array(self::$config["snippet"]) && count(self::$config["snippet"])) {
            foreach(self::$config["snippet"] AS $key => $snippet) {
                $attr                                           = Filemanager::getAttr($snippet);

                if($attr["params"])                             { $attr["params"] = explode(",", $attr["params"]); }

                self::$schema["snippet"][$key]                  = $attr;
            }
        }
        unset(self::$config["snippet"]);

        /**
         * Engine
         */
        if(is_array(self::$config["engine"]) && count(self::$config["engine"])) {
            foreach(self::$config["engine"] AS $key => $engine) {
                $attr                                                               = Filemanager::getAttr($engine);
                if($attr["path"]) {
                    self::$schema["engine"][$key]["destination"]                    = $attr["path"];
                    unset($attr["path"]);
                    unset($attr["instance"]);
                    unset($attr["method"]);
                    unset($attr["params"]);
                } else {
                    if($attr["obj"]) {
                        self::$schema["engine"][$key]["destination"]["obj"]        = $attr["obj"];
                        unset($attr["obj"]);
                    }
                    if($attr["instance"]) {
                        self::$schema["engine"][$key]["destination"]["instance"]   = $attr["instance"];
                        unset($attr["instance"]);
                    }
                    if($attr["method"]) {
                        self::$schema["engine"][$key]["destination"]["method"]     = $attr["method"];
                        unset($attr["method"]);
                    }
                    if($attr["params"]) {
                        self::$schema["engine"][$key]["destination"]["params"]     = explode(",", $attr["params"]);
                        unset($attr["params"]);
                    }
                }
                if($attr["priority"]) {
                    if(!is_array(self::$schema["engine"][$key]["destination"])) {
                        self::$schema["engine"][$key]["destination"]                = array(
                                                                                        "path" => self::$schema["engine"][$key]["destination"]
                                                                                    );
                    }
                    self::$schema["engine"][$key]["destination"]["priority"]        = $attr["priority"];
                    unset($attr["priority"]);
                }

                self::$schema["engine"][$key]["properties"]                         = $attr;
            }
        }
        unset(self::$config["engine"]);

        /**
         * Router
         */
        if(is_array(self::$config["router"]["rule"]) && count(self::$config["router"]["rule"])) {
            foreach(self::$config["router"]["rule"] AS $rule) {
                $attr                                                               = Filemanager::getAttr($rule);
                $key                                                                = $attr["source"];
                self::$schema["router"][$key]                                       = $attr;
                /*
                if($attr["path"]) {
                    self::$schema["router"][$key]["path"]                           = $attr["path"];
                } else {
                    if($attr["obj"]) {
                        self::$schema["router"][$key]["obj"]                        = $attr["obj"];
                    }
                    if($attr["instance"]) {
                        self::$schema["router"][$key]["instance"]                   = $attr["instance"];
                    }
                    if($attr["method"]) {
                        self::$schema["router"][$key]["method"]                     = $attr["method"];
                    }
                    if($attr["params"]) {
                        self::$schema["router"][$key]["params"]                     = explode(",", $attr["params"]);
                    }
                }
                if($attr["priority"]) {
                    self::$schema["router"][$key]["priority"]                       = $attr["priority"];
                }*/
            }
        }
        unset(self::$config["router"]);

        /**
         * Pages And Routing
         */
        if(is_array(self::$config["pages"]["page"]) && count(self::$config["pages"]["page"])) {
            foreach(self::$config["pages"]["page"] AS $page) {
                $attr                                           = Filemanager::getAttr($page);
                $key                                            = ($attr["source"]
                                                                    ? $attr["source"]
                                                                    : $attr["path"]
                                                                );
                if(!$key) {
                    continue;
                }
                unset($attr["source"]);
                if($key == "/") {
                    $key = "*";
                }


                if($attr["destination"]) {
                    self::$schema["router"][$key]               = $attr["destination"];
                    unset($attr["destination"]);
                } elseif($attr["engine"]) {
                    self::$schema["router"][$key]               = self::$schema["engine"][$attr["engine"]]["destination"];
                } else {
                    $path                                       = str_replace(array('(.*)', '(.+)', '^', '$', '*', '+'), "", $key);
                    $arrPath                                    = ltrim(explode("/", $path), "/");
                    self::$schema["router"][$key]               = (self::$schema["router"]["^/" . $arrPath[0] . "*"]
                                                                    ? self::$schema["router"]["^/" . $arrPath[0] . "*"]
                                                                    : self::$schema["router"]["*"]
                                                                );
                }

                if($attr["priority"]) {
                    if(!is_array(self::$schema["router"][$key])) {
                        self::$schema["router"][$key]           = array(
                                                                    "path" => self::$schema["router"][$key]
                                                                );
                    }
                    self::$schema["router"][$key]["priority"]   = $attr["priority"];
                    unset($attr["priority"]);
                }

                if(self::$schema["engine"][$attr["engine"]]["properties"]) {
                    $attr = array_replace(self::$schema["engine"][$attr["engine"]]["properties"], $attr);
                }
                //if(self::$schema["pages"][$key]) {
                    self::$schema["pages"][$key] = array_replace((array)self::$schema["pages"][$key], $attr);
                //} else {
                  //  self::$schema["pages"][$key] = $attr;
                //}
            }
        }
        unset(self::$config["pages"]);

        /**
         * Request
         */
        if(is_array(self::$config["request"]["page"]) && count(self::$config["request"]["page"])) {
            foreach (self::$config["request"]["page"] AS $request) {
                $page_attr                                      = Filemanager::getAttr($request);
                if(is_array($request["get"]) && count($request["get"])) {
                    foreach($request["get"] AS $get) {
                        $attr                                   = Filemanager::getAttr($get);
                        self::$schema["get"][$page_attr["path"]][$attr["name"]] = $attr["scope"];
                    }

                } else if($page_attr["get"]) {
                    self::$schema["get"][$page_attr["path"]] = true;
                }

            }
        }
        unset(self::$config["request"]);

        /**
         * Alias
         */
        if(is_array(self::$config["alias"]["domain"]) && count(self::$config["alias"]["domain"])) {
            foreach (self::$config["alias"]["domain"] AS $domain) {
                $attr                                           = Filemanager::getAttr($domain);
                self::$schema["alias"][$attr["name"]]           = $attr["path"];
            }
        }
        unset(self::$config["alias"]);

        /**
         * Proxy
         */
        if(is_array(self::$config["mirror"]["domain"]) && count(self::$config["mirror"]["domain"])) {
            foreach (self::$config["mirror"]["domain"] AS $domain) {
                $attr                                           = Filemanager::getAttr($domain);
                self::$schema["mirror"][$attr["name"]]           = $attr["proxy"];
            }
        }
        unset(self::$config["mirror"]);


        /**
         * BadPath
         */
        if(is_array(self::$config["badpath"]["rule"]) && count(self::$config["badpath"]["rule"])) {
            foreach(self::$config["badpath"]["rule"] AS $badpath) {
                $attr                                           = Filemanager::getAttr($badpath);
                $key                                            = $attr["source"];
                unset($attr["source"]);
                self::$schema["badpath"][$key]                  = $attr;
            }
        }
        unset(self::$config["badpath"]);


        /**
         * Cache
         */
        if(is_array(self::$config["cache"]["rule"]) && count(self::$config["cache"]["rule"])) {
            foreach (self::$config["cache"]["rule"] AS $cache) {
                $attr                                           = Filemanager::getAttr($cache);
                $key                                            = $attr["path"];
                unset($attr["path"]);
                self::$schema["cache"]["rule"][$key] = $attr;
            }
        }
        if(is_array(self::$config["cache"]["priority"]) && count(self::$config["cache"]["priority"])) {
            foreach (self::$config["cache"]["priority"] AS $cache) {
                $attr                                           = Filemanager::getAttr($cache);
                self::$schema["cache"]["priority"][] = $attr["path"];
            }
        }
        unset(self::$config["cache"]);
    }
}