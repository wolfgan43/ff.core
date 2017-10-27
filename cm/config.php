<?php
/**
 * @package ContentManager
 * @subpackage common
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

define("CM_ROOT", ffCommon_dirname(__FILE__));

if(!defined("CM_CACHE_PATH")) define("CM_CACHE_PATH", FF_DISK_PATH . "/cache");
define("CM_MODULES_PATH", "/modules");
define("CM_MODULES_ROOT", FF_DISK_PATH . CM_MODULES_PATH);

define("CM_CONTENT_ROOT",  FF_DISK_PATH . "/contents");

define("CM_SHOWFILES", FF_SITE_PATH . "/cm/showfiles." . FF_PHP_EXT);