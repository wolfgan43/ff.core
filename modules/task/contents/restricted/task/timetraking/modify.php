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
$oRecord->id = "TimeTrakingModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("task_timetraking_modify_title");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_task_time_tracking";
$oRecord->insert_additional_fields["created"] = new ffData(time(), "Number");
$oRecord->insert_additional_fields["last_update"] = new ffData(time(), "Number");
$oRecord->insert_additional_fields["owner"] = new ffData(get_session("UserNID"), "Number");
$oRecord->update_additional_fields["last_update"] = new ffData(time(), "Number");

if(isset($_REQUEST["task"]))
    $oRecord->insert_additional_fields["ID_task"] = new ffData($_REQUEST["task"], "Number");
if(isset($_REQUEST["user"]))
    $oRecord->insert_additional_fields["ID_user"] = new ffData($_REQUEST["user"], "Number");

    
//$oRecord->widget = "disclosures";
//$oRecord->widget_discl_enable = true;

$oRecord->addEvent("on_do_action", "TimeTrakingModify_on_do_action");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "duration";
$oField->container_class = "timetraking-duration";
$oField->label = ffTemplate::_get_word_by_code("task_timetraking_duration");
$oField->base_type = "timestamp";
$oField->extended_type = "Time";
$oField->app_type = "Time";
$oField->widget = "timepicker";
$oField->required = true;
$oRecord->addContent($oField);

 $cm->oPage->addContent($oRecord);


function TimeTrakingModify_on_do_action($oRecord, $frmAction)
{
    $db = ffDB_Sql::factory();

    switch ($frmAction)
    {
        case "insert":
        case "update":

            break;
        default:
    }
}