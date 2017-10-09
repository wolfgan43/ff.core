<?php
$permission = check_task_permission();
if($permission !== true && !(is_array($permission) && count($permission) && $permission[global_settings("MOD_TASK_GROUP_ADMIN")])) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$db = ffDB_Sql::factory();

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "ProjectModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("task_project_modify_title");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_task_project";
$oRecord->insert_additional_fields["created"] = new ffData(time(), "Number");
$oRecord->insert_additional_fields["last_update"] = new ffData(time(), "Number");
$oRecord->insert_additional_fields["owner"] = new ffData(get_session("UserNID"), "Number");
$oRecord->update_additional_fields["last_update"] = new ffData(time(), "Number");

$oRecord->addEvent("on_done_action", "ProjectModify_on_done_action");
$oRecord->addEvent("on_do_action", "ProjectModify_on_do_action");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->container_class = "task-project-name";
$oField->label = ffTemplate::_get_word_by_code("task_project_name");
$oField->required = true;
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "shared_with";
$oField->container_class = "task-project-shared-with";
$oField->label = ffTemplate::_get_word_by_code("task_project_shared_with");
$oField->extended_type = "Selection";
$oField->source_SQL = "		
						SELECT ID
							, username 
						FROM " . CM_TABLE_PREFIX . "mod_security_users 
						WHERE " . CM_TABLE_PREFIX . "mod_security_users.ID_domains = " . $db->toSql(mod_security_get_domain(), "Number") . "
						[AND] [WHERE]
						[HAVING]
						[ORDER] [COLON] username
						[LIMIT]";
/*$oField->widget = "autocompletetoken";
$oField->autocompletetoken_minLength = 0;
$oField->autocompletetoken_combo = true;
$oField->autocompletetoken_compare = "username";*/
$oField->widget = "actex";
$oField->actex_autocomp = true;	
$oField->actex_multi = true;
$oField->actex_update_from_db = true;	
$oRecord->addContent($oField);


$cm->oPage->addContent($oRecord);
function ProjectModify_on_do_action($oRecord, $frmAction)
{
	switch ($frmAction)
	{
		case "insert":
		 case "update":
		  if($oRecord->form_fields["name"]->getValue()) {
			$db1 = ffDB_Sql::factory();
			$sSQL1 = "SELECT cm_mod_task_project.* 
					FROM cm_mod_task_project 
					WHERE cm_mod_task_project.ID <> " . $db1->toSql($oRecord->key_fields["ID"]->value) . "
						AND cm_mod_task_project.name = " . $db1->toSql($oRecord->form_fields["name"]->value);
			$db1->query($sSQL1);
			if($db1->nextRecord()) {
				$oRecord->tplDisplayError(ffTemplate::_get_word_by_code("warning_project_duplicate"));
				return true;
			}
		  }
		  break;
	}
}
function ProjectModify_on_done_action($oRecord, $frmAction)
{
	$db = ffDB_Sql::factory();

	switch ($frmAction)
	{
		case "insert":
		case "update":
			$sSQL = "UPDATE 
						" . CM_TABLE_PREFIX . "mod_task_project 
					SET
						smart_url = " . $db->toSql(ffCommon_url_rewrite($oRecord->form_fields["name"]->getValue())) . "
					WHERE ID = " . $db->toSql($oRecord->key_fields["ID"]->value);
			$db->execute($sSQL);
		break;
	}
}