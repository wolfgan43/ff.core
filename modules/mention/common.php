<?php
cm::getInstance()->addEvent("on_before_page_process", "mod_mention_on_before_page_process", ffEvent::PRIORITY_NORMAL);
cm::getInstance()->addEvent("on_before_routing", "mod_mention_on_before_rounting", ffEvent::PRIORITY_NORMAL);
//cm::getInstance()->addEvent("mod_security_on_created_session", "mod_mention_mod_security_on_created_session", ffEvent::PRIORITY_NORMAL);

define("MOD_MENTION_PATH", $cm->router->named_rules["mention"]->reverse); 
define("MOD_MENTION_SERVICES_PATH", $cm->router->named_rules["mention_services"]->reverse);

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
                    /*ffErrorHandler::raise("Common Function Not Exist: " . $name, E_USER_ERROR, null, get_defined_vars());
                    die("Fatal Error: Common Function Not Exist");*/
                }
            }
        }    
    }
    
    
}

if(!function_exists("global_settings")) {
	function global_settings($key = null) {
		static $global_settings = false;

		if($global_settings === false) {
			$global_settings = mod_restricted_get_all_setting();
		}

		if(array_key_exists($key, $global_settings)) {
			return $global_settings[$key];
		} else {
			return null;
		}
	}	
}

function mod_mention_on_before_page_process($cm) {
	if(strpos($cm->path_info, MOD_MENTION_PATH) !== false) {
        if(strlen(global_settings("MOD_MENTION_THEME")) && is_dir(FF_DISK_PATH . FF_THEME_DIR . "/" . global_settings("MOD_MENTION_THEME"))) {
		    $cm->layout_vars["theme"] = global_settings("MOD_MENTION_THEME");
        }
	}
}

function mod_mention_on_before_rounting($cm) {
	$permission = check_mention_permission();
	if($permission !== true
		&& 
		(!(is_array($permission) && count($permission)
			&& ($permission[global_settings("MOD_MENTION_GROUP_ADMIN")]
			)
		))
	) {
    	$cm->modules["restricted"]["menu"]["mention"]["hide"] = true;
		$cm->modules["restricted"]["menu"]["mention"]["elements"]["alerts"]["hide"] = true;
		$cm->modules["restricted"]["menu"]["mention"]["elements"]["ban"]["hide"] = true;
		$cm->modules["restricted"]["menu"]["mention"]["elements"]["settings"]["hide"] = true;
	} else {
		if(strpos($cm->path_info, MOD_MENTION_PATH) !== false
		) {
            if(is_dir(FF_DISK_PATH . FF_THEME_DIR . "library/jquery.ui/themes/" . global_settings("MOD_MENTION_JQUERYUI_THEME"))) {
    		    $cm->oPage->jquery_ui_force_theme = global_settings("MOD_MENTION_JQUERYUI_THEME");
            }


			if(!MOD_SEC_GROUPS || $permission["primary_group"] != global_settings("MOD_MENTION_GROUP_ADMIN")) {
                $cm->modules["restricted"]["menu"]["mention"]["elements"]["alerts"]["hide"] = false;
                $cm->modules["restricted"]["menu"]["mention"]["elements"]["ban"]["hide"] = false;
                $cm->modules["restricted"]["menu"]["mention"]["elements"]["settings"]["hide"] = false;
			}
			
			if(function_exists("check_function") && check_function("system_set_js")) {
				system_set_js($cm->oPage, $cm->path_info, false, "/modules/mention/themes/javascript", true);
			}
		}
	}
}

function check_mention_permission($check_group = null) {
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

		if(!(array_key_exists("mention", $user_permission["permissions_custom"]) && count($user_permission["permissions_custom"]["mention"]))) {
	    	$user_permission["permissions_custom"]["mention"] = array();
	    	
			$strGroups = implode(",", $user_permission["groups"]);
			$strPermission = $db->toSql(global_settings("MOD_MENTION_GROUP_ADMIN"), "Text"); 

			$user_permission["permissions_custom"]["mention"][global_settings("MOD_MENTION_GROUP_ADMIN")] = false;
			$user_permission["permissions_custom"]["mention"]["primary_group"] = "";
			
			$sSQL = "SELECT DISTINCT " . CM_TABLE_PREFIX . "mod_security_groups.name
			            , (SELECT GROUP_CONCAT(anagraph.ID) FROM anagraph WHERE anagraph.uid = " . $db->toSql(get_session("UserNID"), "Number") . ") AS anagraph
			        FROM " . CM_TABLE_PREFIX . "mod_security_groups
			          INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users_rel_groups ON " . CM_TABLE_PREFIX . "mod_security_users_rel_groups.gid = " . CM_TABLE_PREFIX . "mod_security_groups.gid
			        WHERE " . CM_TABLE_PREFIX . "mod_security_users_rel_groups.gid IN ( " . $db->toSql($strGroups, "Text", false) . " )
			          AND " . CM_TABLE_PREFIX . "mod_security_groups.name IN ( " . $strPermission . " )";
			$db->query($sSQL);
			if($db->nextRecord()) {
				do {
				    $user_permission["permissions_custom"]["mention"][$db->getField("name", "Text", true)] = true;
				    $user_permission["permissions_custom"]["mention"]["primary_group"] = $db->getField("name", "Text", true);
				} while($db->nextRecord());
			}
			
		    set_session("user_permission", $user_permission);
		}    
		if($check_group === null) { 
	    	return $user_permission["permissions_custom"]["mention"];
		} else {
			return $user_permission["permissions_custom"]["mention"]["primary_group"];
		}
	}    

    return null;
}

function mod_mention_mod_security_on_created_session($user, $old_session_id = null, $permanent_session = null) {
	$user_permission = get_session("user_permission");

	if($user_permission["primary_gid_name"] == global_settings("MOD_MENTION_GROUP_ADMIN")) {
		if(strlen(MOD_MENTION_PATH)) {
			if(strpos($_REQUEST["ret_url"], MOD_MENTION_PATH) !== 0) {
				ffRedirect(FF_SITE_PATH . MOD_MENTION_PATH);
			}
		}
	}
}


function mod_mention_db_get_alerts() {
	$db = ffDB_Sql::factory();
	$alerts = array();

	$sSQL = "SELECT ". CM_TABLE_PREFIX . "mod_mention_alerts.*
				, (SELECT GROUP_CONCAT(DISTINCT ". CM_TABLE_PREFIX . "mod_mention_ban_keyword.name SEPARATOR ',')
					FROM ". CM_TABLE_PREFIX . "mod_mention_ban_keyword
					WHERE ". CM_TABLE_PREFIX . "mod_mention_ban_keyword.status > 0 
				) AS ban_keywords
                , (SELECT GROUP_CONCAT(DISTINCT ". CM_TABLE_PREFIX . "mod_mention_ban_domain.name SEPARATOR ',')
                    FROM ". CM_TABLE_PREFIX . "mod_mention_ban_domain
                    WHERE ". CM_TABLE_PREFIX . "mod_mention_ban_domain.status > 0 
                ) AS ban_domains
				, IFNULL(
					(
						SELECT ". CM_TABLE_PREFIX . "mod_mention_mentions.ID_source
						FROM ". CM_TABLE_PREFIX . "mod_mention_mentions
						WHERE ". CM_TABLE_PREFIX . "mod_mention_mentions.ID_alert = ". CM_TABLE_PREFIX . "mod_mention_alerts.ID
						ORDER BY ". CM_TABLE_PREFIX . "mod_mention_mentions.ID_source DESC
						LIMIT 1
					)
					, 0
				) AS ID_last_mention
			FROM ". CM_TABLE_PREFIX . "mod_mention_alerts
			WHERE 1
			ORDER BY ". CM_TABLE_PREFIX . "mod_mention_alerts.name";
	$db->query($sSQL);
	if($db->nextRecord()) {
		do {
			if(strlen($db->getField("excluded_keywords", "Text", true)) && strlen($db->getField("ban_keywords", "Text", true)))
				$excluded_keywords = $db->getField("excluded_keywords", "Text", true) . "," . $db->getField("ban_keywords", "Text", true);
			else 
				$excluded_keywords = $db->getField("excluded_keywords", "Text", true) . $db->getField("ban_keywords", "Text", true);

            if(strlen($db->getField("blocked_sites", "Text", true)) && strlen($db->getField("ban_domains", "Text", true)))
                $blocked_sites = $db->getField("blocked_sites", "Text", true) . "," . $db->getField("ban_domains", "Text", true);
            else 
                $blocked_sites = $db->getField("blocked_sites", "Text", true) . $db->getField("ban_domains", "Text", true);

			$alerts[$db->getField("name", "Text", true)] = array(
				"ID" => $db->getField("ID", "Number", true)
				, "name" => $db->getField("name", "Text", true)
				, "ID_source" => $db->getField("ID_source", "Number", true)
				, "ID_last_mention" => $db->getField("ID_last_mention", "Number", true) 
				, "path" => $db->getField("path", "Text", true)
				, "primary_keyword" => $db->getField("primary_keyword", "Text", true)
				, "included_keywords" => explode(",", $db->getField("included_keywords", "Text", true))
				, "excluded_keywords" => (strlen($excluded_keywords) ? explode(",", $excluded_keywords) : array())
				, "languages" => explode(",", $db->getField("languages", "Text", true))
				, "sources" => explode(",", $db->getField("sources", "Text", true))
				, "blocked_sites" => (strlen($blocked_sites) ? explode(",", $blocked_sites) : array())
				, "noise_detection" => $db->getField("noise_detection", "Number", true)
				, "created_at" => $db->getField("created_at", "Number", true)
				, "updated_at" => $db->getField("updated_at", "Number", true)
				, "read_access_secret" => $db->getField("read_access_secret", "Text", true)
				, "stats_mentions_total" => $db->getField("stats_mentions_total", "Number", true)
				, "stats_unread_mentions_total" => $db->getField("stats_unread_mentions_total", "Number", true)
				, "stats_favorite_mentions_total" => $db->getField("stats_favorite_mentions_total", "Number", true)
				, "stats_important_mentions_total" => $db->getField("stats_important_mentions_total", "Number", true)
				, "stats_trashed_mentions_total" => $db->getField("stats_trashed_mentions_total", "Number", true)
				, "stats_tasks_total" => $db->getField("stats_tasks_total", "Number", true)
			);
		} while($db->nextRecord());
	}
	
	if(strlen(global_settings("MOD_MENTION_KEYWORDS_BY_DB"))) {
		$arrKeywordsTableField = explode(".", global_settings("MOD_MENTION_KEYWORDS_BY_DB"));
		$keywords_table = $arrKeywordsTableField[0];
		$keywords_field = $arrKeywordsTableField[1];
		
		$keywords_field_prefix = global_settings("MOD_MENTION_KEYWORDS_BY_DB_PREFIX");
		if($keywords_field_prefix)
			$keywords_field_prefix = $keywords_field_prefix . " ";
		$keywords_field_postfix = global_settings("MOD_MENTION_KEYWORDS_BY_DB_POSTFIX"); 
		if($keywords_field_postfix)
			$keywords_field_postfix = " " . $keywords_field_postfix;
		
		if(strlen($keywords_table) && strlen($keywords_field)) {
			$sSQL = "SELECT CONCAT(" . $db->toSql($keywords_field_prefix) . ", " . $keywords_table . "." . $keywords_field . ", " . $db->toSql($keywords_field_postfix) . ") AS ID
						, CONCAT(" . $db->toSql($keywords_field_prefix) . ", " . $keywords_table . "." . $keywords_field . ", " . $db->toSql($keywords_field_postfix) . ") AS name
					FROM " . $keywords_table . "
					WHERE " . $keywords_table . "." . $keywords_field . " != ''
					ORDER BY " . $keywords_table . "." . $keywords_field 
					. (global_settings("MOD_MENTION_MAX_ALERTS")
						? " LIMIT " . global_settings("MOD_MENTION_MAX_ALERTS")
						: ""
					);
			$db->query($sSQL);
			if($db->nextRecord()) {
				do {
					if(!array_key_exists($db->getField("name", "Text", true), $alerts)) {
						$new_alert = mod_mention_db_insert_alert(array("name" => $db->getField("name", "Text", true)));
						if($new_alert === null) {
							break;
						} else {
							$alerts[$db->getField("name", "Text", true)] = $new_alert;
						}
					}
				} while($db->nextRecord());
			}
		}
	}
		
	return $alerts;
}

function mod_mention_db_insert_alert($alert) {
	$db = ffDB_Sql::factory();

	if(global_settings("MOD_MENTION_MAX_ALERTS")) {
		$sSQL = "SELECT ". CM_TABLE_PREFIX . "mod_mention_alerts.* 
				FROM ". CM_TABLE_PREFIX . "mod_mention_alerts";
		$db->query($sSQL);
		if($db->numRows() >= global_settings("MOD_MENTION_MAX_ALERTS")) {
			return null;
		}
	}

	$app_data = mod_mention_api_get_app_data();

	$sSQL = "SELECT " . FF_PREFIX . "languages.* 
			FROM " . FF_PREFIX . "languages 
			WHERE " . FF_PREFIX . "languages.status > 0
			ORDER BY " . FF_PREFIX . "languages.status DESC";
	$db->query($sSQL);
	if($db->nextRecord()) {
		$lang = array();
		do {
			if(count($lang) >= 3)
				break;

			if(array_key_exists($db->getField("tiny_code", "Text", true), $app_data["alert"]["languages"])) {
				$lang[] = $db->getField("tiny_code", "Text", true);
			}
		} while($db->nextRecord());
	}

	$sSQL = "INSERT INTO ". CM_TABLE_PREFIX . "mod_mention_alerts 
			(
				ID
				, ID_source
				, name
				, smart_url
				, primary_keyword
				, included_keywords
				, excluded_keywords
				, languages
				, sources
				, blocked_sites
				, noise_detection
				, created_at
				, updated_at
				, read_access_secret
				, stats_mentions_total
				, stats_unread_mentions_total
				, stats_favorite_mentions_total
				, stats_important_mentions_total
				, stats_trashed_mentions_total
				, stats_tasks_total

			)
			VALUES
			(
				null
				, " . $db->toSql($alert["ID_source"], "Number") . "
				, " . $db->toSql($alert["name"]) . "
				, " . $db->toSql(ffCommon_url_rewrite($alert["name"])) . "
				, " . $db->toSql($alert["name"]) . "
				, " . $db->toSql($alert["name"]) . "
				, ''
				, " . $db->toSql(implode(",", $lang)) . "
				, " . $db->toSql(implode(",", array_keys($app_data["alert"]["sources"]))) . "
				, ''
				, 1
				, " . $db->toSql($alert["created_at"], "Number") . "
				, " . $db->toSql($alert["updated_at"], "Number") . "
				, " . $db->toSql($alert["read_access_secret"]) . "
				, " . $db->toSql($alert["stats_mentions_total"], "Number") . "
				, " . $db->toSql($alert["stats_unread_mentions_total"], "Number") . "
				, " . $db->toSql($alert["stats_favorite_mentions_total"], "Number") . "
				, " . $db->toSql($alert["stats_important_mentions_total"], "Number") . "
				, " . $db->toSql($alert["stats_trashed_mentions_total"], "Number") . "
				, " . $db->toSql($alert["stats_tasks_total"], "Number") . "
			)";
	$db->execute($sSQL);
	
	$alert = array(
		"ID" => $db->getInsertID(true)
		, "name" => $alert["name"]
		, "ID_source" => $alert["ID_source"]
		, "path" => ""
		, "primary_keyword" => $alert["name"]
		, "included_keywords" => array($alert["name"])
		, "excluded_keywords" => array()
		, "languages" => $lang
		, "sources" => array_keys($app_data["alert"]["sources"])
		, "blocked_sites" => array()
		, "noise_detection" => 1
		, "created_at" => $alert["created_at"]
		, "updated_at" => $alert["updated_at"]
		, "read_access_secret" => $alert["read_access_secret"]
		, "stats_mentions_total" => $alert["stats_mentions_total"]
		, "stats_unread_mentions_total" => $alert["stats_unread_mentions_total"]
		, "stats_favorite_mentions_total" => $alert["stats_favorite_mentions_total"]
		, "stats_important_mentions_total" => $alert["stats_important_mentions_total"]
		, "stats_trashed_mentions_total" => $alert["stats_trashed_mentions_total"]
		, "stats_tasks_total" => $alert["stats_tasks_total"]
	);
	
	return $alert;
}

function mod_mention_db_update_alert($alert) {
	$db = ffDB_Sql::factory();

	if($alert["ID"] > 0) {
		$sSQL = "UPDATE ". CM_TABLE_PREFIX . "mod_mention_alerts SET 
					ID_source = " . $db->toSql($alert["ID_source"], "Number") . "
					, created_at = " . $db->toSql($alert["created_at"], "Number") . "
					, updated_at = " . $db->toSql($alert["updated_at"], "Number") . "
					, read_access_secret = " . $db->toSql($alert["read_access_secret"]) . "
					, stats_mentions_total = " . $db->toSql($alert["stats_mentions_total"], "Number") . "
					, stats_unread_mentions_total = " . $db->toSql($alert["stats_unread_mentions_total"], "Number") . "
					, stats_favorite_mentions_total = " . $db->toSql($alert["stats_favorite_mentions_total"], "Number") . "
					, stats_important_mentions_total = " . $db->toSql($alert["stats_important_mentions_total"], "Number") . "
					, stats_trashed_mentions_total = " . $db->toSql($alert["stats_trashed_mentions_total"], "Number") . "
					, stats_tasks_total = " . $db->toSql($alert["stats_tasks_total"], "Number") . "
				WHERE ID = " . $db->toSql($alert["ID"], "Number");
		$db->execute($sSQL);
	} else {
		$alert = mod_mention_db_insert_alert($alert);
	}	
	
	return $alert;
}

function mod_mention_db_delete_alert($id) {
	$db = ffDB_Sql::factory();
	
	if($id > 0) {
		$sSQL = "DELETE FROM 
					". CM_TABLE_PREFIX . "mod_mention_mentions 
				WHERE ID_alert = " . $db->toSql($id, "Number");
		$db->execute($sSQL);

		$sSQL = "DELETE FROM 
					". CM_TABLE_PREFIX . "mod_mention_shares
				WHERE ID_alert = " . $db->toSql($id, "Number");
		$db->execute($sSQL);

		$sSQL = "DELETE FROM 
					". CM_TABLE_PREFIX . "mod_mention_alerts 
				WHERE ID = " . $db->toSql($id, "Number");
		$db->execute($sSQL);
		if($db->affectedRows()) {
			return true;
		}
	}
	return false;	
}

function mod_mention_db_set_mention($ID_alert, $mention) {
	$db = ffDB_Sql::factory();
	
	$sSQL = "SELECT ". CM_TABLE_PREFIX . "mod_mention_mentions.*
			FROM ". CM_TABLE_PREFIX . "mod_mention_mentions
			WHERE ". CM_TABLE_PREFIX . "mod_mention_mentions.ID_source = " . $db->toSql($mention->id, "Number") . "
				AND ". CM_TABLE_PREFIX . "mod_mention_mentions.ID_alert_source = " . $db->toSql($mention->alert_id, "Number") . "
				AND ". CM_TABLE_PREFIX . "mod_mention_mentions.ID_alert = " . $db->toSql($ID_alert, "Number");
	$db->query($sSQL);
	if($db->nextRecord()) {
		$ID_mention = $db->getField("ID" , "Number", true);

		$sSQL = "UPDATE ". CM_TABLE_PREFIX . "mod_mention_mentions SET 
					`title` = " . $db->toSql($mention->title) . "
					, `smart_url` = " . $db->toSql(ffCommon_url_rewrite($mention->title)) . "
					, `description` = " . $db->toSql($mention->description) . "
					, `url` = " . $db->toSql($mention->url) . "
					, `unique_id` = " . $db->toSql($mention->unique_id) . "
					, `published_at` = " . $db->toSql(strtotime($mention->published_at), "Number") . "
					, `created_at` = " . $db->toSql(strtotime($mention->created_at), "Number") . "
					, `updated_at` = " . $db->toSql(strtotime($mention->updated_at), "Number") . "
					, `favorite` = " . $db->toSql($mention->favorite, "Number") . "
					, `trashed` = " . $db->toSql($mention->trashed, "Number") . "
					, `trashed_set_by_user` = " . $db->toSql($mention->trashed_set_by_user, "Number") . "
					, `read` = " . $db->toSql($mention->read, "Number") . "
					, `tone` = " . $db->toSql($mention->tone, "Number") . "
					, `tone_score` = " . $db->toSql($mention->tone_score, "Number") . "
					, `relevance_score` = " . $db->toSql($mention->relevance_score, "Number") . "
					, `source_type` = " . $db->toSql($mention->source_type) . "
					, `source_name` = " . $db->toSql($mention->source_name) . "
					, `source_url` = " . $db->toSql($mention->source_url) . "
					, `language_code` = " . $db->toSql($mention->language_code) . "
				WHERE ". CM_TABLE_PREFIX . "mod_mention_mentions.ID = " . $db->toSql($ID_mention, "Number");
		$db->execute($sSQL);
	} else {
		$sSQL = "INSERT INTO ". CM_TABLE_PREFIX . "mod_mention_mentions
				(
					`ID`
					, `ID_alert`
					, `ID_source`
					, `ID_alert_source`
					, `title`
					, `smart_url`
					, `description`
					, `url`
					, `unique_id`
					, `published_at`
					, `created_at`
					, `updated_at`
					, `favorite`
					, `trashed`
					, `trashed_set_by_user`
					, `read`
					, `tone`
					, `tone_score`
					, `relevance_score`
					, `source_type`
					, `source_name`
					, `source_url`
					, `language_code`
					, `status`
				)
				VALUES
				(
				 	NULL
				 	, " . $db->toSql($ID_alert, "Number") . "
				 	, " . $db->toSql($mention->id, "Number") . "
				 	, " . $db->toSql($mention->alert_id, "Number") . "
				 	, " . $db->toSql($mention->title) . "
				 	, " . $db->toSql(ffCommon_url_rewrite($mention->title)) . "
				 	, " . $db->toSql($mention->description) . "
				 	, " . $db->toSql($mention->url) . "
				 	, " . $db->toSql($mention->unique_id) . "
				 	, " . $db->toSql(strtotime($mention->published_at), "Number") . "
				 	, " . $db->toSql(strtotime($mention->created_at), "Number") . "
				 	, " . $db->toSql(strtotime($mention->updated_at), "Number") . "
				 	, " . $db->toSql($mention->favorite, "Number") . "
				 	, " . $db->toSql($mention->trashed, "Number") . "
				 	, " . $db->toSql($mention->trashed_set_by_user, "Number") . "
				 	, " . $db->toSql($mention->read, "Number") . "
				 	, " . $db->toSql($mention->tone, "Number") . "
				 	, " . $db->toSql($mention->tone_score, "Number") . "
				 	, " . $db->toSql($mention->relevance_score, "Number") . "
				 	, " . $db->toSql($mention->source_type) . "
				 	, " . $db->toSql($mention->source_name) . "
				 	, " . $db->toSql($mention->source_url) . "
				 	, " . $db->toSql($mention->language_code) . "
				 	, '1'
				)";
		$db->execute($sSQL);

		$ID_mention = $db->getInsertID(true);
	}
	return array("ID" => $ID_mention
				, "ID_alert" => $ID_alert
				, "ID_source" => $mention->id
				, "ID_alert_source" => $mention->alert_id
				, "title" => $mention->title
				, "smart_url" => ffCommon_url_rewrite($mention->title)
				, "description" => $mention->description
				, "url" => $mention->url
				, "unique_id" => $mention->unique_id
				, "published_at" => $mention->published_at
				, "created_at" => $mention->created_at
				, "updated_at" => $mention->updated_at
				, "favorite" => $mention->favorite
				, "trashed" => $mention->trashed
				, "trashed_set_by_user" => $mention->trashed_set_by_user
				, "read" => $mention->read
				, "tone" => $mention->tone
				, "tone_score" => $mention->tone_score
				, "relevance_score" => $mention->relevance_score
				, "source_type" => $mention->source_type
				, "source_name" => $mention->source_name
				, "source_url" => $mention->source_url
				, "language_code" => $mention->language_code
	);
}

function mod_mention_api_init() {
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Client/ClientInterface.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Message/MessageInterface.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Message/RequestInterface.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Util/CookieJar.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Exception/ExceptionInterface.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Exception/RuntimeException.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Exception/ClientException.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Client/AbstractClient.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Client/AbstractStream.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Client/FileGetContents.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Listener/ListenerInterface.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Listener/ListenerChain.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Util/Url.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Message/Factory/FactoryInterface.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Message/AbstractMessage.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Message/Request.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Message/Response.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Message/Factory/Factory.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Browser.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Exception/LogicException.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Client/AbstractCurl.php");
	require_once(FF_DISK_PATH . "/modules/mention/library/Buzz/Client/Curl.php");	
}


function mod_mention_api_get_access_token($code = "", $client_id = null, $client_secret = null) {
	static $access_token;

	if(!$access_token) {
		if($code) {
			if($client_id === null)
				$client_id = global_settings("MOD_MENTION_CLIENT_ID");

			if($client_secret === null)
				$client_secret = global_settings("MOD_MENTION_CLIENT_SECRET");
			
			if($client_id && $client_secret) {
				mod_mention_api_init();

			    //get access token   
			    $browser = new Buzz\Browser(new Buzz\Client\Curl);
			    $url = "https://web.mention.net/oauth/v2/token";
			    $response = $browser->post($url, array(), array(
			        "client_id" => $client_id,
			        "client_secret" => $client_secret,
			        // this MUST be the same redirect uri than the
			        // one passed to /authorize
			        "redirect_uri" => "http://" . DOMAIN_NAME . "/services/mention",
			        "response_type" => "token",
			        "code" => $code,
			        "grant_type" => "authorization_code",
			    ));

			    if ($response->getStatusCode() == 200) {
			        $content = $response->getContent();
			        $data = json_decode($content);
			        $access_token = $data->access_token;
			    }
			}
		} else {
			$access_token = global_settings("MOD_MENTION_ACCESS_TOKEN");
		}
	}
	
	return $access_token;	
}

function mod_mention_api_get_app_id($access_token = null) {
	static $app_id;
	
	if(!$app_id) {
		if($access_token === null) {
			if(global_settings("MOD_MENTION_APP_ID")) {
				$app_id = global_settings("MOD_MENTION_APP_ID");
			} else {
				$access_token = mod_mention_api_get_access_token();
			}
		}
		if($access_token) {
			mod_mention_api_init();

			//use access token 
			$browser = new Buzz\Browser(new Buzz\Client\Curl);
			// finding account's path
			$url = "https://api.mention.net/api/accounts/me";
			$response = $browser->get($url, array(
			    "Authorization: Bearer $access_token",
			    "Accept: application/json",
			));

			if ($response->getStatusCode() == 200) {
			    $content = $response->getContent();
			    $data = json_decode($content);
			    $path = $data->_links->me->href;

			    // fetching account
			    $url = "https://api.mention.net" . $path;
			    $response = $browser->get($url, array(
			        "Authorization: Bearer $access_token",
			        "Accept: application/json",
			    ));

			    if ($response->getStatusCode() == 200) {
			        $content = $response->getContent();
			        $data = json_decode($content);
			        $account = $data->account;

			        $app_id = $account->id;	
				}	
			}
		}
	}
	
	return $app_id;
}


function mod_mention_api_set_alert($alert, $id = null, $access_token = null, $app_id = null) {
	if($access_token === null)
		$access_token = mod_mention_api_get_access_token();

	if($app_id === null)
		$app_id = mod_mention_api_get_app_id();

	if($id === null && $alert["ID_source"] > 0)
		$id = $alert["ID_source"];

	mod_mention_api_init();
		
	$browser = new Buzz\Browser(new Buzz\Client\Curl);
	$url = "https://api.mention.net/api/accounts/" . $app_id . "/alerts";
	$header = array(
	    "Authorization: Bearer $access_token",
	    "Accept: application/json",
	    "Content-Type: application/json"
	);
	

	$content = json_encode(array(
	    	"name" => $alert["name"]
			, "included_keywords" => $alert["included_keywords"]
			, "required_keywords" => $alert["required_keywords"]
			, "excluded_keywords" => $alert["excluded_keywords"]
			, "primary_keyword" => $alert["primary_keyword"]
			, "languages" => $alert["languages"]
			, "sources" => $alert["sources"]
			, "blocked_sites" => $alert["blocked_sites"]
			, "noise_detection" => $alert["noise_detection"]
	    )
	);
	
	if($id > 0) {
		$response = $browser->put($url . "/" . $id , $header, $content);	
	} else {
		$response = $browser->post($url, $header, $content);
	}
	
    if ($response->getStatusCode() == 200) {
        $content = $response->getContent();
        $data = json_decode($content);

        if(isset($data->alert)) {
            $alert["ID_source"] = $data->alert->id;
            $alert["read_access_secret"] = $data->alert->read_access_secret;
            $alert["created_at"] = strtotime($data->alert->created_at);
            $alert["updated_at"] = strtotime($data->alert->updated_at);
            $alert["stats_mentions_total"] = $data->alert->stats->mentions->total;
            $alert["stats_unread_mentions_total"] = $data->alert->stats->unread_mentions->total;
            $alert["stats_favorite_mentions_total"] = $data->alert->stats->favorite_mentions->total;
            $alert["stats_important_mentions_total"] = $data->alert->stats->important_mentions->total;
            $alert["stats_trashed_mentions_total"] = $data->alert->stats->trashed_mentions->total;
            $alert["stats_tasks_total"] = $data->alert->stats->tasks->total;

			$alert = mod_mention_db_update_alert($alert);
		}
	} else {
        $content = $response->getContent();
        $data = json_decode($content);
        $alert = $data->form->errors[0];
    }	

	return $alert;
}

function mod_mention_api_delete_alert($alert, $access_token = null, $app_id = null) {
	if($alert["ID"] > 0 && $alert["ID_source"]) {
		if($access_token === null)
			$access_token = mod_mention_api_get_access_token();

		if($app_id === null)
			$app_id = mod_mention_api_get_app_id();

		mod_mention_api_init();
			
		$browser = new Buzz\Browser(new Buzz\Client\Curl);
		$url = "https://api.mention.net/api/accounts/" . $app_id . "/alerts" . "/" . $alert["ID_source"];
		$response = $browser->delete($url, array(
		    "Authorization: Bearer $access_token",
		    "Accept: application/json",
		    "Content-Type: application/json"
		));
	    if ($response->getStatusCode() == 200) {
			mod_mention_db_delete_alert($alert["ID"]);
			return true;
		}	
	}

	return false;
}

function mod_mention_api_get_alerts($id = null, $access_token = null, $app_id = null) {
	$alerts = array();

	if($access_token === null)
		$access_token = mod_mention_api_get_access_token();

	if($app_id === null)
		$app_id = mod_mention_api_get_app_id();

	mod_mention_api_init();
	
    // fetching alerts
    $browser = new Buzz\Browser(new Buzz\Client\Curl);
    $url = "https://api.mention.net/api/accounts/" . $app_id . "/alerts" . ($id ? "/" . $id : "");
    $response = $browser->get($url, array(
        "Authorization: Bearer $access_token",
        "Accept: application/json",
    ));

    if ($response->getStatusCode() == 200) {
        $content = $response->getContent();
        $data = json_decode($content);
        if(is_array($data->alerts) && count($data->alerts)) {
            foreach($data->alerts AS $alerts_key => $alerts_value) {
                $alerts[$alerts_value->name] = array(
                    "ID_source" => $alerts_value->id
                    , "name" => $alerts_value->name
                    , "stats_mentions_total" => $alerts_value->stats->mentions->total
                    , "stats_unread_mentions_total" => $alerts_value->stats->unread_mentions->total
                    , "stats_favorite_mentions_total" => $alerts_value->stats->favorite_mentions->total
                    , "stats_important_mentions_total" => $alerts_value->stats->important_mentions->total
                    , "stats_trashed_mentions_total" => $alerts_value->stats->trashed_mentions->total
                    , "stats_tasks_total" => $alerts_value->stats->tasks->total
                    , "read_access_secret" => $alerts_value->read_access_secret
                    , "created_at" => strtotime($alerts_value->created_at)
                    , "updated_at" => strtotime($alerts_value->updated_at)
                );
			}
		}
	}
	return $alerts;
}

function mod_mention_api_get_app_data($access_token = null, $app_id = null) {
	static $app_data;

	if(!(is_array($app_data) && count($app_data))) {
		if($access_token === null)
			$access_token = mod_mention_api_get_access_token();

		if($app_id === null)
			$app_id = mod_mention_api_get_app_id();

		mod_mention_api_init();
		
	    // fetching alerts
	    $browser = new Buzz\Browser(new Buzz\Client\Curl);
	    $url = "https://api.mention.net/api/app/data";
	    $response = $browser->get($url, array(
	        "Authorization: Bearer $access_token",
	        "Accept: application/json",
	    ));

	    if ($response->getStatusCode() == 200) {
	        $content = $response->getContent();
	        $data = json_decode($content);

	        if(isset($data->app_languages)) {
	            foreach($data->app_languages AS $app_languages_key => $app_languages_value) {
	                $app_data["app"]["languages"][$app_languages_key] = $app_languages_value->name;
				}
			}
	        if(isset($data->alert_languages)) {
	            foreach($data->alert_languages AS $alert_languages_key => $alert_languages_value) {
	                $app_data["alert"]["languages"][$alert_languages_key] = $alert_languages_value->name;
				}
			}
	        if(isset($data->alert_sources)) {
	            foreach($data->alert_sources AS $alert_sources_key => $alert_sources_value) {
	                $app_data["alert"]["sources"][$alert_sources_key] = $alert_sources_value->name;
				}
			}
	        if(isset($data->alert_share_roles)) {
	            foreach($data->alert_share_roles AS $alert_share_roles_key => $alert_share_roles_value) {
	                $app_data["alert"]["share_roles"][$alert_share_roles_key] = $alert_share_roles_value->name;
				}
			}
	        if(isset($data->alert_share_roles)) {
	            foreach($data->alert_share_roles AS $alert_share_roles_key => $alert_share_roles_value) {
	                $app_data["alert"]["share_roles"][$alert_share_roles_key] = $alert_share_roles_value->name;
				}
			}
	        if(isset($data->support_message_subjects)) {
	            foreach($data->support_message_subjects AS $support_message_subjects_key => $support_message_subjects_value) {
	                $app_data["support"]["message_subjects"][$support_message_subjects_key] = array(
                																			"subject" => $support_message_subjects_value->subject
                																			, "tag" => $support_message_subjects_value->tag
	                );
				}
			}
	        if(isset($data->countries)) {
	            foreach($data->countries AS $countries_key => $countries_value) {
	                $app_data["countries"][$countries_key] = $countries_value->name;
				}
			}
	        if(isset($data->task_types)) {
	            foreach($data->task_types AS $task_types_key => $task_types_value) {
	                $app_data["task_types"][$task_types_key] = $task_types_value->name;
				}
			}
		}
	}
	return $app_data;
}

function mod_mention_api_get_mentions($alert, $mentions = array(), $url = null, $access_token = null, $app_id = null) {
	if($alert["ID_source"] > 0) {
		if($access_token === null)
			$access_token = mod_mention_api_get_access_token();

		if($app_id === null)
			$app_id = mod_mention_api_get_app_id();
		
		if($url === null)
			$url = "https://api.mention.net/api/accounts/" . $app_id . "/alerts/" . $alert["ID_source"] . "/mentions" . "?since_id=" . $alert["ID_last_mention"] . "&limit=100";

		mod_mention_api_init();

		// fetching mention
		$browser = new Buzz\Browser(new Buzz\Client\Curl);

		$response = $browser->get($url, array(
		    "Authorization: Bearer $access_token",
		    "Accept: application/json",
		));

		if ($response->getStatusCode() == 200) {
		    $content = $response->getContent();
		    $data = json_decode($content);

		    if(is_array($data->mentions) && count($data->mentions)) {
				foreach($data->mentions AS $mentions_key => $mentions_value) {
					$mentions[] = mod_mention_db_set_mention($alert["ID"], $mentions_value);

					if($mentions_value->id > $alert["ID_last_mention"]) 
						$alert["ID_last_mention"] = $mentions_value->id;
				}

				if(isset($data->_links->more)) {
					$mentions = mod_mention_api_get_mentions($alert, $mentions, "https://api.mention.net" . $data->_links->more->href);
				}
		    }
		}
	}
	return $mentions;
}
?>