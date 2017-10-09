<?php
$permission = check_recruitment_permission();
if($permission !== true && !(is_array($permission) && count($permission))) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$db = ffDB_Sql::factory();
$UserNID = get_session("UserNID");

$oGrid = ffGrid::factory($cm->oPage);
if(MOD_RECRUITMENT_WANT_DIALOG)
{
	$oGrid->ajax_edit = false;
	$oGrid->ajax_addnew = true;
}
$oGrid->id = "Question";
$oGrid->title = ffTemplate::_get_word_by_code("trivia_question_title");
$oGrid->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question.*
                        FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question
                        WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question.ID_user = " . $db->toSql($UserNID, "Number") . "
                        [AND] [WHERE] 
                        [HAVING]
                        [ORDER]";

$oGrid->order_default = "title";
$oGrid->use_search = true;
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->bt_edit_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify/[smart_url_VALUE]?ret_url=" . urlencode($cm->oPage->getRequestUri());
$oGrid->record_id = "JobQuestionModify";
$oGrid->resources[] = $oGrid->record_id;
$oGrid->display_new = true;
$oGrid->display_edit_bt = false;
$oGrid->display_edit_url = true;
$oGrid->display_delete_bt = true;

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

// Campi di ricerca

// Campi visualizzati
$oField = ffField::factory($cm->oPage);
$oField->id = "title";
$oField->label = ffTemplate::_get_word_by_code("recruitment_question_title");
$oGrid->addContent($oField);

$cm->oPage->addContent($oGrid);