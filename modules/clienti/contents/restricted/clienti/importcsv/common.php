<?php
  function get_importcsv_fields($first_col = array()) {
	$db = ffDB_Sql::factory();

	$structure = array(
						"ID_tipo" => array("key" => "ID_tipo"	
												, "value" => "SELECT cm_mod_clienti_tipo.ID FROM cm_mod_clienti_tipo WHERE cm_mod_clienti_tipo.tipo = [VALUE]"
												
										)
                        , "provincia" => array("key" => "provincia"    
                                                , "value" => "SELECT support_province.ID FROM support_province WHERE support_province.CarAbbreviation = [VALUE]"
                                               
                                        )
                        , "nazione" => array("key" => "nazione"    
                                                , "value" => "SELECT support_countries.ID  FROM support_countries WHERE support_countries.`code` = [VALUE]"
                                                
                                        )
						, "codice" => array("require" => true)
                        , "ragsoc" => array("require" => false)
                        , "piva" => array("require" => false)
                        , "cf" => array("require" => false)
						, "email1" => array("validator" => ffValidator::getInstance("email"))
                        , "email2" => array("validator" => ffValidator::getInstance("email"))
										
												
	);
	
	$field = array(
			array(new ffData("ID_tipo"), new ffData("Tipo"))
			, array(new ffData("ragsoc"), new ffData("Ragione sociale"))
			, array(new ffData("nome"), new ffData("nome"))
			, array(new ffData("cognome"), new ffData("cognome"))
			, array(new ffData("piva"), new ffData("piva"))
			, array(new ffData("cf"), new ffData("cf"))
			, array(new ffData("indirizzo"), new ffData("indirizzo"))
			, array(new ffData("cap"), new ffData("cap"))
			, array(new ffData("citta"), new ffData("citta"))
			, array(new ffData("provincia"), new ffData("provincia"))
			, array(new ffData("nazione"), new ffData("nazione"))
            , array(new ffData("isPotenziale"), new ffData("isPotenziale"))
            , array(new ffData("telefono1"), new ffData("telefono1"))
            , array(new ffData("telefono2"), new ffData("telefono2"))
            , array(new ffData("cellulare1"), new ffData("cellulare1"))
            , array(new ffData("cellulare2"), new ffData("cellulare2"))
            , array(new ffData("email1"), new ffData("email1"))
            , array(new ffData("email2"), new ffData("email2"))
            , array(new ffData("fax"), new ffData("fax"))
            , array(new ffData("referente"), new ffData("referente") )
            , array(new ffData("tipo_azienda"), new ffData("tipo_azienda") )
            , array(new ffData("fax2"), new ffData("fax2") )
            , array(new ffData("codice"), new ffData("codice") )
		);
	
	$found_match = 0;
	if(is_array($first_col) && count($first_col) && is_array($field) && count($field)) {
		foreach($field AS $key => $value) {
			if(array_search(ffCommon_url_rewrite($field[$key][0]->getValue()), $first_col)) {
				$found_match++;
			}
		}
	}
	if($found_match > 0 && (($found_match * 2) - count($first_col)) >= 0) {
		$skip_first_col = true;
	} else {
		$skip_first_col = false;
	}
	return array("record" => array("url" => FF_SITE_PATH . "/clienti/clienti/modify"
					, "key" => "ID"
					, "resources" => "clienti"
					, "record_id" => "RecordClienti"
				)
				, "table" => "cm_mod_clienti_main"
				, "structure" => $structure
				, "field" => $field
				, "skip_first_col" => $skip_first_col
	);
}

function importcsv_open($filename = null, $sep_field = null, $page = null, $limit = null, $ref = null) {
	$globals = ffGlobals::getInstance("wizard");
	
	if($filename === null) {
		$filename = get_session("importcsv");
	} else {
		set_session("importcsvsep", $filename);
	}
	if($sep_field === null) {
		$sep_field = get_session("importcsvsep");
	} else {
		set_session("importcsvsep", $sep_field);
	}
	
	if($page === null) {
		$page = (isset($_REQUEST["page"])
					? $_REQUEST["page"]
					: (get_session("importcsvpage") > 0
						? get_session("importcsvpage")
						: 1
					)
				);
	}
	set_session("importcsvpage", $page);

	if($limit === null) {
		$limit = (isset($_REQUEST["limit"])
					? $_REQUEST["limit"]
					: (get_session("importcsvlimit") > 0
						? get_session("importcsvlimit")
						: 10
					)
				);
	}
	set_session("importcsvlimit", $limit);
		
	if($ref === null) {
		$ref = (isset($_REQUEST["ref"])
					? $_REQUEST["ref"]
					: (get_session("importcsvref") > 0
						? get_session("importcsvref")
						: time()
					)
				);
	}
	set_session("importcsvref", $ref);

	$count_data = 0;
	$globals->import_fields = array();
	if(is_file(FF_DISK_PATH . "/uploads/importcsv/" . $filename)) {
		if(!get_session("importcsvlinetotal") > 0) {
			$linecount = count(file(FF_DISK_PATH . "/uploads/importcsv/" . $filename));
			set_session("importcsvlinetotal", $linecount);
		}

		$handle = fopen(FF_DISK_PATH . "/uploads/importcsv/" . $filename, "r");
		if ($handle)
		{
			while (!feof($handle))
			{
				$count_data++;

				$buffer = fgetcsv($handle, 0, $sep_field, '"');
				if(!is_array($buffer))
					continue;
				
		        if($count_data <= ($page - 1) * $limit)
		            continue;
		            
		        if($count_data > (($page - 1) * $limit) + $limit)
		            break;
        					
					
				$globals->import_fields[] = $buffer;
			}
			fclose($handle);
		}
	}
}
function importcsv_exec($csv_rel_field = null, $skip_first_row = null, $page = null, $ref = null) {
	$cm = cm::getInstance();
	$globals = ffGlobals::getInstance("wizard");
	$db = ffDB_Sql::factory();	

	if($csv_rel_field === null) {
		$csv_rel_field = get_session("importcsv_rel_field");
	} else {
		set_session("importcsv_rel_field", $csv_rel_field);
	}
	if($page === null) {
		$page = get_session("importcsvpage");
	} else {
		set_session("importcsvpage", $page);
	}
	if($ref === null) {
		$ref = get_session("importcsvref");
	} else {
		set_session("importcsvref", $ref);
	}


	$arrData = get_importcsv_fields();
	$strError = "";
	if(is_array($arrData["structure"]) && count($arrData["structure"])) {
		foreach($arrData["structure"] AS $structure_key => $structure_value) {
			if(array_key_exists("require", $structure_value)
				&& array_search($structure_key, $csv_rel_field) === false
			) {
				$strError .= ffTemplate::_get_word_by_code("field_require") . " " . $structure_key;
			}
		}
	}
	if(!strlen($strError)) {
		if(is_array($globals->import_fields) && count($globals->import_fields)) {
			foreach($globals->import_fields AS $key => $value) {
				if($page == 1 && $key == 0 && $skip_first_row > 0) {
					continue;
				}

				$field_insert_key = "";
				$field_insert_value = "";
				$field_update = "";
				$field_compare = "";
				$tofix = "";
				if(is_array($value) && count($value)) {
					foreach($value AS $csv_key => $csv_value) {
						if(array_key_exists($csv_rel_field[$csv_key] , $arrData["structure"])
							&& array_key_exists("key", $arrData["structure"][$csv_rel_field[$csv_key]]) 
							&& strlen($arrData["structure"][$csv_rel_field[$csv_key]]["key"])
						) {
							$real_field_key = $arrData["structure"][$csv_rel_field[$csv_key]]["key"];
						} else {
							$real_field_key = $csv_rel_field[$csv_key];
						}

						if(!strlen($real_field_key))
							continue;

						if(!strlen(trim($csv_value))
							&& array_key_exists($csv_rel_field[$csv_key] , $arrData["structure"])
							&& array_key_exists("require", $arrData["structure"][$csv_rel_field[$csv_key]]) 
							&& $arrData["structure"][$csv_rel_field[$csv_key]]["require"] 
						) {
                            if(strpos($tofix, $real_field_key) === false) {
                                if(strlen($tofix))
                                    $tofix     .=",";

                                $tofix         .= $real_field_key;
                            }
							//$strError = ffTemplate::_get_word_by_code("field_is_empty") . " " . $real_field_key;
							//break;
						}							
							
						if(array_key_exists($csv_rel_field[$csv_key] , $arrData["structure"])
							&& array_key_exists("value", $arrData["structure"][$csv_rel_field[$csv_key]]) 
							&& strlen($arrData["structure"][$csv_rel_field[$csv_key]]["value"])
						) {
							$real_field_value = "IFNULL((" . str_replace("[VALUE]", $db->toSql(trim($csv_value)), $arrData["structure"][$csv_rel_field[$csv_key]]["value"]) . "), '')";
						} else {
							$real_field_value = $db->toSql(trim($csv_value));
						}
						
						if(array_key_exists($csv_rel_field[$csv_key] , $arrData["structure"])
							&& array_key_exists("validator", $arrData["structure"][$csv_rel_field[$csv_key]])
						) {
							if($arrData["structure"][$csv_rel_field[$csv_key]]["validator"]->checkValue(new ffData(trim($csv_value)), "", array()) !== false) {
								if(strpos($tofix, $real_field_key) === false) {
									if(strlen($tofix))
										$tofix 	.=",";

									$tofix 		.= $real_field_key;
								}
							}
						}
						
						if(strlen($field_insert_key))
							$field_insert_key .= ", ";

						$field_insert_key 	.= "`" . $real_field_key . "`";

						if(strlen($field_insert_value))
							$field_insert_value .= ", ";

						$field_insert_value .= $real_field_value;

						if(strlen($field_update))
							$field_update .= ", ";

						$field_update 		.= "`" . $real_field_key . "` = " . $real_field_value;

						if(array_key_exists($csv_rel_field[$csv_key] , $arrData["structure"])
							&& array_key_exists("require", $arrData["structure"][$csv_rel_field[$csv_key]])
							&& $arrData["structure"][$csv_rel_field[$csv_key]]["require"]
                            && strlen(trim($csv_value))
						) { 
							if(strlen($field_compare))
								$field_compare .= " AND ";
								
							$field_compare 		.= "LOWER(`" . $real_field_key . "`) = " . strtolower($real_field_value);
						}
					}
				}
				if(!$strError) {
					$ID_node = 0;
					if(strlen($field_compare)) {
						$sSQL = "SELECT " . $arrData["table"] . ".* FROM " . $arrData["table"] . " WHERE " . $field_compare;
                       // echo($sSQL . "<br>"); 
						$db->query($sSQL);
						if($db->nextRecord()) {
							$ID_node = $db->getField("ID", "Number", true);
						}
					}
					$sSQL = "";
					if($ID_node > 0) {
						if(strlen($field_update))
							$sSQL = "UPDATE " . $arrData["table"] . " SET " . $field_update . ", import = " . $db->toSql($ref, "Number") . ", tofix = " . $db->toSql($tofix). " WHERE ID = " . $db->toSql($ID_node, "Number");
					} else {
						if(strlen($field_insert_key) && strlen($field_insert_value))
							$sSQL = "INSERT INTO " . $arrData["table"] . " (ID, " . $field_insert_key . ", import, tofix) VALUES (null, " . $field_insert_value . ", " . $db->toSql($ref, "Number"). ", " . $db->toSql($tofix) . ")";
					}
					if(strlen($sSQL))
						$db->execute($sSQL);
						//echo($sSQL . "<br>");
				}
			}
			if(!$strError) {
				set_session("importcsvpage", $page + 1);

				set_session("importcsvlineprocessed", ((int) get_session("importcsvlineprocessed")) + count($globals->import_fields));

				//return true;	
				if(!isset($_REQUEST["importcsv"]))
					ffRedirect(FF_SITE_PATH . $cm->oPage->page_path . "/step2?" . $cm->oPage->get_globals() . "importcsv=continue");
					
				if($cm->isXHR()) {
					echo ffCommon_jsonenc(array("count" => count($globals->import_fields), "page" => $page), true);
					exit;
				}
			} else {
				return $strError;
			}
		} else {
			set_session("importcsvlineprocessed", "0");
			set_session("importcsvpage", 1);
			if($cm->isXHR()) {
				echo ffCommon_jsonenc(array("count" => 0, "url" => FF_SITE_PATH . $cm->oPage->page_path . "/step3?" . $cm->oPage->get_globals()), true);
				exit;
			} else {
				ffRedirect(FF_SITE_PATH . $cm->oPage->page_path . "/step3?" . $cm->oPage->get_globals() . "ret_url=" . urlencode($_REQUEST["ret_url"]));
			}
		}
	} else {
		return $strError;
	}
	return false;
}
?>
