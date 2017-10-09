<?php
$permission = check_task_permission();
if($permission !== true && !(is_array($permission) && count($permission) && $permission[global_settings("MOD_TASK_GROUP_ADMIN")])) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "TimeTracking";
$oGrid->title = ffTemplate::_get_word_by_code("task_timetracking_title");
$oGrid->source_SQL = "SELECT tbl_src.*
					FROM (
							SELECT
								" . CM_TABLE_PREFIX . "mod_security_users.avatar AS users_avatar
								, " . CM_TABLE_PREFIX . "mod_security_users.username AS users_username
								, " . CM_TABLE_PREFIX . "mod_security_users.email AS users_email    
								, " . CM_TABLE_PREFIX . "mod_task_time_tracking.*
							FROM
								" . CM_TABLE_PREFIX . "mod_task_time_tracking
								INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users ON " . CM_TABLE_PREFIX . "mod_security_users.ID = " . CM_TABLE_PREFIX . "mod_task_time_tracking.owner
						) AS tbl_src
                    [WHERE]
                    [HAVING]
                    [ORDER]";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->record_id = "TimeTrackingModify";
$oGrid->resources[] = $oGrid->record_id;
$oGrid->addEvent("on_before_parse_row", "TimeTracking_on_before_parse_row");
$oGrid->order_default = "created";
$oGrid->display_new = true;
$oGrid->full_ajax = true;
$oGrid->use_search = false;

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "created";
$oField->base_type = "Timestamp";
$oField->container_class = "task-timetraking-created";
$oField->label = ffTemplate::_get_word_by_code("task_timetracking_created");
$oField->extended_type = "Date";
$oField->app_type = "Date";
$oField->order_dir = "DESC";
$oField->order_SQL = "created DESC, last_update DESC, ID DESC";
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "users_username";
$oField->container_class = "task-timetraking-username";
$oField->label = ffTemplate::_get_word_by_code("task_timetracking_username");    
$oField->encode_entities = false;
$oGrid->addContent($oField);  



$oField = ffField::factory($cm->oPage);
$oField->id = "duration";
$oField->base_type = "Timestamp";
$oField->container_class = "task-timetraking-duration";
$oField->label = ffTemplate::_get_word_by_code("task_timetracking_duration");
$oField->extended_type = "Time";
$oField->app_type = "Time";
$oGrid->addContent($oField);

$cm->oPage->addContent($oGrid);

function TimeTracking_on_before_parse_row($component) {
	
    if(isset($component->grid_fields["users_username"])) { 
    	if(check_function("get_user_avatar"))
    		$component->grid_fields["users_username"]->setValue(get_user_avatar($component->db[0]->getField("users_avatar", "Text", true), true, $component->db[0]->getField("users_email", "Text", true)) . $component->db[0]->getField("users_username", "Text", true));
    }	
}