<?php
$permission = check_mention_permission();
if($permission !== true && !(is_array($permission) && count($permission) && $permission[global_settings("MOD_MENTION_GROUP_ADMIN")])) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

/*$oGrid = ffGrid::factory($cm->oPage, null, null, array("name" => "ffGrid_div"));

if(file_exists(FF_DISK_PATH . FF_THEME_DIR . "/" . FRONTEND_THEME . "/contents/clm/" . basename($cm->oPage->page_path) . "/ffGrid.html")) {
	$oGrid->template_dir = FF_DISK_PATH . FF_THEME_DIR . "/" . FRONTEND_THEME . "/contents/clm/" . basename($cm->oPage->page_path);
}*/
$UserNID = get_session("UserNID");
$db = ffDB_Sql::factory();

$cm->oPage->addContent(null, true, "rel"); 
$oGrid = ffGrid::factory($cm->oPage);

$oGrid->full_ajax = true;
$oGrid->id = "BanKeyword";
$oGrid->title = ffTemplate::_get_word_by_code("mentions_ban_keyword_title");
$oGrid->source_SQL = "SELECT
                            " . CM_TABLE_PREFIX . "mod_mention_ban_keyword.*
                        FROM
                            " . CM_TABLE_PREFIX . "mod_mention_ban_keyword
                        WHERE 1
                        [AND] [WHERE] 
                        [HAVING]
                        [ORDER]";

$oGrid->order_default = "name";
$oGrid->use_search = true;
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/keyword/modify";
$oGrid->record_id = "BanKeywordModify";
$oGrid->resources[] = $oGrid->record_id;
$oGrid->display_new = true;
$oGrid->display_edit_bt = false;
$oGrid->display_edit_url = true;
$oGrid->display_delete_bt = true;
//$oGrid->addEvent("on_before_parse_row", "BanKeyword_on_before_parse_row");


// Campi chiave
$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

// Campi di ricerca

// Campi visualizzati
$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = ffTemplate::_get_word_by_code("mentions_ban_keyword_name");
$oGrid->addContent($oField);

$oButton = ffButton::factory($cm->oPage);
$oButton->id = "status";
$oButton->action_type = "gotourl";
$oButton->url = "";
$oButton->aspect = "link";
$oButton->template_file = "ffButton_link_fixed.html";                           
$oGrid->addGridButton($oButton);

$cm->oPage->addContent($oGrid, "rel", null, array("title" => ffTemplate::_get_word_by_code("mod_mention_ban_keyword")));

$oGrid = ffGrid::factory($cm->oPage);
$oGrid->full_ajax = true;
$oGrid->id = "BanDomain";
$oGrid->title = ffTemplate::_get_word_by_code("mentions_ban_title_domain");
$oGrid->source_SQL = "SELECT
                            " . CM_TABLE_PREFIX . "mod_mention_ban_domain.*
                        FROM
                            " . CM_TABLE_PREFIX . "mod_mention_ban_domain
                        WHERE 1
                        [AND] [WHERE] 
                        [HAVING]
                        [ORDER]";

$oGrid->order_default = "name";
$oGrid->use_search = true;
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/domain/modify";
$oGrid->record_id = "BanDomainModify";
$oGrid->resources[] = $oGrid->record_id;
$oGrid->display_new = true;
$oGrid->display_edit_bt = false;
$oGrid->display_edit_url = true;
$oGrid->display_delete_bt = true;
//$oGrid->addEvent("on_before_parse_row", "BanDomain_on_before_parse_row");


// Campi chiave
$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

// Campi di ricerca

// Campi visualizzati
$oField = ffField::factory($cm->oPage);
$oField->id = "name";
$oField->label = ffTemplate::_get_word_by_code("mentions_ban_domain_name");
$oField->addValidator("url");
$oGrid->addContent($oField);

$oButton = ffButton::factory($cm->oPage);
$oButton->id = "status";
$oButton->action_type = "gotourl";
$oButton->url = "";
$oButton->aspect = "link";
$oButton->template_file = "ffButton_link_fixed.html";                           
$oGrid->addGridButton($oButton);

$cm->oPage->addContent($oGrid, "rel", null, array("title" => ffTemplate::_get_word_by_code("mod_mention_ban_domain")));

function BanDomain_on_before_parse_row($component) {
    $cm = cm::getInstance();
    
    if(isset($component->grid_buttons["status"])) {
	    if($component->db[0]->getField("status", "Number", true)) {
	        $component->grid_buttons["status"]->label = ffTemplate::_get_word_by_code("remove_to_status");
	        $component->grid_buttons["status"]->class = "icon ico-visible";
	        $component->grid_buttons["status"]->action_type = "submit"; 
	        $component->grid_buttons["status"]->form_action_url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=0&ret_url=" . urlencode($component->parent[0]->getRequestUri());
	        if($_REQUEST["XHR_DIALOG_ID"]) {
	            $component->grid_buttons["status"]->jsaction = "javascript:ff.ffPage.dialog.doRequest('[[XHR_DIALOG_ID]]', {'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
	        } else {
	            $component->grid_buttons["status"]->jsaction = "javascript:ff.ajax.doRequest({'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
	            //$component->grid_buttons["status"]->action_type = "gotourl";
	            //$component->grid_buttons["status"]->url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=0&frmAction=setstatus&ret_url=" . urlencode($component->parent[0]->getRequestUri());
			}   
	    } else {
	        $component->grid_buttons["status"]->label = ffTemplate::_get_word_by_code("add_to_status");
	        $component->grid_buttons["status"]->class = "icon ico-notvisible";
	        $component->grid_buttons["status"]->action_type = "submit";     
	        $component->grid_buttons["status"]->form_action_url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=1&ret_url=" . urlencode($component->parent[0]->getRequestUri());
	        if($_REQUEST["XHR_DIALOG_ID"]) {
	            $component->grid_buttons["status"]->jsaction = "javascript:ff.ffPage.dialog.doRequest('[[XHR_DIALOG_ID]]', {'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
	        } else {
	        	$component->grid_buttons["status"]->jsaction = "javascript:ff.ajax.doRequest({'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
	            //$component->grid_buttons["status"]->action_type = "gotourl";
	            //$component->grid_buttons["status"]->url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=1&frmAction=setstatus&ret_url=" . urlencode($component->parent[0]->getRequestUri());
			}    
	    }
	}
}
function BanKeyword_on_before_parse_row($component) {
    $cm = cm::getInstance();
    
    if(isset($component->grid_buttons["status"])) {
	    if($component->db[0]->getField("status", "Number", true)) {
	        $component->grid_buttons["status"]->label = ffTemplate::_get_word_by_code("remove_to_status");
	        $component->grid_buttons["status"]->class = "icon ico-visible";
	        $component->grid_buttons["status"]->action_type = "submit"; 
	        $component->grid_buttons["status"]->form_action_url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=0&ret_url=" . urlencode($component->parent[0]->getRequestUri());
	        if($_REQUEST["XHR_DIALOG_ID"]) {
	            $component->grid_buttons["status"]->jsaction = "javascript:ff.ffPage.dialog.doRequest('[[XHR_DIALOG_ID]]', {'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
	        } else {
	            $component->grid_buttons["status"]->jsaction = "javascript:ff.ajax.doRequest({'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
	            //$component->grid_buttons["status"]->action_type = "gotourl";
	            //$component->grid_buttons["status"]->url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=0&frmAction=setstatus&ret_url=" . urlencode($component->parent[0]->getRequestUri());
			}   
	    } else {
	        $component->grid_buttons["status"]->label = ffTemplate::_get_word_by_code("add_to_status");
	        $component->grid_buttons["status"]->class = "icon ico-notvisible";
	        $component->grid_buttons["status"]->action_type = "submit";     
	        $component->grid_buttons["status"]->form_action_url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=1&ret_url=" . urlencode($component->parent[0]->getRequestUri());
	        if($_REQUEST["XHR_DIALOG_ID"]) {
	            $component->grid_buttons["status"]->jsaction = "javascript:ff.ffPage.dialog.doRequest('[[XHR_DIALOG_ID]]', {'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
	        } else {
	        	$component->grid_buttons["status"]->jsaction = "javascript:ff.ajax.doRequest({'action': 'setstatus', fields: [], 'url' : '[[frmAction_url]]'});";
	            //$component->grid_buttons["status"]->action_type = "gotourl";
	            //$component->grid_buttons["status"]->url = $component->grid_buttons["status"]->parent[0]->record_url . "?[KEYS]" . $component->grid_buttons["status"]->parent[0]->addit_record_param . "setstatus=1&frmAction=setstatus&ret_url=" . urlencode($component->parent[0]->getRequestUri());
			}    
	    }
	}
}
?>