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

class Cms extends vgCommon
{
    static $singleton                   = null;

    protected $services                 = array(
                                        );
    protected $controllers              = array(
                                        );
    protected $controllers_rev          = null;
    protected $connectors               = array(
                                        );
    protected $struct					= array();

    private static $envs                = array(
        "cm" => "cm::env"
    );

    private $result                     = null;

    /**
     * @param null $services
     * @param null $params
     * @return Cms|null
     */
    public static function getInstance($service, $params = null)
	{
		if (self::$singleton === null)
			self::$singleton = new Cms();

		return self::$singleton->getService($service, $params);
	}

    /**
     * Cms constructor.
     * @param null $services
     * @param null $params
     */
    public function __construct() {
		$this->loadControllers(__DIR__);
    }
    public static function env($name = null, $value = null)
    {
        $env = parent::env($name, $value);
        if($env === null) {
            foreach(self::$envs AS $func) {
                $cm = cm::getInstance();

                if(is_callable($func)) {
                    $env = call_user_func($func, $name, $value);
                    if($env !== null)  {
                        break;
                    }
                }
            }
        }
        return $env;
    }
    /*public static function getSchema($type = null, $name = null, $default = null) {
        return self::schema($type, $name, $default);
    }*/
    public static function requestCapture($rules = null, $key = null) {
        return self::getRequest($rules, $key);
    }
    public static function parseWidgets($widgets, &$output = null) {
        if(!is_array($widgets)) {
            $widgets = self::extractWidgets($widgets);
        }
        //todo: da fare il parsing autonomamente senza framework
        //todo: da fare funzione per il parsing del css e js e altro in header
        if(is_array($widgets) && count($widgets)) {
            foreach($widgets AS $key => $widget) {
                if($output[$key]) {
                    continue;
                }

                $out_buffer = null;
                $class = ($widget["module"]
                    ? $widget["module"]
                    : $widget["name"]
                );

                $output[$key]           = $widget;
                if(is_dir(__DIR__ . '/../' . $class)) {
                    $class = ucfirst($class);
                    $out_buffer = $class::widget($widget["name"], $widget["params"]);
                } elseif($widget["module"]) {
                    $include = self::getDiskPath("modules") . "/" . $widget["module"] . "/applets/" . $widget["name"] . "/index." . self::PHP_EXT;
                } else {
                    $include = self::getDiskPath("applets") . "/" . $widget["name"] . "/index." . self::PHP_EXT;
                }

                if(is_file($include)) {
                    require $include;
                }
                $output[$key]["buffer"] = $out_buffer;
            }
        }

        return $output;
    }
    private static function extractWidgets($content) {

        return $widgets;
    }
    private function getService($service, $params = null) {
        $controller                                 = "cms" . ucfirst($service);
        if(!is_object($this->controllers[$service])) {
            $this->controllers[$service]            = ($this->controllers_rev[$controller]
                                                        ? new $controller($this, $params)
                                                        : false
                                                    );
        }
        return $this->controllers[$service];
    }



    /**
     * @param null $type
     * @param null $name
     * @param null $default
     * @return array|null
     */
    protected static function schema($type = null, $name = null, $default = null) {
        $schema = parent::schema(array(
            "locale" => self::getDiskPath("cache") . "/locale." . self::PHP_EXT
        ));

		if(is_array($default) && count($default)) {
			if($type && $name && is_array($schema[$type][$name]) && count($schema[$type][$name]) && is_array($default)) {
				return array_replace_recursive($default, $schema[$type][$name]);
			} elseif($type && is_array($schema[$type]) && count($schema[$type]) && is_array($default)) {
				return array_replace_recursive($default, $schema[$type]);
			} else {
				return array_replace_recursive($default, $schema);
			}
		}

		if($type)
			return $schema[$type];
		else
			return $schema;
	}

}
