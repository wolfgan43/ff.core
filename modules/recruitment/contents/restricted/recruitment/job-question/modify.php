<?php
$permission = check_recruitment_permission();
if($permission !== true && !(is_array($permission) && count($permission))) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$db = ffDB_Sql::factory();
$UserNID = get_session("UserNID");
if(!$_REQUEST["keys"]["ID"] > 0) {
    if(isset($_REQUEST["question"]["ID"])) {
       $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID_question
                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_question
                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID = " . $db->toSql($_REQUEST["question"]["ID"], "Number");
        $db->query($sSQL);
        if($db->nextRecord()) {
            $_REQUEST["keys"]["ID"] = $db->getField("ID_question", "Number", true);
        } 
    }
    elseif(strlen(basename($cm->real_path_info))) {
        $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question.ID
                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question
                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question.smart_url = " . $db->toSql(basename($cm->real_path_info)) . "
                    AND " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question.ID_user = " . $db->toSql($UserNID, "Number", false);
        $db->query($sSQL);
        if($db->nextRecord()) {
            $_REQUEST["keys"]["ID"] = $db->getField("ID", "Number", true);
        }
    }
}
$ID_question = $_REQUEST["keys"]["ID"];

if($ID_question)
{

$oGrid = ffGrid::factory($cm->oPage);
if(MOD_RECRUITMENT_WANT_DIALOG)
	$oGrid->full_ajax = true;
$oGrid->id = "Question";
$oGrid->title = ffTemplate::_get_word_by_code("trivia_question_title");
$oGrid->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_question.*
                        FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_question
                        WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_question.ID_question = " . $db->toSql($ID_question, "Number") . "
                        [AND] [WHERE] 
                        [HAVING]
                        [ORDER]";

$oGrid->order_default = "ID";
$oGrid->use_search = true;
if(MOD_RECRUITMENT_WANT_DIALOG) {
	$oGrid->record_url = $cm->oPage->site_path . MOD_RECRUITMENT_PATH . "/job-question/question/" . basename($cm->real_path_info);
	$oGrid->bt_edit_url = $cm->oPage->site_path . MOD_RECRUITMENT_PATH . "/job-question/question/" . basename($cm->real_path_info) . "/[smart_url_VALUE]?ret_url=" . urlencode($cm->oPage->getRequestUri());
} else {
	$oGrid->record_url = $cm->oPage->site_path . "/restricted/recruitment/job-question/question/" . basename($cm->real_path_info);
	$oGrid->bt_edit_url = $cm->oPage->site_path . "/restricted/recruitment/job-question/question/" . basename($cm->real_path_info) . "/[smart_url_VALUE]?ret_url=" . urlencode($cm->oPage->getRequestUri());
	
}

$oGrid->record_id = "QuestionModify";
$oGrid->resources[] = $oGrid->record_id;
$oGrid->display_new = true;
$oGrid->display_edit_bt = false;
$oGrid->display_edit_url = true;
$oGrid->display_delete_bt = true;
$oGrid->addEvent("on_before_parse_row", "JobQuestion_on_before_parse_row");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);


// Campi di ricerca

// Campi visualizzati
$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = ffTemplate::_get_word_by_code("recruitment_question_name");
$oGrid->addContent($oField);

$oButton = ffButton::factory($cm->oPage);
$oButton->id = "jobquestion"; 
//$oButton->action_type = "submit";
//$oButton->url = "";
$oButton->aspect = "link";
$oButton->template_file = "ffButton_link_fixed.html";
//$oButton->template_file = "ffButton_link_fixed.html";                           
$oGrid->addGridButton($oButton);

$cm->oPage->addContent($oGrid);

} else
{
   
    $oRecord = ffRecord::factory($cm->oPage);
    $oRecord->id = "JobQuestionModify";
    $oRecord->resources[] = $oRecord->id;
    $oRecord->title = ffTemplate::_get_word_by_code("recruitment_new_job_question");
    $oRecord->src_table = CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question";
    $oRecord->buttons_options["delete"]["display"] = false;
    $oRecord->insert_additional_fields["ID_user"] =  new ffData($UserNID, "Number");
    $oRecord->addEvent("on_done_action", "JobQuestionModify_on_done_action");

    $oField = ffField::factory($cm->oPage);
    $oField->id = "ID";
    $oField->base_type = "Number";
    $oRecord->addKeyField($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "title";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_question_title");
    $oRecord->addContent($oField);

   $cm->oPage->addContent($oRecord); 
}

function JobQuestionModify_on_done_action($component, $action)
{
    $db = ffDB_Sql::factory();
    
    switch($action) {
		case "insert":
			$smart_url = get_session("UserID")
			. " "
			. $component->form_fields["title"]->getValue();
			
		    $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question
                                SET smart_url = " . $db->toSql(ffCommon_url_rewrite($smart_url)) . "
                                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question.ID = " . $db->toSql($component->key_fields["ID"]->value);
		    $db->execute($sSQL);
		    if (isset($_REQUEST["XHR_DIALOG_ID"])) {
				die(ffCommon_jsonenc(array("url" => $component->parent[0]->site_path . $component->parent[0]->page_path . "/" . ffCommon_url_rewrite($smart_url) . "?ret_url=" . urlencode($_REQUEST["ret_url"]), "close" => true, "refresh" => true, "doredirects" => true), true));
			} else {
				ffRedirect($component->parent[0]->site_path . $component->parent[0]->page_path . "/" . ffCommon_url_rewrite($smart_url) . "?ret_url=" . urlencode($_REQUEST["ret_url"]));
			}
			break;
                default :
    }
}

function JobQuestion_on_before_parse_row($component) 
{
    $cm = cm::getInstance();
    if(isset($component->grid_buttons["job-question"])) 
    {
    /*    //ffErrorHandler::raise("asd", E_USER_ERROR, null, get_defined_vars());
        $component->grid_buttons["job-question"]->label = ffTemplate::_get_word_by_code("choose_job_question");
        $component->grid_buttons["job-question"]->form_action_url = MOD_RECRUITMENT_PATH . "/job-question/question/modify?[KEYS]&ret_url=" . urlencode($component->page_path); 
        if($_REQUEST["XHR_DIALOG_ID"]) 
        {
            $component->grid_buttons["job-question"]->jsaction = "javascript:ff.ffPage.dialog.doRequest('[[XHR_DIALOG_ID]]', {'action': 'status', fields: [], 'url' : '[[frmAction_url]]'});";
        } else 
        {
            $component->grid_buttons["job-question"]->jsaction = "javascript:ff.ajax.doRequest({'action': 'status', fields: [], 'url' : '[[frmAction_url]]'});";
        }  
    * 
    */ 
        if($component->grid_buttons["jobquestion"]->action_type == "submit") {
            $cm->oPage->widgets["dialog"]->process(
                 "jobquestion_" . $component->key_fields["ID"]->getValue()
                 , array(
                    "tpl_id" => $component->id
                    //"name" => "myTitle"
                    , "url" => MOD_RECRUITMENT_PATH . "/job-question/question/modify?keys[ID]=" . $component->key_fields["ID"]->getValue()  . "&ret_url=" . urlencode($component->parent[0]->getRequestUri())//. "&ret_url=" . urldecode('[CLOSEDIALOG]')//// . "&ret_url=" . urlencode($component->page_path)
                    , "title" => ffTemplate::_get_word_by_code("choose_job_question")
                    , "callback" => ""
                    , "class" => ""
                    , "params" => array()
                )
                , $cm->oPage
            );
        $component->grid_buttons["jobquestion"]->jsaction = "ff.ffPage.dialog.doOpen('" . "jobquestion_" . $component->key_fields["ID"]->getValue() . "')";
        
        }
    }
}


