<?php
/**
 * @ignore
 * @package ContentManager
 * @subpackage common
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */
if (!defined("CM_MAIN_INIT"))
{
	// DEBUG expressions
	if (isset($_REQUEST["__FORCE_XHR__"]))
		$_SERVER["HTTP_X_REQUESTED_WITH"] = "XMLHttpRequest";

	// load forms php framework
/*	if (defined("CM_ONLY_INIT") && !defined("FF_ONLY_INIT"))
		define("FF_ONLY_INIT" , true);
*/	require(__DIR__ . "/../ff/main.php");

	// load configs..

	// ..main
	require(__DIR__ . "/config." . FF_PHP_EXT);

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
	require(__DIR__ . "/conf/config." . FF_PHP_EXT);

	// global config
	if (@is_file(FF_DISK_PATH . "/conf/config." . FF_PHP_EXT))
		require FF_DISK_PATH . "/conf/config." . FF_PHP_EXT;

	define("CM_MAIN_INIT", true);
	if (defined("CM_ONLY_INIT"))
		return;
}
/*elseif (defined("CM_ONLY_INIT")) 
{
	require(__DIR__ . "/../ff/main.php");
}*/
spl_autoload_register(function ($class) {
    switch ($class) {
        case "PHPMailer":
            require(__TOP_DIR__ . "/library/phpmailer/class.phpmailer." . FF_PHP_EXT);
            break;
        case "PHPExcel":
            require(__TOP_DIR__ . "/library/PHPexcel/class.PHPexcel." . FF_PHP_EXT);
            break;
        default:
            if (strpos($class, "cm") === 0) {
                require(__DIR__ . '/' . $class . '.' . FF_PHP_EXT);
            }
    }

});

// load classes
//require(__DIR__ . "/cmRouter." . FF_PHP_EXT);
//require(__DIR__ . "/cm." . FF_PHP_EXT);

// load addons
require(__DIR__ . "/cm_cascadeloader." . FF_PHP_EXT);

// load commons..

// ..main
require(__DIR__ . "/common." . FF_PHP_EXT);

// ..from conf
if (@is_file(FF_DISK_PATH . "/conf/common." . FF_PHP_EXT))
	require FF_DISK_PATH . "/conf/common." . FF_PHP_EXT;
	
// run the Content Manager
$cm = cm::getInstance();
$cm->process();

// done!