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


$alert_smart_url = basename($cm->real_path_info);
$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_mention_alerts.ID 
		FROM " . CM_TABLE_PREFIX . "mod_mention_alerts
		WHERE " . CM_TABLE_PREFIX . "mod_mention_alerts.smart_url = " . $db->toSql($alert_smart_url);
$db->query($sSQL);
if($db->nextRecord()) {
	$ID_alert = $db->getField("ID", "Number", true);
}


$oGrid = ffGrid::factory($cm->oPage);

$oGrid->full_ajax = true;
$oGrid->id = "Mentions";
$oGrid->title = ffTemplate::_get_word_by_code("mentions_title");
$oGrid->source_SQL = "SELECT
                            " . CM_TABLE_PREFIX . "mod_mention_mentions.*
                        FROM
                            " . CM_TABLE_PREFIX . "mod_mention_mentions
                        WHERE 1
                        [AND] [WHERE] 
                        [HAVING]
                        [ORDER]";

$oGrid->order_default = "published_at";
$oGrid->use_search = true;
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->record_id = "MentionsModify";
$oGrid->resources[] = $oGrid->record_id;
$oGrid->display_new = true;
$oGrid->display_edit_bt = false;
$oGrid->display_edit_url = true;
$oGrid->display_delete_bt = true;
$oGrid->addEvent("on_before_parse_row", "Mentions_on_before_parse_row");


// Campi chiave
$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

// Campi di ricerca

// Campi visualizzati
$oField = ffField::factory($cm->oPage);
$oField->id = "published_at";
$oField->label = ffTemplate::_get_word_by_code("mentions_published_at");
$oField->base_type = "Timestamp";
$oField->extended_type = "DateTime";
$oField->app_type = "DateTime";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_alert";
$oField->label = ffTemplate::_get_word_by_code("mentions_alert");
$oField->extended_type = "Selection";
$oField->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_mention_alerts.ID
							, " . CM_TABLE_PREFIX . "mod_mention_alerts.name
						FROM " . CM_TABLE_PREFIX . "mod_mention_alerts
						WHERE 1
						ORDER BY " . CM_TABLE_PREFIX . "mod_mention_alerts.name";
$oGrid->addContent($oField);


$oField = ffField::factory($cm->oPage);
$oField->id = "title";
$oField->label = ffTemplate::_get_word_by_code("mentions_title");
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "unique_id";
$oField->label = ffTemplate::_get_word_by_code("mentions_unique_id");
$oGrid->addContent($oField);  
		                      
$oField = ffField::factory($cm->oPage);
$oField->id = "source_type";
$oField->label = ffTemplate::_get_word_by_code("mentions_source_type");
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "source_url";
$oField->label = ffTemplate::_get_word_by_code("mentions_source_url");
$oGrid->addContent($oField);


$oField = ffField::factory($cm->oPage);
$oField->id = "ID_alert";
$oField->container_class = "alert";
$oField->label = ffTemplate::_get_word_by_code("mentions_alert");
$oField->extended_type = "Selection";
$oField->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_mention_alerts.ID
							, " . CM_TABLE_PREFIX . "mod_mention_alerts.name
						FROM " . CM_TABLE_PREFIX . "mod_mention_alerts
						WHERE 1
						ORDER BY " . CM_TABLE_PREFIX . "mod_mention_alerts.name";
$oField->multi_select_one_label	= ffTemplate::_get_word_by_code("mentions_all_alert");
$oField->default_value = new ffData($ID_alert, "Number");
$oField->encode_entities = false;
$oGrid->addSearchField($oField);


$oButton = ffButton::factory($cm->oPage);
$oButton->id = "status";
$oButton->action_type = "gotourl";
$oButton->url = "";
$oButton->aspect = "link";
$oButton->template_file = "ffButton_link_fixed.html";                           
$oGrid->addGridButton($oButton);

$cm->oPage->addContent($oGrid);

function Mentions_on_before_parse_row($component) {
    $cm = cm::getInstance();
    
    if(isset($component->grid_buttons["status"])) {
	    if($component->db[0]->getField("status", "Number", true)) {
	        $component->grid_buttons["status"]->label = ffTemplate::_get_word_by_code("remove_to_status");
	        $component->grid_buttons["status"]->class = "icon ico-visible";
	        $component->grid_buttons["status"]->action_type = "submit"; 
	        $component->grid_buttons["status"]->form_action_url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=0&ret_url=" . urlencode($component->parent[0]->getRequestUri());
	        if($_REQUEST["XHR_DIALOG_ID"]) {
	            $component->grid_buttons["status"]->jsaction = "javascript:ff.ffPage.dialog.doRequest('[[XHR_DIALOG_ID]]', {'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
	        } else {
	            $component->grid_buttons["status"]->jsaction = "javascript:ff.ajax.doRequest({'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
	            //$component->grid_buttons["status"]->action_type = "gotourl";
	            //$component->grid_buttons["status"]->url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=0&frmAction=setstatus&ret_url=" . urlencode($component->parent[0]->getRequestUri());
			}   
	    } else {
	        $component->grid_buttons["status"]->label = ffTemplate::_get_word_by_code("add_to_status");
	        $component->grid_buttons["status"]->class = "icon ico-notvisible";
	        $component->grid_buttons["status"]->action_type = "submit";     
	        $component->grid_buttons["status"]->form_action_url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=1&ret_url=" . urlencode($component->parent[0]->getRequestUri());
	        if($_REQUEST["XHR_DIALOG_ID"]) {
	            $component->grid_buttons["status"]->jsaction = "javascript:ff.ffPage.dialog.doRequest('[[XHR_DIALOG_ID]]', {'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
	        } else {
	        	$component->grid_buttons["status"]->jsaction = "javascript:ff.ajax.doRequest({'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
	            //$component->grid_buttons["status"]->action_type = "gotourl";
	            //$component->grid_buttons["status"]->url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=1&frmAction=setstatus&ret_url=" . urlencode($component->parent[0]->getRequestUri());
			}    
	    }
	}
}
?>