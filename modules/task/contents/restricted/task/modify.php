<?php
$permission = check_task_permission();
if($permission !== true && !(is_array($permission) && count($permission) && $permission[global_settings("MOD_TASK_GROUP_ADMIN")])) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$db = ffDB_Sql::factory();

if(isset($_REQUEST["frmAction"]) && isset($_REQUEST["setstatus"])) {
    $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_task
                    SET status = " . $db->toSql($_REQUEST["setstatus"]) . "
                    WHERE 
                    	" . CM_TABLE_PREFIX . "mod_task.ID = " . $db->toSql($_REQUEST["keys"]["ID"], "Number");
    $db->execute($sSQL);
    if($_REQUEST["setstatus"]) {
		$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_task.*
					, " . CM_TABLE_PREFIX . "mod_security_users.username AS assigned_to_name
					, " . CM_TABLE_PREFIX . "mod_security_users.email AS assigned_to_mail
				FROM " . CM_TABLE_PREFIX . "mod_task
					INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users ON " . CM_TABLE_PREFIX . "mod_security_users.ID = " . CM_TABLE_PREFIX . "mod_task.owner
				WHERE " . CM_TABLE_PREFIX . "mod_task.ID = " . $db->toSql($_REQUEST["keys"]["ID"], "Number");
		$db->query($sSQL);
		if($db->nextRecord()) {
			$task_name = $db->getField("description", "Text", true);
			$task_smart_url = $db->getField("smart_url", "Text", true);
			$frequency = $db->getField("frequency", "Text", true);
			$interval = $db->getField("interval", "Number", true);
			$description = $db->getField("description", "Text", true);
			$full_description = $db->getField("full_description", "Text", true);
			$ID_project = $db->getField("ID_project", "Number", true);
			$deadline = $db->getField("deadline", "Timestamp");
			$assigned_to = $db->getField("assigned_to", "Number", true);
			$shared_with = $db->getField("shared_with", "Text", true);
			$ID_domain = $db->getField("ID_domain", "Number", true);
			$owner = $db->getField("owner", "Number", true);
			
			if($owner != $assigned_to && check_function("process_mail")) {
				$to[] = array(
					"name" => $db->getField("assigned_to_name", "Text", true)
					, "mail" => $db->getField("assigned_to_mail", "Text", true)
				);
				$bcc = null;
				if(strlen($shared_with)) {
					$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_security_users.* 
							FROM " . CM_TABLE_PREFIX . "mod_security_users
							WHERE ID IN(" . $db->toSql($shared_with, "Text", true) . ")";
					$db->query($sSQL);
					if($db->nextRecord()) {
						do {
							$bcc[] = array(
								"name" => $db->getField("username", "Text", true)
								, "mail" => $db->getField("email", "Text", true)
							);
						} while($db->nextRecord());
					}
				}

				$from = array(
					"name" => global_settings("MOD_TASK_FROM_NAME")
					, "mail" => global_settings("MOD_TASK_FROM_EMAIL")
				);

				$fields["user"]["name"] = (strlen(get_session("user_firstname") . get_session("user_lastname")) ? get_session("user_firstname") . " " . get_session("user_lastname") . " (" . get_session("UserID") . ")" :  get_session("UserID"));
				$fields["task"]["name"] = $task_name;
				$fields["task"]["link"] = "/restricted/task#" . $task_smart_url;
				$fields["task"]["status"] = ffTemplate::_get_word_by_code("mod_task_status_completed");
				
				if(is_file(FF_DISK_PATH . FF_THEME_DIR . "/" . $cm->oPage->getTheme() . "/contents/mail/email.tpl")) {
					$tpl_email_path = FF_THEME_DIR . "/" . $cm->oPage->getTheme() . "/contents/mail/email.tpl";
				} else {
					$tpl_email_path = "/modules/task/themes/restricted/mail/email.tpl";
				}

				$subject = (strlen(get_session("user_firstname")) ? get_session("user_firstname") . " (" . get_session("UserID") . ")" :  get_session("UserID")) . " " . ffTemplate::_get_word_by_code("mod_task_complete") . " " . $task_name;
				$email_struct = array(
						"mail" => array(
							"smtp" => array(
								"host" => global_settings("MOD_TASK_SMTP_HOST")
								, "auth" => global_settings("MOD_TASK_SMTP_AUTH")
								, "username" => global_settings("MOD_TASK_SMTP_USER")
								, "password" => global_settings("MOD_TASK_SMTP_PASSWORD")
							)
							, "subject" => null
							, "name" => ""
							, "tpl_path" => null
							, "notify" => false
							, "from" => null
							, "cc" => null
							, "bcc" => null
						)
						, "example" => null
						, "debug" => null
					);
				$res = process_mail($email_struct, $to, $subject, $tpl_email_path, $fields, $from, $bcc);
				
			}

			if($frequency) {
				$new_deadline = new DateTime($deadline->getValue("Date", FF_SYSTEM_LOCALE));
				do {
					$new_deadline->modify("+" . $interval . " " . $frequency);
				} while($new_deadline->format("Y-m-d") < date("Y-m-d"));
				
				$sSQL = "INSERT INTO " . CM_TABLE_PREFIX . "mod_task 
						(
							`ID`
							, `description`
							, `full_description`
							, `ID_project`
							, `deadline`
							, `created`
							, `last_update`
							, `owner`
							, `assigned_to`
							, `shared_with`
							, `status`
							, `ID_domain`
							, `interval`
							, `frequency`
						)
						VALUES
						(
							null
							, " . $db->toSql($description) . "
							, " . $db->toSql($full_description) . "
							, " . $db->toSql($ID_project, "Number") . "
							, " . $db->toSql($new_deadline->getTimestamp(), "Number") . "
							, " . $db->toSql(time(), "Number") . "
							, " . $db->toSql(time(), "Number") . "
							, " . $db->toSql($owner, "Number") . "
							, " . $db->toSql($assigned_to, "Number") . "
							, " . $db->toSql($shared_with) . "
							, " . $db->toSql("0", "Number") . "
							, " . $db->toSql($ID_domain, "Number") . "
							, " . $db->toSql($interval, "Number") . "
							, " . $db->toSql($frequency) . "
							
						)";
				$db->execute($sSQL);
			}
		}
    }
    if($_REQUEST["XHR_DIALOG_ID"]) {
        die(ffCommon_jsonenc(array("url" => $_REQUEST["ret_url"], "close" => false, "refresh" => true), true));
    } else {
        ffRedirect($_REQUEST["ret_url"]);
    }
}

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "TaskModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("task_modify_title");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_task";
$oRecord->insert_additional_fields["created"] = new ffData(time(), "Number");
$oRecord->insert_additional_fields["last_update"] = new ffData(time(), "Number");
$oRecord->insert_additional_fields["owner"] = new ffData(get_session("UserNID"), "Number");
$oRecord->update_additional_fields["last_update"] = new ffData(time(), "Number");
	
//$oRecord->widget = "disclosures";
//$oRecord->widget_discl_enable = true;

$oRecord->addEvent("on_do_action", "TaskModify_on_do_action");
$oRecord->addEvent("on_done_action", "TaskModify_on_done_action");

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "description";
$oField->container_class = "task-description";
$oField->placeholder = ffTemplate::_get_word_by_code("task_description");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "assigned_to";
$oField->container_class = "task-assigned-to";
$oField->placeholder = ffTemplate::_get_word_by_code("task_assigned_to");
//$oField->base_type = "Number";
$oField->extended_type = "Selection";
$oField->source_SQL = "        
                        SELECT ID
                            , username 
                            , IF(avatar = ''
                                , ''
                                , IF(avatar LIKE 'http%://%' 
                                    , avatar
                                    , CONCAT('" . CM_SHOWFILES ."/thumb', avatar)
                                )
                            ) AS avatar
                        FROM " . CM_TABLE_PREFIX . "mod_security_users 
                        WHERE " . CM_TABLE_PREFIX . "mod_security_users.ID_domains = " . $db->toSql(mod_security_get_domain(), "Number") . "
                        [AND] [WHERE]
                        [HAVING]
                        [ORDER] [COLON] username
                        [LIMIT]";
/*$oField->widget = "autocompletetoken";
$oField->autocompletetoken_minLength = 0;
$oField->autocompletetoken_combo = false;
$oField->autocompletetoken_limit = 1;  
$oField->autocompletetoken_compare = "username";
$oField->autocompletetoken_theme = "facebook";*/
$oField->widget = "actex";
$oField->actex_autocomp = true;	
$oField->actex_multi = true;
$oField->actex_update_from_db = true;	
$oField->actex_attr = array("image" => array("prefix" => ""
                                                , "field" => "avatar"
                                                , "postfix" => ""
                            )
                            ,"name" => array("prefix" => ""
                                                , "field" => "username"
                                                , "postfix" => ""
                            )
                        );                        
//$oField->multi_select_one = false;
$oField->default_value = new ffData(get_session("UserNID"), "Number");

$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "deadline";
$oField->container_class = "task-deadline";
$oField->placeholder = ffTemplate::_get_word_by_code("task_deadline");
$oField->base_type = "Timestamp";
$oField->extended_type = "Date";
$oField->app_type	 = "Date";
$oField->widget = "datepicker";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "ID_project";
$oField->container_class = "task-project";
$oField->placeholder = ffTemplate::_get_word_by_code("task_project");
$oField->extended_type = "Selection";
$oField->source_SQL = "SELECT ID
							, name 
						FROM " . CM_TABLE_PREFIX . "mod_task_project 
						WHERE " . CM_TABLE_PREFIX . "mod_task_project.ID_domain = " . $db->toSql(mod_security_get_domain(), "Number") . "
						[AND] [WHERE]
						[ORDER] [COLON] name
						[LIMIT]";
$oField->widget = "autocomplete";
$oField->autocomplete_multi = false;
$oField->autocomplete_readonly = true;
$oField->autocomplete_minLength = 0;
$oField->autocomplete_combo = true;
$oField->autocomplete_compare = "name";
$oField->autocomplete_strip_char = '/\.|\-|;|\.';   
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "interval";
$oField->container_class = "task-interval";
$oField->base_type = "Number";
$oField->placeholder = ffTemplate::_get_word_by_code("task_interval");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "frequency";
$oField->container_class = "task-frequency";
$oField->placeholder = ffTemplate::_get_word_by_code("task_frequency");
$oField->extended_type = "Selection";
$oField->multi_pairs = array(
							array(new ffData(""), new ffData(ffTemplate::_get_word_by_code("never")))
							, array(new ffData("day"), new ffData(ffTemplate::_get_word_by_code("day")))
							, array(new ffData("week"), new ffData(ffTemplate::_get_word_by_code("week")))
							, array(new ffData("month"), new ffData(ffTemplate::_get_word_by_code("month")))
							, array(new ffData("year"), new ffData(ffTemplate::_get_word_by_code("year")))
);
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "shared_with";
$oField->container_class = "task-shared-with";
$oField->placeholder = ffTemplate::_get_word_by_code("task_shared_with");
$oField->extended_type = "Selection";
$oField->source_SQL = "		
						SELECT ID
							, username 
                            , IF(avatar = ''
                                , ''
                                , IF(avatar LIKE 'http%://%' 
                                	, avatar
                                	, CONCAT('" . CM_SHOWFILES ."/thumb', avatar)
                                )
                            ) AS avatar
						FROM " . CM_TABLE_PREFIX . "mod_security_users 
						WHERE " . CM_TABLE_PREFIX . "mod_security_users.ID_domains = " . $db->toSql(mod_security_get_domain(), "Number") . "
						[AND] [WHERE]
						[HAVING]
						[ORDER] [COLON] username
						[LIMIT]";
/*$oField->widget = "autocompletetoken";
$oField->autocompletetoken_minLength = 0;
$oField->autocompletetoken_combo = true;
$oField->autocompletetoken_compare = "username";
$oField->autocompletetoken_theme = "facebook";*/
$oField->widget = "actex";
$oField->actex_autocomp = true;	
$oField->actex_multi = true;
$oField->actex_update_from_db = true;	
$oField->actex_attr = array("image" => array("prefix" => ""
                                                , "field" => "avatar"
                                                , "postfix" => ""
                            )
                            ,"name" => array("prefix" => ""
                                                , "field" => "username"
                                                , "postfix" => ""
                            )
                        );
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "full_description";
$oField->container_class = "task-full-description";
$oField->placeholder = ffTemplate::_get_word_by_code("task_full_description");
$oField->extended_type = "Text";
$oField->widget = "tiny_mce";
$oRecord->addContent($oField);

$cm->oPage->addContent($oRecord);


function TaskModify_on_do_action($oRecord, $frmAction)
{
	$db = ffDB_Sql::factory();

	switch ($frmAction)
	{
		case "insert":
		case "update":
			if(!$oRecord->form_fields["ID_project"]->getValue()
				&& strlen($_REQUEST["autocomplete_" . $oRecord->id . "_ID_project"])
			) {
				$project = $_REQUEST["autocomplete_" . $oRecord->id . "_ID_project"];
				$sSQL = "INSERT INTO " . CM_TABLE_PREFIX . "mod_task_project
						(
							ID
							, name
							, smart_url
							, owner
							, created
							, last_update
							, ID_domain
						) VALUES (
							null
							, " . $db->toSql($project) . "
							, " . $db->toSql(ffCommon_url_rewrite($project)) . "
							, " . $db->toSql(get_session("UserNID"), "Number") . "
							, " . $db->toSql(time(), "Number") . "
							, " . $db->toSql(time(), "Number") . "
							, " . $db->toSql(mod_security_get_domain(), "Number") . "
						)";
				$db->execute($sSQL);
				$ID_project = $db->getInsertID(true);

				$oRecord->form_fields["ID_project"]->setValue($ID_project);
			}
		break;
		default:
	}
}

function TaskModify_on_done_action($oRecord, $frmAction)
{
	$cm = cm::getInstance();
    $db = ffDB_Sql::factory();

    switch ($frmAction)
    {
        case "insert":
        case "update":   
			$task_smart_url = $oRecord->form_fields["deadline"]->getValue("Date", FF_SYSTEM_LOCALE) . "-" . ffCommon_url_rewrite(trim(substr($oRecord->form_fields["description"]->getValue(), 0, 50)));
            $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_task
                    SET smart_url = " . $db->toSql($task_smart_url) . "
                    WHERE ID = " . $db->toSql($oRecord->key_fields["ID"]->value);
            $db->execute($sSQL);
            
			$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_task.*
						, " . CM_TABLE_PREFIX . "mod_security_users.username AS assigned_to_name
						, " . CM_TABLE_PREFIX . "mod_security_users.email AS assigned_to_mail
					FROM " . CM_TABLE_PREFIX . "mod_task
						INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users ON " . CM_TABLE_PREFIX . "mod_security_users.ID = " . CM_TABLE_PREFIX . "mod_task.assigned_to
					WHERE " . CM_TABLE_PREFIX . "mod_task.ID = " . $db->toSql($oRecord->key_fields["ID"]->value);
			$db->query($sSQL);
			if($db->nextRecord()) {
				$task_name = $db->getField("description", "Text", true);
				$task_smart_url = $db->getField("smart_url", "Text", true);
				$frequency = $db->getField("frequency", "Text", true);
				$interval = $db->getField("interval", "Number", true);
				$description = $db->getField("description", "Text", true);
				$full_description = $db->getField("full_description", "Text", true);
				$ID_project = $db->getField("ID_project", "Number", true);
				$deadline = $db->getField("deadline", "Timestamp");
				$assigned_to = $db->getField("assigned_to", "Number", true);
				$shared_with = $db->getField("shared_with", "Text", true);
				$ID_domain = $db->getField("ID_domain", "Number", true);
				$owner = $db->getField("owner", "Number", true);				

				if(check_function("process_mail")) {
		            if(get_session("UserNID") !=  $oRecord->form_fields["assigned_to"]->value->getValue() 
		            	&& $oRecord->form_fields["assigned_to"]->value->getValue() != $oRecord->form_fields["assigned_to"]->value_ori->getValue()
		            ) {
		            	$to = array();
		            	
						$to[] = array(
							"name" => $db->getField("assigned_to_name", "Text", true)
							, "mail" => $db->getField("assigned_to_mail", "Text", true)
						);
						$bcc = null;
						/*if(strlen($shared_with)) {
							$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_security_users.* 
									FROM " . CM_TABLE_PREFIX . "mod_security_users
									WHERE ID IN(" . $db->toSql($shared_with, "Text", true) . ")";
							$db->query($sSQL);
							if($db->nextRecord()) {
								do {
									$bcc[] = array(
										"name" => $db->getField("username", "Text", true)
										, "mail" => $db->getField("email", "Text", true)
									);
								} while($db->nextRecord());
							}
						}*/

						$from = array(
							"name" => global_settings("MOD_TASK_FROM_NAME")
							, "mail" => global_settings("MOD_TASK_FROM_EMAIL")
						);
						$fields["user"]["name"] = (strlen(get_session("user_firstname") . get_session("user_lastname")) ? get_session("user_firstname") . " " . get_session("user_lastname") . " (" . get_session("UserID") . ")" :  get_session("UserID"));
						$fields["task"]["name"] = $task_name;
						$fields["task"]["link"] = "/restricted/task#" . $task_smart_url;
						$fields["task"]["status"] = ffTemplate::_get_word_by_code("mod_task_status_assigned");
						
						if(is_file(FF_DISK_PATH . FF_THEME_DIR . "/" . $cm->oPage->getTheme() . "/contents/mail/email.tpl")) {
							$tpl_email_path = FF_THEME_DIR . "/" . $cm->oPage->getTheme() . "/contents/mail/email.tpl";
						} else {
							$tpl_email_path = "/modules/task/themes/restricted/mail/email.tpl";
						}
						$subject = (strlen(get_session("user_firstname")) ? get_session("user_firstname") . " (" . get_session("UserID") . ")" :  get_session("UserID")) . " " . ffTemplate::_get_word_by_code("mod_task_assign") . " " . $task_name;
						$email_struct = array(
								"mail" => array(
									"smtp" => array(
										"host" => global_settings("MOD_TASK_SMTP_HOST")
										, "auth" => global_settings("MOD_TASK_SMTP_AUTH")
										, "username" => global_settings("MOD_TASK_SMTP_USER")
										, "password" => global_settings("MOD_TASK_SMTP_PASSWORD")
									)
									, "subject" => null
									, "name" => ""
									, "tpl_path" => null
									, "notify" => false
									, "from" => null
									, "cc" => null
									, "bcc" => null
								)
								, "example" => null
								, "debug" => null
							);
						$res = process_mail($email_struct, $to, $subject, $tpl_email_path, $fields, $from, $bcc);
					}
										
		            if($oRecord->form_fields["shared_with"]->value->getValue() != $oRecord->form_fields["shared_with"]->value_ori->getValue()) {
						$to = array();
						$share_with_new = explode(",", $oRecord->form_fields["shared_with"]->value->getValue());
						$share_with_old = explode(",", $oRecord->form_fields["shared_with"]->value_ori->getValue());
						$share_with = array_diff($share_with_new, $share_with_old);
						if(is_array($share_with) && count($share_with)) {
							$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_security_users.* 
									FROM " . CM_TABLE_PREFIX . "mod_security_users
									WHERE ID IN(" . $db->toSql(implode(",", $share_with), "Text", false) . ")
										AND ID <> " . $db->toSql(get_session("UserNID"), "Number") . "
										AND ID <> " . $db->toSql($assigned_to, "Number") . "
										AND ID <> " . $db->toSql($owner, "Number");
							$db->query($sSQL);
							if($db->nextRecord()) {
								do {
									$to[] = array(
										"name" => $db->getField("username", "Text", true)
										, "mail" => $db->getField("email", "Text", true)
									);
								} while($db->nextRecord());
								$bcc = null;

								$from = array(
									"name" => global_settings("MOD_TASK_FROM_NAME")
									, "mail" => global_settings("MOD_TASK_FROM_EMAIL")
								);
								$fields["user"]["name"] = (strlen(get_session("user_firstname") . get_session("user_lastname")) ? get_session("user_firstname") . " " . get_session("user_lastname") . " (" . get_session("UserID") . ")" :  get_session("UserID"));
								$fields["task"]["name"] = $task_name;
								$fields["task"]["link"] = "/restricted/task#" . $task_smart_url;
								$fields["task"]["status"] = ffTemplate::_get_word_by_code("mod_task_status_shared");
								
								if(is_file(FF_DISK_PATH . FF_THEME_DIR . "/" . $cm->oPage->getTheme() . "/contents/mail/email.tpl")) {
									$tpl_email_path = FF_THEME_DIR . "/" . $cm->oPage->getTheme() . "/contents/mail/email.tpl";
								} else {
									$tpl_email_path = "/modules/task/themes/restricted/mail/email.tpl";
								}
								$subject = (strlen(get_session("user_firstname")) ? get_session("user_firstname") . " (" . get_session("UserID") . ")" :  get_session("UserID")) . " " . ffTemplate::_get_word_by_code("mod_task_share") . " " . $task_name;
								$email_struct = array(
										"mail" => array(
											"smtp" => array(
												"host" => global_settings("MOD_TASK_SMTP_HOST")
												, "auth" => global_settings("MOD_TASK_SMTP_AUTH")
												, "username" => global_settings("MOD_TASK_SMTP_USER")
												, "password" => global_settings("MOD_TASK_SMTP_PASSWORD")
											)
											, "subject" => null
											, "name" => ""
											, "tpl_path" => null
											, "notify" => false
											, "from" => null
											, "cc" => null
											, "bcc" => null
										)
										, "example" => null
										, "debug" => null
									);
								$res = process_mail($email_struct, $to, $subject, $tpl_email_path, $fields, $from, $bcc);								
								
							}
						}
					}					
				}
			}
            break;
        default:
    }
}