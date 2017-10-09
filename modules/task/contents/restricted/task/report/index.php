<?php
$permission = check_task_permission();
if($permission !== true && !(is_array($permission) && count($permission) && $permission[global_settings("MOD_TASK_GROUP_ADMIN")])) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$db = ffDB_Sql::factory();  

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "TimeTracking";
$oGrid->title = ffTemplate::_get_word_by_code("task_timetracking_title");
$oGrid->source_SQL = "SELECT tbl_src.*
                    FROM (
                            SELECT
                                " . CM_TABLE_PREFIX . "mod_security_users.avatar AS users_avatar
                                , " . CM_TABLE_PREFIX . "mod_security_users.username AS users_username
                                , " . CM_TABLE_PREFIX . "mod_security_users.email AS users_email
                                , " . CM_TABLE_PREFIX . "mod_task_project.name AS project_name
                                , " . CM_TABLE_PREFIX . "mod_task_project.ID AS ID_project
                                , " . CM_TABLE_PREFIX . "mod_task.description AS description
                                , " . CM_TABLE_PREFIX . "mod_task_time_tracking.*
                            FROM
                                " . CM_TABLE_PREFIX . "mod_task_time_tracking
                            INNER JOIN " . CM_TABLE_PREFIX . "mod_task ON " . CM_TABLE_PREFIX . "mod_task.ID = " . CM_TABLE_PREFIX . "mod_task_time_tracking.ID_task 
                            INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users ON " . CM_TABLE_PREFIX . "mod_security_users.ID = " . CM_TABLE_PREFIX . "mod_task_time_tracking.owner
                            LEFT JOIN " . CM_TABLE_PREFIX . "mod_task_project ON " . CM_TABLE_PREFIX . "mod_task_project.ID = " . CM_TABLE_PREFIX . "mod_task.ID_project
                        WHERE (" . CM_TABLE_PREFIX . "mod_task.owner = " . $db->toSql(get_session("UserNID"), "Number"). "
                                OR " . CM_TABLE_PREFIX . "mod_task.assigned_to = " . $db->toSql(get_session("UserNID"), "Number"). "
                                OR FIND_IN_SET(" . $db->toSql(get_session("UserNID"), "Number") . ", " . CM_TABLE_PREFIX . "mod_task.shared_with)
                            )
                        ) AS tbl_src
                    [WHERE]
                    [HAVING]
                    [ORDER]";
$oGrid->record_url = $cm->oPage->site_path . ffcommon_dirname($cm->oPage->page_path) . "/timetraking/modify";
$oGrid->record_id = "TimeTrackingModify";
$oGrid->resources[] = $oGrid->record_id;
$oGrid->addEvent("on_before_parse_row", "TimeTracking_on_before_parse_row");
$oGrid->order_default = "ID";
$oGrid->display_new = false;
$oGrid->display_edit_url = false;
$oGrid->display_delete_bt = false;
$oGrid->full_ajax = true;
$oGrid->use_search = true;

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oField->order_SQL = "created DESC, last_update DESC, ID DESC";
$oGrid->addKeyField($oField);


$oField = ffField::factory($cm->oPage);
$oField->id = "users_username";
$oField->container_class = "task-timetraking-username";
$oField->label = ffTemplate::_get_word_by_code("task_assigned_to");
$oField->encode_entities = false;
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "description";
$oField->container_class = "task-description";
$oField->label = ffTemplate::_get_word_by_code("task_description");
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "project_name";
$oField->container_class = "task-project";
$oField->label = ffTemplate::_get_word_by_code("task_project");
$oField->encode_entities = false;
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
$oField->id = "duration";
$oField->base_type = "Timestamp";
$oField->container_class = "task-timetraking-duration";
$oField->label = ffTemplate::_get_word_by_code("task_timetracking_duration");
$oField->extended_type = "Time";
$oField->app_type = "Time";
$oGrid->addContent($oField);


    $oField = ffField::factory($cm->oPage);
    $oField->id = "owner";
    $oField->container_class = "assigned-to";
    $oField->label = ffTemplate::_get_word_by_code("task_assigned_to");
    $oField->extended_type = "Selection";
    $oField->source_SQL = "SELECT ID
                                , username 
                            FROM " . CM_TABLE_PREFIX . "mod_security_users 
                            ORDER BY username ASC";
    $oField->properties["onchange"] = 'javascript:$(this).closest(\'.search\').find(\'input[type=button]\').click();';
    $oField->multi_select_one_label    = ffTemplate::_get_word_by_code("task_all_team");
    $oGrid->addSearchField($oField);
    
    $oField = ffField::factory($cm->oPage);
    $oField->id = "ID_project";
    $oField->container_class = "project";
    $oField->label = ffTemplate::_get_word_by_code("task_project");
    $oField->extended_type = "Selection";
    $oField->source_SQL = "SELECT ID
                                , name 
                            FROM " . CM_TABLE_PREFIX . "mod_task_project 
                            ORDER BY name";
    $oField->properties["onchange"] = 'javascript:$(this).closest(\'.search\').find(\'input[type=button]\').click();';
    $oField->multi_select_one_label    = ffTemplate::_get_word_by_code("task_all_project");
    $oField->default_value = new ffData($ID_project, "Number");
    $oField->encode_entities = false;
    $oGrid->addSearchField($oField);
    
    $oField = ffField::factory($cm->oPage);
    $oField->id = "created";
    $oField->base_type = "Timestamp";
    $oField->app_type = "Date";
    $oField->src_interval = TRUE;
    $oField->interval_from_label = ffTemplate::_get_word_by_code("search_from");
    $oField->interval_to_label = ffTemplate::_get_word_by_code("search_to");
    $oField->src_operation     = "[NAME]";
    $oField->widget = "datepicker";
    $oField->src_having = true;
    $oGrid->addSearchField($oField);


$cm->oPage->addContent($oGrid);

function TimeTracking_on_before_parse_row($component) {
    
    if(isset($component->grid_fields["users_username"])) { 
        if(check_function("get_user_avatar"))
            $component->grid_fields["users_username"]->setValue(get_user_avatar($component->db[0]->getField("users_avatar", "Text", true), true, $component->db[0]->getField("users_email", "Text", true)) . $component->db[0]->getField("users_username", "Text", true));
    }    
}