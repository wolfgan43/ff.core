<?php
$permission = check_recruitment_permission();
if($permission !== true && !(is_array($permission) && count($permission))) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$arrType = array (
                        array(new ffData("1", "Number"), new ffData(ffTemplate::_get_word_by_code("Testo"))),
                        array(new ffData("2", "Number"), new ffData(ffTemplate::_get_word_by_code("Risposta multipla")))
                );

$db = ffDB_Sql::factory();
$UserNID = get_session("UserNID");

if(isset($_REQUEST["frmAction"]) && $_REQUEST["frmAction"] == "SelectedQuestionModify_update")
{
    $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_recruitment_job_question
                    SET " . CM_TABLE_PREFIX . "mod_recruitment_job_question.previous_question = " . $db->toSql(($_REQUEST["SelectedQuestionModify_previous_question"] > 0 ? $_REQUEST["SelectedQuestionModify_previous_question"] : 0), "Number") . "
                        , " . CM_TABLE_PREFIX . "mod_recruitment_job_question.next_question = " . $db->toSql(($_REQUEST["SelectedQuestionModify_next_question"]>0 ?$_REQUEST["SelectedQuestionModify_next_question"] : 0), "Number") . "
                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID = " . $db->toSql($_REQUEST["SelectedQuestionModify_keys"]["ID"], "Number");
		    $db->execute($sSQL);
		    
		    ffredirect($cm->oPage->site_path . $cm->oPage->page_path . "/job-question/modify/?question[ID]=" . $_REQUEST["SelectedQuestionModify_keys"]["ID"]);
    
} elseif(isset($_REQUEST["keys"]["ID"])) {
    
    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_question.smart_url
                ,  " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID_question
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_question
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID = " . $db->toSql($_REQUEST["keys"]["ID"], "Number");
    $db->query($sSQL);
    if($db->nextRecord()) {
        
        $ID_selected_questionary = $db->getField("ID_question", "Number", true);
    }
    
$ID_selected_question = $_REQUEST["keys"]["ID"];

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "SelectedQuestionModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("recruitment_job_question_modify_title");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_recruitment_job_question";

$oField = ffField::factory($cm->oPage); 
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField); 


if($ID_selected_question && $ID_selected_questionary)
{
    $oField = ffField::factory($cm->oPage);
    $oField->id = "previous_question";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_question_modify_previous_question");
    $oField->widget = "activecomboex";
    $oField->base_type = "Number";
    $oField->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID, " . CM_TABLE_PREFIX . "mod_recruitment_job_question.name
                            FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_question
                            WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID_question = " . $db->toSql($ID_selected_questionary, "Number") . "
                                AND " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID <> " . $db->toSql($ID_selected_question, "Number") . " 
                            ORDER BY name";
    $oField->actex_update_from_db = true;
    $oRecord->addContent($oField);
    
    $oField = ffField::factory($cm->oPage);
    $oField->id = "next_question";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_question_modify_next_question");
    $oField->widget = "activecomboex";
    $oField->base_type = "Number";
    $oField->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID, " . CM_TABLE_PREFIX . "mod_recruitment_job_question.name
                            FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_question
                            WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID_question = " . $db->toSql($ID_selected_questionary, "Number") . "
                                AND " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID <> " . $db->toSql($ID_selected_question, "Number") . " 
                            ORDER BY name";
    $oField->actex_update_from_db = true;
    $oRecord->addContent($oField);
    
    $cm->oPage->addContent($oRecord); 
}
}