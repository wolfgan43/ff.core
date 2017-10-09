<?php
$cm = cm::getInstance();

$cm->modules["restricted"]["options"] = array();
$cm->modules["restricted"]["menu"] = array();
$cm->modules["restricted"]["menu_bypath"] = array();
$cm->modules["restricted"]["layout_bypath"] = array();

if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING)
{
	$mod_res_globals = ffGlobals::getInstance("__mod_restricted__");
	// calculate hash
	
	$cache = ffCache::getInstance(CM_CACHE_ADAPTER);
	$restricted_options			= $cache->get("__mod_restricted_options" . mod_res_get_hash() . "__", $ffcache_success);
	$restricted_menu			= $cache->get("__mod_restricted_menu" . mod_res_get_hash() . "__", $ffcache_success);
	$restricted_layout_bypath	= $cache->get("__mod_restricted_layout_bypath" . mod_res_get_hash() . "__", $ffcache_success);
	$restricted_settings		= $cache->get("__mod_restricted_settings" . mod_res_get_hash() . "__", $ffcache_success);

	if ($ffcache_success)
	{
		$cm->modules["restricted"]["options"]		= unserialize($restricted_options);
		$cm->modules["restricted"]["menu"]			= unserialize($restricted_menu);
		$cm->modules["restricted"]["layout_bypath"]	= unserialize($restricted_layout_bypath);
		$cm->modules["restricted"]["settings"]		= unserialize($restricted_settings);

		foreach ($cm->modules["restricted"]["menu"] as $key => $value)
		{
			$cm->modules["restricted"]["menu_bypath"][$value["path"]][] =& $cm->modules["restricted"]["menu"][$key];
			if (is_array($value["elements"]) && count($value["elements"]))
			{
				foreach ($value["elements"] as $subkey => $subvalue)
				{
					if (strlen($subvalue["path"]))
						$cm->modules["restricted"]["menu_bypath"][$subvalue["path"]][] =& $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];
				}
				reset($value["elements"]);
			}
		}
		reset($cm->modules["restricted"]["menu"]);
	}
}

if (!$ffcache_success)
{
	$cm->addEvent("on_load_module", "mod_restricted_cm_on_load_module");
	if (CM_ENABLE_MEM_CACHING && MOD_RES_MEM_CACHING) $cm->addEvent("on_modules_loaded", "mod_restricted_cm_on_modules_loaded");

	$tmp = cm_confCascadeFind(FF_DISK_PATH, "", "mod_restricted.xml");
	if (is_file($tmp))
		mod_restricted_load_config($tmp);

	mod_restricted_load_config(cm_confCascadeFind(CM_ROOT . "/conf", "/cm", "mod_restricted.xml"));

	mod_restricted_load_by_path();
}

function mod_restricted_load_by_path()
{
	$cm = cm::getInstance();

	$script_path_parts = explode("/", $cm->path_info);
	$script_path_tmp = FF_DISK_PATH . "/conf/contents";
	$script_path_count = 0;
	while ($script_path_count < count($script_path_parts) && $script_path_tmp .= $script_path_parts[$script_path_count] . "/")
	{
		if (is_file($script_path_tmp . "mod_restricted.xml"))
		{
			mod_restricted_load_config($script_path_tmp . "mod_restricted.xml");
		}
		$script_path_count++;
	}
}

function mod_restricted_cm_on_load_module($cm, $mod)
{
	$tmp = cm_confCascadeFind(CM_MODULES_ROOT . "/" . $mod . "/conf", "/modules/" . $mod, "mod_restricted.xml");
	if (is_file($tmp))
		mod_restricted_load_config($tmp);
}

function mod_restricted_cm_on_modules_loaded($cm)
{
	$cache = ffCache::getInstance(FF_CACHE_ADAPTER);
	$mod_res_globals = ffGlobals::getInstance("__mod_restricted__");
	$cache->set("__mod_restricted_options" . mod_res_get_hash() . "__", null, serialize($cm->modules["restricted"]["options"]));
	$cache->set("__mod_restricted_menu" . mod_res_get_hash() . "__", null, serialize($cm->modules["restricted"]["menu"]));
	$cache->set("__mod_restricted_layout_bypath" . mod_res_get_hash() . "__", null, serialize($cm->modules["restricted"]["layout_bypath"]));
	$cache->set("__mod_restricted_settings" . mod_res_get_hash() . "__", null, serialize($cm->modules["restricted"]["settings"]));
}

function mod_restricted_get_setting($name, $DomainID = null, $db = null)
{
	if ($db === null)
		$db = ffDb_Sql::factory();
	elseif (is_array($db))
		$db =& $db[0];

	if (!is_object($db))
		ffErrorHandler::raise("invalid db object", E_USER_ERROR, null, get_defined_vars());

	$sSQL = "SELECT * FROM " . CM_TABLE_PREFIX . "mod_restricted_settings WHERE name = " . $db->toSql(new ffData($name));

	if ($DomainID === null)
	{
		$res = cm::getInstance()->modules["restricted"]["events"]->doEvent("get_domain");
		$rc = end($res);
		if ($rc)
			$DomainID = $rc;
		else if (is_callable("mod_security_get_domain") && MOD_SEC_MULTIDOMAIN)
		{
			$DomainID = mod_security_get_domain();
		}
	}	
	
	if ($DomainID !== null)
	{
		$sSQL .= " AND ID_domains = " . $db->toSql($DomainID);
	}

	$db->query($sSQL);
	if ($db->nextRecord())
	{
		return $db->getField("value")->getValue();
	}
	else
	{
		return "";
	}
}

function mod_restricted_get_all_setting($DomainID = null, $db = null)
{
    if ($db === null)
        $db = ffDb_Sql::factory();
    elseif (is_array($db))
        $db =& $db[0];

    if (!is_object($db))
        ffErrorHandler::raise("invalid db object", E_USER_ERROR, null, get_defined_vars());

    $sSQL = "SELECT * FROM " . CM_TABLE_PREFIX . "mod_restricted_settings WHERE 1 ";

	if ($DomainID === null)
	{
		$res = cm::getInstance()->modules["restricted"]["events"]->doEvent("get_domain");
		$rc = end($res);
		if ($rc)
			$DomainID = $rc;
		else if (is_callable("mod_security_get_domain") && MOD_SEC_MULTIDOMAIN)
		{
			$DomainID = mod_security_get_domain();
		}
	}	
	
    if ($DomainID !== null)
    {
        $sSQL .= " AND ID_domains = " . $db->toSql($DomainID);
    }

    $db->query($sSQL);
    if ($db->nextRecord())
    {
        $res = array();
        do {
            $res[$db->getField("name", "Text", true)] = $db->getField("value", "Text", true);
        } while($db->nextRecord());
        
        return $res;
    }
    else
    {
        return null;
    }
}

function mod_restricted_set_setting($name, $value, $DomainID = null, $db = null)
{
	if ($db === null)
		$db = ffDb_Sql::factory();
	elseif (is_array($db))
		$db =& $db[0];

    if (!is_object($db))
        ffErrorHandler::raise("invalid db object", E_USER_ERROR, null, get_defined_vars());
	
    
    

	if ($DomainID === null)
	{
		$res = cm::getInstance()->modules["restricted"]["events"]->doEvent("get_domain");
		$rc = end($res);
		if ($rc)
			$DomainID = $rc;
		else if (is_callable("mod_security_get_domain") && MOD_SEC_MULTIDOMAIN)
		{
			$DomainID = mod_security_get_domain();
		}
	}	
	
	if ($DomainID !== null)
	{
		$sSQL_and = " AND ID_domains = " . $db->toSql($DomainID);
	}
        
        $sSQL = "SELECT ID
                    FROM " . CM_TABLE_PREFIX . "mod_restricted_settings
                    WHERE name = " . $db->toSql($name) . $sSQL_and;
        $db->query($sSQL);
        if($db->nextRecord()) {
            $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_restricted_settings SET
                            value = " . $db->toSql($value) . "
                        WHERE name = " . $db->toSql($name) . $sSQL_and;
            $db->execute($sSQL);
        } else {
            if ($DomainID !== null)
            {
                    $fields = ", ID_domains";
                    $values = ", " . $db->toSql($DomainID);
            }
            $sSQL = "INSERT INTO
                                    " . CM_TABLE_PREFIX . "mod_restricted_settings (
                                            value
                                            , name
                                            " . $fields . "
                                    ) VALUES (
                                            " . $db->toSql($value) . "
                                            , " . $db->toSql($name) . "
                                            " . $values . "
                                    )
                    ";
            $db->execute($sSQL);
	}
}

function mod_restricted_load_config($file)
{
	$cm = cm::getInstance();
	
	$xml = new SimpleXMLElement("file://" . $file, null, true);
	
    static $sect_compare;
    if($sect_compare == "" && strpos($file, FF_DISK_PATH . "/conf/contents") === 0) {
        $sect_compare = ffCommon_dirname(substr($file, strlen(FF_DISK_PATH . "/conf/contents"))); 
    }

	if (isset($xml->menu) && count($xml->menu->children()))
	{
		foreach ($xml->menu->children() as $key => $value)
		{
			if ($key == "comment")
				continue;
		
			if (!isset($cm->modules["restricted"]["menu"][$key]))
			{
				$attrs = $value->attributes();

				$path = (string)$attrs["path"];

				if($path != "/" && strlen($sect_compare) && strpos($path, $sect_compare) !== 0 && strpos($cm->path_info, $sect_compare) === 0)
					continue;
 
				$cm->modules["restricted"]["menu"][$key] = array();
					
				$label = (string)$attrs["label"];
				
                                $class = (string)$attrs["class"];
				$params = (string)$attrs["params"];
				$acl = (string)$attrs["acl"];
				$redir = (string)$attrs["redir"];
				$location = (string)$attrs["location"];
				$hide = (string)$attrs["hide"];
				$profiling_skip = (string)$attrs["profiling_skip"];
				$globals_exclude = (string)$attrs["globals_exclude"];
				
				// converte i valori dei flag boolean con i relativi default
				if (strlen($hide) && $hide == "true")
					$hide = true;
				else
					$hide = false; // default
				if (strlen($profiling_skip) && $profiling_skip == "true")
					$profiling_skip = true;
				else
					$profiling_skip = false; // default
				
				if (!strlen($path))
					$path = strtolower("/" . $key);

				if (!strlen($label))
					$label = $key;
				
				$cm->modules["restricted"]["menu"][$key]["name"] = $key;
				$cm->modules["restricted"]["menu"][$key]["path"] = $path;
				$cm->modules["restricted"]["menu"][$key]["label"] = $label;
				$cm->modules["restricted"]["menu"][$key]["class"] = $class;
				$cm->modules["restricted"]["menu"][$key]["hide"] = $hide;
				$cm->modules["restricted"]["menu"][$key]["profiling_skip"] = $profiling_skip;
				$cm->modules["restricted"]["menu"][$key]["params"] = $params;
				$cm->modules["restricted"]["menu"][$key]["globals_exclude"] = $globals_exclude;
				if (strlen($acl))
					$cm->modules["restricted"]["menu"][$key]["acl"] = explode(",", $acl);
				if (strlen($redir))
					$cm->modules["restricted"]["menu"][$key]["redir"] = $redir;
				if (strlen($location))
					$cm->modules["restricted"]["menu"][$key]["location"] = $location;
				
				$cm->modules["restricted"]["menu_bypath"][$path][] =& $cm->modules["restricted"]["menu"][$key];
			}

			if (count($value))
			{
				foreach ($value as $subkey => $subvalue)
				{
					if ($subkey == "h")
					{
						$is_heading = true;
						$subkey = "h" . uniqid(rand(), true);
					}
					else
					{
						$is_heading = false;
					}
					
					if (!isset($cm->modules["restricted"]["menu"][$key]["elements"][$subkey]))
					{
						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey] = array();
					
						$attrs = $subvalue->attributes();
						
						$acl = (string)$attrs["acl"];
						if (strlen($acl))
							$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["acl"] = explode(",", $acl);

						$description = (string)$attrs["description"];
						if ($description)
							$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["description"] = $description;

						$jsaction = (string)$attrs["jsaction"];
						if ($jsaction)
							$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["jsaction"] = $jsaction;

						$hide = (string)$attrs["hide"];
						if (strlen($hide) && $hide == "true")
							$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["hide"] = true;
						else
							$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["hide"] = false;

						$profiling_skip = (string)$attrs["profiling_skip"];
						if (strlen($profiling_skip) && $profiling_skip == "true")
							$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["profiling_skip"] = true;
						else
							$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["profiling_skip"] = false;

						$location = (string)$attrs["location"];
						if (strlen($location))
							$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["location"] = $location;

						if (!$is_heading)
						{
							$params = (string)$attrs["params"];
							$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["params"] = $params;

							$path = (string)$attrs["path"];
							if (!strlen($path))
								$path = strtolower($cm->modules["restricted"]["menu"][$key]["path"] . "/" . $subkey);
							$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["path"] = $path;
							$cm->modules["restricted"]["menu_bypath"][$path][] =& $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];
						}
						
						$label = (string)$attrs["label"];

						if (!strlen($label))
							$label = $subkey;
						
						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["label"] = $label;
						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["is_heading"] = $is_heading;
                                                
                                                $class = (string)$attrs["class"];
                                                if(strlen($class))
                                                    $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["class"] = $class;
					}
				}
			}
		}
	}
	
	if (isset($xml->layout) && count($xml->layout->children()))
	{
		foreach ($xml->layout->children() as $key => $value)
		{
			if ($key == "comment")
				continue;
			
			$attrs = $value->attributes();
			if ($key == "nolayout")
			{
				$path = (string)$attrs["path"];
				if (!strlen($path))
					ffErrorHandler::raise("mod_restricted: malformed xml (missing path parameter on layout/nolayout section)", E_USER_ERROR, null, get_defined_vars());

				$cm->modules["restricted"]["layout_bypath"][$path] = $key;
			}
			else
			{
				$path = (string)$attrs["path"];
				if (!strlen($path))
					$cm->modules["restricted"]["options"]["layout"][$key] = (string)$value;
				else
				{
					$name = (string)$attrs["name"];
					$cm->modules["restricted"]["layout_bypath"][$path][$key] = $name;
				}
			}
		}
	}

	if (isset($xml->settings) && count($xml->settings->children()))
	{
		foreach ($xml->settings->children() as $key => $value)
		{
			if ($key == "comment")
				continue;
			
			if (!isset($cm->modules["restricted"]["settings"][$key]))
			{
				$cm->modules["restricted"]["settings"][$key] = new ffSerializable($value);
			}
			else
			{
				foreach ($value->children() as $subkey => $subvalue)
				{
					if (isset($cm->modules["restricted"]["settings"][$key]->$subkey))
					{
						if (count($attrs = $subvalue->attributes()))
						{
							foreach ($attrs as $attr_key => $attr_value)
							{
								$cm->modules["restricted"]["settings"][$key]->$subkey->$attr_key = (string)$attr_value;
							}
						}
					}
					else
						$cm->modules["restricted"]["settings"][$key]->$subkey = new ffSerializable($subvalue);
				}
			}
		}
	}

	if (isset($xml->sections) && count($xml->sections->children()))
	{
		foreach ($xml->sections->children() as $key => $value)
		{
			if ($key == "comment")
				continue;
			
			$attrs = $value->attributes();
			
			if (!isset($cm->modules["restricted"]["sections"][$key]) || (string)$attrs->replace == "true")
			{
				$cm->modules["restricted"]["sections"][$key] = new ffSerializable($value);
			}
			else
			{
				foreach ($value->children() as $subkey => $subvalue)
				{
					if (isset($cm->modules["restricted"]["sections"][$key]->$subkey))
					{
						if (count($attrs = $subvalue->attributes()))
						{
							foreach ($attrs as $attr_key => $attr_value)
							{
								$cm->modules["restricted"]["sections"][$key]->$subkey->$attr_key = (string)$attr_value;
							}
						}
					}
					else
						$cm->modules["restricted"]["sections"][$key]->$subkey = new ffSerializable($subvalue);
				}
			}
		}
		//ffErrorHandler::raise ("gotcha2!", E_USER_ERROR, $this, get_defined_vars ());
	}
}


function mod_restricted_add_menu_child($key, $path = "", $label = "", $params = "", $acl = "", $redir = "", $visible = true, $class="") {
    $cm = cm::getInstance();
    
    if (!isset($cm->modules["restricted"]["menu"][$key]))
    {
        $cm->modules["restricted"]["menu"][$key] = array();

        //$attrs = $value->attributes();

       /* $path = (string)$attrs["path"];
        $label = (string)$attrs["label"];
        $params = (string)$attrs["params"];
        $acl = (string)$attrs["acl"];
        $redir = (string)$attrs["redir"];
        */
        if (!strlen($path))
            $path = strtolower("/" . $key);

        if (!strlen($label))
            $label = $key;

        $cm->modules["restricted"]["menu"][$key]["path"] = $path;
        $cm->modules["restricted"]["menu"][$key]["label"] = $label;
        $cm->modules["restricted"]["menu"][$key]["class"] = $class;
        $cm->modules["restricted"]["menu"][$key]["params"] = $params;
        $cm->modules["restricted"]["menu"][$key]["visible"] = $visible;
        if (strlen($acl))
            $cm->modules["restricted"]["menu"][$key]["acl"] = explode(",", $acl);
        if (strlen($redir))
            $cm->modules["restricted"]["menu"][$key]["redir"] = $redir;

        $cm->modules["restricted"]["menu_bypath"][$path][] =& $cm->modules["restricted"]["menu"][$key];
    }
    
}

function mod_restricted_add_menu_sub_element($key, $subkey, $path = "", $label = "", $params = "", $acl = "", $location = null, $hide = false, $description = null, $profiling_skip = false) { 
    $cm = cm::getInstance();

	if ($subkey == "h")
	{
		$is_heading = true;
		$subkey = "h" . uniqid(rand(), true);
	}
	else
	{
		$is_heading = false;
	}

	if (!isset($cm->modules["restricted"]["menu"][$key]["elements"][$subkey]))
	{
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey] = array();

		if (strlen($acl))
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["acl"] = explode(",", $acl);

		if ($description)
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["description"] = $description;

		if ($hide)
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["hide"] = true;
		else
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["hide"] = false;

		if ($profiling_skip)
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["profiling_skip"] = true;
		else
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["profiling_skip"] = false;

		if (strlen($location))
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["location"] = $location;

		if (!$is_heading)
		{
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["params"] = $params;

			if (!strlen($path))
				$path = strtolower($cm->modules["restricted"]["menu"][$key]["path"] . "/" . $subkey);
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["path"] = $path;
			$cm->modules["restricted"]["menu_bypath"][$path][] =& $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];
		}

		if (!strlen($label))
			$label = $subkey;

		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["label"] = $label;
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["is_heading"] = $is_heading;
	}
}

function mod_restricted_checkacl_bylevel($acl, $level = null, $usernid = null, $path_info = null)
{
	if ($acl === null)
		return true;
	
	if ($level === null)
	{
		if ($usernid === null)
		{
			$level = get_session("UserLevel");
		}
		else
		{
			if ($path_info === null)
				$path_info = cm::getInstance()->path_info;
			
			$options = mod_security_get_settings($path_info);

			$db = ffDB_Sql::factory();
			$level = $db->lookup("SELECT level FROM " . $options["table_name"] . " WHERE ID = " . $db->toSql($usernid));
			if (!$level)
				ffErrorHandler::raise("wrong mod_restricted_checkacl_bylevel use, cannot determine level", E_USER_ERROR, null, get_defined_vars());
		}
	}
	
/*	if (defined("MOD_SECURITY_SESSION_STARTED") && $level == 3)
		return true;
*/	
	if (in_array(get_session("UserLevel"), $acl))
		return true;
}

function mod_res_removelabel($topbar, $label)
{
	$mod_data =& cm::getInstance()->modules["restricted"];
	
	foreach ($mod_data["menu"][$topbar]["elements"] as $key => $value)
	{
		if ($value["is_heading"] && $value["label"] == $label)
		{
			unset($mod_data["menu"][$topbar]["elements"][$key]);
			break;
		}
	}
	reset($mod_data["menu"][$topbar]["elements"]);
}

function mod_res_remove_element($topbar, $navbar = null)
{
	$mod_data =& cm::getInstance()->modules["restricted"];

	if ($navbar !== null)
	{
		$path = $mod_data["menu"][$topbar]["elements"][$navbar]["path"];
		unset($mod_data["menu_bypath"][$path]);
		unset($mod_data["menu"][$topbar]["elements"][$navbar]);
	}
	else
	{
		if (isset($mod_data["menu"][$topbar]["elements"]) && count($mod_data["menu"][$topbar]["elements"]))
		{
			foreach ($mod_data["menu"][$topbar]["elements"] as $key => $value)
			{
				$path = $mod_data["menu"][$topbar]["elements"][$key]["path"];
				if (strlen($path))
				{
					//echo $key . " " . $path . "<br />";
					unset($mod_data["menu_bypath"][$path]);
				}
			}
		}
		$path = $mod_data["menu"][$topbar]["path"];
		unset($mod_data["menu_bypath"][$path]);
		unset($mod_data["menu"][$topbar]);
	}
}

function mod_res_disable_element($topbar, $navbar = null)
{
	$mod_data =& cm::getInstance()->modules["restricted"];

	if ($navbar !== null)
	{
		$mod_data["menu"][$topbar]["elements"][$navbar]["acl"] = array(0);
	}
	else
	{
		if (isset($mod_data["menu"][$topbar]["elements"]) && count($mod_data["menu"][$topbar]["elements"]))
		{
			foreach ($mod_data["menu"][$topbar]["elements"] as $key => $value)
			{
				$mod_data["menu"][$topbar]["elements"][$key]["acl"] = array(0);
			}
			reset($mod_data["menu"][$topbar]["elements"]);
		}
		$mod_data["menu"][$topbar]["acl"] = array(0);
	}
}

function mod_res_access_denied($confirm_url = null)
{
	$cm = cm::getInstance();
	
	$res_path = (string)$cm->router->getRuleById("restricted")->reverse;
	
	if ($confirm_url === null)
	{
		if (isset($_REQUEST["ret_url"]))
			$confirm_url = $_REQUEST["ret_url"];
		else
			$confirm_url = FF_SITE_PATH . $res_path . "?" . $cm->oPage->get_globals();
	}
	
	access_denied($confirm_url, FF_SITE_PATH . $res_path . "/dialog");
}

function mod_res_get_hash()
{
	$cm = cm::getInstance();
	
	if (MOD_RES_MEM_CACHING_BYPATH)
		$hash .= "_" . $cm->path_info;
	/*if (MOD_RES_MEM_CACHING_BYDOMAIN && function_exists("mod_security_get_domain"))
		$hash .= "_" . mod_security_get_domain();
	if (MOD_RES_MEM_CACHING_BYUSER && defined("MOD_SECURITY_SESSION_STARTED"))
		$hash .= "_" . get_session("UserNID");
	if (MOD_RES_MEM_CACHING_BYUSERLEVEL && defined("MOD_SECURITY_SESSION_STARTED"))
		$hash .= "_" . get_session("UserLevel");
	if (MOD_RES_MEM_CACHING_BYPROFILE && MOD_SEC_PROFILING && defined("MOD_SECURITY_SESSION_STARTED"))
		$hash .= "_" . mod_sec_getprofile_byuser(get_session("UserNID"));*/
	
	return $hash;
}