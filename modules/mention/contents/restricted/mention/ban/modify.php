<?php
$permission = check_mention_permission();
if($permission !== true && !(is_array($permission) && count($permission) && $permission[global_settings("MOD_MENTION_GROUP_ADMIN")])) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$UserNID = get_session("UserNID");
$db = ffDB_Sql::factory();

if(isset($_REQUEST["frmAction"]) && isset($_REQUEST["setstatus"])) {
    $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_mention_ban
                    SET status = " . $db->toSql($_REQUEST["setstatus"]) . "
                    WHERE 
                    	" . CM_TABLE_PREFIX . "mod_mention_ban.ID = " . $db->toSql($_REQUEST["keys"]["ID"], "Number");
    $db->execute($sSQL);
    
    if($_REQUEST["XHR_DIALOG_ID"]) {
        die(ffCommon_jsonenc(array("url" => $_REQUEST["ret_url"], "close" => false, "refresh" => true), true));
    } else {
        ffRedirect($_REQUEST["ret_url"]);
    }
}

$oRecord = ffRecord::factory($cm->oPage);
/*
if(file_exists(FF_DISK_PATH . FF_THEME_DIR . "/" . FRONTEND_THEME . "/contents/clm/" . basename($cm->oPage->page_path) . "/ffRecord.html")) {
	$oRecord->template_dir = FF_DISK_PATH . FF_THEME_DIR . "/" . FRONTEND_THEME . "/contents/clm/" . basename($cm->oPage->page_path);
} elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR . "/" . FRONTEND_THEME . "/contents/clm/ffRecord.html")) {
	$oRecord->template_dir = FF_DISK_PATH . FF_THEME_DIR . "/" . FRONTEND_THEME . "/contents/clm";
}*/
$oRecord->id = "BanModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("mentions_ban_modify_title");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_mention_ban";
$oRecord->addEvent("on_done_action", "BanModify_on_done_action");
$oRecord->insert_additional_fields["status"] = new ffData("1", "Number");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = ffTemplate::_get_word_by_code("mentions_ban_modify_name");
$oField->required = true;
$oRecord->addContent($oField);  


$cm->oPage->addContent($oRecord);   

function BanModify_on_done_action($component, $action) {
	$db = ffDB_Sql::factory();

	switch($action) {
		case "insert":
		case "update":
			
			break;
		default:	
	}
}
?>
