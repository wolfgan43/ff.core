<?php
mod_security_check_session();

$permission = check_recruitment_permission();
if($permission !== true && !(is_array($permission) && count($permission))) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$db = ffDB_Sql::factory();
$UserNID = get_session("UserNID");



$oGrid = ffGrid::factory($cm->oPage);
if(MOD_RECRUITMENT_WANT_DIALOG)
	$oGrid->full_ajax = true;
/*
$oGrid->ajax_search = true;
$oGrid->ajax_delete  = true;
$oGrid->ajax_addnew  = true;
 * */
$oGrid->id = "job-advertisement";
$oGrid->title = ffTemplate::_get_word_by_code("recruitment_job_advertisement");
$oGrid->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.*
                            , " . CM_TABLE_PREFIX . "mod_recruitment_category.name AS job_advertisement_category
                            , " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.name AS job_advertisement_subcategory
                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                        LEFT JOIN " . CM_TABLE_PREFIX . "mod_recruitment_category ON " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID_category = " . CM_TABLE_PREFIX . "mod_recruitment_category.ID
                        LEFT JOIN " . CM_TABLE_PREFIX . "mod_recruitment_subcategory ON " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID_subcategory = " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID
                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID_user = " . $db->toSql($UserNID, "Number") . "
                    [AND] [WHERE] 
                    [HAVING]
                    [ORDER]";
$oGrid->order_default = "ID";
$oGrid->use_search = true;
if(strlen($_REQUEST["company"]))
$oGrid->addit_insert_record_param = "company=" . $_REQUEST["company"] . "&";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->bt_edit_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify/[smart_url_VALUE]?ret_url=" . urlencode($cm->oPage->getRequestUri());
//$oGrid->bt_insert_url = $cm->oPage->site_path . $cm->oPage->page_path . "/new?ret_url=" . urlencode(FF_SITE_PATH . MOD_RECRUITMENT_PATH);
$oGrid->record_id = "JobAdvertisementModify";
$oGrid->resources[] = $oGrid->record_id;
$oGrid->display_new = true;
$oGrid->display_edit_bt = false;
$oGrid->display_edit_url = true;
$oGrid->display_delete_bt = true;
$oGrid->addEvent("on_before_parse_row", "RecruitmentSeeCV_on_before_parse_row");
if(isset($_REQUEST["XHR_DIALOG_ID"])) {
	$oGrid->use_search = false;
}


$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "title";
$oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_title");
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "job_advertisement_category";
$oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_category");
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "job_advertisement_subcategory";
$oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_subcategory");
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "required_workers";
$oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_required_workers");
$oGrid->addContent($oField);

if(!$_REQUEST["XHR_DIALOG_ID"])
{
    $oButton = ffButton::factory($cm->oPage);
    $oButton->id = "recruitment_cv_submit";
    $oButton->aspect = "link";
    $oButton->template_file = "ffButton_link_fixed.html";                           
    $oGrid->addGridButton($oButton);
}

$cm->oPage->addContent($oGrid);

function RecruitmentSeeCV_on_before_parse_row($component) {
    if(isset($component->grid_buttons["recruitment_cv_submit"])) {
        $component->grid_buttons["recruitment_cv_submit"]->class = "icon ico-cv-submit";
        $component->grid_buttons["recruitment_cv_submit"]->action_type = "submit"; 
        $component->grid_buttons["recruitment_cv_submit"]->label = ffTemplate::_get_word_by_code("recruitment_cv_submitted");
        $component->grid_buttons["recruitment_cv_submit"]->form_action_url = $component->grid_buttons["recruitment_cv_submit"]->parent[0]->page_path . "/cv-submitted?[KEYS]" . $component->grid_buttons["recruitment_cv_submit"]->parent[0]->addit_record_param . "ret_url=" . urlencode($component->parent[0]->getRequestUri());
        if($_REQUEST["XHR_DIALOG_ID"]) {
            $component->grid_buttons["recruitment_cv_submit"]->jsaction = "javascript:ff.ffPage.dialog.doRequest('[[XHR_DIALOG_ID]]', {'action': 'cv_submitted', fields: [], 'url' : '[[frmAction_url]]'});";
        } else {
            $component->grid_buttons["recruitment_cv_submit"]->jsaction = "javascript:ff.ajax.doRequest({'action': 'cv_submitted', fields: [], 'url' : '[[frmAction_url]]'});";
        }
    }
}