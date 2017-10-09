<?php
$permission = check_mention_permission();
if($permission !== true && !(is_array($permission) && count($permission) && $permission[global_settings("MOD_MENTION_GROUP_ADMIN")])) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$UserNID = get_session("UserNID");
$db = ffDB_Sql::factory();

if(isset($_REQUEST["frmAction"]) && isset($_REQUEST["setstatus"])) {
    $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_mention_mentions
                    SET status = " . $db->toSql($_REQUEST["setstatus"]) . "
                    WHERE 
                    	" . CM_TABLE_PREFIX . "mod_mention_mentions.ID = " . $db->toSql($_REQUEST["keys"]["ID"], "Number");
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
$oRecord->id = "MentionsModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("mentions_modify_title");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_mention_mentions";
$oRecord->addEvent("on_done_action", "MentionsModify_on_done_action");
$oRecord->insert_additional_fields["status"] = new ffData("1", "Number");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "published_at";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_published_at");
$oField->base_type = "Timestamp";
$oField->extended_type = "DateTime";
$oField->app_type = "DateTime";
$oField->widget = "datepicker";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_alert";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_alert");
$oField->extended_type = "Selection";
$oField->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_mention_alerts.ID
							, " . CM_TABLE_PREFIX . "mod_mention_alerts.name
						FROM " . CM_TABLE_PREFIX . "mod_mention_alerts
						WHERE 1
						ORDER BY " . CM_TABLE_PREFIX . "mod_mention_alerts.name";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "title";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_title");
$oField->required = true;
$oRecord->addContent($oField);  

$oField = ffField::factory($cm->oPage);
$oField->id = "description";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_description");
$oField->widget = "ckeditor";
$oRecord->addContent($oField);  

$oField = ffField::factory($cm->oPage);
$oField->id = "unique_id";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_unique_id");
$oRecord->addContent($oField);  
		                      
$oField = ffField::factory($cm->oPage);
$oField->id = "source_type";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_source_type");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "source_url";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_source_url");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "language_code";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_language_code");
$oRecord->addContent($oField);


$oField = ffField::factory($cm->oPage);
$oField->id = "favorite";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_favorite");
$oField->base_type = "Number";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "trashed";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_trashed");
$oField->base_type = "Number";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "trashed_set_by_user";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_trashed_set_by_user");
$oField->base_type = "Number";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "read";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_read");
$oField->base_type = "Number";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "tone";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_tone");
$oField->base_type = "Number";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "tone_score";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_tone_score");
$oField->base_type = "Number";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "relevance_score";
$oField->label = ffTemplate::_get_word_by_code("mentions_modify_relevance_score");
$oField->base_type = "Number";
$oRecord->addContent($oField);


$cm->oPage->addContent($oRecord);   

function MentionsModify_on_done_action($component, $action) {
	$db = ffDB_Sql::factory();

	switch($action) {
		case "insert":
		case "update":
			
			break;
		default:	
	}
}
?>
