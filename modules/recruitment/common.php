<?php
cm::getInstance()->addEvent("on_before_page_process", "mod_recruitment_on_before_page_process", ffEvent::PRIORITY_NORMAL);
cm::getInstance()->addEvent("on_before_routing", "mod_recruitment_on_before_rounting", ffEvent::PRIORITY_NORMAL);
//cm::getInstance()->addEvent("mod_security_on_created_session", "mod_recruitment_mod_security_on_created_session", ffEvent::PRIORITY_NORMAL);

define("MOD_RECRUITMENT_PATH", $cm->router->named_rules["recruitment"]->reverse); 
define("MOD_RECRUITMENT_SERVICES_PATH", $cm->router->named_rules["recruitment_services"]->reverse);
define("MOD_RECRUITMENT_SUBMIT_CV_PATH", $cm->router->named_rules["recruitment_submit_cv"]->reverse);

if(!function_exists("check_function")) {
    function check_function($name, $module = null) {
        if(function_exists($name)) {
            return true;
        } else {
            if(strpos($name, "process_addon_") === 0) { 
                if(strlen($module) && file_exists(FF_DISK_PATH . "/modules/" . $module . "/library/process/addon/" . substr($name, strlen("process_addon_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/modules/" . $module . "/library/process/addon/" . substr($name, strlen("process_addon_")) . "." . FF_PHP_EXT);
                } elseif(file_exists(FF_DISK_PATH . "/library/gallery/process/addon/" . substr($name, strlen("process_addon_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/library/gallery/process/addon/" . substr($name, strlen("process_addon_")) . "." . FF_PHP_EXT);
                }
                if(function_exists($name)) {
                    return true;
                } else {
                    return false;
                }
            } elseif(strpos($name, "process_") === 0) {
                if(strlen($module) && file_exists(FF_DISK_PATH . "/modules/" . $module . "/library/process/" . substr($name, strlen("process_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/modules/" . $module . "/library/process/" . substr($name, strlen("process_")) . "." . FF_PHP_EXT);
                } elseif(file_exists(FF_DISK_PATH . "/library/gallery/process/" . substr($name, strlen("process_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/library/gallery/process/" . substr($name, strlen("process_")) . "." . FF_PHP_EXT);
                }

                if(function_exists($name)) {
                    return true;
                } else {
                    return false;
                }
            } elseif(strpos($name, "MD_general_") === 0) {
                require_once(FF_DISK_PATH . "/conf/gallery/modules/common" . "." . FF_PHP_EXT);
                if(function_exists($name)) {
                    return true;
                } else {
                    return false;
                }
            } elseif(strpos($name, "MD_") === 0) {
                require_once(FF_DISK_PATH . "/conf/gallery/modules/" . substr($name, 3, strpos($name, "_", 4) - 3) . "/common" . "." . FF_PHP_EXT);
                if(function_exists($name)) {
                    return true;
                } else {
                    return false;
                }
            } elseif(strpos($name, "ecommerce_cart_") === 0) {
                if(strlen($module) && file_exists(FF_DISK_PATH . "/modules/" . $module . "/library/ecommerce/cart/" . substr($name, strlen("ecommerce_cart_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/modules/" . $module . "/library/ecommerce/cart/" . substr($name, strlen("ecommerce_cart_")) . "." . FF_PHP_EXT);
                } elseif(file_exists(FF_DISK_PATH . "/library/gallery/ecommerce/cart/" . substr($name, strlen("ecommerce_cart_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/library/gallery/ecommerce/cart/" . substr($name, strlen("ecommerce_cart_")) . "." . FF_PHP_EXT);
                }
                
                if(function_exists($name)) {
                    return true;
                } else {
                    return false;
                }
                
            } elseif(strpos($name, "ecommerce_") === 0) {
                if(strlen($module) && file_exists(FF_DISK_PATH . "/modules/" . $module . "/library/ecommerce/" . substr($name, strlen("ecommerce_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/modules/" . $module . "/library/ecommerce/" . substr($name, strlen("ecommerce_")) . "." . FF_PHP_EXT);
                } elseif(file_exists(FF_DISK_PATH . "/library/gallery/ecommerce/" . substr($name, strlen("ecommerce_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/library/gallery/ecommerce/" . substr($name, strlen("ecommerce_")) . "." . FF_PHP_EXT);
                }
                
                if(function_exists($name)) {
                    return true;
                } else {
                    return false;
                }
            } elseif(strpos($name, "class.") === 0) {
                if(strlen($module) && file_exists(FF_DISK_PATH . "/modules/" . $module . "/library/" . substr($name, strlen("class.")) . "/" . $name . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/modules/" . $module . "/library/" . substr($name, strlen("class.")) . "/" . $name . "." . FF_PHP_EXT);
                } elseif(file_exists(FF_DISK_PATH . "/library/" . substr($name, strlen("class.")) . "/" . $name . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/library/" . substr($name, strlen("class.")) . "/" . $name . "." . FF_PHP_EXT);
                }
                
                if(class_exists(substr($name, strlen("class.")))) {
                    return true;
                } else {
                    return false;
                }
            } elseif(strpos($name, "service_") === 0) {
                if(strlen($module) && file_exists(FF_DISK_PATH . "/modules/" . $module . "/library/service/" . substr($name, strlen("service_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/modules/" . $module . "/library/service/" . substr($name, strlen("service_")) . "." . FF_PHP_EXT);
                } elseif(file_exists(FF_DISK_PATH . "/library/gallery/service/" . substr($name, strlen("service_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/library/gallery/service/" . substr($name, strlen("service_")) . "." . FF_PHP_EXT);
                }
                
                if(function_exists($name)) {
                    return true;
                } else {
                    return false;
                }
            } elseif(strpos($name, "mod_") === 0) {
                if(file_exists(FF_DISK_PATH . "/modules/" . substr($name, 4, strpos($name, "_", 4) - 4) . "/events." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/modules/" . substr($name, 4, strpos($name, "_", 4) - 4) . "/events." . FF_PHP_EXT);
                }
                if(function_exists($name)) {
                    return true;
                } else {
                    return false;
                }
            } elseif(strpos($name, "job_") === 0) {
                if(strlen($module) && file_exists(FF_DISK_PATH . "/modules/" . $module . "/library/job/" . substr($name, strlen("job_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/modules/" . $module . "/library/job/" . substr($name, strlen("job_")) . "." . FF_PHP_EXT);
                } elseif(file_exists(FF_DISK_PATH . "/library/gallery/job/" . substr($name, strlen("job_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/library/gallery/job/" . substr($name, strlen("job_")) . "." . FF_PHP_EXT);
                }

                if(function_exists($name)) {
                    return true;
                } else {
                    return false;
                }
            } elseif(strpos($name, "system_") === 0) {
                if(strlen($module) && file_exists(FF_DISK_PATH . "/modules/" . $module . "/library/system/" . substr($name, strlen("system_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/modules/" . $module . "/library/system/" . substr($name, strlen("system_")) . "." . FF_PHP_EXT);
                } elseif(file_exists(FF_DISK_PATH . "/library/gallery/system/" . substr($name, strlen("system_")) . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/library/gallery/system/" . substr($name, strlen("system_")) . "." . FF_PHP_EXT);
                }

                if(function_exists($name)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if(strlen($name) && file_exists(FF_DISK_PATH . "/library/gallery/common/" . $name . "." . FF_PHP_EXT)) {
                    require_once(FF_DISK_PATH . "/library/gallery/common/" . $name . "." . FF_PHP_EXT);
                }
                if(function_exists($name)) {
                    return true;
                } else {
                    return false;
                    /*ffErrorHandler::raise("Common Function Not Exist: " . $name, E_USER_ERROR, null, get_defined_vars());*/
                    die("Fatal Error: Common Function Not Exist");
                }
            }
        }    
    }
    
    
}
/*
if(check_function("ecommerce_cart_callback")) {
    cm::getInstance()->addEvent("vg_on_cart_process", "ecommerce_cart_callback");
    cm::getInstance()->addEvent("vg_on_cart_confirm_process", "ecommerce_cart_callback");
    
    cm::getInstance()->addEvent("vg_on_mpay_payed", "ecommerce_cart_callback");
    cm::getInstance()->addEvent("vg_on_recalc_bill_done", "ecommerce_cart_callback");
    cm::getInstance()->addEvent("vg_on_delete_order", "ecommerce_cart_callback");
    cm::getInstance()->addEvent("vg_on_form_done", "mod_recruitment_set_backer");
} */


function mod_recruitment_on_before_page_process($cm) {
    $globals = ffGlobals::getInstance("gallery"); 
    
    
    if(strpos($cm->path_info, MOD_RECRUITMENT_PATH) !== false) {
        if(is_dir(FF_DISK_PATH . FF_THEME_DIR . "/" . global_settings("MOD_RECRUITMENT_THEME"))) {
            $cm->layout_vars["theme"] = global_settings("MOD_RECRUITMENT_THEME");
        }
    }
}

function mod_recruitment_on_before_rounting($cm) {
     $permission = check_recruitment_permission(); 
    if($permission !== true
        && 
        (!(is_array($permission) && count($permission)
            && ($permission[global_settings("MOD_RECRUITMENT_GROUP_ADMIN")]
                || $permission[global_settings("MOD_RECRUITMENT_GROUP_ADVERTISER")]
                || $permission[global_settings("MOD_RECRUITMENT_GROUP_USER")]
            )
        ))
    ) {
        $cm->modules["restricted"]["menu"]["recruitment"]["hide"] = true;
        $cm->modules["restricted"]["menu"]["recruitment"]["elements"]["cv"]["hide"] = true;
        $cm->modules["restricted"]["menu"]["recruitment"]["elements"]["job-advertisement"]["hide"] = true;
        $cm->modules["restricted"]["menu"]["recruitment"]["elements"]["job-question"]["hide"] = true;
        $cm->modules["restricted"]["menu"]["recruitment"]["elements"]["category"]["hide"] = true; 
    } else {
        if(strpos($cm->path_info, MOD_RECRUITMENT_PATH) !== false
            || strpos($cm->path_info, MOD_RECRUITMENT_USER_PATH) !== false
        ) {
            if(is_dir(FF_DISK_PATH . FF_THEME_DIR . "library/jquery.ui/themes/" . global_settings("MOD_RECRUITMENT_JQUERYUI_THEME"))) {
                $cm->oPage->jquery_ui_force_theme = global_settings("MOD_RECRUITMENT_JQUERYUI_THEME");
            }

            if(!MOD_SEC_GROUPS || $permission["primary_group"] != global_settings("MOD_RECRUITMENT_GROUP_ADMIN")) {
                $cm->modules["restricted"]["menu"]["recruitment"]["elements"]["cv"]["hide"] = false;
                $cm->modules["restricted"]["menu"]["recruitment"]["elements"]["job-advertisement"]["hide"] = false;
                $cm->modules["restricted"]["menu"]["recruitment"]["elements"]["job-question"]["hide"] = false;
                $cm->modules["restricted"]["menu"]["recruitment"]["elements"]["category"]["hide"] = false;
			}
            if($permission["primary_group"] != global_settings("MOD_RECRUITMENT_GROUP_ADVERTISER")) {
                $cm->modules["restricted"]["menu"]["recruitment"]["elements"]["job-advertisement"]["hide"] = false;
                $cm->modules["restricted"]["menu"]["recruitment"]["elements"]["job-question"]["hide"] = false;
            }
            if($permission["primary_group"] != global_settings("MOD_RECRUITMENT_GROUP_USER")) {
            	$cm->modules["restricted"]["menu"]["recruitment"]["elements"]["cv"]["hide"] = false;
            }
            
            if(function_exists("check_function") && check_function("system_set_js")) {
                system_set_js($cm->oPage, $cm->path_info, false, "/modules/recruitment/themes/javascript", true);
            }
        }
    }
}

function check_recruitment_permission($check_group = null) {
    if(!MOD_SEC_GROUPS) 
        return true;

    $db = ffDB_Sql::factory();

    $user_permission = get_session("user_permission");
    $userID = get_session("UserID");

    if(is_array($user_permission) && count($user_permission) 
        && is_array($user_permission["groups"]) && count($user_permission["groups"])
        && $userID != MOD_SEC_GUEST_USER_NAME
    ) {
        if(!array_key_exists("permissions_custom", $user_permission))
            $user_permission["permissions_custom"] = array();

        if(!(array_key_exists("recruitment", $user_permission["permissions_custom"]) && count($user_permission["permissions_custom"]["recruitment"]))) {
            $user_permission["permissions_custom"]["recruitment"] = array();
            
            $strGroups = implode(",", $user_permission["groups"]);
            $strPermission = $db->toSql(global_settings("MOD_RECRUITMENT_GROUP_ADMIN"), "Text") 
                            . "," . $db->toSql(global_settings("MOD_RECRUITMENT_GROUP_USER"), "Text"); 

            $user_permission["permissions_custom"]["recruitment"][global_settings("MOD_RECRUITMENT_GROUP_ADMIN")] = false;
            $user_permission["permissions_custom"]["recruitment"][global_settings("MOD_RECRUITMENT_GROUP_USER")] = false;
            $user_permission["permissions_custom"]["recruitment"]["primary_group"] = "";
            
            $sSQL = "SELECT DISTINCT " . CM_TABLE_PREFIX . "mod_security_groups.name
                        , (SELECT GROUP_CONCAT(anagraph.ID) FROM anagraph WHERE anagraph.uid = " . $db->toSql(get_session("UserNID"), "Number") . ") AS anagraph
                    FROM " . CM_TABLE_PREFIX . "mod_security_groups
                      INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users_rel_groups ON " . CM_TABLE_PREFIX . "mod_security_users_rel_groups.gid = " . CM_TABLE_PREFIX . "mod_security_groups.gid
                    WHERE " . CM_TABLE_PREFIX . "mod_security_users_rel_groups.gid IN ( " . $db->toSql($strGroups, "Text", false) . " )
                      AND " . CM_TABLE_PREFIX . "mod_security_groups.name IN ( " . $strPermission . " )";
            $db->query($sSQL);
            if($db->nextRecord()) {
                do {
                    $user_permission["permissions_custom"]["recruitment"][$db->getField("name", "Text", true)] = true;
                    $user_permission["permissions_custom"]["recruitment"]["primary_group"] = $db->getField("name", "Text", true);
                } while($db->nextRecord());
            }
            
            set_session("user_permission", $user_permission);
        }
        if($check_group === null) { 
            return $user_permission["permissions_custom"]["recruitment"];
        } else {
            return $user_permission["permissions_custom"]["recruitment"]["primary_group"];
        }
    }    
    return null;
}

function mod_recruitment_mod_security_on_created_session($user, $old_session_id = null, $permanent_session = null) {
    $user_permission = get_session("user_permission");

    if($user_permission["primary_gid_name"] == global_settings("MOD_RECRUITMENT_GROUP_ADMIN")) {
        if(strlen(MOD_RECRUITMENT_PATH)) {
            if(strpos($_REQUEST["ret_url"], MOD_RECRUITMENT_PATH) !== 0) {
                ffRedirect(FF_SITE_PATH . MOD_RECRUITMENT_PATH);
            }
        }
    }

    if(strtolower($user_permission["primary_gid_name"]) == "recruitment"
        || $user_permission["primary_gid_name"] == global_settings("MOD_RECRUITMENT_GROUP_USER")
    ) {
    }
}

function mod_security_AllUserInfoStructure($db = null)
{
    static $structure = array();
    
    if(is_array ($structure) && !count($structure))
    {
        $cm = cm::getInstance();
        if ($db === null)
            $db = ffDb_Sql::factory();


        $options = mod_security_get_settings($cm->path_info);
        $array_normalized = normalizeAllUserInfo();

        if (strlen(MOD_SEC_DEFAULT_FIELDS))
        {
            foreach (explode(",", MOD_SEC_DEFAULT_FIELDS) as $value) {
                if(array_key_exists($value, $array_normalized)) {
                    $structure["user"][$value]["table"] = $options["table_name"];
                    $structure["user"][$value]["field"] = $value;
                    $structure["user"][$value]["field_normalized"] = $array_normalized[$value];
                } else {
                    $structure["user"][$value]["table"] = $options["table_name"];
                    $structure["user"][$value]["field"] = $value;
                    $structure["user"][$value]["field_normalized"] = $value;
                }
            }
        }
		$sSQL = "SELECT " . $options["table_dett_name"] . ".*
                    FROM " . $options["table_dett_name"] . "
                    WHERE 1 
                    GROUP BY " . $options["table_dett_name"] . ".field";
        $db->query($sSQL);
        if($db->nextRecord())
        {
            do {
                $field = $db->getField("field", "Text", true);
                if(array_key_exists($field, $array_normalized)) {
                    $structure["field"][$field]["table"] = $options["table_dett_name"];
                    $structure["field"][$field]["field"] = $field;
                    $structure["field"][$field]["field_normalized"] = $array_normalized[$field];
                } else {
                    $structure["field"][$field]["table"] = $options["table_dett_name"];
                    $structure["field"][$field]["field"] = $field;
                    $structure["field"][$field]["field_normalized"] = $field;
                }
            } while($db->nextRecord());
        }

        if((is_array($structure["field"]) && count($structure["field"])) && (isset($cm->modules["security"]["fields"]) && count($cm->modules["security"]["fields"])))
        {
			foreach ($cm->modules["security"]["fields"] as $key => $value)
            {
                if(!array_key_exists($key, $structure["field"]))
                {
                    if(array_key_exists($key, $array_normalized)) {
                        $structure["other"][$key]["table"] = $options["table_dett_name"];
                        $structure["other"][$key]["field"] = $key;
                        $structure["other"][$key]["field_normalized"] = $array_normalized[$key];
                    } else {
                        $structure["other"][$key]["table"] = $options["table_dett_name"];
                        $structure["other"][$key]["field"] = $key;
                        $structure["other"][$key]["field_normalized"] = $key;
                    }
                }
            }
        }
    }  
    return $structure;
}

function mod_security_getAllUserInfo($ID_user = null, $db = null, $destroy_session = true)
{
	if ($ID_user === null)
    	$ID_user = get_session("UserNID");

	return getAllUserInfo($ID_user, $db, $destroy_session);
}

function getAllUserInfo($ID_user, $db = null, $destroy_session = true)
{
    static $information = array();
    if(!array_key_exists($ID_user, $information))
    {
        $cm = cm::getInstance();

        if ($db === null)
            $db = ffDb_Sql::factory();

        $options = mod_security_get_settings($cm->path_info);
        $structure = mod_security_AllUserInfoStructure();
        
        if(is_array($structure) && count($structure))
        {
            if(is_array($structure["user"]) && count($structure["user"]))
            {
                $sSQL = "SELECT " . $options["table_name"] . ".*
                            FROM " . $options["table_name"] . "
                            WHERE " . $options["table_name"] . ".ID = " . $db->toSql($ID_user, "Number");
                $db->query($sSQL);
                if($db->nextRecord())
                {
                    if(is_array($db->fields) && count($db->fields))
                    {
                        foreach($db->fields AS $key_field => $key_value)
                        {
                            if(array_key_exists($key_field, $structure["user"]))
                            {
                                $information[$ID_user][$structure["user"][$key_field]["field_normalized"]] = $db->getField($key_field, "Text", true);
                            }
                        }
                    }
                } 
            }
            
            if(is_array($structure["field"]) && count($structure["field"]))
            {
                $sSQL = "SELECT " . $options["table_dett_name"] . ".*
                            FROM " . $options["table_dett_name"] . "
                            WHERE " . $options["table_dett_name"] . ".ID_users = " . $db->toSql($ID_user, "Number");
                $db->query($sSQL);
                if($db->nextRecord())
                {
                    do {
                        $field = $db->getField("field", "Text", true);
                        if(array_key_exists($field, $structure["field"]))
                        {
                            $information[$ID_user][$structure["field"][$field]["field_normalized"]] = $db->getField("value", "Text", true);
                        }
                    } while($db->nextRecord());
                }
            }
        } else if ($destroy_session)
        {
            mod_security_destroy_session(false);
            unset($_GET[session_name()], $_POST[session_name()], $_COOKIE[session_name()], $_REQUEST[session_name()]);
            //ffErrorHandler::raise("mod_security: User Not Found!!!", E_USER_ERROR, null, get_defined_vars());
        }
    }
    return $information[$ID_user];
}

function normalizeAllUserInfo() {
    $array_key = array(
        "name"          => "firstname",
        "surname"       => "lastname",
        "billaddress"   => "address",
        "billtown"      => "city",
        "billcap"       => "cap",
        "billcf"        => "cf",
        "billpiva"      => "piva",
        "billprovince"  => "prov",
        "cellular"      => "cell"
    );
    return($array_key);
}

function mod_security_setAllUserInfo($field, $ID_user = null, $db = null)
{
    if ($ID_user === null)
        $ID_user = get_session("UserNID");
    return setAllUserInfo($field, $ID_user, $db);
}

function setAllUserInfo($field, $ID_user, $db)
{
    if (is_array($field) && count($field))
    {
        $cm = cm::getInstance();
        if ($db === null)
            $db = ffDb_Sql::factory();
        
        $options = mod_security_get_settings($cm->path_info);
        
        if(!$ID_user && strlen($field["email"])) {
            $unknow_user = mod_recruitment_verify_existing_user($field["email"]);
            if ($unknow_user["ID"] > 0) {
                ffRedirect("/login?username=" . urlencode($unknow_user["username"]) . "&ret_url=" . (isset($_REQUEST["ret_url"]) ? urlencode($_REQUEST["ret_url"]) : "/"));
            } else {
                $name = $field["name"] . " " . $field["surname"];
                $username = ffCommon_url_rewrite($field["name"] . $field["surname"]);
                $password = mod_sec_createRandomPassword();
                
                $sSQL_manage = "INSERT INTO " . $options["table_name"] . "
							(
								ID
								, username
								, username_slug
                                                                , password
								, email
								, status
							)
							VALUES
							(
								null
								, " . $db->toSql($username, "Text") . "
								, " . $db->toSql(ffCommon_url_rewrite($username), "Text") . "
                                                                , PASSWORD(" . $db->toSql($password) . ")
								, " . $db->toSql($field["email"], "Text") . "
								, " . $db->toSql("1", "Text") . "
							)";
                $db->execute($sSQL_manage);
                $ID_user = $db->getInsertID(true);
                
                mod_security_create_session(null, $ID_user); // DA CONTROLLARE + PROBLEMA ESPERIENZE
                
                $to[0]["name"] = $name;
                $to[0]["mail"] = $field["email"];

                $fields["account"]["username"] = $username;
                $fields["account"]["password"] = $password;
                $fields["account"]["email"] = $field["email"];
                $fields["account"]["login"] = "http://" . DOMAIN_INSET . "/login?username=" . urlencode($username) . "&ret_url=" . urlencode(FF_SITE_PATH . USER_RESTRICTED_PATH . "/account"); // DA CONTROLLARE
                
                if(function_exists("check_function") && check_function("process_mail", "recruitment")) {
					$rc = process_mail(array( 
                                                    "mail" => array(
                                                            "smtp" => array(
                                                                    "host" => RICHIESTE_MAIL_HOST
                                                                    , "auth" => RICHIESTE_MAIL_ENABLE_SMTP
                                                                    , "username" => RICHIESTE_MAIL_USER
                                                                    , "password" => RICHIESTE_MAIL_PASS
                                                            )
                                                            , "subject" => ffTemplate::_get_word_by_code("email_auto_registration_subject")
                                                            , "name" => ""
                                                            , "tpl_path" =>  "/modules/recruitment/themes/restricted/mail/email.tpl"
                                                            , "from" => array(
                                                                            "name" => RICHIESTE_MAIL_FROM_NAME
                                                                            , "mail" => RICHIESTE_MAIL_FROM
                                                                        )
                                                    )
		
                        ), $to, NULL, NULL, $fields, null, null, null, false, true, false);
					//echo $rc;
				}
                
            }
        }
        $normalized = normalizeAllUserInfo();
        
        if($ID_user > 0)
        {
        $structure = mod_security_AllUserInfoStructure();
		
		foreach($field AS $key => $value)
        {
            if(is_array($structure["user"]) && array_key_exists($key, $structure["user"]))
            {
                if(!strlen($sSQL))
                    $sSQL = $options["table_name"] . "." . $key . " = " . $db->toSql($value);
                else
                    $sSQL .= ", " . $options["table_name"] . "." . $key . " = " . $db->toSql($value);
            } else if (is_array($structure["user"]) && array_key_exists($normalized[$key], $structure["user"]))
            {
                if(!strlen($sSQL))
                    $sSQL = $options["table_name"] . "." . $normalized[$key] . " = " . $db->toSql($value);
                else
                    $sSQL .= ", " . $options["table_name"] . "." . $normalized[$key] . " = " . $db->toSql($value);
            } else if(is_array($structure["field"]) && array_key_exists($key, $structure["field"]))
            {
                $sSQL2 = "SELECT ID
                            FROM " . $options["table_dett_name"] . "
                            WHERE " . $options["table_dett_name"] . ".field = " . $db->toSql($key, "Text") . "
                                AND " . $options["table_dett_name"] . ".ID_users = " . $db->toSql($ID_user, "Number");
                $db->query($sSQL2);
                if($db->nextRecord()) {
                    $sSQL2 = "UPDATE " . $options["table_dett_name"] . " SET " . 
                                $options["table_dett_name"] . ".value = " . $db->toSql($value, "Text") . "
                                WHERE " . $options["table_dett_name"] . ".field = " . $db->toSql($key, "Text") . "
                                    AND " . $options["table_dett_name"] . ".ID_users = " . $db->toSql($ID_user, "Number");
                    $db->execute($sSQL2);
                } else {
                    $sSQL2 = "INSERT INTO " . $options["table_dett_name"] . "
                                (
                                    ID
                                    , ID_users
                                    , field
                                    , value
                                ) VALUES
                                (
                                    null
                                    , " . $db->toSql($ID_user, "Number") . "
                                    , " . $db->toSql($key, "Text") . "
                                    , " . $db->toSql($value, "Text") . "
                                )";
                    $db->execute($sSQL2);
                }
            } else if(is_array($structure["field"]) && array_key_exists($normalized[$key], $structure["field"]))
            {
                $sSQL2 = "SELECT ID
                            FROM " . $options["table_dett_name"] . " 
                            WHERE " . $options["table_dett_name"] . ".field = " . $db->toSql($normalized[$key], "Text") . "
                                AND " . $options["table_dett_name"] . ".ID_users = " . $db->toSql($ID_user, "Number");
                $db->query($sSQL2);
                if($db->nextRecord()) {
                    $sSQL2 = "UPDATE " . $options["table_dett_name"] . " SET " . 
                                $options["table_dett_name"] . ".value = " . $db->toSql($value, "Text") . "
                                WHERE " . $options["table_dett_name"] . ".field = " . $db->toSql($normalized[$key], "Text") . "
                                    AND " . $options["table_dett_name"] . ".ID_users = " . $db->toSql($ID_user, "Number");
                    $db->execute($sSQL2);
                } else {
                    $sSQL2 = "INSERT INTO " . $options["table_dett_name"] . "
                                    ( 
                                        ID
                                        , ID_users
                                        , field
                                        , value
                                    ) VALUES
                                    (
                                        null
                                        , " . $db->toSql($ID_user, "Number") . "
                                        , " . $db->toSql($normalized[$key], "Text") . "
                                        , " . $db->toSql($value, "Text") . "
                                    )";
                    $db->execute($sSQL2);
                }
            } else if(is_array($structure["other"]) && array_key_exists($key, $structure["other"]))
            {
                $sSQL2 = "INSERT INTO " . $options["table_dett_name"] . "
                            (
                                " . $options["table_dett_name"] . ".ID
                                , " . $options["table_dett_name"] . ".ID_users
                                , " . $options["table_dett_name"] . ".field
                                , " . $options["table_dett_name"] . ".value
                            ) VALUES
                            (
                                null
                                , " . $db->toSql($ID_user, "Number") . " 
                                , " . $db->toSql($key, "Text") . " 
                                , " . $db->toSql($value, "Text") . "
                            ) ";
                $db->execute($sSQL2); 
            } else if(is_array($structure["other"]) && array_key_exists($normalized[$key], $structure["other"]))
            {
                $sSQL2 = "INSERT INTO " . $options["table_dett_name"] . "
                            (
                                " . $options["table_dett_name"] . ".ID
                                , " . $options["table_dett_name"] . ".ID_users
                                , " . $options["table_dett_name"] . ".field
                                , " . $options["table_dett_name"] . ".value
                            ) VALUES
                            (
                                null
                                , " . $db->toSql($ID_user, "Number") . " 
                                , " . $db->toSql($normalized[$key], "Text") . " 
                                , " . $db->toSql($value, "Text") . "
                            ) ";
                $db->execute($sSQL2);
            } 
        }
            
        if(strlen($sSQL))
        {
            $sSQL = "UPDATE " . $options["table_name"] . " SET " . $sSQL .  " WHERE " . $options["table_name"] . ".ID = " . $db->toSql($ID_user, "Number");
            $db->execute($sSQL);
        }
        }
    }
    return $ID_user;
}

function mod_recruitment_verify_existing_user($email = null)
{
    $db = ffDb_Sql::factory();
    $exixting = false;
    
    $options = mod_security_get_settings($cm->path_info);
    
    if(strlen($email)) {
        $sSQL = "SELECT " . $options["table_name"] . ".*
			FROM " . $options["table_name"] . "
			WHERE " . $options["table_name"] . ".email = " . $db->toSql($email, "Text");
        $db->query($sSQL);
        if ($db->nextRecord()) {
            $exixting["ID"] = $db->getField("ID", "Number", true);
            $exixting["username"] = $db->getField("username", "Text", true);
        } 
    }
    
    return $exixting;
        
}

function mod_recruitment_get_menu() 
{
    $cm = cm::getInstance();
    
    $menu = array(
                "basic" => ffTemplate::_get_word_by_code("recruit_basic_description"),
                "experience" => ffTemplate::_get_word_by_code("recruit_experience_description"),
                "lang" => ffTemplate::_get_word_by_code("recruit_lang_description"),
                "other" => ffTemplate::_get_word_by_code("recruit_other_description"),
            );
	

    $tpl_name = "menu";

    $filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/recruitment/contents/" . $tpl_name . ".html", $cm->oPage->theme, false);
    if ($filename === null)
    {
        $filename = cm_moduleCascadeFindTemplate(CM_MODULES_ROOT . "/recruitment/themes", "/contents/" . $tpl_name . ".html", $cm->oPage->theme);
    }
    
    $tpl = ffTemplate::factory(ffCommon_dirname($filename));
    $tpl->load_file($tpl_name . ".html", "main");

    $tpl->set_var("theme", $cm->oPage->theme);
    $tpl->set_var("site_path", $cm->oPage->site_path);
    $tpl->set_var("idea_base_path", $cm->oPage->site_path . ffcommon_dirname($cm->oPage->page_path));
    $tpl->set_var("ret_url", urlencode($_REQUEST["ret_url"]));


    if(is_array($menu) && count($menu)) 
    {
        foreach($menu AS $type_key => $type_value) 
        {
            $process_url = $cm->oPage->site_path . MOD_RECRUITMENT_PATH . "/" . $type_key . $cm->real_path_info . "?ret_url=" . urlencode($cm->oPage->site_path . ffcommon_dirname($cm->oPage->page_path) . "/" . $type_key . $cm->real_path_info);
            $tpl->set_var("recruting_menu_action", $process_url);
            $tpl->set_var("item_label", $type_value);
            //$tpl->set_var("recruting_menu_action_try", "jQuery(this).closest('form').action = '" . $process_url . "'; document.getElementById('frmAction').value = 'cvModify_update'; jQuery(this).closest('form').submit();");
            $tpl->parse("SezRecruitmentMenuItem", true);
        }
    }
    $tpl_menu = $tpl->rpparse("main", false);

    return $tpl_menu;
	
} 

function mod_recruitment_control_smart_url($table)
{
	$db = ffDB_Sql::factory();
	$sSQL = "UPDATE " . CM_TABLE_PREFIX . $table . "
				SET " . CM_TABLE_PREFIX . $table . ".smart_url = 
					CONCAT( " . CM_TABLE_PREFIX . $table . ".smart_url, '-', " . CM_TABLE_PREFIX . $table . ".ID)
				WHERE " . CM_TABLE_PREFIX . $table . ".smart_url IN
											(
												SELECT smart_url
												FROM	(SELECT " . CM_TABLE_PREFIX . $table . ".smart_url AS smart_url
															, COUNT(" . CM_TABLE_PREFIX . $table . ".ID) AS answer_count
															FROM " . CM_TABLE_PREFIX . $table . "
															WHERE 1
															GROUP BY " . CM_TABLE_PREFIX . $table . ".smart_url
															HAVING answer_count > 1
														) AS tbl

											)";
	$db->execute($sSQL);
}
?>