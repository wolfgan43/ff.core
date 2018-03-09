<?php
/**
 * @package ContentManager
 * @subpackage common
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

define("CM_ROOT", ffCommon_dirname(__FILE__));

if(!defined("CM_CACHE_PATH")) define("CM_CACHE_PATH", FF_DISK_PATH . "/cache");
define("CM_MODULES_PATH", "/modules");
define("CM_MODULES_ROOT", __TOP_DIR__ . CM_MODULES_PATH);

define("CM_CONTENT_ROOT",  __PRJ_DIR__ . "/contents");

if(!defined("CM_SHOWFILES")) define("CM_SHOWFILES", "/cm/showfiles." . FF_PHP_EXT);

function cmAutoload() {
	static $loaded = false;
	if(!$loaded) {
		spl_autoload_register(function ($class) {
			switch ($class) {
				case "PHPMailer":
					require(__TOP_DIR__ . "/library/phpmailer/class.phpmailer." . FF_PHP_EXT);
					break;
				case "PHPExcel":
					require(__TOP_DIR__ . "/library/PHPexcel/class.PHPexcel." . FF_PHP_EXT);
					break;
				default:
					if(strpos($class, "cm") === 0) {
						require(__DIR__ . '/' . $class . '.' . FF_PHP_EXT);
					}
			}
		});
		$loaded = true;
	}
}