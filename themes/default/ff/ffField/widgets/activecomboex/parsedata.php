<?php
// ----------------------------------------
//  		FRAMEWORK FORMS vAlpha
//		      PLUGIN EXTRAS (activecomboex)
//			   by Samuele Diella
// ----------------------------------------

//require_once("../../../../../../ff/main.php");
//require_once("../../../../../../modules/security/common.php");

//if ($plgCfg_ActiveComboEX_UseOwnSession)
if (isset($_POST[session_name()]))
	session_id($_POST[session_name()]);
elseif (isset($_GET[session_name()]))
	session_id($_GET[session_name()]);
elseif (isset($_COOKIE[session_name()]))
	session_id($_COOKIE[session_name()]);
@session_start();
//else
//	mod_security_check_session();

$php_array = array();

$father_value = $_REQUEST["father_value"];
$data_src = $_REQUEST["data_src"];

$ff = get_session("ff");
$actex_sql = $ff["activecomboex"][$data_src]["sql"];
$actex_field = $ff["activecomboex"][$data_src]["field"];
$actex_main_db = $ff["activecomboex"][$data_src]["main_db"];

//$actex_sql = get_session("activecomboex_sql_" . $data_src);
//$actex_field = get_session("activecomboex_field_" . $data_src);
//$actex_main_db = get_session("activecomboex_main_db_" . $data_src);

//$bFindWhereStatement = preg_match("/\sWHERE\s/", $actex_sql);
$bFindWhereTag = preg_match("/\[WHERE\]/", $actex_sql);
$bFindWhereOptions = preg_match("/(\[AND\]|\[OR\])/", $actex_sql);

if ($actex_main_db)
	$db = mod_security_get_main_db();
else
	$db = ffDB_Sql::factory();

if (strlen($actex_field))
{
	$sSqlWhere = "";
	
	if (!$bFindWhereOptions)
		$sSqlWhere .= " WHERE ";
		
	$sSqlWhere .= " $actex_field = " . $db->toSql(new ffData($father_value));
	
	$sSQL = str_replace("[AND]", "AND", $actex_sql);
	$sSQL = str_replace("[OR]", "OR", $sSQL);
	$sSQL = str_replace("[WHERE]", $sSqlWhere, $sSQL);
    $sSQL = str_replace("[HAVING]", "", $sSQL); 
} else {
	$sSQL = str_replace("[AND]", "", $actex_sql);
	$sSQL = str_replace("[OR]", "", $sSQL);
	$sSQL = str_replace("[WHERE]", "", $sSQL);
    $sSQL = str_replace("[HAVING]", "", $sSQL); 
}

$sSQL = str_replace("[FATHER_VALUE]", $db->toSql($father_value), $sSQL); 

if (is_array($_REQUEST["ffActex_parent_data"]) && count($_REQUEST["ffActex_parent_data"]))
{
	foreach ($_REQUEST["ffActex_parent_data"] as $key => $value)
	{
		$sSQL = str_replace("[" . $key . "_VALUE]", $db->toSql($value), $sSQL); 
	}
	reset($_REQUEST["ffActex_parent_data"]);
}

$db->query($sSQL);
$i = -1;
if ($db->nextRecord())
{
	do
	{
		$i++;
		$php_array[$i]["value"] = ffCommon_charset_encode($db->getField($db->fields_names[0], "Text", true));
		$php_array[$i]["desc"] = ffCommon_charset_encode($db->getField($db->fields_names[1], "Text", true));
		//$php_array[$i]["value"] = ffCommon_charset_encode($db->getResult(NULL, 0)->getValue());
		//$php_array[$i]["desc"] = ffCommon_charset_encode($db->getResult(NULL, 1)->getValue());
	}
	while ($db->nextRecord());
}

if ($jsonp = $_REQUEST["XHR_JSONP"])
{
	$jsonp_pre = $jsonp . "(";
	$jsonp_post = ")";

	header("Content-type: application/javascript; charset=utf-8");
}
else
	header("Content-type: application/json; charset=utf-8");

echo $jsonp_pre . ffCommon_jsonenc($php_array) . $jsonp_post;
exit;
/*
header("Content-type: application/json; charset=utf-8");
die(json_encode($php_array));*/