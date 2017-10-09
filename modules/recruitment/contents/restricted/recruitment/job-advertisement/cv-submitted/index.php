<?php

$permission = check_recruitment_permission();
if($permission !== true && !(is_array($permission) && count($permission))) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$db = ffDB_Sql::factory();
$UserNID = get_session("UserNID");

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->full_ajax = true;
/*
$oGrid->ajax_search = true;
$oGrid->ajax_delete  = true;
$oGrid->ajax_addnew  = true;
 * */
$oGrid->id = "cv-submitted";
$oGrid->title = ffTemplate::_get_word_by_code("recruitment_job_advertisement_cv_submitted");
$oGrid->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.*
                            , (" . CM_TABLE_PREFIX . "mod_security_users.email) AS user_name
                            , IF(" . CM_TABLE_PREFIX . "mod_recruitment_cv.last_update > 0, " . CM_TABLE_PREFIX . "mod_recruitment_cv.last_update > 0, " . CM_TABLE_PREFIX . "mod_recruitment_cv.created) AS last_update
                            , " . CM_TABLE_PREFIX . "mod_recruitment_cv.subcategory_string
                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit
                        INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users ON " . CM_TABLE_PREFIX . "mod_security_users.ID = " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_user
                        INNER JOIN " . CM_TABLE_PREFIX . "mod_recruitment_cv ON " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID = " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_cv
                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_advertisement = " . $db->toSql($_REQUEST["keys"]["ID"], "Number") . "
                    [AND] [WHERE] 
                    [HAVING]
                    [ORDER]";
$oGrid->order_default = "ID";
$oGrid->use_search = true;
//$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
//$oGrid->bt_edit_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify/[smart_url_VALUE]?ret_url=" . urlencode($cm->oPage->getRequestUri());
//$oGrid->bt_insert_url = $cm->oPage->site_path . $cm->oPage->page_path . "/new?ret_url=" . urlencode(FF_SITE_PATH . MOD_RECRUITMENT_PATH);
$oGrid->record_id = "JobAdvertisementCVSubmitModify";
$oGrid->resources[] = $oGrid->record_id;
//$oGrid->display_new = true;
//$oGrid->display_edit_bt = false;
//$oGrid->display_edit_url = true;
//$oGrid->display_delete_bt = true;

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "user_name";
$oField->label = ffTemplate::_get_word_by_code("recruitment_cv_submitted_user");
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "subcategory_string";
$oField->label = ffTemplate::_get_word_by_code("recruitment_cv_submitted_user");
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "last_update";
$oField->base_type = "Timestamp";
$oField->app_type = "Date";
$oField->label = ffTemplate::_get_word_by_code("recruitment_cv_last_update");
$oGrid->addContent($oField);

$cm->oPage->addContent($oGrid);

