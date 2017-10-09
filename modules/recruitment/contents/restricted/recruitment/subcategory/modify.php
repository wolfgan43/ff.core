<?php
$permission = check_recruitment_permission();
if($permission !== true && !(is_array($permission) && count($permission))) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}
$db = ffDB_Sql::factory();
$sSQL_where = "";
$category = false;
if(isset($_REQUEST["category"]))
{
    $sSQL_where = "AND " . CM_TABLE_PREFIX . "mod_recruitment_category.smart_url = " . $db->toSql($_REQUEST["category"], "Text");
    $category = true;
}

$UserNID = get_session("UserNID");

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "SubCategoryModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("recruitment_subcategory_modify");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_recruitment_subcategory";
$oRecord->buttons_options["print"]["display"] = false;
$oRecord->addEvent("on_done_action", "subcategoryModify_on_done_action");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_category";
$oField->container_class = "category";
$oField->base_type = "Number";
$oField->source_SQL = "SELECT ID, name
                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_category 
                    WHERE 1 $sSQL_where
                    ORDER BY name";
$oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_category");
$oField->widget = "activecomboex";
$oField->actex_update_from_db = true;
if($category)
{
    $oField->multi_select_one = false;
} else
{
    
    $oField->resources[] = "CategoryModify";
    $oField->actex_dialog_url = $cm->oPage->site_path . "/restricted/recruitment/category/modify";
    $oField->actex_dialog_edit_params = array("keys[ID_category]" => null);
    $oField->actex_dialog_delete_url = $oField->actex_dialog_url . "?frmAction=CategoryModify_confirmdelete";
}
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = ffTemplate::_get_word_by_code("recruitment_subcategory_modify_name");
$oRecord->addContent($oField);
    
$cm->oPage->addContent($oRecord);

function subcategoryModify_on_done_action($component, $action)
{
    switch($action) {
        case "insert":
        case "update":
            $db = ffDB_Sql::factory();
            $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_recruitment_subcategory
                        SET " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.smart_url = " . $db->toSql(ffCommon_url_rewrite($component->form_fields["name"]->getValue())) . "
                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID = " . $db->toSql($component->key_fields["ID"]->value, "Number");
            $db->execute($sSQL);
            break;
        default:
    }
}