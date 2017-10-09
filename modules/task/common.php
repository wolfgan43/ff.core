<?php
cm::getInstance()->addEvent("on_before_page_process", "mod_task_on_before_page_process", ffEvent::PRIORITY_NORMAL);
cm::getInstance()->addEvent("on_before_routing", "mod_task_on_before_rounting", ffEvent::PRIORITY_NORMAL);
cm::getInstance()->addEvent("mod_security_on_created_session", "mod_task_mod_security_on_created_session", ffEvent::PRIORITY_NORMAL);

define("MOD_TASK_PATH", $cm->router->named_rules["task"]->reverse); 
define("MOD_TASK_SERVICES_PATH", $cm->router->named_rules["task_services"]->reverse);

if(!function_exists("global_settings")) {
	function global_settings($key = null) {
		static $global_settings = false;

		if($global_settings === false) {
			$global_settings = mod_restricted_get_all_setting();
		}

		if(is_array($global_settings) && array_key_exists($key, $global_settings)) {
			return $global_settings[$key];
		} else {
			return null;
		}
	}	
}

if(!defined("DOMAIN_INSET")) define("DOMAIN_INSET", $_SERVER["HTTP_HOST"]);
if(!defined("DISK_UPDIR")) define("DISK_UPDIR", FF_DISK_PATH . FF_UPDIR);

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
				require_once(FF_DISK_PATH . VG_ADDONS_PATH . "/common" . "." . FF_PHP_EXT);
				if(function_exists($name)) {
					return true;
				} else {
					return false;
				}
			} elseif(strpos($name, "MD_") === 0) {
				require_once(FF_DISK_PATH . VG_ADDONS_PATH . "/" . substr($name, 3, strpos($name, "_", 4) - 3) . "/common" . "." . FF_PHP_EXT);
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
					/*ffErrorHandler::raise("Common Function Not Exist: " . $name, E_USER_ERROR, null, get_defined_vars());
					die("Fatal Error: Common Function Not Exist");*/
				}
			}
		}	
	}
	
	
}

function mod_task_on_before_page_process($cm) {
	if(strpos($cm->path_info, MOD_TASK_PATH) !== false) {
        if(strlen(global_settings("MOD_TASK_THEME")) && is_dir(FF_DISK_PATH . FF_THEME_DIR . "/" . global_settings("MOD_TASK_THEME"))) {  
		    $cm->layout_vars["theme"] = global_settings("MOD_TASK_THEME");
        }
	}
}

function mod_task_on_before_rounting($cm) { 
	$permission = check_task_permission();
	if($permission !== true
		&& 
		(!(is_array($permission) && count($permission)
			&& ($permission[global_settings("MOD_TASK_GROUP_ADMIN")]
			)
		))
	) {
    	$cm->modules["restricted"]["menu"]["task"]["hide"] = true;
		$cm->modules["restricted"]["menu"]["task"]["elements"]["project"]["hide"] = true;
		$cm->modules["restricted"]["menu"]["task"]["elements"]["settings"]["hide"] = true;
	} else {
		if(strpos($cm->path_info, MOD_TASK_PATH) !== false
		) {
            if(is_dir(FF_DISK_PATH . FF_THEME_DIR . "library/jquery-ui.themes/" . global_settings("MOD_TASK_JQUERYUI_THEME"))) {
    		    $cm->oPage->jquery_ui_theme = global_settings("MOD_TASK_JQUERYUI_THEME");
            }

			$cm->modules["restricted"]["menu"]["task"]["elements"]["alerts"]["hide"] = false;
			$cm->modules["restricted"]["menu"]["task"]["elements"]["settings"]["hide"] = false;
			if($permission["primary_group"] != global_settings("MOD_TASK_GROUP_ADMIN")) {
			}
			
		}
	}
}

function check_task_permission($check_group = null) {
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

		if(!(array_key_exists("task", $user_permission["permissions_custom"]) && count($user_permission["permissions_custom"]["task"]))) {
	    	$user_permission["permissions_custom"]["task"] = array();
	    	
			$strGroups = implode(",", $user_permission["groups"]);
			$strPermission = $db->toSql(global_settings("MOD_TASK_GROUP_ADMIN"), "Text"); 

			$user_permission["permissions_custom"]["task"][global_settings("MOD_TASK_GROUP_ADMIN")] = false;
			$user_permission["permissions_custom"]["task"]["primary_group"] = "";
			
			$sSQL = "SELECT DISTINCT " . CM_TABLE_PREFIX . "mod_security_groups.name
			            , (SELECT GROUP_CONCAT(anagraph.ID) FROM anagraph WHERE anagraph.uid = " . $db->toSql(get_session("UserNID"), "Number") . ") AS anagraph
			        FROM " . CM_TABLE_PREFIX . "mod_security_groups
			          INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users_rel_groups ON " . CM_TABLE_PREFIX . "mod_security_users_rel_groups.gid = " . CM_TABLE_PREFIX . "mod_security_groups.gid
			        WHERE " . CM_TABLE_PREFIX . "mod_security_users_rel_groups.gid IN ( " . $db->toSql($strGroups, "Text", false) . " )
			          AND " . CM_TABLE_PREFIX . "mod_security_groups.name IN ( " . $strPermission . " )";
			$db->query($sSQL);
			if($db->nextRecord()) {
				do {
				    $user_permission["permissions_custom"]["task"][$db->getField("name", "Text", true)] = true;
				    $user_permission["permissions_custom"]["task"]["primary_group"] = $db->getField("name", "Text", true);
				} while($db->nextRecord());
			}
			
		    set_session("user_permission", $user_permission);
		}    
		if($check_group === null) { 
	    	return $user_permission["permissions_custom"]["task"];
		} else {
			return $user_permission["permissions_custom"]["task"]["primary_group"];
		}
	}    

    return null;
}

function mod_task_mod_security_on_created_session($user, $old_session_id = null, $permanent_session = null) {
	$user_permission = get_session("user_permission");

	if($user_permission["primary_gid_name"] == global_settings("MOD_TASK_GROUP_ADMIN")) {
		if(strlen(MOD_TASK_PATH)) {
			if(strpos($_REQUEST["ret_url"], MOD_TASK_PATH) !== 0) {
				ffRedirect(FF_SITE_PATH . MOD_TASK_PATH);
			}
		}
	}
}