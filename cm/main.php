<?php
/**
 * @ignore
 * @package ContentManager
 * @subpackage common
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

if (!defined("FF_URLPARAMS")) define("FF_URLPARAMS", "__QUERY__,__DEBUG__,__CLEARCACHE__,__GENCACHE__,__NOCACHE__,__NOLAYOUT__,__SHOWCASCADELOADER__");

if (!defined("CM_MAIN_INIT"))
{
	// DEBUG expressions
	if (isset($_REQUEST["__FORCE_XHR__"]))
		$_SERVER["HTTP_X_REQUESTED_WITH"] = "XMLHttpRequest";

	// load forms php framework
/*	if (defined("CM_ONLY_INIT") && !defined("FF_ONLY_INIT"))
		define("FF_ONLY_INIT" , true);
*/	require(dirname(__FILE__) . "/../ff/main.php");

	// load configs..

	// ..main
	require(ffCommon_dirname(__FILE__) . "/config." . FF_PHP_EXT);

	// ..check
	if (!is_dir(CM_MODULES_ROOT))
		ffErrorHandler::raise("CM: missing modules dir: " . CM_MODULES_ROOT, E_USER_ERROR, null, get_defined_vars());

	if (!is_dir(CM_CONTENT_ROOT))
		ffErrorHandler::raise("CM: missing content dir: " . CM_CONTENT_ROOT, E_USER_ERROR, null, get_defined_vars());
	
	if (defined("FF_DEFAULT_THEME"))
		ffErrorHandler::raise("CM: avoid set FF_DEFAULT_THEME in config.php, use CM_DEFAULT_THEME in /conf/cm/config.php", E_USER_ERROR, null, get_defined_vars());

	// ..from conf
	if (@is_file(FF_DISK_PATH . "/conf/cm/config." . FF_PHP_EXT))
		require FF_DISK_PATH . "/conf/cm/config." . FF_PHP_EXT;
	require(ffCommon_dirname(__FILE__) . "/conf/config." . FF_PHP_EXT);

	// global config
	if (@is_file(FF_DISK_PATH . "/conf/config." . FF_PHP_EXT))
		require FF_DISK_PATH . "/conf/config." . FF_PHP_EXT;
	
	define("CM_MAIN_INIT", true);
	if (defined("CM_ONLY_INIT"))
		return;
}

// load classes
cmAutoload();

// load addons
require(ffCommon_dirname(__FILE__) . "/cm_cascadeloader." . FF_PHP_EXT);

// load commons..

// ..main
require(ffCommon_dirname(__FILE__) . "/common." . FF_PHP_EXT);

// ..from conf
if (@is_file(FF_DISK_PATH . "/conf/common." . FF_PHP_EXT))
	require FF_DISK_PATH . "/conf/common." . FF_PHP_EXT;
	
// run the Content Manager
$cm = cm::getInstance();
$ff->events->doEvent("cm::getInstance", array($cm));
$cm->process();

// done!