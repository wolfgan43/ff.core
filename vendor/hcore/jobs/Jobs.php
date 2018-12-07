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

class Jobs extends vgCommon
{
	const SOCKET											= 100;	//Number soket
	const TIMEOUT											= 60; //Timeout Script (second)
	const ANTIFLOOD											= 4; //Block execution jobs Run (second)
	const DELAY_SCHEDULE    							    = 3600; //reschedule jobs with error (second)
	const REPEAT											= "1week";

	static $singleton                   					= null;

	protected $controllers              					= null;
	protected $services										= array(
																"nosql" 					=> null
																//, "sql"						=> null
																//, "fs" 						=> null
															);
    protected $connectors										= array(
																"sql"                       => array(
																	"host"          		=> null
																	, "username"    		=> null
																	, "password"   			=> null
																	, "name"       			=> null
																	, "prefix"				=> "TRACE_DATABASE_"
																	, "table"               => "trace_spooler"
																	, "key"                 => "ID"
																)
																, "nosql"                   => array(
																	"host"          		=> null
																	, "username"    		=> null
																	, "password"    		=> null
																	, "name"       			 => null
																	, "prefix"				=> "TRACE_MONGO_DATABASE_"
																	, "table"               => "cache_spooler"
																	, "key"                 => "ID"
																	)
																, "fs"                      => array(
																	"service"				=> "php"
																	, "path"                  => "/cache/spooler"
																	, "name"                => array("source", "params")
                                                                )
															);
    private $struct											= array(
    															"kid"						=> "number"
																, "pid"						=> "number"
																, "type"					=> "string" //request or script
																, "domain"					=> "string" //origin domain
																, "source"					=> "string"	//url request or abs path script
																, "referer"					=> "string"	//who call the service
																, "params"					=> "array"	//params
																, "request"					=> "array"	//params
																, "schedule"				=> "number" //schedule start job
																, "delay"					=> "number" //time in sec to repeat job
																, "repeat"					=> "string"	//range of time (1week, 1day, 1month ecc)
																, "priority"				=> "number" //number 0 to n where 0 is max priority

																, "created"					=> "number"	//timestamp creation
																, "last_update"				=> "number"	//timestamp last_update
																, "status"					=> "string"	//idle, running, completed
																, "server"					=> "array"
                                                                , "cookie"					=> "array"
																, "runned"					=> "number" //how many times is runned
                                                                , "response"                => "array"
																, "called"					=> "number" //how many times is called from user
															);
    private	$delay											= 10; //second
	private	$max_delay										= 3600;//3600; //second (1 hour)

    /**
     * @param $service
     * @return Jobs|null
     */
    public static function getInstance($service)
	{
		if (self::$singleton === null)
			self::$singleton = new Jobs($service);
		else {
			self::$singleton->service = $service;
		}
		return self::$singleton;
	}

    /**
     * Jobs constructor.
     * @param $service
     */
    public function __construct($service)
	{
		$this->service = $service;
        $this->setConfig($this->connectors, $this->services);

      //  $this->setConfig();
	}

    /**
     * @param $url
     * @param null $params
     * @return mixed
     */
    public static function api($url, $params = null)
	{
		if(DEBUG_PROFILING === true)                        { Debug::startWatch(); }

		$get 									            = $_GET;
		$request 								            = $_REQUEST;

		$_GET 									            = $params;
		$_REQUEST 								            = $_GET;
		//parse_str($params						, $_GET);

		$arrPath = explode("/", ltrim($url, "/api/"), 2);
		$include 								            = self::resolve_include_api("/" . $arrPath[1], "/api/" . $arrPath[0], "include");
		if($include) {
			$return["result"] 					            = self::getInclude($include);
		} else {
			$return["error"] 					            = "missing include path: ". "/" . $arrPath[1];
		}

		$_GET 									            = $get;
		$_REQUEST 								            = $request;

		if(DEBUG_PROFILING === true && is_array($return))   $return["exTime"] = Debug::stopWatch();

		return $return;
	}


	private static function resolve_include_api($path_info, $api_path, $out = null) {
        $real_path_info 				= null;
        $include 						= null;

        $arrPath 						= explode("/", trim($path_info, "/"));
        $target 						= $arrPath[0];
        $is_valid_path 				= preg_replace("/[^a-z0-9\/-]+/i", "", $path_info) == $path_info;

        if($api_path && $is_valid_path && is_file(self::$disk_path . $api_path . $path_info . "." . self::PHP_EXT)) {
            $real_path_info 			= $path_info;
            $include = self::$disk_path . $api_path . $path_info . "." . self::PHP_EXT;
        } elseif($api_path && is_file(self::$disk_path . $api_path . "/" . $target . "." . self::PHP_EXT)) {
            $real_path_info 			= substr($path_info, strlen($target) + 1);
            $include 					= self::$disk_path . $api_path . "/" . $target . "." . self::PHP_EXT;
        } else {
            foreach (glob(self::$disk_path . "/modules/*", GLOB_ONLYDIR) AS $module_path) {
                if($is_valid_path && is_file($module_path . $api_path . $path_info . "." . self::PHP_EXT)) {
                    $real_path_info 	= $path_info;
                    $include 			= $module_path . $api_path . $path_info . "." . self::PHP_EXT;
                    break;
                } elseif(is_file($module_path . $api_path . "/" . $target . "." . self::PHP_EXT)) {
                    $real_path_info 	= substr($path_info, strlen($target) + 1);
                    $include 			= $module_path . $api_path . "/" . $target . "." . self::PHP_EXT;
                    break;
                }
            }
        }

        if(!$real_path_info && is_file(self::$disk_path . "/conf/gallery" . $api_path . "/" . $target . "." . self::PHP_EXT)) {
            $real_path_info 			= substr($path_info, strlen($target) + 1);
            $include 					= self::$disk_path . "/conf/gallery" . $api_path . "/" . $target . "." . self::PHP_EXT;
        }

        $res = array(
            "real_path_info" 			=> $real_path_info
            , "include" 				=> $include
        );

        return ($out
            ? $res[$out]
            : $res
        );
    }

    /**
     * @param $url
     * @param null $params
     * @return mixed
     */
    public static function srv($url, $params = null)
	{
		if(DEBUG_PROFILING === true)                        { Debug::startWatch(); }

		$post 									            = $_POST;
		$request 								            = $_REQUEST;

		$_POST 									            = $params;
		$_REQUEST 								            = $_POST;
		//parse_str($params						, $_GET);


		if(strpos(ltrim($url, "/"), "srv/") === 0)
			$url 								            = substr($url, 4);

		$include 								            = self::resolve_include_service("/" . $url);

		if($include) {
			$return 				 			            = self::getInclude($include);
		} else {
			$return["error"] 					            = "missing include path: " . "/" . $url;
		}

		$_POST 									            = $post;
		$_REQUEST 								            = $request;

		if(DEBUG_PROFILING === true && is_array($return))   { $return["exTime"] = Debug::stopWatch(); }

		return $return;
	}

	private static function resolve_include_service($path_info) {
        $real_path_info 				= null;
        $include 						= null;

        if(is_file(self::$disk_path . "/contents/services" . $path_info . "." . self::PHP_EXT)) {
            $include 					= self::$disk_path . "/contents/services" . $path_info . "." . self::PHP_EXT;
        } elseif(is_file(self::$disk_path . "/contents/srv" . $path_info . "." . self::PHP_EXT)) {
            $include 					= self::$disk_path . "/contents/srv" . $path_info . "." . self::PHP_EXT;
        } elseif(is_file(self::$disk_path . "/applets/services" . $path_info . "." . self::PHP_EXT)) {
            $include 					= self::$disk_path . "/applets/services" . $path_info . "." . self::PHP_EXT;
        } elseif(is_file(self::$disk_path . "/applets/services" . $path_info . "/index." . self::PHP_EXT)) {
            $include 					= self::$disk_path . "/applets/services" . $path_info . "/index." . self::PHP_EXT;
        } elseif(is_file(self::$disk_path . "/applets/srv" . $path_info . "." . self::PHP_EXT)) {
            $include 					= self::$disk_path . "/applets/srv" . $path_info . "." . self::PHP_EXT;
        } else {
            if(strpos($path_info, "/", 1)) {
                $arrPathinfo = explode("/", ltrim($path_info, "/"), 2);
                if(is_file(self::$disk_path . "/modules/" . $arrPathinfo[0] . "/contents/services/" . $arrPathinfo[1] . "." . self::PHP_EXT)) {
                    $include = self::$disk_path . "/modules/" . $arrPathinfo[0] . "/contents/services/" . $arrPathinfo[1] . "." . self::PHP_EXT;
                }
            }
            if(!$include) {
                foreach (glob(self::$disk_path . "/modules/*", GLOB_ONLYDIR) AS $module_path) {
                    if (is_file($module_path . "/contents/services" . $path_info . "." . self::PHP_EXT)) {
                        $include = $module_path . "/contents/services" . $path_info . "." . self::PHP_EXT;
                        break;
                    } elseif (is_file($module_path . "/contents/srv" . $path_info . "." . self::PHP_EXT)) {
                        $include = $module_path . "/contents/srv" . $path_info . "." . self::PHP_EXT;
                        break;
                    }
                }
            }
        }

        if(!$include && is_file(self::$disk_path . "/conf/gallery/srv" . $path_info . "." . self::PHP_EXT)) {
            $include 					= self::$disk_path . "/conf/gallery" . $path_info . "." . self::PHP_EXT;
        }

        return $include;
    }
    /**
     * @param $url
     * @param null $params
     * @param string $method
     * @param null $response
     * @param null $server
     * @return array|mixed|null|object
     */
    public static function req($url, $params = null, $method = "POST", $response = null, $server = null) {
		if(DEBUG_PROFILING === true)                        { Debug::startWatch(); }

		//check_function("get_locale");
		if(!$server) {
            $server                                         = self::getServer();
        }

        if(strpos($url, "://") === false) {
            $url 								            = "http" . ($server["HTTPS"] ? "s" : "") . "://" . $server["HTTP_HOST"] . $url;
        }

        $data             		                            = $params;

		$agent                                              = ($data["agent"]
                                                                ? $data["agent"]
                                                                : $server["HTTP_USER_AGENT"]
                                                            );
        $cookie                                             = ($data["cookie"]
                                                                ? $data["cookie"]
                                                                : self::getCookie()
                                                            );

        unset($data["agent"]);
        unset($data["cookie"]);
        if(strpos($url, $server["HTTP_HOST"]) === false) {
            unset($cookie[session_name()]);
        }

        if(!$data["referer"]) {
            $data["referer"]          			            = $server["HTTP_REFERER"];
        }

        if(!$data["locale"]) {
            $data["locale"]             			        = (function_exists("get_locale")
                                                                ? get_locale()
                                                                : null
                                                            );
        }

		$res                                                = self::file_post_contents_with_headers($url, $data, $method, "30", $agent, $cookie);

        if($res["headers"]["response_code"] == "200") {
			$return 							            = json_decode($res["content"], true);
			if(json_last_error()) {
				$return["html"] 				            = $res["content"];
			}
		} else {
		    Cms::errorDocument($res["headers"]["response_code"]);
		}

		if($return && is_array($response)) {
			$return                                         = $return + $response;
		}

		if(self::DEBUG) {
			$return["exTime"] 					            = Debug::stopWatch();
			if(strpos($res["content"], "Fatal error") !== false) {
				$return["error"] 				            = strip_tags($res["content"]);
			} elseif(!$res["content"] && $res["headers"]["response_code"] == "200") {
                $return["error"]                            = "Possible Max Execution Time";
            }
		}

		return $return;
	}


    /**
     * @param $url
     * @param array $params
     * @param null $server
     * @return bool|Exception
     */
    public static function async($url, $params = array(), $server = null) {
        $errno                                              = null;
        $errstr                                             = null;
		if(!$server) {
			$server 							            = self::getServer();
        }

		if(strpos($url, "://") === false) {
			$url 								            = "http" . ($server["HTTPS"] ? "s" : "") . "://" . $server["HTTP_HOST"] . $url;
        }
        $url_info 								            = parse_url($url);

		$data             						            = $params;
		//if(!$data["pathinfo"] && $server["PATH_INFO"] != $url_info['path']) {
           // $data["pathinfo"]                               = $server["PATH_INFO"];
        //}
		$data["referer"]          			                = $server["HTTP_REFERER"];
		$data["locale"]             			            = (function_exists("get_locale")
                                                                ? get_locale()
                                                                : null
                                                            );
        $agent                                              = ($data["agent"]
                                                                ? $data["agent"]
                                                                : $server["HTTP_USER_AGENT"]
                                                            );
        $cookie                                             = ($data["cookie"]
                                                                ? $data["cookie"]
                                                                : self::getCookie()
                                                            );

        unset($data["agent"]);
        unset($data["cookie"]);
        if(strpos($url, $server["HTTP_HOST"]) === false) {
            unset($cookie[session_name()]);
        }

		$postdata 								            = http_build_query(
                                                                $data
                                                            );

		switch ($url_info['scheme']) {
			case 'https':
				$scheme 						            = 'ssl://';
				$port 							            = 443;
				break;
			case 'http':
			default:
				$scheme 						            = '';
				$port 							            = 80;
		}

        $out                                        = "POST ".$url_info['path']." HTTP/1.1\r\n";
        $out                                        .= "Host: ".$url_info['host']."\r\n";
        if($agent) {
            $out                                    .= "User-Agent: "  . $agent . "\r\n";
        }
        $out                                        .= "Content-Type: application/x-www-form-urlencoded\r\n";
        if(strpos($url, $server["HTTP_HOST"]) !== false
            && Auth::HTTP_USERNAME) {
            $out                                    .= "Authorization: Basic " . base64_encode(Auth::HTTP_USERNAME . ":" . Auth::HTTP_USERNAME) . "\r\n";
        }
        if($cookie) {
            $out                                    .= "Cookie: " . http_build_query($cookie, '', '; ') . "\r\n";
        }
        $out                                        .= "Content-Length: ".strlen($postdata)."\r\n";
        $out                                        .= "Connection: Close\r\n\r\n";
        if (isset($postdata)) {
            $out                                    .= $postdata;
        }

        if(self::DEBUG) {
            Logs::write(str_replace(array("&", "="), array("\n\t&", " = "), $out), "request_async");
        }

		try {
            /*$fp 								            = fsockopen($scheme . $url_info['host']
                                                                , $port
                                                                , $errno
                                                                , $errstr
                                                                , 30
                                                            );*/
            $fp 								            = stream_socket_client($scheme . $url_info['host'] . ":" . $port
                                                                , $errno
                                                                , $errstr
                                                                , 30
                                                                , STREAM_CLIENT_ASYNC_CONNECT
                                                            );
			if($fp) {
				fwrite($fp, $out);
				fclose($fp);
			}
		} catch (Exception $e) {
			$errstr                                         = $e;
			Logs::write("Error: " . $errstr, "request_async");
		}

        if(self::DEBUG) {
            Logs::write("\nResponse (" . getmypid() . "): " . ($errstr ? $errstr : "OK") . "\n------------------------------------------------------------------------------------------------------------------------", "request_async");
        }

		return ($errstr ? $errstr : false);
	}

    /**
     * @param $callback
     * @param null $schedule
     * @param null $repeat
     */
    public static function setScript($callback, $schedule = null, $repeat = null) {
	    static $source                                      = null;

	    if(!$source) {
	        if(strpos($callback, "/job/") === 0) {
                $source                                     = $callback;
            }
        } else {
            $params                                         = (is_callable($callback)
                                                                ? call_user_func($callback)
                                                                : (is_array($callback)
                                                                    ? $callback
                                                                    : array()
                                                                )
                                                            );

            Jobs::getInstance()->add($source, $params, $schedule, $repeat);

            $source                                         = null;
        }
    }

    /**
     * @param null $callback
     * @return null
     */
    public static function runScript($callback = null) {
	    static $return                                      = null;
        static $params                                      = null;

        if(is_callable($callback)) {
            $return                                         = call_user_func($callback, $params);
            $params                                         = null;
        } elseif($callback) {
            $params                                         = $callback;
        } elseif($callback === false) {
            $res                                            = $return;
            $return                                         = null;
        }

        return $res;
    }


    public static function file_post_contents($url, $data = null, $method = "POST", $timeout = 60, $user_agent = null, $cookie = null, $username = null, $password = null, $head = false) {
        if($username === null)      { $username = Auth::HTTP_USERNAME; }
        if($password === null)      { $password = Auth::HTTP_PASSWORD; }
        if(!$method)                { $method = "POST"; }

        $headers                    = array();
        if($method == "POST")       { $headers[] = "Content-type: application/x-www-form-urlencoded"; }
        if(strpos($url, $_SERVER["HTTP_HOST"]) !== false
            && $username)           { $headers[] = "Authorization: Basic " . base64_encode($username . ":" . $password); }

        if($cookie)                 { $headers[] = "Cookie: " . http_build_query($cookie, '', '; '); }

        $opts = array(
            'ssl' => array(
                "verify_peer" 		=> false,
                "verify_peer_name" 	=> false
            ),
            'http' => array(
                'method'  			=> $method,
                'timeout'  			=> $timeout
            )
        );
        if($user_agent)             { $opts['http']['user_agent'] = $user_agent; }
        if(count($headers))         { $opts['http']['header'] = implode("\r\n", $headers); }

        if($data) {
            $postdata 						= http_build_query($data);
            if ($method == "POST") {
                $opts["http"]["content"] 	= $postdata;
            } else {
                $url 						= $url . "?" . $postdata;
            }
        }
        $context = stream_context_create($opts);

        $res = file_get_contents($url, false, $context);
        if($head) {
            $res = array(
                "headers" => self::parseHeaders($http_response_header)
                , "content" => $res
            );
        }

        return $res;
    }

    public static function file_post_contents_with_headers($url, $data = null, $method = "POST", $timeout = 60, $user_agent = null, $cookie = null, $username = null, $password = null) {
        return self::file_post_contents($url, $data, $method, $timeout, $user_agent, $cookie, $username, $password, true);
    }

    private static function parseHeaders( $headers )
    {
        $head = array();
        foreach( $headers as $k=>$v )
        {
            $t = explode( ':', $v, 2 );
            if( isset( $t[1] ) )
                $head[ trim($t[0]) ] = trim( $t[1] );
            else
            {
                $head[] = $v;
                if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) )
                    $head['response_code'] = intval($out[1]);
            }
        }

        return $head;
    }

    /**
     * @param null $source
     * @param null $params
     * @param null $schedule
     * @param null $repeat
     * @param null $priority
     * @return bool|null
     */
    public function add($source = null, $params = null, $schedule = null, $repeat = null, $priority = null) {
		if(strpos($source, "/api") === 0) {
			$type 						                    = "api";
		} elseif(strpos($source, "srv/") === 0) {
			$type 						                    = "service";
		} elseif(strpos($source, "/srv/") === 0) {
			$type 						                    = "service";
			$source 					                    = ltrim($source, "/");
        } elseif(strpos($source, "/job/") === 0) {
            $type 						                    = "script";
		} elseif(substr($source, 0, 1) === "/") {
			$type 						                    = "request";
		} elseif(strpos($source, "://") !== false) {
			$type 						                    = "request";
		} elseif(strpos($source, "::") !== false) {
			$type 						                    = "obj";
		} else {
			$type 						                    = "func";
		}

        $status                                             = "idle";
        $created 						                    = time();
		$delay 							                    = $this->delay;
		$server 						                    = $this->getServer();

        if($schedule && !is_numeric($schedule))
            $schedule                                       = strtotime($schedule);

		if(!$schedule)
			$schedule 					                    = $created + $delay;
        elseif($schedule > time())
            $status                                         = "completed";

		$insert = array(
			"kid"						                    => ceil($created * microtime())
			, "type"					                    => $type
			, "domain"					                    => $this::DOMAIN
			, "source"					                    => $source
			, "referer"					                    => $server["HTTP_REFERER"]
			, "params"					                    => ($params ? $params : array())
			, "request"					                    => ($params ? $params : array())
			, "schedule"				                    => $schedule
			, "delay"					                    => $delay
			, "repeat"					                    => ($repeat === true
                                                                ? $this::REPEAT
                                                                : $repeat
                                                            )
			, "priority"				                    => $priority

			, "created"					                    => $created
			, "last_update"				                    => $created

			, "server"					                    => $server
			, "cookie"					                    => $this::getCookie()
			, "status"					                    => $status
			, "runned"					                    => 0
			, "called"					                    => 0
			, "pid"						                    => 0
		);

		$storage                                            = $this->getStorage();
		$job                                                = $storage->read(array(
                                                                "source" 		    => $insert["source"]
                                                                , "params"			=> $insert["params"]
                                                            ), array(
                                                                "kid"				=> true
                                                                , "status"			=> true
                                                                , "repeat"			=> true
                                                            ));

		if(is_array($job["result"]) && count($job["result"])) {
			if(!$job["result"][0]["repeat"] && $job["result"][0]["status"] != "running") {
				$storage->update(array(
						"called"		=> "++"
						, "last_update"	=> $created
						, "status"		=> "idle"
					)
					, array(
						"kid" 		    => $job["result"][0]["kid"]
					)
				);
			}
		} else {
			$storage->insert($insert);
		}

		return $this->getResult();
	}

    /**
     * @param bool $exec
     * @return bool|null
     */
    public function run($exec = false) {
		if((filemtime(__FILE__) + $this::ANTIFLOOD) >= time()) {
			return false;
		}
		touch(__FILE__, time());

		set_time_limit($this::TIMEOUT);

		$this->getStorage()->update(array(
			"status"						=> "idle"
        ), array(
			"status"						=> "completed"
			, "schedule<="					=> time()
            , "domain"                      => $this::DOMAIN
		));

		$storage 							= $this->getStorage();

		$jobs                               = $this->ps();
		$running 							= 0;
		if(is_array($jobs) && count($jobs)) {
			foreach($jobs AS $job) {
				if(($job["last_update"] + ($this::TIMEOUT * 2)) < time()) {
					@posix_kill($job["pid"], SIGKILL);
					$this->getStorage()->update(array(
						"status"			=> "error"
						, "last_update" 	=> time()
						, "pid"				=> "0"
					), array(
						"kid" 				=> $job["kid"]
					));
				} else {
					$running++;
				}
			}

		}
		$socket 							= $this::SOCKET - $running;

		if($socket > 0) {
			$jobs = $storage->read(array(
				"status" 				    => "idle"
                , "domain"                  => $this::DOMAIN
			), array(
				"kid"						=> true
				, "type" 					=> true
				, "source" 					=> true
				, "params" 					=> true
				, "request" 				=> true
                , "referer" 				=> true
                , "schedule"                => true
				, "server" 				    => true
				, "cookie" 				    => true
				, "repeat"					=> true
				, "delay"					=> true
				, "last_update"				=> true
			), array(
			    "priority"                  => "-1"
				, "schedule"				=> "1"
                , "last_update"             => "1"
				, "called"					=> "-1"
			), $socket);

            $res                            = array();
			if($exec && is_array($jobs["result"]) && count($jobs["result"])) {
                foreach($jobs["result"] AS $job) {
                    Jobs::async("/api/jobs/tools/exec", array("kid" => $job["kid"]));

                    $res[] = array(
                        "source"        => $job["source"]
                        , "domain"      => $job["domain"]
                        , "kid"         => $job["kid"]
                        , "referer"     => $job["referer"]
                    );
                }
            } else {
                $res = $jobs["result"];
            }

            if($socket - count($res) > 0) {
                $this->getStorage()->update(array(
                    "status"						=> "idle"
                    , "schedule"                    => time() + $this::DELAY_SCHEDULE
                ), array(
                    "status"						=> "error"
                    , "schedule<="					=> time()
                    , "domain"                      => $this::DOMAIN
                ));
                if($this::DEBUG) {
                    Logs::write("Re-Schedule Jobs with Errors", "error_jobs");
                }
            }

			return $res;
		} elseif($this::DEBUG) {
			Logs::write("no socket available", "jobs");
		}

		return $this->getResult();
	}

    /**
     * @param $where
     * @return mixed
     */
    private function getJob($where) {
        $storage                            = $this->getStorage();
        $jobs = $storage->read($where, array(
            "kid"						    => true
            , "type" 					    => true
            , "source" 					    => true
            , "params" 					    => true
            , "request" 				    => true
            , "referer" 				    => true
            , "schedule"                    => true
            , "cookie" 				        => true
            , "repeat"					    => true
            , "delay"					    => true
            , "last_update"				    => true
        ), null, 1);
        if(is_array($jobs["result"]) && count($jobs["result"])) {
            $job                            = array_shift($jobs["result"]);
        }

        return $job;
    }

    /**
     * @param $kid
     * @param bool $debug
     * @return null
     */
    public function forceRun($kid, $debug = false) {

        $job = $this->getJob(is_array($kid)
            ? $kid
            : array("kid" => $kid)
        );

        if(is_array($job)) {
            $funcController 	            = "controller_" . $job["type"];
            $log 					        = $this->$funcController($job, $debug);
        }

        return $this->isError();
    }


    public function ps($status = null, $domain = null) {
        $jobs = $this->getStorage()->read(array(
            "status" 						=> ($status
                                                ? $status
                                                : "running"
                                            )
            , "domain"                      => ($domain
                                                ? $domain
                                                : $this::DOMAIN
                                            )
        ), array(
            "kid"							=> true
            , "pid" 						=> true
            , "last_update"					=> true
        ));

        return $jobs["result"];
    }
    public function renew() {
        $this->getStorage()->update(array(
            "status"					=> "idle"
        ), array(
            "status" 						=> "error"
        ));
    }
    /**
     * @param $job
     */
    public function exec($job) {
        if($job && !is_array($job)) {
            $job                            = $this->getJob(array("kid" => $job));
        }

		if(is_array($job)) {
			$this->getStorage()->update(array(
				"status"					=> "running"
				, "pid"						=> getmypid()
				, "last_update" 			=> time()
			), array(
				"kid" 						=> $job["kid"]
			));

            $request                        = (count($job["request"])
                                                ? $job["request"]
                                                : $job["params"]
                                            );
			$request["referer"]             = $job["referer"];
			$request["cookie"]              = $job["cookie"];
			$request["agent"]               = $job["server"]["HTTP_USER_AGENT"];

            //$log                            = $this::req("/" . $job["source"], $request);

            $Controller 				    = "controller_" . $job["type"];

            $log 							= $this->$Controller($job);

			//after process
			$request 						= array();
			$status  						= "completed";
			$repeat 						= $job["repeat"];
			$now 							= time();

			if($log) {
				$log["result"] 				= json_encode($log["result"]);
				$log["created"] 			= $now;
				if($log["request"] != "null" && is_array($log["request"]) && count($log["request"])) {
					$request 				= $log["request"];
					$status 				= "idle";
				}

				if($log["repeat"] === true)
					$repeat 				= $this::REPEAT;
				else
					$repeat 				= (string) $log["repeat"];

				//} elseif($log === false) {
				//	$status 		= "idle";
			} elseif($log === null) {
			    Logs::write($job, "job_response_empty");
			}

			if($repeat && !count($request)) {
				//$status 					= "idle";
				$delay 						= $this->delay;
				$request					= $job["params"];
				if(is_numeric($repeat)) {
					$schedule 				= $now + $repeat;
				} else {
					$date 					= DateTime::createFromFormat('U', ($job["type"] == "script"
                                                ? $job["schedule"]
                                                : $now
                                            ));
					$date->modify("+" . $repeat);

					$schedule 				= $date->getTimestamp();
				}
			} else {
				$delay 						= $job["delay"] + ceil($log["exTime"]);
				if($delay > $this->max_delay)
					$delay 					= $this->max_delay;

				$schedule 					= $now + $delay;
			}

			unset($log["request"]);
            unset($job["params"]);
            unset($job["schedule"]);
            unset($job["repeat"]);
            unset($job["delay"]);
            $job["response"]                = $log;
            $job["status"]                  = ($job["response"]["error"]
                                                ? "error"
                                                : ($job["response"]["result"] && $job["response"]["result"] !== "null"
                                                    ? "ok"
                                                    : "warning"
                                                )
                                            );
            $this->getStorage("logs")->insert($job);

            $this->getStorage()->update(array(
				"last_update" 				=> $now
				, "status"					=> ($job["status"] == "error"
                                                ? "error"
                                                : $status
                                            )
				, "pid"						=> "0"
				, "delay"					=> $delay
				, "schedule"				=> $schedule
				, "repeat"					=> $repeat
				, "request"					=> $request
				, "runned"					=> "++"
			), array(
				"kid" 						=> $job["kid"]
			));

            $error                          = implode(", ", (array) $job["response"]["error"]);
		} else {
			$error                          = $this->isError("Invalid Job");
		}

        return array(
            "status" => ($error
                ? "500"
                : "200"
            )
            , "error" => $error
        );
	}

    /**
     * @param $schedule
     * @return bool|null
     */
    public function dump($schedule) {
		return $this->getResult();
	}

    /**
     * @param $type
     * @param null $config
     * @return array|mixed|null
     */
    public function getConfig($type, $config = null) {
		if(!$config)
			$config                         = $this->services[$type]["connector"];

		if(is_array($config))
			$config                         = array_replace($this->connectors[$type], array_filter($config));
		else
			$config                         = $this->connectors[$type];

		return $config;
	}

    /**
     *
     */
    public function getScripts() {
        $it                                 = new FilesystemIterator($this::getDiskPath("asset") . "/jobs");
        foreach ($it as $fileinfo) {
            if($fileinfo->getATime() > $fileinfo->getMTime() )
                continue;

            $filename                       = $fileinfo->getFilename();

            $this::setScript("/job/" . $filename);
            require_once($this::getDiskPath("job") . "/" . $filename);
        }
    }

    /**
     * @param $include
     * @param null $cm
     * @return mixed
     */
    private static function getInclude($include) {
        if(class_exists("cm")) {
            $cm = cm::getInstance();
            $cm->oPage->output_buffer       = "";
            require($include);

            if (is_array($cm->oPage->output_buffer))
                $return                     = $cm->oPage->output_buffer;
            elseif (strlen($cm->oPage->output_buffer)) {
                $return["html"]             = $cm->oPage->output_buffer;
            }
        } else {
            require($include);
        }

        return $return;
    }

    /**
     * @return array
     */
    private static function getServer() {
		$server = array();

		if(is_array($_SERVER) && count($_SERVER)) {
			foreach($_SERVER AS $key => $value) {
				switch ($key) {
					case "HTTP_COOKIE";
					case "REMOTE_ADDR";
					case "REMOTE_PORT";
					case "REQUEST_TIME_FLOAT";
					case "REQUEST_TIME";

					break;
					default:
						$server[$key]       = $value;
				}
			}
		}

		return $server;
	}

    private static function getCookie() {
        return $_COOKIE;
    }

    /**
     * @param $job
     * @return mixed
     */
    private function controller_api($job) {
        return (class_exists("cm")
            ? $this::api($job["source"], count($job["request"])
                ? $job["request"]
                : $job["params"]
            )
            : $this::req("/" . $job["source"], count($job["request"])
                ? $job["request"]
                : $job["params"]
            )
        );
	}

    /**
     * @param $job
     * @return mixed
     */
    private function controller_service($job) {
        return (class_exists("cm")
            ? $this::srv($job["source"], count($job["request"])
                ? $job["request"]
                : $job["params"]
            )
            : $this::req("/" . $job["source"], count($job["request"])
                ? $job["request"]
                : $job["params"]
            )
        );
	}

    /**
     * @param $job
     * @return array|mixed|null|object
     */
    private function controller_request($job) {
        return $this::req($job["source"], count($job["request"])
            ? $job["request"]
            : $job["params"]
        );
	}

    /**
     * @param $job
     * @param bool $debug
     * @return null
     */
    private function controller_script($job, $debug = false) {
        $return                             = null;
	    $params                             = ($debug //da aggiungee i parametri di ingresso
                                                ? $debug
                                                : $job["request"]
                                            );
        $script                             = $this::getDiskPath("job") . "/" . basename($job["source"]);
        $output                             = exec("php -l " . addslashes($script));

        if(strpos($output, "No syntax errors") === 0) {
            Jobs::runScript($params);
            require($script);
            if(!$return)
                $return                     = Jobs::runScript(false);
        } else {
            $this->isError("syntax errors into script");
            Logs::write($output, "error_jobs");
        }
        return $return;
    }

    /**
     * @param $job
     */
    private function controller_func($job) {
//todo: da fare la call di una funzione
	}

    /**
     * @param $job
     */
    private function controller_obj($job) {
//todo: da fare la call di un oggetto
	}

    /**
     * @return null|Storage
     */
    private function getStorage($table_suffix = null)
	{
        $services = $this->services;
	    if($table_suffix) {
	        foreach($services AS &$controller) {
                $controller["connector"]["table"] .= "_" . $table_suffix;
            }
        }

		return Storage::getInstance($services, array(
			"struct" => $this->struct
		));
	}
	/**
	 * @param null $service
	 */
	/*private function controller()
	{
		$type                                                           	= $this->service;

		if(!$this->driver[$type]) {
			$controller                                                 	= "jobs" . ucfirst($type);
			require_once($this->getAbsPathPHP("/jobs/services/" . $type, true));

			$driver                                                     	= new $controller($this);
			//$db                                                         	= $driver->getDevice();

			$this->driver[$type] 											= $driver;
		}

		return $this->driver[$type];
	}*/
	/*private function setConfig()
	{
		foreach($this->connectors AS $name => $connector) {
			if(!$connector["name"]) {
				$prefix = ($connector["prefix"] && defined($connector["prefix"] . "NAME") && constant($connector["prefix"] . "NAME")
					? $connector["prefix"]
					: $this::getPrefix($name)
				);

				if (is_file($this->getAbsPathPHP("/config")))
				{
					require_once($this->getAbsPathPHP("/config"));

					$this->connectors[$name]["host"] = (defined($prefix . "HOST")
						? constant($prefix . "HOST")
						: "localhost"
					);
					$this->connectors[$name]["name"] = (defined($prefix . "NAME")
						? constant($prefix . "NAME")
						:  ""
					);
					$this->connectors[$name]["username"] = (defined($prefix . "USER")
						? constant($prefix . "USER")
						: ""
					);
					$this->connectors[$name]["password"] = (defined($prefix . "PASSWORD")
						? constant($prefix . "PASSWORD")
						: ""
					);

				}
			}
		}

		foreach($this->services AS $type => $data)
		{
			if(!$data)
			{
				$this->services[$type] = array(
					"service" => $this->connectors[$type]["service"]
					, "connector" => $this->connectors[$type]
				);
			}
		}


	}*/



    /**
     * @return bool|null
     */
    private function getResult()
	{
		return ($this->isError()
			? $this->isError()
			: false
		);
	}
}

