<?php
$permission = check_task_permission();
if($permission !== true && !(is_array($permission) && count($permission) && $permission[global_settings("MOD_TASK_GROUP_ADMIN")])) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$db = ffDB_Sql::factory();

if(isset($_REQUEST["frmAction"]) && isset($_REQUEST["setstatus"])) {
    $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_task
                    SET status = " . $db->toSql($_REQUEST["setstatus"]) . "
                    WHERE 
                        " . CM_TABLE_PREFIX . "mod_task.ID = " . $db->toSql($_REQUEST["keys"]["ID"], "Number");
    $db->execute($sSQL);
    if($_REQUEST["XHR_DIALOG_ID"]) {
        die(ffCommon_jsonenc(array("url" => $_REQUEST["ret_url"], "close" => false, "refresh" => true), true));
    } else {
        ffRedirect($_REQUEST["ret_url"]);
    }
}

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "TimeTrackingModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("task_timetracking_modify_title");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_task_time_tracking";
$oRecord->insert_additional_fields["created"] = new ffData(time(), "Number");
$oRecord->insert_additional_fields["last_update"] = new ffData(time(), "Number");
$oRecord->insert_additional_fields["owner"] = new ffData(get_session("UserNID"), "Number");
$oRecord->update_additional_fields["last_update"] = new ffData(time(), "Number");

   
//$oRecord->widget = "disclosures";
//$oRecord->widget_discl_enable = true;

$oRecord->addEvent("on_do_action", "TimeTrackingModify_on_do_action");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

if(isset($_REQUEST["task"])) {
    $oRecord->insert_additional_fields["ID_task"] = new ffData($_REQUEST["task"], "Number");
} else {
	
}
if(isset($_REQUEST["user"])) {
    $oRecord->insert_additional_fields["ID_user"] = new ffData($_REQUEST["user"], "Number");
} else {
	
}

$oField = ffField::factory($cm->oPage);
$oField->id = "duration";
$oField->container_class = "timetracking-duration";
$oField->label = ffTemplate::_get_word_by_code("task_timetracking_duration");
$oField->base_type = "TimeToSec";
$oField->extended_type = "Time";
$oField->app_type = "Time";
$oField->widget = "timepicker";
$oField->required = true;
$oRecord->addContent($oField);


if($_REQUEST["task"] > 0) {
	$sSQL_addit_field = "";
	$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_task_time_tracking.ID_user
				, SEC_TO_TIME(SUM(" . CM_TABLE_PREFIX . "mod_task_time_tracking.duration)) AS tot_duration
				, " . CM_TABLE_PREFIX . "mod_security_users.avatar AS users_avatar
				, " . CM_TABLE_PREFIX . "mod_security_users.username AS users_username
				, " . CM_TABLE_PREFIX . "mod_security_users.email AS users_email
			FROM " . CM_TABLE_PREFIX . "mod_task_time_tracking
				INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users ON " . CM_TABLE_PREFIX . "mod_security_users.ID = " . CM_TABLE_PREFIX . "mod_task_time_tracking.ID_user
			WHERE " . CM_TABLE_PREFIX . "mod_task_time_tracking.ID_task = " . $db->toSql($_REQUEST["task"], "Number") . "
			GROUP BY " . CM_TABLE_PREFIX . "mod_task_time_tracking.ID_user
			ORDER BY users_username";
	$db->query($sSQL);
	if($db->nextRecord()) {
		if(check_function("get_user_avatar")) {
			do {
				$users[$db->getField("ID_user", "Number", true)] = get_user_avatar($db->getField("users_avatar", "Text", true), true, $db->getField("users_email", "Text", true)) . $db->getField("users_username", "Text", true);
				

				$sSQL_addit_field .= ", '" . $db->getField("tot_duration", "Number", true) . "' AS user" . $db->getField("ID_user", "Number", true);
			} while($db->nextRecord());
		}

		$oGrid = ffGrid::factory($cm->oPage);
		$oGrid->id = "TimeTracking";
		$oGrid->widget_discl_enable = true;
		$oGrid->widget_def_open = false; 
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
										INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users ON " . CM_TABLE_PREFIX . "mod_security_users.ID = " . CM_TABLE_PREFIX . "mod_task_time_tracking.ID_user
									WHERE " . CM_TABLE_PREFIX . "mod_task_time_tracking.ID_task = " . $db->toSql($_REQUEST["task"], "Number") . "
								) AS tbl_src
		                    [WHERE]
		                    [HAVING]
		                    [ORDER]";
		$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
		$oGrid->record_id = "TimeTrackingModify";
		$oGrid->resources[] = $oGrid->record_id;
		$oGrid->addEvent("on_before_parse_row", "TimeTracking_on_before_parse_row");
		$oGrid->order_default = "created";
		$oGrid->full_ajax = true;
		$oGrid->display_new = false;
		$oGrid->display_search = false;
		$oGrid->use_paging = false;

		$oField = ffField::factory($cm->oPage);
		$oField->id = "ID";
		$oField->base_type = "Number";
		$oGrid->addKeyField($oField);

		$oField = ffField::factory($cm->oPage);
		$oField->id = "users_username";
		$oField->container_class = "task-timetraking-username";
		$oField->label = ffTemplate::_get_word_by_code("task_timetracking_username");
		$oGrid->addContent($oField);

		$oField = ffField::factory($cm->oPage);
		$oField->id = "created";
		$oField->base_type = "Timestamp";
		$oField->container_class = "task-timetraking-created";
		$oField->label = ffTemplate::_get_word_by_code("task_timetracking_created");
		$oField->extended_type = "DateTime";
		$oField->app_type = "DateTime";
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
		$oField->base_type = "TimeToSec";
		$oField->container_class = "task-timetraking-duration";
		$oField->label = ffTemplate::_get_word_by_code("task_timetracking_duration");
		$oField->extended_type = "Time";
		$oField->app_type = "Time";
		$oGrid->addContent($oField);

		$oRecord->addContent($oGrid);
		$cm->oPage->addContent($oGrid);

		$oGrid = ffGrid::factory($cm->oPage);
		$oGrid->id = "TimeTrackingTotal";
		//$oGrid->title = ffTemplate::_get_word_by_code("task_timetracking_total_title");
		$oGrid->source_SQL = "SELECT SEC_TO_TIME(SUM(" . CM_TABLE_PREFIX . "mod_task_time_tracking.duration)) AS tot_duration
								$sSQL_addit_field 
							FROM " . CM_TABLE_PREFIX . "mod_task_time_tracking
								INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users ON " . CM_TABLE_PREFIX . "mod_security_users.ID = " . CM_TABLE_PREFIX . "mod_task_time_tracking.ID_user
							WHERE " . CM_TABLE_PREFIX . "mod_task_time_tracking.ID_task = " . $db->toSql($_REQUEST["task"], "Number") . "
							[AND] [WHERE]
		                    [HAVING]
		                    [ORDER]";
		$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
		$oGrid->record_id = "TimeTrackingModify";
		$oGrid->resources[] = $oGrid->record_id;
		$oGrid->addEvent("on_before_parse_row", "TimeTracking_on_before_parse_row");
		$oGrid->order_default = "tot_duration";
		$oGrid->full_ajax = true;
		$oGrid->display_new = false;
		$oGrid->display_search = false;
		$oGrid->use_paging = false;
		$oGrid->display_delete_bt = false;
		$oGrid->display_edit_url = false;
		

		$oField = ffField::factory($cm->oPage);
		$oField->id = "ID";
		$oField->base_type = "Number";
		$oGrid->addKeyField($oField);

		if(is_array($users) && count($users)) {
			foreach($users AS $users_key => $users_value) {
				$oField = ffField::factory($cm->oPage);
				$oField->id = "user" . $users_key;
				$oField->label = $users_value;
				$oField->label_encode_entities = false;
				$oGrid->addContent($oField, false);
			}
		}

		$oField = ffField::factory($cm->oPage);
		$oField->id = "tot_duration";
		$oField->label = ffTemplate::_get_word_by_code("task_timetracking_tot_duration");
		$oField->label_encode_entities = false;
		$oField->container_class = "task-timetraking-tot-duration";
		$oGrid->addContent($oField, false);

		$oRecord->addContent($oGrid);
		$cm->oPage->addContent($oGrid);
	}	
}


$cm->oPage->addContent($oRecord);

function TimeTracking_on_before_parse_row($component) {
	if($component->db[0]->getField("owner", "Number", true) == get_session("UserNID")) {
		$component->display_edit_url = true;
		$component->display_delete_bt = true;
	} else {
		$component->display_edit_url = false;
		$component->display_delete_bt = false;
	}
	
	
    if(isset($component->grid_fields["users_username"])) { 
    	if(check_function("get_user_avatar"))
    		$component->grid_fields["users_username"]->setValue(get_user_avatar($component->db[0]->getField("users_avatar", "Text", true), true, $component->db[0]->getField("users_email", "Text", true)) . $component->db[0]->getField("users_username", "Text", true));
    }	
}

function TimeTrackingModify_on_do_action($oRecord, $frmAction)
{
    $db = ffDB_Sql::factory();

    switch ($frmAction)
    {
        case "insert":
        case "update":
			if($oRecord->form_fields["duration"]->getValue() == 0)
			{
				$oRecord->tplDisplayError(ffTemplate::_get_word_by_code("warning_duration_zero"));
				return true;
			}
            break;
        default:
    }
}