<?php
/**
 * @ignore
 * @package theme_default
 * @subpackage common
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

if(!defined("FF_DISK_PATH")) // very old bad stuffs, to maintain backward compatibility
{
	$basedir = ffCommon_dirname(ffCommon_dirname(ffCommon_dirname(ffCommon_dirname(ffCommon_dirname(__FILE__)))));
	require_once($basedir . "/ff/main.php");
}

// get globals & parameters
$type = $_REQUEST["type"];
$title = $_REQUEST["title"];
$message = $_REQUEST["message"];
$cancelurl = $_REQUEST["cancelurl"];
$confirmurl = $_REQUEST["confirmurl"];
$dlg_site_path = $_REQUEST["dlg_site_path"];

// get action
$choiche = $_REQUEST["choiche"];

$tpl = ffTemplate::factory(ffCommon_dirname(__FILE__));
$tpl->load_file("dialog.html", "main");

$tpl->set_var("message", $message);
$tpl->set_var("confirmurl", $confirmurl);
$tpl->set_var("cancelurl", $cancelurl);

switch (strtolower($type))
{
	case "yesno":
		$tpl->set_var("OKOnly", "");
        $tpl->set_var("CancelOnly", "");
		$tpl->parse("YesNo", false);
		if ($choiche) 
		{
			if ($choiche == "yes")
				ffRedirect($confirmurl);
			else
				ffRedirect($cancelurl);
		}
		break;

	case "okonly":
		$tpl->set_var("YesNo", "");
        $tpl->set_var("CancelOnly", "");
		$tpl->parse("OKOnly", false);
		if (strlen($choiche))
		{
			ffRedirect($confirmurl);
		}
		break;

    case "cancelonly":
        $tpl->set_var("YesNo", "");
        $tpl->parse("CancelOnly", false);
        $tpl->set_var("OKOnly", "");
        if (strlen($choiche))
        {
            ffRedirect($cancelurl);
        }
        break;
		
	default:
		$tpl->set_var("YesNo", "");
        $tpl->set_var("CancelOnly", "");
		$tpl->set_var("OKOnly", "");
}

$ff = ffGlobals::getInstance("ff");

$res = $ff->events->doEvent("dialog_onProcess", array($tpl));
$rc = end($res);
if ($rc === null)
	$tpl->pparse("main", false);
