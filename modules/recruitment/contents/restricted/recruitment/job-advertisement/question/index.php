<?php
mod_security_check_session();

$permission = check_recruitment_permission();
if($permission !== true && !(is_array($permission) && count($permission))) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$db = ffDB_Sql::factory();
$UserNID = get_session("UserNID");

if(!isset($_REQUEST["keys"]["ID"])) {
	ffRedirect($_REQUEST["ret_url"]);
}

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "JobAdvertisementModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("recruitment_advertisement_modify");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_recruitment_job_advertisement";
$oRecord->buttons_options["delete"]["display"] = false;
$oRecord->insert_additional_fields["last_update"] =  new ffData(time(), "Number");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_question";
$oField->container_class = "question";
$oField->base_type = "Number";
$oField->source_SQL = "SELECT ID, title
                        FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question 
                        WHERE ID_user = " . $db->toSql($UserNID, "Number") . "
                        ORDER BY title";
$oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_category");
$oField->widget = "activecomboex";
$oField->actex_update_from_db = true;
$oRecord->addContent($oField);

$cm->oPage->addContent($oRecord);