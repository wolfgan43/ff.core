<?php
$permission = check_task_permission();
if($permission !== true && !(is_array($permission) && count($permission) && $permission[global_settings("MOD_TASK_GROUP_ADMIN")])) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->id = "Project";
$oGrid->title = ffTemplate::_get_word_by_code("task_project_title");
$oGrid->source_SQL = "SELECT
						" . CM_TABLE_PREFIX . "mod_task_project.*
					FROM
						" . CM_TABLE_PREFIX . "mod_task_project
					WHERE 1 [AND]
					[WHERE]
					[HAVING]
					[ORDER]";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->record_id = "ProjectModify";
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
$oField->id = "name";
$oField->container_class = "task-project-name";
$oField->label = ffTemplate::_get_word_by_code("task_project_name");
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "shared_by";
$oField->container_class = "task-project-shared-with";
$oField->label = ffTemplate::_get_word_by_code("task_project_shared_with");
$oField->extended_type = "Selection";
$oField->source_SQL = "SELECT ID
							, username 
						FROM " . CM_TABLE_PREFIX . "mod_security_users 
						ORDER BY username";
$oField->multi_select_one_label = "";
$oGrid->addContent($oField);

$cm->oPage->addContent($oGrid);

