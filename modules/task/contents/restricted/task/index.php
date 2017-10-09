<?php   
$permission = check_task_permission();
if($permission !== true && !(is_array($permission) && count($permission) && $permission[global_settings("MOD_TASK_GROUP_ADMIN")])) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}
$db = ffDB_Sql::factory();

$project_smart_url = basename($cm->real_path_info);
if(strlen($project_smart_url)) {
	$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_task_project.ID 
			FROM " . CM_TABLE_PREFIX . "mod_task_project
			WHERE " . CM_TABLE_PREFIX . "mod_task_project.smart_url = " . $db->toSql($project_smart_url);
	$db->query($sSQL);
	if($db->nextRecord()) {
		$ID_project = $db->getField("ID", "Number", true);
	}
}
$oGrid = ffGrid::factory($cm->oPage);
$oGrid->full_ajax = true;
$oGrid->id = "Task";
$oGrid->title = ffTemplate::_get_word_by_code("task_title");
$oGrid->source_SQL = "SELECT tbl_src.*
					FROM (
						SELECT
							" . CM_TABLE_PREFIX . "mod_security_users.avatar AS users_avatar
							, " . CM_TABLE_PREFIX . "mod_security_users.username AS users_username
							, " . CM_TABLE_PREFIX . "mod_security_users.email AS users_email
							, " . CM_TABLE_PREFIX . "mod_task.*
							, " . CM_TABLE_PREFIX . "mod_task_project.name AS project_name
							, " . CM_TABLE_PREFIX . "mod_task_project.smart_url AS project_smart_url
							, IF(deadline > CURDATE()
								, 0
								, 1
							) AS deadline_priority
						FROM
							" . CM_TABLE_PREFIX . "mod_task
							INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users ON " . CM_TABLE_PREFIX . "mod_security_users.ID = " . CM_TABLE_PREFIX . "mod_task.assigned_to
							LEFT JOIN " . CM_TABLE_PREFIX . "mod_task_project ON " . CM_TABLE_PREFIX . "mod_task_project.ID = " . CM_TABLE_PREFIX . "mod_task.ID_project
						WHERE (" . CM_TABLE_PREFIX . "mod_task.owner = " . $db->toSql(get_session("UserNID"), "Number"). "
                                OR " . CM_TABLE_PREFIX . "mod_task.assigned_to = " . $db->toSql(get_session("UserNID"), "Number"). "
                                OR FIND_IN_SET(" . $db->toSql(get_session("UserNID"), "Number") . ", " . CM_TABLE_PREFIX . "mod_task.shared_with)
                            )
					) AS tbl_src
					[WHERE]
					[HAVING]
					[ORDER]
					";
$oGrid->record_id = "TaskModify";
$oGrid->record_url = $cm->oPage->site_path . $cm->oPage->page_path . "/modify";
$oGrid->resources[] = $oGrid->record_id;
$oGrid->order_default = "deadline";
$oGrid->default_records_per_page = 100;
//$oGrid->open_adv_search = true;
$oGrid->addEvent("on_before_parse_row", "Task_on_before_parse_row");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->data_source = "ID";
$oField->base_type = "Number";
$oGrid->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "users_username";
$oField->container_class = "task-assigned-to";
$oField->label = ffTemplate::_get_word_by_code("task_assigned_to");
$oField->encode_entities = false;
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "description";
$oField->container_class = "task-description";
$oField->label = ffTemplate::_get_word_by_code("task_description");
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "project_name";
$oField->container_class = "task-project";
$oField->label = ffTemplate::_get_word_by_code("task_project");
$oField->encode_entities = false;
$oGrid->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "deadline";
$oField->container_class = "task-deadline";
$oField->label = ffTemplate::_get_word_by_code("task_deadline");
$oField->base_type = "Timestamp";
$oField->extended_type = "Date";
$oField->app_type	 = "Date";
$oField->order_SQL = "deadline_priority, deadline, ID";
$oGrid->addContent($oField);

$oButton = ffButton::factory($cm->oPage);
$oButton->id = "status";
$oButton->action_type = "gotourl";
$oButton->url = "";
$oButton->aspect = "link";
$oButton->template_file = "ffButton_link_fixed.html";                           
$oGrid->addGridButton($oButton);

/* RICERCA */    
	$oField = ffField::factory($cm->oPage);
	$oField->id = "assigned_to";
	$oField->container_class = "assigned-to";
	$oField->label = ffTemplate::_get_word_by_code("task_assigned_to");
	$oField->extended_type = "Selection";
	$oField->source_SQL = "SELECT ID
								, username 
							FROM " . CM_TABLE_PREFIX . "mod_security_users 
							ORDER BY username ASC";
	$oField->properties["onchange"] = 'javascript:$(this).closest(\'.search\').find(\'input[type=button]\').click();';
	$oField->multi_select_one_label	= ffTemplate::_get_word_by_code("task_all_team");
	$oField->default_value = new ffData(get_session("UserNID"), "Number");
	$oGrid->addSearchField($oField);
	
	$oField = ffField::factory($cm->oPage);
	$oField->id = "ID_project";
	$oField->container_class = "project";
	$oField->label = ffTemplate::_get_word_by_code("task_project");
	$oField->extended_type = "Selection";
	$oField->source_SQL = "SELECT ID
								, name 
							FROM " . CM_TABLE_PREFIX . "mod_task_project 
							ORDER BY name";
	$oField->properties["onchange"] = 'javascript:$(this).closest(\'.search\').find(\'input[type=button]\').click();';
	$oField->multi_select_one_label	= ffTemplate::_get_word_by_code("task_all_project");
	$oField->default_value = new ffData($ID_project, "Number");
	$oField->encode_entities = false;
	$oGrid->addSearchField($oField);

    
    $oButton = ffButton::factory($cm->oPage);
    $oButton->id = "timetracking";
    $oButton->class = "icon ico-timetracking";
    if(1 || $_REQUEST["XHR_DIALOG_ID"]) {
        $oButton->form_action_url = ""; //impostato nell'evento
    }
    $oButton->aspect = "link";
    //$oButton->image = "print.png";
    $oButton->label = ffTemplate::_get_word_by_code("task_timetracking_bt");
    $oButton->template_file = "ffButton_link_fixed.html";
    $oGrid->addGridButton($oButton);
        
	$oField = ffField::factory($cm->oPage);
	$oField->id = "status";
	$oField->container_class = "status";
	$oField->label = ffTemplate::_get_word_by_code("task_status");
	$oField->base_type = "Number";
	$oField->extended_type = "Selection";
	$oField->multi_pairs = array( 
									array( new ffData("0", "Number"),  new ffData(ffTemplate::_get_word_by_code("task_status_opened"))),
									array( new ffData("1", "Number"),  new ffData(ffTemplate::_get_word_by_code("task_status_closed")))
								);
	$oField->properties["onchange"] = 'javascript:$(this).closest(\'.search\').find(\'input[type=button]\').click();';
    $oField->multi_select_one = false;
    $oField->multi_select_noone = true;
    $oField->multi_select_noone_label = ffTemplate::_get_word_by_code("task_status_all");
    $oField->multi_select_noone_val = new ffData(null);
    $oField->default_value = new ffData("0", "Number");
	$oGrid->addSearchField($oField);

$cm->oPage->addContent($oGrid);

$cm->oPage->tplAddJs("task", "task.js", "/modules/task/themes/javascript");

$cm->oPage->addContent('<div class="task-container"></div>');

function Task_on_before_parse_row($component) {
    $cm = cm::getInstance();
    
	if(isset($component->grid_fields["project_name"])) {
		if($component->db[0]->getField("project_name", "Text", true)) {
			$component->grid_fields["project_name"]->setValue('<a class="project" href="' . $cm->oPage->site_path . $cm->oPage->page_path . "/" . $component->db[0]->getField("project_smart_url", "Text", true) . '">' . $component->db[0]->getField("project_name", "Text", true) . '</a>');
		}
	}
	
	if(isset($component->grid_fields["deadline"])) {
		if($component->db[0]->getField("deadline", "Number", true) > time()) {
			$component->grid_fields["deadline"]->container_class = "green";
		} else {
			$component->grid_fields["deadline"]->container_class = "overdue";
		}
	}

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
	
    if(isset($component->grid_fields["users_username"])) { 
    	if(check_function("get_user_avatar"))
    		$component->grid_fields["users_username"]->setValue(get_user_avatar($component->db[0]->getField("users_avatar", "Text", true), true, $component->db[0]->getField("users_email", "Text", true)) . $component->db[0]->getField("users_username", "Text", true));
    }	
    
    if(isset($component->grid_buttons["timetracking"]) ) {   
        if($component->grid_buttons["timetracking"]->action_type == "submit") {
            $cm->oPage->widgets["dialog"]->process(
                 "timetracking_" . $component->key_fields["ID"]->getValue()
                 , array(
                    "tpl_id" => $component->id
                    //"name" => "myTitle"
                    , "url" => FF_SITE_PATH . $cm->oPage->page_path . "/timetracking/modify"
                            . "?task=" . $component->key_fields["ID"]->getValue() . "&user=" . urlencode(get_session("UserNID"))
                            . "&ret_url=" . urlencode($component->parent[0]->getRequestUri())
                    , "title" => ffTemplate::_get_word_by_code("task_timetracking_add")
                    , "callback" => ""
                    , "class" => ""
                    , "params" => array()
                )
                , $cm->oPage
            );
            $component->grid_buttons["timetracking"]->jsaction = "ff.ffPage.dialog.doOpen('" . "timetracking_" . $component->key_fields["ID"]->getValue() . "')";
        }

     
            $component->grid_buttons["timetracking"]->visible = true;
      
    }
}