<?php
/**
 *   VGallery: CMS based on FormsFramework
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
 * @package VGallery
 * @subpackage core
 * @author Alessandro Stucchi <wolfgan@gmail.com>
 * @copyright Copyright (c) 2004, Alessandro Stucchi
 * @license http://opensource.org/licenses/lgpl-3.0.html
 * @link https://bitbucket.org/cmsff/vgallery
 */

class Router
{
    const PRIORITY_TOP 			                            = 0;
    const PRIORITY_VERY_HIGH	                            = 1;
    const PRIORITY_HIGH			                            = 2;
    const PRIORITY_NORMAL 		                            = 3;
    const PRIORITY_LOW			                            = 4;
    const PRIORITY_VERY_LOW		                            = 5;
    const PRIORITY_BOTTOM 		                            = 6;
    const PRIORITY_DEFAULT 		                            = Router::PRIORITY_NORMAL;

    private static $cache                                   = array();

    private $alias                                          = array();
	private $rules                                          = array();
	private $sorted                                         = false;
    protected $controllers                                  = array(
    );
    protected $controllers_rev                              = null;

    public function __construct()
	{

        //$this->stats->setConfig($this->connectors, $this->services);
    }

    /**
     * @return null
     */
    public function getRouter()
	{
		return $this->router;
	}

	public function check($path, $source = null) {
        if(!self::$cache[$path . ":" . $source]) {
            self::$cache[$path . ":" . $source] = ($source
                ? preg_match($this->regexp($source), $path)
                : $this->find($path)
            );
        }

        return self::$cache[$path . ":" . $source];
    }
    public function run($path = null) {
        $rule                                               = $this->check($path);

        $destination                                        = $rule["destination"];
        if($destination) {
            if(is_array($destination)) {
                if($destination["obj"]) {
                    try {
                        $output = call_user_func_array(array(new $destination["obj"], $destination["method"]), $this->replaceMatches($rule["matches"], $destination["params"]));
                        if(!$output) {
                           /* $page = Cms::getInstance("page");
                            $page->addContent($output);
                            $page->run();*/
                            exit;
                        }
                    } catch (exception $exception) {
                        Cms::errorDocument(500);
                    }
                } else if(is_callable($destination["method"])) {
                    $output = call_user_func_array($destination["method"], $this->replaceMatches($rule["matches"], $destination["params"]));
                    if(!$output) {
                        exit;
                    }
                } elseif(class_exists($destination["method"])) {
                    $class = new ReflectionClass($destination["method"]);
                    $instance = $class->newInstanceArgs($this->replaceMatches($rule["matches"], $destination["params"]));

                    //return new $destination["func"](implode(",", $this->replaceMatches($rule["matches"], $destination["params"])));
                }
            } elseif($rule["redirect"]) {
                Cms::redirect($this->replaceMatches($rule["matches"], $destination), $rule["redirect"]);
            } elseif(is_numeric($destination) || ctype_digit($destination)) {
                Cms::errorDocument($destination);
            } else {
                Cms::execute($destination);
            }
        }
        return $output;
    }
	public function addRules($rules) {
        if(is_array($rules) && count($rules)) {
            foreach($rules AS $rule => $params) {

/*echo "\n-----------------\n";
    print_r($rule);
    echo "\ndest: \n";
    print_r($params);*/
                $this->addRule($rule, $params);

            }
        }

    }
    public function addRule($source, $destination, $priority = null, $redirect = false) {
        if(is_array($destination)) {
            $priority = ($priority
                ? $priority
                : $destination["priority"]
            );

            $redirect = ($redirect
                ? $redirect
                : $destination["redirect"]
            );

            unset($destination["priority"]);
            unset($destination["redirect"]);

            if($destination["path"]) {
                $destination = $destination["path"];
            }
        }

        /*if(is_array($source) && !$destination) {
            $destination                = $source["destination"];
            $priority                   = ($source["priority"]
                                            ? $source["priority"]
                                            : cmsRouter::PRIORITY_DEFAULT
                                        );
            $redirect                   = $source["redirect"];

            $source                     = $source["source"];
        }*/

        if($source && $destination) {
            $this->sorted               = false;
            $key                        = $this->getPriority($priority) . "-" . (9 - substr_count($source, "/")) . "-" . $source;

            $rule                       = array(
                "source"                => $source
                , "destination"         => $destination
                , "redirect"            => $redirect //null or redirect code
            );

            if(!$this->setAlias($source, $rule)) {
                $this->rules[$key]      = $rule;
            }
        }
    }
    private function setAlias($source, $rule) {
        $key = rtrim(rtrim(rtrim(ltrim($source, "^"), "$"), "*"), "/");
        if(strpos($key, "*") === false && strpos($key, "+") === false && strpos($key, "(") === false && strpos($key, "[") === false) {
            $this->alias[$key] = $rule;
            $this->alias[$key . "/"] = $rule;
            return true;
        }
    }

    private function getPriority($priority = null) {
        if($priority === null) {
            $priority = Router::PRIORITY_DEFAULT;
        }

        return (is_numeric($priority)
            ? $priority
            : constant("Router::PRIORITY_" . strtoupper($priority))
        );
    }
    private function replaceMatches($matches, $in) {
        if(is_array($matches)) {
            foreach($matches AS $key => $match) {
                if(is_array($in)) {
                    foreach($in AS $i => $value) {
                        $in[$i]         = str_replace('$' . $key, $match, $value);
                    }
                } else {
                    $in                 = str_replace('$' . $key, $match, $in);
                }
            }
        }

        return $in;
    }

    private function sort() {
        if(!$this->sorted) {
            ksort($this->rules);
            $this->sorted = true;
        }
    }
    private function find($path) {
        $matches = array();

        $res = $this->alias[$path];
        if($res) {
            $res["matches"] = array(
                rtrim($path)
            );
        } else {
            foreach ($this->alias AS $source => $rule) {
                if(strpos($path, $source) === 0) {
                    $res = $rule;
                    $res["matches"] = $matches;
                    break;
                }
            }
            if(!$res) {
                $this->sort();

                foreach ($this->rules as $source => $rule) {
                    if (preg_match($this->regexp($rule["source"]), $path, $matches)) {
                        $res = $rule;
                        $res["matches"] = $matches;
                        break;
                    }
                }
            }
        }

        return $res;
    }
    private function regexp($rule) {
        return "#" . (strpos($rule, "[") === false && strpos($rule, "^") === false && strpos($rule, "$") === false && strpos($rule, "(") === false
                ? str_replace("*", "(.*)", $rule)
                : $rule
            ) . "#i";
    }

}