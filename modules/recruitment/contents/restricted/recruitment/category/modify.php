<?php
$permission = check_recruitment_permission();
if($permission !== true && !(is_array($permission) && count($permission))) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$db = ffDB_Sql::factory();
$UserNID = get_session("UserNID");

$_REQUEST["keys"]["ID"] = $_REQUEST["keys"]["ID_category"];

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "CategoryModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("recruitment_category_modify");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_recruitment_category";
$oRecord->addEvent("on_done_action", "categoryModify_on_done_action");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = ffTemplate::_get_word_by_code("recruitment_category_modify_name");
$oRecord->addContent($oField);
    
$cm->oPage->addContent($oRecord);

function categoryModify_on_done_action($component, $action)
{
    switch($action) {
        case "insert":
        case "update":
            $db = ffDB_Sql::factory();
            $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_recruitment_category
                        SET " . CM_TABLE_PREFIX . "mod_recruitment_category.smart_url = " . $db->toSql(ffCommon_url_rewrite($component->form_fields["name"]->getValue())) . "
                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_category.ID = " . $db->toSql($component->key_fields["ID"]->value, "Number");
            $db->execute($sSQL);
            break;
        default:
    }
}