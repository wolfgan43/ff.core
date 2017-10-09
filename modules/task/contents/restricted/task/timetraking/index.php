<?php
$permission = check_task_permission();
if($permission !== true && !(is_array($permission) && count($permission) && $permission[global_settings("MOD_TASK_GROUP_ADMIN")])) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "TimeTraking";
$oGrid->title = ffTemplate::_get_word_by_code("task_timetraking_title");
$oGrid->source_SQL = "SELECT
                        " . CM_TABLE_PREFIX . "mod_task_time_tracking.*
                    FROM
                        " . CM_TABLE_PREFIX . "mod_task_time_tracking
                    WHERE 1 [AND]
                    [WHERE]
                    [HAVING]
                    [ORDER]";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->record_id = "TimeTrakingModify";
$oGrid->resources[] = $oGrid->record_id;
$oGrid->order_default = "name";
$oGrid->display_new = true;
$oGrid->full_ajax = true;
$oGrid->use_search = false;

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "duration";
$oField->container_class = "task-timetraking-duration";
$oField->label = ffTemplate::_get_word_by_code("task_timetraking_duration");
$oGrid->addContent($oField);

$cm->oPage->addContent($oGrid);

