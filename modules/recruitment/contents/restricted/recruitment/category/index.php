<?php
$permission = check_recruitment_permission();
if($permission !== true && !(is_array($permission) && count($permission))) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$db = ffDB_Sql::factory();
$UserNID = get_session("UserNID");

$oGrid = ffGrid::factory($cm->oPage);
if(MOD_RECRUITMENT_WANT_DIALOG) 
	$oGrid->full_ajax = true;
$oGrid->id = "category";
$oGrid->title = ffTemplate::_get_word_by_code("recruitment_category");
$oGrid->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_category.*
                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_category
                    WHERE 1
                    [AND] [WHERE] 
                    [HAVING]
                    [ORDER]";
$oGrid->order_default = "ID";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
//$oGrid->bt_edit_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify/[smart_url_VALUE]?ret_url=" . urlencode($cm->oPage->getRequestUri());
//$oGrid->bt_insert_url = $cm->oPage->site_path . $cm->oPage->page_path . "/new?ret_url=" . urlencode(FF_SITE_PATH . MOD_RECRUITMENT_PATH);
$oGrid->record_id = "CategoryModify";
$oGrid->resources[] = $oGrid->record_id;

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = ffTemplate::_get_word_by_code("recruitment_category_name");
$oGrid->addContent($oField);

$cm->oPage->addContent($oGrid);