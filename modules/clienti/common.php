<?php

$path = CM_MODULES_ROOT . "/clienti/conf";

$conf = cm_confCascadeFind($path, "", "mod_clienti.xml");
if (is_file($conf))
mod_clienti_load_config($conf);

function mod_clienti_load_config($file)
{
	$cm = cm::getInstance();

	$xml = new SimpleXMLElement("file://" . $file, null, true);

	if (isset($xml->fields) && count($xml->fields->children()))
	{
		foreach ($xml->fields->children() as $key => $value)
		{
			$key = (string)$key;

			if (!isset($cm->modules["clienti"]["fields"][$key]))
			{
				$cm->modules["clienti"]["fields"][$key] = array();

				$attrs = $value->attributes();
				foreach ($attrs as $subkey => $subvalue)
				{
					$subkey = (string)$subkey;
					$subvalue = (string)$subvalue;

					$cm->modules["clienti"]["fields"][$key][$subkey] = $subvalue;
				}
			}
		}
	}
}

function mod_clienti_add_custom_fields($oRecord, $from_domains = false)
{
	$cm = cm::getInstance();

	if (isset($cm->modules["clienti"]["fields"]) && count($cm->modules["clienti"]["fields"]))
	{
		foreach ($cm->modules["clienti"]["fields"] as $key => $value)
		{
			$enable = true;

			$oField = ffField::factory($cm->oPage);
			$oField->id = $key;
			$oField->store_in_db = false;

			foreach ($value as $subkey => $subvalue)
			{
				switch ($subkey)
				{
					case "file_show_delete":
						if ($subvalue == "true")
							$subvalue = true;
						elseif ($subvalue == "false")
							$subvalue = false;
						break;

					default:
						$subvalue = str_replace("[FF_SITE_PATH]", FF_SITE_PATH, $subvalue);
						$subvalue = str_replace("[FF_DISK_PATH]", FF_DISK_PATH, $subvalue);
				}

				switch ($subkey)
				{
					case "validators":
						$validators = explode(",", $subvalue);
						foreach($validators as $validators_key => $validators_value)
						{
							$validators_value = trim($validators_value);
							if ($validators_value == "")
								continue;

							$oField->addValidator($validators_value);
						}
						break;

					case "group":
						$group = $subvalue;

						if (cm_getMainTheme () == "restricted" && !isset($oRecord->groups[$group]))
						{
								$oRecord->addContent(null, true, $group);
						}
						if (isset($cm->modules["clienti"]["groups"][$group]["title"]))
							$oRecord->groups[$group]["title"] = $cm->modules["clienti"]["groups"][$group]["title"];
						break;

					case "enable_acl":
						if (!$from_domains)
						{
							$acl = explode(",", $subvalue);
							if (is_array($acl) && count($acl))
							{
								$acl = array_flip($acl);
								if (!isset($acl[get_session("UserLevel")]))
								{
									$enable = false;
								}
							}
						}
						break;

					case "acl":
						if (!$from_domains)
						{
							$acl = explode(",", $subvalue);
							if (is_array($acl) && count($acl))
							{
								$acl = array_flip($acl);
								if (!isset($acl[get_session("UserLevel")]))
								{
									$oField->store_in_db = false;
									$oField->control_type = "label";
								}
							}
						}
						break;

					default:
						$tmp = '$oField->' . $subkey . ' = "' . $subvalue . '";';
						eval($tmp);
				}
			}
			reset($value);

			switch($oField->extended_type)
			{
				case "Boolean":
					if ($oField->control_type == "label")
					{
						$oField->extended_type = "Selection";
						$oField->multi_pairs = array(
							array(new ffData(0, $oField->base_type), new ffData("No"))
							, array(new ffData(1, $oField->base_type), new ffData("Si"))
						);
					}
					else
					{
						$oField->unchecked_value = new ffData(0, $oField->base_type);
						$oField->checked_value = new ffData(1, $oField->base_type);
					}
					break;

				case "Selection":
				/*	if (!$from_domains && MOD_SEC_MULTIDOMAIN && MOD_SEC_MULTIDOMAIN_EXTERNAL_DB && mod_security_is_admin())
						$oField->db = array(mod_security_get_main_db());
					elseif ($from_domains && MOD_SEC_MULTIDOMAIN_EXTERNAL_DB)
						$oField->db = array(mod_security_get_db_by_domain($_REQUEST["keys"]["ID"]));
				 * 
				 */
			}

			if ($enable) $oRecord->addContent($oField, $group);
		}
		reset($cm->modules["clienti"]["fields"]);
	}
}