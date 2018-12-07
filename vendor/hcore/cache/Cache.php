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
class Cache extends vgCommon {
    private static $singleton           = null;

    protected $services                 = array(
    );
    protected $controllers              = array(
    );
    protected $controllers_rev          = null;
    protected $connectors               = array(
    );

    public static function log($data, $filename = "log") //writeLog
    {
        if(DEBUG_LOG === true) {
            Logs::write($data, $filename);
        }
    }

    public static function set($key, $bucket = null) {
        switch(strtoupper($bucket)) {
            case "V":
            case "G":
            case "S":
            case "D":
            case "T":
            case "M":
                $bucket = "keys_" . strtoupper($bucket);
                break;
            default:
               // $key = $bucket . "-" . $key;
               // $bucket = "M";
        }
        Kernel::set($key, $bucket);
    }
    public static function get($bucket = null) {
        return ($bucket
            ? Kernel::get($bucket)
            : Kernel::get()
        );
    }


    public static function getInstance($service, $params = null)
    {
        if (self::$singleton === null)
            self::$singleton = new Cache();

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

    private function getService($service, $params = null) {
        $controller                                 = "cache" . ucfirst($service);
        if(!is_object($this->controllers[$service])) {
            $this->controllers[$service]            = ($this->controllers_rev[$controller]
                ? new $controller($this, $params)
                : false
            );
        }

        return $this->controllers[$service];
    }

}

