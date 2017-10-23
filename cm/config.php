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

define("CM_MODULES_PATH", "/modules");
define("CM_MODULES_ROOT", __TOP_DIR__ . CM_MODULES_PATH);

define("CM_CONTENT_ROOT",  __PRJ_DIR__ . "/contents");

if(!defined("CM_SHOWFILES")) define("CM_SHOWFILES", "/cm/showfiles." . FF_PHP_EXT);