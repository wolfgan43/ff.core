<?php
if(mod_security_check_session(false))
{
    $permission = check_recruitment_permission();
    if($permission !== true && !(is_array($permission) && count($permission))) {
        ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
    }
}
$interface = false;

if(MOD_RECRUITMENT_WANT_DIALOG && $_REQUEST["XHR_DIALOG_ID"])
    $interface = true;


$db = ffDB_Sql::factory();
$UserNID = get_session("UserNID");

if(strlen($cm->real_path_info)) {
	$globals_settori = ffGlobals::getInstance();
	if (!isset($globals_settori->parts))
	{
		$globals_settori->parts = array();
		if (strlen(trim($cm->path_info, "/")))
			$globals_settori->parts = mb_split("/", trim($cm->path_info, "/"));	

		$globals_settori->ID_comune = $db->lookup("support_citta", "smart_url", str_replace("comune-","",$globals_settori->parts[0]), null,  "ID", "Number", true);
		$globals_settori->ID_regione = $db->lookup("support_citta", "ID", $globals_settori->ID_comune, null,  "RegionID", "Number", true);
		$globals_settori->ID_provincia = $db->lookup("support_citta", "ID", $globals_settori->ID_comune, null, "DisctrictID", "Number", true);	        
		$globals_settori->comunename = ComuneName($globals_settori->ID_comune);
	}
	else
		$globals_settori->comunename = $globals_settori->COMUNE_DATA["slug"];
}
if(strlen(basename($cm->real_path_info)))
{
    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.*
                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.smart_url = " . $db->toSql(basename($cm->real_path_info));
    $db->query($sSQL);
    if($db->nextRecord()) {
        $smart_url = $db->getField("smart_url", "Text", true);
    }
}
if($UserNID)
{
    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv.*
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_user = " . $db->toSql($UserNID, "Number");
    $db->query($sSQL);
    if($db->nextRecord() || !$interface)
    {
        $oGrid = ffGrid::factory($cm->oPage);
		if(MOD_RECRUITMENT_WANT_DIALOG) {
			$oGrid->ajax_search = true;
			$oGrid->ajax_delete  = true;
			$oGrid->ajax_addnew  = true;
		}
        $oGrid->id = "cv";
        $oGrid->title = ffTemplate::_get_word_by_code("recruitment_cv_title");
        $oGrid->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv.*
                                , (" . CM_TABLE_PREFIX . "mod_recruitment_cv.subcategory_string) AS curriculum_category
                            FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv
                            WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_user = " . $db->toSql($UserNID, "Number") . "
                            [AND] [WHERE] 
                            [HAVING]
                            [ORDER]";
        $oGrid->order_default = "ID";
        $oGrid->use_search = false;
        $oGrid->use_paging = false;

        $oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";

        if(!isset($_REQUEST["ret_url"])) {
            if($cm->oPage->page_path != $cm->router->named_rules["recruitment_submit_cv"]->reverse) {
                $query_string = "ret_url=" . urlencode($cm->oPage->site_path . str_replace($cm->router->named_rules["recruitment_submit_cv"]->reverse, "", $cm->oPage->page_path));
            }        
        } else {
            $query_string = "ret_url=" . urlencode($_REQUEST["ret_url"]);
        }
        if(strlen($smart_url)) {
            if(strlen($query_string))
                $query_string .= "&";
                
            $query_string .= "su=" . $smart_url;
        }
        if(strlen($query_string))
            $query_string = "?" . $query_string;        

        $oGrid->bt_edit_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify/[smart_url_VALUE]" . $query_string;
        $oGrid->bt_insert_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify" . $query_string;
        $oGrid->record_id = "cvModify";
        $oGrid->resources[] = $oGrid->record_id;
        $oGrid->display_new = true;
        $oGrid->display_edit_bt = false;
        $oGrid->display_edit_url = true;
        $oGrid->display_delete_bt = true;
        if($interface || $UserNID)
        {
            $oGrid->user_vars["smart_url"] = $smart_url;
            $oGrid->addEvent("on_before_parse_row", "cv_on_before_parse_row");
        }

        // Campi chiave
        $oField = ffField::factory($cm->oPage);
        $oField->id = "ID";
        $oField->base_type = "Number";
        $oGrid->addKeyField($oField);
        /*
        $oField = ffField::factory($cm->oPage);
        $oField->id = "ID_user";
        $oField->base_type = "Number";
        $oGrid->addKeyField($oField);
        */
        $oField = ffField::factory($cm->oPage);
        $oField->id = "curriculum_category"; 
        $oField->required = true;
        $oField->label = ffTemplate::_get_word_by_code("recruitment_cv_category");
        $oGrid->addContent($oField);

        if($interface)
        {
        $oButton = ffButton::factory($cm->oPage);
        $oButton->id = "select";
        //$oButton->action_type = "gotourl";
        //$oButton->url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify/[KEYS]?ret_url=" . urlencode($cm->oPage->getRequestUri());
        $oButton->aspect = "link";
        //$oButton->label = ffTemplate::_get_word_by_code("recruitment_cv_selected");
        $oButton->template_file = "ffButton_link_fixed.html";                           
        $oGrid->addGridButton($oButton);
        }
		if(is_file(FF_DISK_PATH . "/themes/comune.info/css/recruitment.css"))
			$cm->oPage->tplAddCss("recruitment-css", "recruitment.css", "/themes/comune.info/css");
        $cm->oPage->addContent($oGrid);
    } else {
        if(!isset($_REQUEST["ret_url"])) {
            if($cm->oPage->page_path != $cm->router->named_rules["recruitment_submit_cv"]->reverse) {
                $query_string = "ret_url=" . urlencode($cm->oPage->site_path . str_replace($cm->router->named_rules["recruitment_submit_cv"]->reverse, "", $cm->oPage->page_path));
            }        
        } else {
            $query_string = "ret_url=" . urlencode($_REQUEST["ret_url"]);
        }   
             
        if(strlen($query_string))
            $query_string = "?" . $query_string;
        
        die(ffCommon_jsonenc(array("url" => $cm->oPage->site_path . $cm->oPage->page_path . "/modify?ret_url=" . urlencode($cm->oPage->getRequestUri()), "close" => false, "refresh" => true, "doredirects" => false), true));
    }
} else
{
    //ffErrorHandler::raise("asd", E_USER_ERROR, null, get_defined_vars());
    if(!isset($_REQUEST["ret_url"])) {
        if($cm->oPage->page_path != $cm->router->named_rules["recruitment_submit_cv"]->reverse) {
            $query_string = "ret_url=" . urlencode($cm->oPage->site_path . str_replace($cm->router->named_rules["recruitment_submit_cv"]->reverse, "", $cm->oPage->page_path));
        }        
    } else {
        $query_string = "ret_url=" . urlencode($_REQUEST["ret_url"]);
    }
    if(strlen($smart_url)) {
        if(strlen($query_string))
            $query_string .= "&";
            
        $query_string .= "su=" . $smart_url;
    }
    if(strlen($query_string))
        $query_string = "?" . $query_string;   
    ffRedirect($cm->oPage->site_path . $cm->oPage->page_path . "/modify" . $query_string);
    //die(ffCommon_jsonenc(array("url" => $cm->oPage->site_path . $cm->oPage->page_path . "/modify?ret_url=" . urlencode($cm->oPage->getRequestUri()) . (isset($_REQUEST["insertcv"]) ? "&insertcv=1" : ""), "close" => false, "refresh" => true, "doredirects" => false), true));
}

function cv_on_before_parse_row($component)
{
    
    
    /*
    $ID = $component->key_fields["ID"]->getValue();
    if(isset($component->user_vars["selected"]) && is_array($component->user_vars["selected"]) && count($component->user_vars["selected"]))
    {
        if(isset($component->user_vars["selected"]["adver_" . $ID]))
            $cv_selected = $component->user_vars["selected"]["adver_" . $ID];
    }
    
    if($interface)
{
    $info = array();
    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.*
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_user = " . $db->toSql($UserNID, "Number");
    if($db->nextRecord())
    {
        do {
            $info["adver_" . $db->getField("ID_advertisement", "Number", true)] = $db->getField("ID_cv", "Number", true);
        } while($db->nextRecord());
    }
     $oGrid->user_vars["selected"] = $info;
}
   */
    if(isset($_REQUEST["XHR_DIALOG_ID"]))
    {
	    if(isset($component->grid_buttons["select"])) {
	        //ffErrorHandler::raise("asd", E_USER_ERROR, null, get_defined_vars());
	    $component->grid_buttons["select"]->class = "icon ico-visible";
	    $component->grid_buttons["select"]->action_type = "submit"; 
	    $component->grid_buttons["select"]->label = ffTemplate::_get_word_by_code("recruitment_cv_selected");
	    $component->grid_buttons["select"]->form_action_url = $component->grid_buttons["select"]->parent[0]->record_url . "/" . $component->user_vars["smart_url"] . "?[KEYS]" . $component->grid_buttons["select"]->parent[0]->addit_record_param . "setcv=1&ret_url=" . (isset($_REQUEST["ret_url"]) ? urlencode($_REQUEST["ret_url"]) : urlencode($component->parent[0]->getRequestUri()));
		    if($_REQUEST["XHR_DIALOG_ID"]) {
		        $component->grid_buttons["select"]->jsaction = "javascript:ff.ffPage.dialog.doRequest('[[XHR_DIALOG_ID]]', {'action': 'setcv', fields: [], 'url' : '[[frmAction_url]]'});";
		    } else {
		        $component->grid_buttons["select"]->jsaction = "javascript:ff.ajax.doRequest({'action': 'setcv', fields: [], 'url' : '[[frmAction_url]]'});";
		    }
	    }  
    }
}