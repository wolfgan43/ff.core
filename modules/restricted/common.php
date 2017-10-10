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
		mod_restricted_load_config_file($tmp);

	mod_restricted_load_config_file(cm_confCascadeFind(CM_ROOT . "/conf", "/cm", "mod_restricted.xml"));

	mod_restricted_load_by_path();
}

if(MOD_RES_NAVTABS && !defined("FF_THEME_RESTRICTED_RANDOMIZE_COMP_ID")) 
	define("FF_THEME_RESTRICTED_RANDOMIZE_COMP_ID", true);

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
			mod_restricted_load_config_file($script_path_tmp . "mod_restricted.xml");
		}
		$script_path_count++;
	}
}

function mod_restricted_cm_on_load_module($cm, $mod)
{
	$tmp = cm_confCascadeFind(CM_MODULES_ROOT . "/" . $mod . "/conf", "/modules/" . $mod, "mod_restricted.xml");
	if (is_file($tmp))
		mod_restricted_load_config_file($tmp);
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
	
	if ($db === null)
	{
		if (MOD_SEC_MULTIDOMAIN && MOD_SEC_MULTIDOMAIN_EXTERNAL_DB && $DomainID !== null)
			$db = mod_security_get_db_by_domain($DomainID);
		else
			$db = ffDb_Sql::factory();
	}
	elseif (is_array($db))
		$db =& $db[0];

	if (!is_object($db))
		ffErrorHandler::raise("invalid db object", E_USER_ERROR, null, get_defined_vars());

	$sSQL = "SELECT * FROM " . CM_TABLE_PREFIX . "mod_restricted_settings WHERE name = " . $db->toSql(new ffData($name));

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
	
    /*if ($DomainID !== null) // è corretto che vengano solo presi quelli generici (domain = 0) in assenza di specificazione
    {*/
        $sSQL .= " AND ID_domains = " . $db->toSql($DomainID);
    //}

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
	
    $sSQL = "UPDATE
					" . CM_TABLE_PREFIX . "mod_restricted_settings
				SET
					value = " . $db->toSql($value) . "
				WHERE
					name = " . $db->toSql($name) . "
		";

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

	$db->execute($sSQL);
	if (!$db->affectedRows())
	{
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

function mod_restricted_load_config_file($file)
{
	$xml = new SimpleXMLElement("file://" . $file, null, true);
	mod_restricted_load_config($xml, $file);
}

function mod_restricted_load_config($xml, $file = null)
{
	$cm = cm::getInstance();
	
    static $sect_compare;
    if($file !== null && sect_compare == "" && strpos($file, FF_DISK_PATH . "/conf/contents") === 0) {
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
 
				/*if ($key == "h")
				{
					$is_heading = true;
					$key = "h" . uniqid(rand(), true);
				}
				else
				{
					$is_heading = false;
				}*/
				
				$cm->modules["restricted"]["menu"][$key] = array();
					
				$label = (string)$attrs["label"];
				$icon = (string)$attrs["icon"];
				
				$actions = (string)$attrs["actions"];
				$dialog = (string)$attrs["dialog"];
				if ($dialog == "true")
					$dialog = true;
				else
					$dialog = false; // default				

				$readonly = (string)$attrs["readonly"];
				if ($readonly == "true")
					$readonly = true;
				else
					$readonly = false; // default				

				$description = (string)$attrs["description"];
				$class = (string)$attrs["class"];
				$params = (string)$attrs["params"];
				$acl = (string)$attrs["acl"];
				$redir = (string)$attrs["redir"];
				$location = (string)$attrs["location"];
                $position = (string)$attrs["position"];
                $settings = (string)$attrs["settings"];
				$hide = (string)$attrs["hide"];
				$profiling_skip = (string)$attrs["profiling_skip"];
				$profiling_default = (string)$attrs["profiling_default"];
				$profiling_acl = (string)$attrs["profiling_acl"];
				$globals_exclude = (string)$attrs["globals_exclude"];
				$collapse = (string)$attrs["collapse"];

				// converte i valori dei flag boolean con i relativi default
				if (strlen($hide) && $hide == "true")
					$hide = true;
				else
					$hide = false; // default

				if (strlen($profiling_skip) && $profiling_skip == "true")
					$profiling_skip = true;
				else
					$profiling_skip = false; // default
				
				if (strlen($profiling_default))
				{
					if ($profiling_default === "false" || !$profiling_default)
						$profiling_default = false;
					else if ($profiling_default === "true" || $profiling_default)
						$profiling_default = true;
				}
				else
					$profiling_default = null;
				
				if (!strlen($path))
					$path = strtolower("/" . $key);

				if (!strlen($label))
					$label = $key;
				
				$cm->modules["restricted"]["menu"][$key]["name"] = $key;
				$cm->modules["restricted"]["menu"][$key]["path"] = $path;
				$cm->modules["restricted"]["menu"][$key]["label"] = $label;
				//$cm->modules["restricted"]["menu"][$key]["is_heading"] = $is_heading;
				$cm->modules["restricted"]["menu"][$key]["icon"] = $icon;
				if($actions)
					$cm->modules["restricted"]["menu"][$key]["actions"] = explode(",", $actions);
				
				$cm->modules["restricted"]["menu"][$key]["dialog"] = $dialog;
				$cm->modules["restricted"]["menu"][$key]["readonly"] = $readonly;

				if ($description)
					$cm->modules["restricted"]["menu"][$key]["description"] = $description;

				$cm->modules["restricted"]["menu"][$key]["class"] = $class;
				$cm->modules["restricted"]["menu"][$key]["hide"] = $hide;
				$cm->modules["restricted"]["menu"][$key]["profiling_skip"] = $profiling_skip;
				if ($profiling_default !== null) $cm->modules["restricted"]["menu"][$key]["profiling_default"] = $profiling_default;
				if (strlen($profiling_acl)) $cm->modules["restricted"]["menu"][$key]["profiling_acl"] = $profiling_acl;
				$cm->modules["restricted"]["menu"][$key]["params"] = $params;
				$cm->modules["restricted"]["menu"][$key]["globals_exclude"] = $globals_exclude;
				if (strlen($acl))
					$cm->modules["restricted"]["menu"][$key]["acl"] = explode(",", $acl);
				if (strlen($redir))
					$cm->modules["restricted"]["menu"][$key]["redir"] = $redir;
				if (strlen($location))
					$cm->modules["restricted"]["menu"][$key]["location"] = $location;
                if (strlen($position))
                    $cm->modules["restricted"]["menu"][$key]["position"] = $position;
                if (strlen($settings))
                    $cm->modules["restricted"]["menu"][$key]["settings"] = $settings;

				if($collapse == "true")
					$cm->modules["restricted"]["menu"][$key]["collapse"] = true;
				elseif($collapse == "false")
					$cm->modules["restricted"]["menu"][$key]["collapse"] = false;
				
				$cm->modules["restricted"]["menu_bypath"][$path][] =& $cm->modules["restricted"]["menu"][$key];

				$favorite = (string)$attrs["favorite"];
				if($favorite === "true") {
					$section_favorite[$key] = $attrs;
				}
			}

			if (count($value))
			{
				foreach ($value as $subkey => $subvalue)
				{
					/*if ($subkey == "h")
					{
						$is_heading = true;
						$subkey = "h" . uniqid(rand(), true);
					}
					else
					{
						$is_heading = false;
					}*/
					
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

						//if (!$is_heading)
						//{
							$params = (string)$attrs["params"];
							$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["params"] = $params;

							$path = (string)$attrs["path"];
							if (!strlen($path))
								$path = strtolower($cm->modules["restricted"]["menu"][$key]["path"] . "/" . $subkey);
							$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["path"] = $path;
							$cm->modules["restricted"]["menu_bypath"][$path][] =& $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];
							
							$favorite = (string)$attrs["favorite"];
							if($favorite === "true") {
								$section_favorite[$key. "-" . $subkey] = $attrs;
							}

						//}
						
						$label = (string)$attrs["label"];
						$icon = (string)$attrs["icon"];
						$actions = (string)$attrs["actions"];
						$dialog = (string)$attrs["dialog"];
						if ($dialog == "true")
							$dialog = true;
						else
							$dialog = false; // default				

						$readonly = (string)$attrs["readonly"];
						if ($readonly == "true")
							$readonly = true;
						else
							$readonly = false; // default				

						if (!strlen($label))
							$label = $subkey;
						
						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["label"] = $label;
						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["icon"] = $icon;
						if($actions)
							$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["actions"] = explode(",", $actions);

						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["dialog"] = $dialog;
						$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["readonly"] = $readonly;
						//$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["is_heading"] = $is_heading;
                                                
                        $class = (string)$attrs["class"];
                        if(strlen($class))
                            $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["class"] = $class;
                            
						if($location && isset($cm->modules["restricted"]["sections"]["nav"]->$location)) {
							$cm->modules["restricted"]["sections"][$location]["elements"][$subkey] = $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];
						}
					}
				}
			}
		}

		if($section_favorite)
			$cm->modules["restricted"]["sections"]["favorite"] = $section_favorite;
	}
	
	if (isset($xml->fullbar))
		$cm->modules["restricted"]["fullbar"] = true;

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


function mod_restricted_add_menu_child($data) {
    $cm = cm::getInstance();
    
    if (ffIsset($data, "key"))					$key 		= $data["key"];
    if (ffIsset($data, "path"))					$path 		= $data["path"];
    if (ffIsset($data, "label"))				$label 		= $data["label"];
    if (ffIsset($data, "icon"))					$icon 		= $data["icon"];
    if (ffIsset($data, "actions"))				$actions 	= $data["actions"];
    if (ffIsset($data, "params"))				$params 	= $data["params"];
    if (ffIsset($data, "visible"))				$visible 	= $data["visible"];
    if (ffIsset($data, "acl"))					$acl 		= $data["acl"];
    if (ffIsset($data, "location"))				$location 	= $data["location"];
    if (ffIsset($data, "position"))				$position 	= $data["position"];
    if (ffIsset($data, "settings"))				$settings 	= $data["settings"];
    if (ffIsset($data, "redir"))				$redir 		= $data["redir"];
    if (ffIsset($data, "class"))				$class 		= $data["class"];
    if (ffIsset($data, "readonly"))				$readonly 	= $data["readonly"];
    if (ffIsset($data, "dialog"))				$dialog 	= $data["dialog"];
    if (ffIsset($data, "favorite"))				$favorite 	= $data["favorite"];
    if (ffIsset($data, "collapse"))				$collapse 	= $data["collapse"];
    if (ffIsset($data, "rel"))					$rel 		= $data["rel"];
    if (ffIsset($data, "profiling_skip"))		$profiling_skip 	= $data["profiling_skip"];

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
        $cm->modules["restricted"]["menu"][$key]["icon"] = $icon;
        $cm->modules["restricted"]["menu"][$key]["actions"] = $actions;
        $cm->modules["restricted"]["menu"][$key]["dialog"] = $dialog;
        $cm->modules["restricted"]["menu"][$key]["readonly"] = $readonly;
        $cm->modules["restricted"]["menu"][$key]["class"] = $class;
        $cm->modules["restricted"]["menu"][$key]["params"] = $params;
        $cm->modules["restricted"]["menu"][$key]["visible"] = $visible;
        if (strlen($acl))
            $cm->modules["restricted"]["menu"][$key]["acl"] = explode(",", $acl);

        if ($location)
            $cm->modules["restricted"]["menu"][$key]["location"] = $location;
        if ($position)
            $cm->modules["restricted"]["menu"][$key]["position"] = $position;
        if ($settings)
            $cm->modules["restricted"]["menu"][$key]["settings"] = $settings;

        if (strlen($redir))
            $cm->modules["restricted"]["menu"][$key]["redir"] = $redir;

		if($collapse !== null)
			$cm->modules["restricted"]["menu"][$key]["collapse"] = $collapse;
        
		if ($profiling_skip)
			$cm->modules["restricted"]["menu"][$key]["profiling_skip"] = true;
		else
			$cm->modules["restricted"]["menu"][$key]["profiling_skip"] = false;

        $cm->modules["restricted"]["menu"][$key]["rel"] = $rel;
        
        $cm->modules["restricted"]["menu_bypath"][$path][] =& $cm->modules["restricted"]["menu"][$key];
        
        if($favorite)
			$cm->modules["restricted"]["sections"]["favorite"][] =& $cm->modules["restricted"]["menu"][$key];

    }
    
}

function mod_restricted_add_menu_sub_element($data) {
    $cm = cm::getInstance();

    if (ffIsset($data, "key"))					$key 				= $data["key"];
    if (ffIsset($data, "subkey"))				$subkey 			= $data["subkey"];
    if (ffIsset($data, "path"))					$path 				= $data["path"];
    if (ffIsset($data, "label"))				$label 				= $data["label"];
    if (ffIsset($data, "icon"))					$icon 				= $data["icon"];
    if (ffIsset($data, "actions"))				$actions 			= $data["actions"];
    if (ffIsset($data, "params"))				$params 			= $data["params"];
    if (ffIsset($data, "acl"))					$acl 				= $data["acl"];
    if (ffIsset($data, "location"))				$location 			= $data["location"];
    if (ffIsset($data, "position"))				$position 			= $data["position"];
    if (ffIsset($data, "settings"))				$settings 			= $data["settings"];
    if (ffIsset($data, "hide"))					$hide 				= $data["hide"];
    if (ffIsset($data, "description"))			$description		= $data["description"];
    if (ffIsset($data, "profiling_skip"))		$profiling_skip 	= $data["profiling_skip"];
    if (ffIsset($data, "readonly"))				$readonly 			= $data["readonly"];
    if (ffIsset($data, "dialog"))				$dialog 			= $data["dialog"];
    if (ffIsset($data, "class"))				$class 				= $data["class"];
    if (ffIsset($data, "favorite"))				$favorite 			= $data["favorite"];
    if (ffIsset($data, "rel"))					$rel 				= $data["rel"];

	if ($subkey && !isset($cm->modules["restricted"]["menu"][$key]["elements"][$subkey]))
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
        if (strlen($position))
            $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["position"] = $position;
        if (strlen($settings))
            $cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["settings"] = $settings;
		//if (!$is_heading)
		//{
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["params"] = $params;

			if (!strlen($path))
				$path = strtolower($cm->modules["restricted"]["menu"][$key]["path"] . "/" . $subkey);
			$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["path"] = $path;
			$cm->modules["restricted"]["menu_bypath"][$path][] =& $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];
			
		//}

		if (!strlen($label))
			$label = $subkey;

		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["label"] = $label;
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["icon"] = $icon;
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["actions"] = $actions;
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["dialog"] = $dialog;
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["readonly"] = $readonly;
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["class"] = $class;
		$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["rel"] = $rel;
		//$cm->modules["restricted"]["menu"][$key]["elements"][$subkey]["is_heading"] = $is_heading;

		if($location && isset($cm->modules["restricted"]["sections"]["nav"]->$location)) {
			$cm->modules["restricted"]["sections"][$location]["elements"][$subkey] = $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];
		}		
		
		if($favorite)
            $cm->modules["restricted"]["sections"]["favorite"][] =& $cm->modules["restricted"]["menu"][$key]["elements"][$subkey];

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

	if ($navbar !== null && ffArrIsset($mod_data, "menu", $topbar, "elements", $navbar))
	{
		if (!isset($mod_data["menu"][$topbar]["elements"][$navbar]["old_acl"]))
		{
			$mod_data["menu"][$topbar]["elements"][$navbar]["old_acl"] = $mod_data["menu"][$topbar]["elements"][$navbar]["acl"];
			$mod_data["menu"][$topbar]["elements"][$navbar]["acl"] = array(0);
		}
	}
	else if (ffArrIsset($mod_data, "menu", $topbar))
	{
		if (isset($mod_data["menu"][$topbar]["elements"]) && count($mod_data["menu"][$topbar]["elements"]))
		{
			foreach ($mod_data["menu"][$topbar]["elements"] as $key => $value)
			{
				if (!isset($mod_data["menu"][$topbar]["elements"][$key]["old_acl"]))
				{
					$mod_data["menu"][$topbar]["elements"][$key]["old_acl"] = $mod_data["menu"][$topbar]["elements"][$key]["acl"];
					$mod_data["menu"][$topbar]["elements"][$key]["acl"] = array(0);
				}
			}
			reset($mod_data["menu"][$topbar]["elements"]);
		}
		if (!isset($mod_data["menu"][$topbar]["acl"]))
		{
			$mod_data["menu"][$topbar]["old_acl"] = $mod_data["menu"][$topbar]["acl"];
			$mod_data["menu"][$topbar]["acl"] = array(0);
		}
	}
}

function mod_res_enable_element($topbar, $navbar = null)
{
	$mod_data =& cm::getInstance()->modules["restricted"];

	if ($navbar !== null)
	{
		if (isset($mod_data["menu"][$topbar]["elements"][$navbar]["old_acl"]))
		{
			$mod_data["menu"][$topbar]["elements"][$navbar]["acl"] = $mod_data["menu"][$topbar]["elements"][$navbar]["old_acl"];
			unset($mod_data["menu"][$topbar]["elements"][$navbar]["old_acl"]);
		}
	}
	else
	{
		if (isset($mod_data["menu"][$topbar]["elements"]) && count($mod_data["menu"][$topbar]["elements"]))
		{
			foreach ($mod_data["menu"][$topbar]["elements"] as $key => $value)
			{
				if (isset($mod_data["menu"][$topbar]["elements"][$key]["old_acl"]))
				{
					$mod_data["menu"][$topbar]["elements"][$key]["acl"] = $mod_data["menu"][$topbar]["elements"][$key]["old_acl"];
					unset($mod_data["menu"][$topbar]["elements"][$key]["old_acl"]);
				}
			}
			reset($mod_data["menu"][$topbar]["elements"]);
		}
		if (isset($mod_data["menu"][$topbar]["old_acl"]))
		{
			$mod_data["menu"][$topbar]["acl"] = $mod_data["menu"][$topbar]["old_acl"];
			unset($mod_data["menu"][$topbar]["old_acl"]);
		}
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
    $hash = "";
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