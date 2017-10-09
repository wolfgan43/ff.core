<?php
if (isset($_SERVER["SHELL"]))
{
	define("CM_DONT_RUN", true);
	define("DISABLE_CACHE", true);

	if (is_file("../../../../cm/main.php")) 
		require("../../../../cm/main.php");
}

// ------------------------------------------------------------------------------------------------------------

$client_id = global_settings("MOD_MENTION_CLIENT_ID");
$client_secret = global_settings("MOD_MENTION_CLIENT_SECRET");
$access_token = global_settings("MOD_MENTION_ACCESS_TOKEN");
$app_id = global_settings("MOD_MENTION_APP_ID");








if(!strlen($access_token)) {
	if(isset($_GET['code'])) {
		$access_token = mention_get_access_token($_GET['code']);
	} else {
		$url = "https://web.mention.net/authorize?";
		$url .= http_build_query(array(
		    "client_id" => $client_id,
		    "redirect_uri" => "http://" . DOMAIN_NAME . "/services/mention",
		    "response_type" => "code",
		));

		header("Location: $url");
		exit;
	}
}

if($access_token) {
	$db = ffDB_Sql::factory();
	$max_operation = 20;
	$count_alert_insert_operation = 0;
	$count_alert_update_operation = 0;
	$count_alert_delete_operation = 0;
	$count_mention_operation = 0;

	$mention_alerts = mod_mention_db_get_alerts();
	$mention_alerts_set = mod_mention_api_get_alerts();
	$mention_alerts_check = array();
    $error = array(
        "insert" => array()
        , "update" => array()
        , "delete" => array()
    );
    
    
	$alert_to_update = array_intersect_key($mention_alerts, $mention_alerts_set);
	if(is_array($alert_to_update) && count($alert_to_update)) {
		foreach($alert_to_update AS $alert_to_update_key => $alert_to_update_value) {
			if($mention_alerts[$alert_to_update_key]["updated_at"] < $mention_alerts_set[$alert_to_update_key]["updated_at"]) {
				if(!$mention_alerts[$alert_to_update_key]["ID_source"])
					$mention_alerts[$alert_to_update_key]["ID_source"] = $mention_alerts_set[$alert_to_update_key]["ID_source"];

                $tmp_alert = mod_mention_api_set_alert($mention_alerts[$alert_to_update_key]);
                if(is_array($tmp_alert) && count($tmp_alert))
				    $mention_alerts_check[$alert_to_update_key] = $tmp_alert;
                else
                    $error["update"][] = $alert_to_update_key . " " . $tmp_alert;

				$count_alert_update_operation++;
				if($count_alert_update_operation >= $max_operation)
					break;
			}
		}
	}

	$alert_to_insert = array_diff_key($mention_alerts, $mention_alerts_set); 
	if(is_array($alert_to_insert) && count($alert_to_insert)) {
		foreach($alert_to_insert AS $alert_to_insert_key => $alert_to_insert_value) {
			$alert_to_insert_value["ID_source"] = 0;

			$tmp_alert = mod_mention_api_set_alert($alert_to_insert_value);
            if(is_array($tmp_alert) && count($tmp_alert))
                $mention_alerts_check[$alert_to_insert_key] = $tmp_alert;
            else
                $error["insert"][] = $alert_to_insert_key . " " . $tmp_alert;
			
			$count_alert_insert_operation++;
			if($count_alert_insert_operation >= $max_operation)
				break;
		}
	}
	
	$alert_to_delete = array_diff_key($mention_alerts_set, $mention_alerts); 
	if(is_array($alert_to_delete) && count($alert_to_delete)) {
		foreach($alert_to_delete AS $alert_to_delete_key => $alert_to_delete_value) {
			if(0) {
				if(mod_mention_api_delete_alert($alert_to_delete_value))
					unset($mention_alerts_check[$alert_to_delete_key]);
			} else {
				$mention_alerts_check[$alert_to_delete_key] = mod_mention_db_update_alert($alert_to_delete_value);
			}
			
			$count_alert_delete_operation++;
			if($count_alert_delete_operation >= $max_operation)
				break;
		}
	}
	if(is_array($mention_alerts_check) && count($mention_alerts_check)) {
		foreach($mention_alerts_check AS $mention_alerts_key => $mention_alerts_value) {
			$mention_alerts_check[$mention_alerts_key] = mod_mention_api_get_mentions($mention_alerts_value);

			$count_mention_operation++;
			if($count_mention_operation >= $max_operation)
				break;
		}
	}
//	print_r($mentions);
	$operation = array("alerts" => array(
							"insert" => array("done" => $count_alert_insert_operation, "total" => count($alert_to_insert), "error" => $error["insert"])
							, "update" => array("done" => $count_alert_update_operation, "total" => count($alert_to_update), "error" => $error["update"])
							, "delete" => array("done" => $count_alert_delete_operation, "total" => count($alert_to_delete), "error" => $error["delete"])
						)
						, "mentions" => array("done" => $count_mention_operation, "total" => count($mention_alerts))
					);
    if(1 || $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest") {  
        die(ffCommon_jsonenc(array("close" => true, "refresh" => true, "resources" => array("AlertsModify"), "operation" => $operation), true));
    } else {
		die("fine");
    }

	print_r($alert_to_update);
	echo "\n\n\n\n\n";
	print_r($alert_to_insert);
	echo "\n\n\n\n\n";
	print_r($alert_to_delete);
}


// ------------------------------------------------------------------------------------------------------------

if (isset($_SERVER["SHELL"]))
{
	if (defined("LOCKFILE"))
		unlink(LOCKFILE);
	return;
}

exit;
