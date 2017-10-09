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

if(!$_REQUEST["keys"]["ID"] > 0 && strlen(basename($cm->real_path_info))) {
    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID
                ,  " . CM_TABLE_PREFIX . "mod_recruitment_job_question.smart_url
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_question
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_question.smart_url = " . $db->toSql(basename($cm->real_path_info));
    $db->query($sSQL);
    if($db->nextRecord()) {
        $_REQUEST["keys"]["ID"] = $db->getField("ID", "Number", true);
        $smart_url_selected_question = $db->getField("smart_url", "Text", true);
    }
}

$ID_selected_question = $_REQUEST["keys"]["ID"];

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "SelectedQuestionModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("recruitment_job_question_modify_title");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_recruitment_job_question";
$oRecord->insert_additional_fields["ID_user"] =  new ffData($UserNID, "Number");
$oRecord->user_vars["smart_url_selected_question"] = $smart_url_selected_question;

$oRecord->addEvent("on_done_action", "QuestionModify_on_done_action");

$oField = ffField::factory($cm->oPage); 
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField); 

if($ID_selected_question)
{
$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = ffTemplate::_get_word_by_code("recruitment_job_question_modify_name");
$oField->required = true;
$oRecord->addContent($oField);

$cm->oPage->tplAddJs("job-question-modify", "job-question-modify.js", "/modules/recruitment/themes/javascript", false, true);
/*
$oField = ffField::factory($cm->oPage);
$oField->id = "ID_advertisement";
$oField->base_type = "Number";
$oField->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID
                                , " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.title
                        FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                        WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID_user = " . $db->toSql($UserNID, "Number");
$oField->extended_type = "Selection";
$oField->label = ffTemplate::_get_word_by_code("recruitment_job_question_ID_advertisement");
$oField->required = true;
$oRecord->addContent($oField);
*/
$oField = ffField::factory($cm->oPage);
$oField->id = "type";
$oField->base_type = "Number";
$oField->extended_type = "Selection";
$oField->multi_pairs = $arrType;
$oField->label = ffTemplate::_get_word_by_code("recruitment_job_question_modify_type");
$oField->required = true;
$oRecord->addContent($oField);

$cm->oPage->addContent($oRecord);   

$oDetail = ffDetails::factory($cm->oPage);
$oDetail->id = "AnswerDetail";
$oDetail->class = "multiple_choice";
$oDetail->title = ffTemplate::_get_word_by_code("job_question_answer_title");
$oDetail->widget_discl_enable = false;
$oDetail->src_table = CM_TABLE_PREFIX . "mod_recruitment_job_question_answer";
$oDetail->order_default = "name";
$oDetail->fields_relationship = array ("ID_question" => "ID");
$oRecord->addEvent("on_done_action", "AnswerModify_on_done_action");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oDetail->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = ffTemplate::_get_word_by_code("job_question_answer_name");
$oField->required = true;
$oDetail->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "answer_value";
$oField->base_type = "Number";
$oField->label = ffTemplate::_get_word_by_code("job_question_answer_value");
$oDetail->addContent($oField);

$oRecord->addContent($oDetail);
$cm->oPage->addContent($oDetail);
} else
{
    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question.ID
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question.smart_url = " . $db->toSql(basename($cm->real_path_info));
    $db->query($sSQL);
    if($db->nextRecord()) {
        $question_ID = $db->getField("ID", "Number", true);
    }
    
    $oRecord->insert_additional_fields["ID_question"] =  new ffData($question_ID, "Number");
    
    $oField = ffField::factory($cm->oPage);
    $oField->id = "name";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_question_modify_name");
    $oField->required = true;
    $oRecord->addContent($oField); 
    
    $cm->oPage->addContent($oRecord);
}


function QuestionModify_on_done_action($component, $action)
{
    
    $db = ffDB_Sql::factory();
    switch($action) {
		case "insert":
			$smart_url = get_session("UserID")
			. " "
			. $component->form_fields["name"]->getValue();
			
		    $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_recruitment_job_question
                                SET " . CM_TABLE_PREFIX . "mod_recruitment_job_question.smart_url = " . $db->toSql(ffCommon_url_rewrite($smart_url)) . "
                                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID = " . $db->toSql($component->key_fields["ID"]->value);
		    $db->execute($sSQL);
		    
		    ffredirect($component->parent[0]->site_path . $component->parent[0]->page_path . "/" . ffCommon_url_rewrite($smart_url) . "?ret_url=" . urlencode($_REQUEST["ret_url"]));
			break;
                default :
    }
}

function AnswerModify_on_done_action($component, $action)
{
    $db = ffDB_Sql::factory();
    switch($action) {
        
		case "insert":
                case "update":
                    if(is_array($component->detail["AnswerDetail"]->recordset) && count($component->detail["AnswerDetail"]->recordset)) {
                        foreach($component->detail["AnswerDetail"]->recordset AS $rst_key => $rst_value) {
                            $smart_url = get_session("UserID")
                            . " "
                            . $rst_value["name"]->getValue();
                            $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_recruitment_job_question_answer
                                    SET " . CM_TABLE_PREFIX . "mod_recruitment_job_question_answer.smart_url = " . $db->toSql(ffCommon_url_rewrite($smart_url)) . "
                                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_question_answer.ID = " . $db->toSql($rst_value["ID"]->getValue("Number"));
                            $db->execute($sSQL);
                            
                        }
                    }
			
		    
		    if (isset($_REQUEST["XHR_DIALOG_ID"])) {
				die(ffCommon_jsonenc(array("url" => $component->parent[0]->ret_url . "/?ret_url=" . urlencode($_REQUEST["ret_url"]), "close" => true, "refresh" => true, "doredirects" => true), true));
			} else {
				ffRedirect($component->parent[0]->ret_url . "/?ret_url=" . urlencode($_REQUEST["ret_url"]));
			}
			break;
                default :
    }
}