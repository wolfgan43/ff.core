<?php
$permission = check_mention_permission();
if($permission !== true && !(is_array($permission) && count($permission) && $permission[global_settings("MOD_MENTION_GROUP_ADMIN")])) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

/*$oGrid = ffGrid::factory($cm->oPage, null, null, array("name" => "ffGrid_div"));

if(file_exists(FF_DISK_PATH . FF_THEME_DIR . "/" . FRONTEND_THEME . "/contents/clm/" . basename($cm->oPage->page_path) . "/ffGrid.html")) {
	$oGrid->template_dir = FF_DISK_PATH . FF_THEME_DIR . "/" . FRONTEND_THEME . "/contents/clm/" . basename($cm->oPage->page_path);
}*/
$UserNID = get_session("UserNID");
$db = ffDB_Sql::factory();

$oGrid = ffGrid::factory($cm->oPage);

$oGrid->full_ajax = true;
$oGrid->id = "Alerts";
$oGrid->title = ffTemplate::_get_word_by_code("alerts_title");
$oGrid->source_SQL = "SELECT
                            " . CM_TABLE_PREFIX . "mod_mention_alerts.*
                        FROM
                            " . CM_TABLE_PREFIX . "mod_mention_alerts
                        WHERE 1
                        [AND] [WHERE] 
                        [HAVING]
                        [ORDER]";

$oGrid->order_default = "name";
$oGrid->use_search = true;
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->record_id = "AlertsModify";
$oGrid->resources[] = $oGrid->record_id;
$oGrid->display_new = true;
$oGrid->display_edit_bt = false;
$oGrid->display_edit_url = true;
$oGrid->display_delete_bt = true;


// Campi chiave
$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

// Campi di ricerca

// Campi visualizzati
$oField = ffField::factory($cm->oPage);
$oField->id = "updated_at";
$oField->label = ffTemplate::_get_word_by_code("alerts_updated_at");
$oField->base_type = "Timestamp";
$oField->extended_type = "DateTime";
$oField->app_type = "DateTime";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = ffTemplate::_get_word_by_code("alerts_name");
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "path";
$oField->label = ffTemplate::_get_word_by_code("alerts_path");
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "stats_mentions_total";
$oField->label = ffTemplate::_get_word_by_code("alerts_stats_mentions_total");
$oField->base_type = "Number";
$oField->control_type = "label";
$oGrid->addContent($oField);  
		                      
$oField = ffField::factory($cm->oPage);
$oField->id = "stats_unread_mentions_total";
$oField->label = ffTemplate::_get_word_by_code("alerts_stats_unread_mentions_total");
$oField->base_type = "Number";
$oField->control_type = "label";
$oGrid->addContent($oField);  

$oField = ffField::factory($cm->oPage);
$oField->id = "stats_favorite_mentions_total";
$oField->label = ffTemplate::_get_word_by_code("alerts_stats_favorite_mentions_total");
$oField->base_type = "Number";
$oField->control_type = "label";
$oGrid->addContent($oField);  

$oField = ffField::factory($cm->oPage);
$oField->id = "stats_trashed_mentions_total";
$oField->label = ffTemplate::_get_word_by_code("alerts_stats_trashed_mentions_total");
$oField->base_type = "Number";
$oField->control_type = "label";
$oGrid->addContent($oField);  

$oField = ffField::factory($cm->oPage);
$oField->id = "stats_tasks_total";
$oField->label = ffTemplate::_get_word_by_code("alerts_stats_tasks_total");
$oField->base_type = "Number";
$oField->control_type = "label";
$oGrid->addContent($oField);  

if(strlen(global_settings("MOD_MENTION_CLIENT_ID")) && strlen(global_settings("MOD_MENTION_CLIENT_SECRET"))) {
    $oButton = ffButton::factory($cm->oPage);
    $oButton->id = "sync"; 
    $oButton->class = "noactivebuttons";
    $oButton->action_type = "submit";
    $oButton->aspect = "link";
    $oButton->label = ffTemplate::_get_word_by_code("mention_sync_bt");
    $cm->oPage->widgetLoad("dialog");
    $cm->oPage->widgets["dialog"]->process(
            "syncMention"
            , array(
                "tpl_id" => "Alerts"
                , "title" => ffTemplate::_get_word_by_code("mention_sync_title")
                , "url" => $cm->oPage->site_path . MOD_MENTION_SERVICES_PATH . "/sync-mention"
            )
            , $cm->oPage
        );
    $oButton->jsaction = "ff.ffPage.dialog.doOpen('syncMention')";
    $oGrid->addActionButtonHeader($oButton);  
} else {
	$oButton = ffButton::factory($cm->oPage);
	$oButton->id = "install";
	$oButton->class = "install";
	$oButton->action_type = "gotourl";
	$oButton->url = $cm->oPage->site_path . ffCommon_dirname($cm->oPage->page_path) . "/settings";
	$oButton->aspect = "link";
	$oButton->label = ffTemplate::_get_word_by_code("mention_install_bt");
	$oGrid->addActionButtonHeader($oButton);
}
$cm->oPage->addContent($oGrid);


?>