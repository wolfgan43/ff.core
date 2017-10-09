<?php
function process_mail($email_struct, $to, $subject = NULL, $tpl_email_path = NULL, $fields = NULL, $from = NULL, $bcc = NULL,  $cc = NULL, $preview = false, $enable_check = true, $invert_mail = false, $owner = null, $body = null, $attach = array(), $prefix = null) 
{
	$cm = cm::getInstance();
	$struct = array(
		"db" => false
		, "ID" => 0
		, "theme" => (is_object($cm->oPage) ? $cm->oPage->getTheme() : FRONTEND_THEME)
		, "default" => null
		, "domain" => (strpos(strtolower($_SERVER["HTTP_HOST"]), "www.") === 0 
						? substr($_SERVER["HTTP_HOST"], strpos($_SERVER["HTTP_HOST"], ".") + 1)
						: $_SERVER["HTTP_HOST"]
					)
		, "lang" => FF_LOCALE
		, "mail" => array(
			"smtp" => array(
				"host" => "localhost"
				, "auth" => false
				, "username" => ""
				, "password" => ""
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
		, "prefix" => null
		, "prefix_label" => null
	);
	if(is_array($email_struct)) {
		$struct = array_merge($struct, $email_struct);
	} elseif(strlen($email_struct) && is_numeric($email_struct)) {
		$struct["db"] = true;
		$struct["ID"] = $email_struct;
	}
	
	if($struct["db"])
		$db_mail = ffDB_Sql::factory();	
		
	if($struct["db"] && $struct["ID"] > 0) {
	    $sSQL = "SELECT * FROM email WHERE ID = " . $db_mail->toSql($struct["ID"], "Number");
	    $db_mail->query($sSQL);
	    if($db_mail->nextRecord()) {
            $struct["default"]= array(
                "theme" => THEME_INSET
                , "path" => "/" . GALLERY_TPL_PATH
            );
			$struct["mail"]["smtp"] = array(
				"host" => A_SMTP_HOST
				, "auth" => SMTP_AUTH
				, "username" => A_SMTP_USER
				, "password" => A_SMTP_PASSWORD
			);
	    	$struct["mail"]["subject"] = ffTemplate::_get_word_by_code("email_" . preg_replace('/[^a-zA-Z0-9]/', '', $db_mail->getField("name")->getValue()) . "_subject");
	        $struct["mail"]["name"] = $db_mail->getField("name")->getValue();
	        $struct["mail"]["notify"] =  $db_mail->getField("enable_notify", "Number", true);

	        $struct["mail"]["from"] = array(
	        	"name" => $db_mail->getField("from_name")->getValue()
	        	, "mail" => $db_mail->getField("from_email")->getValue()
	        );  
	        $struct["mail"]["tpl_path"] = $db_mail->getField("tpl_email_path")->getValue();
	        if(strlen($db_mail->getField("theme")->getValue())) {
		        $struct["theme"] = $db_mail->getField("theme")->getValue();
			}
			if(strlen($db_mail->getField("fields_example", "Text", true))) 
				$struct["example"] = json_decode($db_mail->getField("fields_example", "Text", true), true);
			
			if(strlen($db_mail->getField("email_debug", "Text", true))) 
				$struct["debug"] = $db_mail->getField("email_debug", "Text", true);

			/*
	        $mail_name = preg_replace('/[^a-zA-Z0-9]/', '', $db_mail->getField("name")->getValue());
	        $mail_dir = $db_mail->getField("name")->getValue();
	        $enable_notify = $db_mail->getField("enable_notify", "Number", true);
	        
	        $default_from["name"] = $db_mail->getField("from_name")->getValue();
	        $default_from["mail"] = $db_mail->getField("from_email")->getValue();

	        if($tpl_email_path === NULL) {
	            $tpl_email_path = $db_mail->getField("tpl_email_path")->getValue();
				if(strlen($db_mail->getField("theme")->getValue())) {
					$struct["theme"] = $db_mail->getField("theme")->getValue();	
				} else {
					$struct["theme"] = FRONTEND_THEME;
				}
			} else {
				$struct["theme"] = FRONTEND_THEME;			
			}*/
		}
 	}
	
	
	if($prefix === null){
		if($struct["prefix_label"] ===  null) 
			$struct["prefix_label"] = preg_replace('/[^a-zA-Z0-9]/', '', $struct["mail"]["name"]);
	} else
	{
		$struct["prefix_label"] = $prefix;
	}
	
	if($struct["prefix"] === null)
	{
		$struct["prefix"] = preg_replace('/[^a-zA-Z0-9]/', '', $struct["mail"]["name"]);
	}
	if($struct["example"]) {
        if($fields === null) {
        	if($preview) {
				$fields = $struct["example"];
			}
        }
	} else {
		if($struct["db"] && is_array($fields) && count($fields)) {
			$sSQL = "UPDATE email SET fields_example = " . $db_mail->toSql(json_encode($fields)) . " WHERE ID = " . $db_mail->toSql($struct["ID"], "Number");
			$db_mail->execute($sSQL);
		}
		
	}    

	if($preview === "send") {
		if(strlen($struct["debug"]))
			$to = array(0 => $struct["debug"]);

		$preview = false;
	}
	if($tpl_email_path === null)
		$tpl_email_path = $struct["mail"]["tpl_path"];

	if(strlen($tpl_email_path)) {
		if(is_file(FF_DISK_PATH . $tpl_email_path)) {
			$tpl_email_html_path = FF_DISK_PATH . $tpl_email_path;
		} elseif(is_file(FF_DISK_PATH . $tpl_email_path . "/email.tpl")) {
			$tpl_email_html_path = FF_DISK_PATH . $tpl_email_path . "/email.tpl";
		} elseif(strlen($struct["default"]["path"]) && is_file(FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["theme"] . $struct["default"]["path"] . $tpl_email_path)) {
        	$tpl_email_html_path = FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["theme"] . $struct["default"]["path"] . $tpl_email_path;
        	$tpl_error = false;
		} elseif(strlen($struct["default"]["theme"])) {
			$tpl_email_html_path = FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["default"]["theme"] . "/contents/mail/email.tpl";
			$tpl_error = true;
		} else {
			return ffTemplate::_get_word_by_code("mail_tpl_not_exist");
		}

		if(is_file(ffCommon_dirname($tpl_email_html_path) . "/" . ffGetFilename($tpl_email_html_path) . ".txt")) {
        	$tpl_email_txt_path = ffCommon_dirname($tpl_email_html_path) . "/" . ffGetFilename($tpl_email_html_path) . ".txt";
        	$tpl_error = false;
		} else {
			if(strlen($struct["theme"]) && is_file(FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["theme"] . "/contents/mail/email.txt")) {
				$tpl_email_txt_path = FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["default"]["theme"] . "/contents/mail/email.txt";	
			} elseif(strlen($struct["default"]["theme"]) && is_file(FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["default"]["theme"] . "/contents/mail/email.txt")) {
				$tpl_email_txt_path = FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["theme"] . "/contents/mail/email.txt";	
			}
			$tpl_error = true;
		}
	} else {
		if(strlen($struct["theme"]) && is_file(FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["theme"] . "/contents/mail/email.tpl")) {
			$tpl_email_html_path = FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["theme"] . "/contents/mail/email.tpl";
			$tpl_email_txt_path = FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["theme"] . "/contents/mail/email.txt";	
		} elseif(strlen($struct["default"]["theme"]) && is_file(FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["default"]["theme"] . "/contents/mail/email.tpl")) {
			$tpl_email_html_path = FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["default"]["theme"] . "/contents/mail/email.tpl";
			$tpl_email_txt_path = FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["default"]["theme"] . "/contents/mail/email.txt";	
		} else {
			return ffTemplate::_get_word_by_code("mail_default_tpl_not_exist");
		}
		$tpl_error = true;
	}

	if($tpl_error) {
		if(strlen($tpl_email_path))
			$tpl_email_path = ffTemplate::_get_word_by_code("wrong_path") . " (" . $tpl_email_path . ")<br />";

		$tpl_email_path .= ffTemplate::_get_word_by_code("use_this_path") . " (" . str_replace(FF_DISK_PATH, "", $tpl_email_html_path) . ")";
	}
	
    if($subject === NULL)
        $subject = $struct["mail"]["subject"];

    if($from === NULL) {
        $from["name"] = $struct["mail"]["from"]["name"];
        $from["mail"] = $struct["mail"]["from"]["mail"];
    } elseif(is_array($from)) {
		if(!array_key_exists("mail", $from)) {
			$from = array(
				"name" => $from[0]
				, "mail" => $from[0]
			);
		}
	} elseif(!is_array($from) && strlen($from)) {
		$from = array(
			"name" => $from
			, "mail" => $from
		);
    }

	if($invert_mail) {
		$domain_name = $struct["domain"];

		$tmp_from = $from;
		$from = $to;
		$to = array(0 => $tmp_from);
		
	    if(is_array($from)) {
        	if(count($from)) {
		        foreach($from AS $from_value) {
		            if(is_array($from_value)) {
		                if($from_value["mail"]) {
		                    if(!$from_value["name"])
		                        $from_value["name"] = $from_value["mail"];
							
                        		$tmp_from["name"] = $from_value["name"]; 
                        		$tmp_from["mail"] = $from_value["mail"]; 
		                }
		            } else {
		                if($from_value) {
                        	$tmp_from["name"] = $from_value; 
                        	$tmp_from["mail"] = $from_value; 
		                }
		            }
		            break;
		        }
			} else {
				$tmp_from["name"] = "" . ucfirst($domain_name); 
                $tmp_from["mail"] = "noreply@" . $domain_name; 
			}
	    } else {
	        if($from) {
                $tmp_from["name"] = $from; 
                $tmp_from["mail"] = $from; 
			} else {
				$tmp_from["name"] = "" . ucfirst($domain_name); 
                $tmp_from["mail"] = "noreply@" . $domain_name; 
			}
	    }
	    $from = array();
	    $from["name"] = $tmp_from["name"];
	    $from["mail"] = $tmp_from["mail"];	
	}            

	if(!$from["mail"] || !$from["name"])
		$strError = ffTemplate::_get_word_by_code("email_from_undefined");

    if(!check_function("class.phpmailer"))
        return ffTemplate::_get_word_by_code("class_mailer_undefined");

    $mail = new phpmailer();
    $mail->SetLanguage(strtolower(substr($struct["lang"], 0, -1)), FF_DISK_PATH . "/library/phpmailer/language/");
    $mail->Subject    = $subject;

    if(0) { // non funzona l'invio con il mailer
	    if($struct["mail"]["smtp"]["auth"]) {
		    $mail->IsSMTP();
		    $mail->Host = $struct["mail"]["smtp"]["host"];
		    $mail->SMTPAuth    = $struct["mail"]["smtp"]["auth"];
		    $mail->Username    = $struct["mail"]["smtp"]["username"];
		    $mail->Password    = $struct["mail"]["smtp"]["password"];
		} else {
			$mail->IsMail();
		}
	} else {
	    $mail->Host = $struct["mail"]["smtp"]["host"];
	    $mail->Mailer   = "smtp";
	    $mail->SMTPAuth    = $struct["mail"]["smtp"]["auth"];
	    $mail->Username    = $struct["mail"]["smtp"]["username"];
	    $mail->Password    = $struct["mail"]["smtp"]["password"];
	}

	$mail->CharSet = strtolower(FF_DEFAULT_CHARSET);
	$mail->Encoding = "quoted-printable";

    if(!$struct["db"])
        $enable_check = false;

    if(is_array($to)) {
        if(count($to)) {
	        foreach($to AS $to_value) {
	            if(is_array($to_value)) {
	                if($to_value["mail"]) {
	                    if(!$to_value["name"])
	                        $to_value["name"] = $to_value["mail"];
						
						$mailbox_error = verifyMailbox($to_value["mail"]);
						//if($mailbox_error == false) {
                        	$mail->AddAddress($to_value["mail"], $to_value["name"]);
                        	$arrAddress["to"][$to_value["mail"]] = $to_value["name"];
							if($from["mail"] == $to_value["mail"])
								$from["mail"] = "noreply" . substr($from["mail"], strpos($from["mail"], "@"));
						//}
	                    if($enable_check)
	                        check_email($to_value["mail"], $to_value["name"], $mailbox_error);
	                }
	            } else {
	                if($to_value) {
						$mailbox_error = verifyMailbox($to_value);
						//if($mailbox_error == false) {
		                    $mail->AddAddress($to_value, $to_value);
		                    $arrAddress["to"][$to_value] = $to_value;
							if($from["mail"] == $to_value)
								$from["mail"] = "noreply" . substr($from["mail"], strpos($from["mail"], "@"));
						//}
	                    if($enable_check)
	                        check_email($to_value, $to_value, $mailbox_error);
	                }
	            }
	        }
		} else {
			$strError = ffTemplate::_get_word_by_code("email_to_undefined");
		}
    } else {
        if($to) {
			$mailbox_error = verifyMailbox($to);
			//if($mailbox_error == false) {
                $mail->AddAddress($to, $to);
                $arrAddress["to"][$to] = $to;
				if($from["mail"] == $to)
					$from["mail"] = "noreply" . substr($from["mail"], strpos($from["mail"], "@"));
			//}

            if($enable_check)
                check_email($to, $to, $mailbox_error);
		} else {
			$strError = ffTemplate::_get_word_by_code("email_to_undefined");
		}
    }

    if($mail->ErrorInfo) {
        $strError = $mail->ErrorInfo;
	}
    if(!$strError || $preview) {
	    $mail->From     = $from["mail"];
	    $mail->FromName = $from["name"];

	    $arrAddress["from"][$from["mail"]] = $from["name"];

		$cc_db = null;
		$bcc_db = null;
		if($struct["db"] && $struct["ID"] > 0) {
		    $sSQL = "SELECT 
		                email_address.name
		                , email_address.email
		                , email_rel_address.type
		            FROM
		                email_rel_address
		                INNER JOIN email ON email.ID = email_rel_address.ID_email
		                INNER JOIN email_address ON email_address.ID = email_rel_address.ID_address
		            WHERE
		                email.ID = " . $db_mail->toSql($struct["ID"], "Number") . "
		            ORDER BY email_rel_address.type, email_address.name, email_address.email";
		    $db_mail->query($sSQL);
		    if($db_mail->nextRecord()) {
		        $i = 0;
		        do {
		            $i++;
		            ${$db_mail->getField("type")->getValue() . "_db"}[$i]["name"] = $db_mail->getField("name")->getValue();
		            ${$db_mail->getField("type")->getValue() . "_db"}[$i]["mail"] = $db_mail->getField("email")->getValue();
		        } while($db_mail->nextRecord());
		    }
		}
		
	    if($cc === null) {
	    	if(is_array($cc_db)) {
				$cc = $cc_db;
	    	} else {
				$cc = $struct["mail"]["cc"];	
	    	}
			$enable_check_cc = false;
	    } else {
			$enable_check_cc = true;
	    }
 		if(is_array($cc)) {
	        foreach($cc AS $cc_value) {
	            if(is_array($cc_value)) {
	                if(!$cc_value["mail"]) 
	                    $cc_value["mail"] = $struct["mail"]["cc"]["mail"];

	                if(!$cc_value["name"])
	                    $cc_value["name"] = $cc_value["mail"];
	                
	                if($cc_value["mail"]) {
						$mailbox_error = verifyMailbox($cc_value["mail"]);
						if($mailbox_error == false) {
                        	$mail->AddCC($cc_value["mail"], $cc_value["name"]);
                        	$arrAddress["cc"][$cc_value["mail"]] = $cc_value["name"];
						}
	                    if($enable_check && $enable_check_cc)
	                        check_email($cc_value["mail"], $cc_value["name"], $mailbox_error);
	                }
	            } else {
	                if($cc_value !== false) {
	                    $cc_value = $struct["mail"]["cc"]["mail"];
	                    
	                    if($cc_value) {
							$mailbox_error = verifyMailbox($cc_value);
							if($mailbox_error == false) {
                            	$mail->AddCC($cc_value, $cc_value);
                            	$arrAddress["cc"][$cc_value] = $cc_value;
							}
	                        if($enable_check && $enable_check_cc)
	                            check_email($cc_value, $cc_value, $mailbox_error);
	                    }
	                }
	            }
	        }
	    } else {
	        if($cc !== false) { 
	            $cc = $struct["mail"]["cc"]["mail"];
	            
	            if($cc) {
					$mailbox_error = verifyMailbox($cc);
					if($mailbox_error == false) {
                    	$mail->AddCC($cc, $cc);
                    	$arrAddress["cc"][$cc] = $cc;
					}
	                if($enable_check && $enable_check_cc)
	                    check_email($cc, $cc, $mailbox_error);
	            }
	        }
	    }

	    if($bcc === null) {
	    	if(is_array($bcc_db)) {
				$bcc = $bcc_db;
	    	} else {
				$bcc = $struct["mail"]["bcc"];	
	    	}
			$enable_check_bcc = false;
	    } else {
			$enable_check_bcc = true;
	    }

	    if(is_array($bcc)) {
	        foreach($bcc AS $bcc_value) {
	            if(is_array($bcc_value)) {
	                if(!$bcc_value["mail"]) 
	                    $bcc_value["mail"] = $struct["mail"]["bcc"]["mail"];

	                if(!$bcc_value["name"])
	                    $bcc_value["name"] = $bcc_value["mail"];
	                
	                if($bcc_value["mail"]) {
						$mailbox_error = verifyMailbox($bcc_value["mail"]);
						if($mailbox_error == false) {
                        	$mail->AddBCC($bcc_value["mail"], $bcc_value["name"]);
                        	$arrAddress["bcc"][$bcc_value["mail"]] = $bcc_value["name"];
						}
	                    if($enable_check && $enable_check_bcc)
	                        check_email($bcc_value["mail"], $bcc_value["name"], $mailbox_error);
	                }
	            } else {
	                if($bcc_value !== false) {
	                    $bcc_value = $struct["mail"]["bcc"]["mail"];
	                    
	                    if($bcc_value) {
							$mailbox_error = verifyMailbox($bcc_value);
							if($mailbox_error == false) {
                            	$mail->AddBCC($bcc_value, $bcc_value);
                            	$arrAddress["bcc"][$bcc_value] = $bcc_value;
							}

	                        if($enable_check && $enable_check_bcc)
	                            check_email($bcc_value, $bcc_value, $mailbox_error);
	                    }
	                }
	            }
	        }
	    } else {
	        if($bcc !== false) { 
	            $bcc = $struct["mail"]["bcc"]["mail"];

	            if($bcc) {
					$mailbox_error = verifyMailbox($bcc);
					if($mailbox_error == false) {
                    	$mail->AddBCC($bcc, $bcc);
                    	$arrAddress["bcc"][$bcc] = $bcc;
					}
                    	
	                if($enable_check && $enable_check_bcc)
	                    check_email($bcc, $bcc, $mailbox_error);
	            }
	        }
	    }

	    $tpl = ffTemplate::factory(ffCommon_dirname($tpl_email_html_path));
	    $tpl->load_file(basename($tpl_email_html_path), "main");
	    
	    $tpl_alt = ffTemplate::factory(ffCommon_dirname($tpl_email_txt_path));
	    $tpl_alt->load_file(basename($tpl_email_txt_path), "main");

	    
	    $tpl->set_var("site_path", FF_SITE_PATH);
	    $tpl->set_var("site_updir", SITE_UPDIR);
	    $tpl->set_var("domain_inset", $struct["domain"]);
	    $tpl->set_var("theme_inset", THEME_INSET);
	    $tpl->set_var("theme", $struct["theme"]);
	    $tpl->set_var("email_name", $struct["mail"]["name"]);
	    $tpl->set_var("language_inset", $struct["lang"]);

	    $headers_mail = NULL;        
	    $headers_tag = NULL;
	    if($preview) {
	        $headers_mail["preview_general"]["_email_preview_name"] = $struct["mail"]["name"];
	        $headers_mail["preview_general"]["_email_preview_theme"] = $struct["theme"];
	        $headers_mail["preview_general"]["_email_preview_template"] = $tpl_email_path;
	        $headers_mail["preview_general"]["_email_preview_default_theme"] = $struct["default"]["theme"];
	        $headers_mail["preview_general"]["_email_preview_default_path"] = $struct["default"]["path"];
	        $headers_mail["preview_general"]["_email_preview_enable_notify"] = ($struct["mail"]["notify"] ? ffTemplate::_get_word_by_code("yes") : ffTemplate::_get_word_by_code("no"));
	        $headers_mail["preview_general"]["_email_preview_mail_debug"] = $struct["debug"];
	        
	        if($from) 
	            $headers_mail["preview_from"] = $from;
	        if($to) 
	            $headers_mail["preview_to"] = $to;
			if($cc) 
	            $headers_mail["preview_cc"] = $cc;
	        if($bcc) 
	            $headers_mail["preview_bcc"] = $bcc;
	        if($subject) 
	            $headers_mail["preview_subject"] = $subject;
	        
			if(is_array($arrAddress) && count($arrAddress)) {
				foreach($arrAddress AS $arrAddress_type => $arrAddress_value) {
					if(is_array($arrAddress_value) && count($arrAddress_value)) {
						$count_address = 0;
						foreach($arrAddress_value AS $address_email => $address_name) {
							$headers_tag['tags_' . $arrAddress_type]['{' . $arrAddress_type . "_email_" . $count_address .'}'] = $address_email . " ( example )";
							$headers_tag['tags_' . $arrAddress_type]['{' . $arrAddress_type . "_name_" . $count_address .'}'] = $address_name . " ( example )";
							$count_address++;
						}
					}
				}
			}
	        
	        if(is_array($fields) && count($fields)) {
				foreach ($fields AS $fields_key => $fields_value) {
            		if(strlen($fields_key)) {
            			$field_label = "_" . $fields_key;
            			$fields_tag = $fields_key . "_";
						
						$headers_tag['tags_title']["{_" . $struct["prefix"] . $field_label . "}"] = (strpos(ffTemplate::_get_word_by_code($struct["prefix"] . $field_label), $struct["prefix"] . $field_label) === false  ? ffTemplate::_get_word_by_code($struct["prefix"] . $field_label) : ffTemplate::_get_word_by_code("not_set"));
					}

					if(is_array($fields_value) && count($fields_value)) {
						foreach ($fields_value AS $fields_value_key => $fields_value_value) {
							if(strtolower($fields_value_key) == "settings")
								continue;

							if(is_array($fields_value_value) && count($fields_value_value)) {
								if(isset($fields_value["settings"]) 
									&& isset($fields_value["settings"]["type"]) 
									&& strlen($fields_value["settings"]["type"])
								) {
									$field_type = $fields_value["settings"]["type"];
									foreach ($fields_value_value AS $fields_value_value_key => $fields_value_value_value) {
										$headers_tag['tags_row [' . ffCommon_specialchars('<!--BeginSezRow' . $field_type . '--><!--EndSezRow' . $field_type . '-->') . ']']['{' . $fields_tag . $fields_value_value_key .'}'] = $fields_value_value_value . " ( example )";
										$headers_tag['tags_row_label [' . ffCommon_specialchars('<!--BeginSezRow' . $field_type . '--><!--EndSezRow' . $field_type . '-->') . ']']['{_' . $struct["prefix"] . $field_label . "_" . $fields_value_value_key . ' }'] = ffTemplate::_get_word_by_code($struct["prefix"] . $field_label . "_" . $fields_value_value_key);
									}
								} else {
									$field_type = ucfirst($fields_key) . ucfirst($fields_value_key);
									foreach ($fields_value_value AS $fields_value_value_key => $fields_value_value_value) {
										$headers_tag['tags_row [' . ffCommon_specialchars('<!--BeginSezRow' . $field_type . '-->{content}{_' . $struct["prefix"] . '_sep}<!--EndSezRow' . $field_type . '-->') . ']']['{' . $fields_tag . $fields_value_key . "_" . $fields_value_value_key .'}'] = $fields_value_value_value . " ( example )";
									}
								}
							} else {
									$headers_tag['tags']['{' . $fields_tag . $fields_value_key .'}'] = $fields_value_value . " ( example )";
									$headers_tag['tags_label']['{_' . $struct["prefix"] . $field_label . "_" . $fields_value_key . '}'] = (strpos(ffTemplate::_get_word_by_code($struct["prefix"] . $field_label . "_" . $fields_value_key), $struct["prefix"] . $field_label . "_" . $fields_value_key) === false ? ffTemplate::_get_word_by_code($struct["prefix"] . $field_label . "_" . $fields_value_key) : ffTemplate::_get_word_by_code("not_set"));
							}
						}
					}
				}
	        }

			
		    if($headers_mail !== NULL && is_array($headers_mail) && is_array($struct["default"])) {
			    $tpl_header = ffTemplate::factory(FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["default"]["theme"] . $struct["default"]["path"]);
			    $tpl_header ->load_file("email_header.html", "main");
		        
		        foreach ($headers_mail AS $headers_key => $headers_value) {
		            $tpl_header->set_var("SezHeader", "");
		            if(is_array($headers_value)) {
		                foreach ($headers_value AS $headers_value_key => $headers_value_value) {
		                    if(is_array($headers_value_value)) {
		                        $headers_value_value = $headers_value_value["name"] . "[" . $headers_value_value["mail"] . "]";
		                        $tpl_header->set_var("headers_label", "");
		                    } else {
		                        if(!is_int($headers_value_key)) {
	                                if(substr($headers_key, 0, 1) == "_")
	                                    $tpl_header->set_var("headers_label", ffTemplate::_get_word_by_code("email_" . ltrim($headers_key, "_") . "_" . $headers_value_key));
		                            else 
	                                    $tpl_header->set_var("headers_label", $headers_value_key);
		                        } else {
		                            $tpl_header->set_var("headers_label", "");
		                        }
		                    }
		                    $tpl_header->set_var("real_name", ffCommon_specialchars(preg_replace('/[^a-zA-Z0-9]/', '', "email_" . $headers_key . "_" . $headers_value_key)));
		                    $tpl_header->set_var("headers_class", "data");
		                    $tpl_header->set_var("headers_value", $headers_value_value);
		                    $tpl_header->parse("SezHeader", true);
		                }
		            } elseif(strlen($headers_value)) {
		                $tpl_header->set_var("real_name", ffCommon_specialchars(preg_replace('/[^a-zA-Z0-9]/', '', "email_" . $headers_key . "_" . $headers_value)));
		                $tpl_header->set_var("headers_label", "");
		                $tpl_header->set_var("headers_class", "data");
		                $tpl_header->set_var("headers_value", $headers_value);
		                $tpl_header->parse("SezHeader", true);
		            }
		            $tpl_header->set_var("real_name", ffCommon_specialchars(preg_replace('/[^a-zA-Z0-9]/', '', $struct["prefix"] . "_" . $headers_key)));
		            $tpl_header->set_var("group_name", ffTemplate::_get_word_by_code("email_" . ltrim($headers_key, "_")));
		            $tpl_header->set_var("group_class", preg_replace('/[^a-zA-Z0-9\-]/', '', "email-" . ltrim($headers_key, "-")));
		            $tpl_header->parse("SezHeadersGroups", true);
		        }
		        $tpl_header->parse("SezHeaders", false);
		        
		        $tpl_header->set_var("email_template_title", ffTemplate::_get_word_by_code("email_template_explanation_header"));
		        $preview_header_mail = $tpl_header->rpparse("main", false);
			}
			
			if($headers_tag !== NULL && is_array($headers_tag) && is_array($struct["default"])) {
			    $tpl_header = ffTemplate::factory(FF_DISK_PATH . FF_THEME_DIR . "/" . $struct["default"]["theme"] . $struct["default"]["path"]);
			    $tpl_header ->load_file("email_header.html", "main");
		        
		        foreach ($headers_tag AS $headers_key => $headers_value) {
		            $tpl_header->set_var("SezHeader", "");
		            if(is_array($headers_value)) {
		                foreach ($headers_value AS $headers_value_key => $headers_value_value) {
		                    if(is_array($headers_value_value)) {
		                        $headers_value_value = $headers_value_value["name"] . "[" . $headers_value_value["mail"] . "]";
		                        $tpl_header->set_var("headers_label", "");
		                    } else {
		                        if(!is_int($headers_value_key)) {
	                                if(substr($headers_key, 0, 1) == "_")
	                                    $tpl_header->set_var("headers_label", ffTemplate::_get_word_by_code("email_" . ltrim($headers_key, "_") . "_" . $headers_value_key));
		                            else 
	                                    $tpl_header->set_var("headers_label", $headers_value_key);
		                        } else {
		                            $tpl_header->set_var("headers_label", "");
		                        }
		                    }
		                    $tpl_header->set_var("real_name", ffCommon_specialchars(preg_replace('/[^a-zA-Z0-9]/', '', "email_" . $headers_key . "_" . $headers_value_key)));
		                    $tpl_header->set_var("headers_class", "example");
		                    $tpl_header->set_var("headers_value", $headers_value_value);
		                    $tpl_header->parse("SezHeader", true);
		                }
		            } elseif(strlen($headers_value)) {
		                $tpl_header->set_var("real_name", ffCommon_specialchars(preg_replace('/[^a-zA-Z0-9]/', '', "email_" . $headers_key . "_" . $headers_value)));
		                $tpl_header->set_var("headers_label", "");
		                $tpl_header->set_var("headers_class", "example");
		                $tpl_header->set_var("headers_value", $headers_value);
		                $tpl_header->parse("SezHeader", true);
		            }
		            $tpl_header->set_var("real_name", ffCommon_specialchars(preg_replace('/[^a-zA-Z0-9]/', '', $struct["prefix"] . "_" . $headers_key)));
		            $tpl_header->set_var("group_name", ffTemplate::_get_word_by_code("email_" . ltrim($headers_key, "_")));
		            $tpl_header->set_var("group_class", preg_replace('/[^a-zA-Z0-9\-]/', '', "email-" . ltrim($headers_key, "-")));
		            $tpl_header->parse("SezHeadersGroups", true);
		        }
		        $tpl_header->parse("SezHeaders", false);
		        
		        $tpl_header->set_var("email_template_title", ffTemplate::_get_word_by_code("email_template_explanation_tag")); 
		        $preview_header_tag = $tpl_header->rpparse("main", false);
			}
	    }       

	    
	    
	    
	    if($owner !== NULL) {
	        $tpl->set_var("owner", $owner);
	        $tpl->parse("SezOwner", false);
		} else {
			$tpl->set_var("SezOwner", "");
		}

		if(is_array($arrAddress) && count($arrAddress)) {
			foreach($arrAddress AS $arrAddress_type => $arrAddress_value) {
				if(is_array($arrAddress_value) && count($arrAddress_value)) {
					$count_address = 0;
					foreach($arrAddress_value AS $address_email => $address_name) {
						$tpl->set_var($arrAddress_type . "_email_" . $count_address, $address_email);
						$tpl->set_var($arrAddress_type . "_name_" . $count_address, $address_name);
						$count_address++;
					}
				}
			}
		}
		
		
	    if($fields !== NULL && is_array($fields)) {
	        //$fileds = ksort($fields);
	        //reset($fields);
			$count_group = 0;
			$group_type = array("Table" => true);
			
	        foreach ($fields AS $fields_key => $fields_value) {
            	if(strlen($fields_key)) {
            		$field_label = "_" . $fields_key;
            		$fields_tag = $fields_key . "_";
				}
            	
	            $tpl->set_var("SezFieldLabel", "");
	            $tpl->set_var("SezField", "");

				foreach($group_type AS $group_key => $group_value) {
	                $tpl->set_var("Sez" . $group_key . "FieldLabel", "");
	                $tpl->set_var("Sez" . $group_key . "Field", "");
	                $tpl->set_var("Sez" . $group_key . "Row", "");
				}
	            
	            $tpl_alt->set_var("SezField", "");
	            
				if(isset($fields_value["settings"]) 
					&& isset($fields_value["settings"]["type"]) 
					&& strlen($fields_value["settings"]["type"])) 
				{
					$field_type = $fields_value["settings"]["type"];
				} else {
					$field_type = "";
				}

	            if($field_type) {
	                switch($field_type) {
						case "Table":
							$count_row = 0;
							
							if(is_array($fields_value) && count($fields_value)) {
					            foreach ($fields_value AS $fields_value_key => $fields_value_value) {
									if(strtolower($fields_value_key) == "settings")
										continue;
									
									if(is_array($fields_value_value) && count($fields_value_value)) {
										$tpl->set_var("Sez" . $field_type . "Field", "");
										foreach ($fields_value_value AS $fields_value_value_key => $fields_value_value_value) {
											if(!$count_row) {
												$tpl->set_var("fields_label", ffTemplate::_get_word_by_code($struct["prefix_label"] . ($prefix === null ? $field_label . ($struct["prefix_label"] . $field_label ? "_" : "") : "") . $fields_value_value_key));
												
												$tpl->parse("Sez" . $field_type . "FieldLabel", true);
											}

								            $tpl->set_var("real_name", ffCommon_specialchars(preg_replace('/[^a-zA-Z0-9]/', '', $struct["prefix"] . $field_label . "_" . $fields_value_key . "_" . $fields_value_value_key)));
					                        if($fields_value_value_key == "link") {
					                            if(check_function("transmute_inlink"))
					                                $tpl->set_var("fields_value", transmute_inlink((strpos($fields_value_value_value, "http") === 0 ? "" : "http://" . $_SERVER["HTTP_HOST"] . FF_SITE_PATH) . $fields_value_value_value, $struct["prefix"] . $field_label . "_" . $fields_value_value_key));
					                        } else {
								                $tpl->set_var("fields_value", $fields_value_value_value);
					                        }
					                        
					                        $tpl->set_var($fields_tag . $fields_value_value_key, $fields_value_value_value); //custom vars  
								            $tpl->parse("Sez" . $field_type . "Field", true);

								            $tpl_alt->set_var($fields_tag . $fields_value_value_key, $fields_value_value_value); //custom vars  
											$tpl_alt->set_var("fields_label", ffTemplate::_get_word_by_code($struct["prefix_label"] . ($prefix === null ? $field_label . ($struct["prefix_label"] . $field_label ? "_" : "") : "") . $fields_value_value_key));
											$tpl_alt->set_var("fields_value", $fields_value_value_value);
								            $tpl_alt->parse("SezField", true);
										}
										
										$tpl->parse("SezRow" . $field_type, true); //custom vars
										$tpl->parse("Sez" . $field_type . "Row", true);

										$count_row++;
									} else {
										$tpl->set_var("fields_label", ffTemplate::_get_word_by_code($struct["prefix_label"] . ($prefix === null ? $field_label . ($struct["prefix_label"] . $field_label ? "_" : "") : "") . $fields_value_key));
										$tpl->parse("Sez" . $field_type . "FieldLabel", true);
				                        
				                        $tpl->set_var("real_name", ffCommon_specialchars(preg_replace('/[^a-zA-Z0-9]/', '', $struct["prefix"] . $field_label . "_" . $fields_value_key)));
				                        if($fields_value_key == "link") {
				                            if(check_function("transmute_inlink"))
				                                $tpl->set_var("fields_value", transmute_inlink((strpos($fields_value_value, "http") === 0 ? "" : "http://" . $_SERVER["HTTP_HOST"] . FF_SITE_PATH) . $fields_value_value, $struct["prefix"] . $field_label . "_" . $fields_value_key));
				                        } else {
						                    $tpl->set_var("fields_value", $fields_value_value);
						                }
						                
						                $tpl->set_var($fields_tag . $fields_value_key, $fields_value_value); //custom vars  
				                        $tpl->parse("Sez" . $field_type . "Field", true);
				                        
				                        $tpl_alt->set_var($fields_tag . $fields_value_key, $fields_value_value); //custom vars  
										$tpl_alt->set_var("fields_label", ffTemplate::_get_word_by_code($struct["prefix_label"] . ($prefix === null ? $field_label . ($struct["prefix_label"] . $field_label ? "_" : "") : "") . $fields_value_key));
										$tpl_alt->set_var("fields_value", $fields_value_value);
						                $tpl_alt->parse("SezField", true);
						                
						                $tpl->parse("SezRow" . $field_type, false); //custom vars
						                $tpl->parse("Sez" . $field_type . "Row", false);
									}
								}
				            }
							break;
						default:
					}
				} else {
		            if(is_array($fields_value) && count($fields_value)) {
			            foreach ($fields_value AS $fields_value_key => $fields_value_value) {
			                $tpl->set_var("SezRow" . ucfirst($fields_value_key), "");
			                $tpl_alt->set_var("SezRow" . ucfirst($fields_value_key), "");
							if(is_array($fields_value_value) && count($fields_value_value)) {
								
								$count_row = 0;
								foreach ($fields_value_value AS $fields_value_value_key => $fields_value_value_value) {
									if(strtolower($fields_value_value_key) == "settings")
										continue;

					                $tpl->set_var("real_name", ffCommon_specialchars(preg_replace('/[^a-zA-Z0-9]/', '', $struct["prefix"] . ($prefix ? $field_label . "_" : "") . $fields_value_value_key)));
		                            if($fields_value_value_key == "link") {
		                                $tpl->set_var("fields_label", "");
		                                if(check_function("transmute_inlink"))
		                                    $tpl->set_var("fields_value", transmute_inlink((strpos($fields_value_value_value, "http") === 0 ? "" : "http://" . $_SERVER["HTTP_HOST"] . FF_SITE_PATH) . $fields_value_value_value, $struct["prefix"] . $field_label . "_" . $fields_value_value_key));
		                            } else {
										$tpl->set_var("fields_label", ffTemplate::_get_word_by_code($struct["prefix_label"] . ($prefix === null ? $field_label . ($struct["prefix_label"] . $field_label ? "_" : "") : "") . $fields_value_value_key));
										$tpl->set_var("fields_value", $fields_value_value_value);
		                            }
		                            $tpl->set_var($fields_tag . $fields_value_key . "_" . $fields_value_value_key, $fields_value_value_value); //custom vars  
					                $tpl->parse("SezField", true);

					                $tpl_alt->set_var($fields_tag . $fields_value_key . "_" . $fields_value_value_key, $fields_value_value_value); //custom vars  
									$tpl_alt->set_var("fields_label", ffTemplate::_get_word_by_code($struct["prefix_label"] . ($prefix === null ? $field_label . ($struct["prefix_label"] . $field_label ? "_" : "") : "") . $fields_value_value_key));
									$tpl_alt->set_var("fields_value", $fields_value_value_value);
					                $tpl_alt->parse("SezField", true);
 									
 									//custom vars
 									if(strpos(ffTemplate::_get_word_by_code($struct["prefix"] . "_sep"), $struct["prefix"] . "_sep") === false) {
										if($count_row < count($fields_value_value) - 1) 
											$sep = ffTemplate::_get_word_by_code($struct["prefix"] . "_sep");
						                else {
											$sep = "";
						                }
									}
					                $tpl_alt->set_var("content", $fields_value_value_value . $sep);
					                $tpl_alt->parse("SezRow" . ucfirst($fields_key) . ucfirst($fields_value_key), true);
					                $tpl->set_var("content", $fields_value_value_value . $sep);
						            $tpl->parse("SezRow" . ucfirst($fields_key) . ucfirst($fields_value_key), true); 
						            
						            $count_row++;
								}
							} else {
		                        $tpl->set_var("real_name", ffCommon_specialchars(preg_replace('/[^a-zA-Z0-9]/', '', $struct["prefix"] . $field_label . "_" . $fields_value_key)));
		                        if($fields_value_key == "link") {
		                            $tpl->set_var("fields_label", "");
		                            if(check_function("transmute_inlink"))
		                                $tpl->set_var("fields_value", transmute_inlink((strpos($fields_value_value, "http") === 0 ? "" : "http://" . $_SERVER["HTTP_HOST"] . FF_SITE_PATH) . $fields_value_value, $struct["prefix"] . $field_label . "_" . $fields_value_key));
		                        } else {
									$tpl->set_var("fields_label", ffTemplate::_get_word_by_code($struct["prefix_label"] . ($prefix === null ? $field_label . ($struct["prefix_label"] . $field_label ? "_" : "") : "") . $fields_value_key));
									$tpl->set_var("fields_value", $fields_value_value);
				                }
				                $tpl->set_var($fields_tag . $fields_value_key, $fields_value_value); //custom vars  
		                        $tpl->parse("SezField", true);
		                        
		                        $tpl_alt->set_var($fields_tag . $fields_value_key, $fields_value_value); //custom vars  
								$tpl_alt->set_var("fields_label", ffTemplate::_get_word_by_code($struct["prefix_label"] . ($prefix === null ? $field_label . ($struct["prefix_label"] . $field_label ? "_" : "") : "") . $fields_value_key));
								$tpl_alt->set_var("fields_value", $fields_value_value);
				                $tpl_alt->parse("SezField", true);
							}
			            }
					}
				}

	            $tpl->parse("SezStyle" . $field_type, false);

	            foreach($group_type AS $group_key => $group_value) {
	                if($group_key != $field_type) {
	                	$tpl->set_var("SezStyle" . $group_key, "");
					}
				}
				if(strlen($field_type))
					$tpl->set_var("SezStyle", "");


	            $tpl->set_var("real_name", ffCommon_specialchars(preg_replace('/[^a-zA-Z0-9]/', '', $struct["prefix"] . $field_label)));
	            $tpl->set_var("group_name", ffTemplate::_get_word_by_code($struct["prefix"] . $field_label));
	            $tpl->parse("SezGroups", true);

	            $tpl_alt->set_var("group_name", ffTemplate::_get_word_by_code($struct["prefix"] . $field_label));
	            $tpl_alt->parse("SezGroups", true);
	            
	            $count_group++;
	        }                       
	        $tpl->parse("SezFields", false);
	        
	        $tpl_alt->parse("SezFields", false);
	    } else {
	        $tpl->set_var("SezFields", "");
	        
	        $tpl_alt->set_var("SezFields", "");
	    }

		if($body !== null) {
		    if(is_array($body)) {
	        	if(count($body)) {
	        		$tpl->set_var("pre_body", $body["pre"]);
	        		$tpl->set_var("post_body", $body["post"]);
				}
			} elseif(strlen($body)) {
	        	$tpl->set_var("pre_body", $body);
			}
			
		} 
	    
	    $tpl->set_var("real_name", ffCommon_specialchars(preg_replace('/[^a-zA-Z0-9]/', '', $struct["prefix"])));
	    
	    $tpl_content = $tpl->rpparse("main", false);
	    
	    $tpl_alt_content = $tpl_alt->rpparse("main", false);

	    
	    $mail->IsHTML();
	    $mail->Body    = $tpl_content;
	    //$mail->AltBody = $tpl_alt_content;
	    
	    if($preview) {
	        preg_match("/<body[^>]*>(.*?)<\/body>/is", $tpl_content, $body_only);
        	//preg_match('/<body[.*]>(.*)<\/body>/s', $tpl_content, $body_only);

	        return   '<div class="mail-header">' . $preview_header_mail . '</div>' . '<div class="mail-body-wrapper">' . '<div class="mail-body-container">' . $body_only[1] . '</div>' . $preview_header_tag . '</div>';
	    } else {
		    if(is_array($attach) && count($attach)) {
				foreach($attach AS $attach_key => $attach_value) {
					$mail->AddAttachment(FF_DISK_PATH . FF_UPDIR . $attach_value, $attach_key);
				}
		    }

	        $rc = $mail->Send();

	        if($rc)
	            return false;    
	        else
	            return $mail->ErrorInfo;
	    }
	} else {
		return $strError;
	}
   
}

function email_system($email, $theme = null, $tpl_email_path = null) {
    $cm = cm::getInstance();
    
    if(!strlen($email))
    	return false;

    $dbtemp = ffDB_Sql::factory();
    $fields_example = null;
	$debug = EMAIL_DEBUG;
	
	if(is_numeric($email)) {
		$sSQL_where = " ID = " . $dbtemp->toSql($email, "Number");
	} else {
		$sSQL_where = " name = " . $dbtemp->toSql($email);
	}
	
    $sSql = "SELECT * FROM `email` WHERE " . $sSQL_where . " AND owner = 0";
    $dbtemp->query($sSql);
    if($dbtemp->nextRecord()) {
        $ID_mail = $dbtemp->getField("ID")->getValue();
        $email_name = $dbtemp->getField("name", "Text", true);
	    $enable_notify = $dbtemp->getField("enable_notify", "Number", true);
	    
	    $default_from["name"] = $dbtemp->getField("from_name")->getValue();
	    $default_from["mail"] = $dbtemp->getField("from_email")->getValue();

	    $tpl_email_path = $dbtemp->getField("tpl_email_path")->getValue();
		if(strlen($dbtemp->getField("theme")->getValue())) {
			$theme = $dbtemp->getField("theme")->getValue();	
		} else {
			$theme = FRONTEND_THEME;
		}
		
	    if(strlen($dbtemp->getField("fields_example", "Text", true))) {
        	$fields_example = json_decode($dbtemp->getField("fields_example", "Text", true), true);
		}  

		if(strlen($dbtemp->getField("email_debug", "Text", true))) 
			$debug = $dbtemp->getField("email_debug", "Text", true);
		
		 $sSQL = "SELECT 
			        email_address.name
			        , email_address.email
			        , email_rel_address.type
			    FROM
			        email_rel_address
			        INNER JOIN email ON email.ID = email_rel_address.ID_email
			        INNER JOIN email_address ON email_address.ID = email_rel_address.ID_address
			    WHERE
			        email.ID = " . $dbtemp->toSql($ID_mail, "Number") . "
			    ORDER BY email_rel_address.type, email_address.name, email_address.email";
		$dbtemp->query($sSQL);
		if($dbtemp->nextRecord()) {
			$i = 0;
			do {
			    $i++;
			    ${"default_" . $dbtemp->getField("type")->getValue()}[$i]["name"] = $dbtemp->getField("name")->getValue();
			    ${"default_" . $dbtemp->getField("type")->getValue()}[$i]["mail"] = $dbtemp->getField("email")->getValue();
			} while($dbtemp->nextRecord());
		}
    } else {
    	if(is_numeric($email)) {
			return false;
    	} else {
    		$email_name = $email;
    		$enable_notify = false;
   		
    		if(!(strlen($tpl_email_path) && is_dir(FF_DISK_PATH . $tpl_email_path))) {
		        $form_path = "/email/" . $email_name;
		        $res = true; 

			    if(!(strlen($theme) && is_dir(FF_DISK_PATH . FF_THEME_DIR . "/" . $theme))) {
    				if(is_dir(FF_DISK_PATH . FF_THEME_DIR . "/" . FRONTEND_THEME)) {
						$theme = FRONTEND_THEME;
    				} else {
						$theme = $cm->oPage->theme;
    				}
			    }
		        
		        if(!is_dir(FF_DISK_PATH . FF_THEME_DIR . "/" . $theme . "/" . GALLERY_TPL_PATH . "/email")) {
		            $res = @mkdir(FF_DISK_PATH . FF_THEME_DIR . "/" . $theme . "/" . GALLERY_TPL_PATH . "/email");
		            if($res) {
		                $res = @chmod(FF_DISK_PATH . FF_THEME_DIR . "/" . $theme . "/" . GALLERY_TPL_PATH . "/email", 0777);
		            }
		        }            

		        if($res && !is_dir(FF_DISK_PATH . FF_THEME_DIR . "/" . $theme . "/" . GALLERY_TPL_PATH . $form_path)) {
		            $res = @mkdir(FF_DISK_PATH . FF_THEME_DIR . "/" . $theme . "/" . GALLERY_TPL_PATH . $form_path);
		            if($res) {
		                $res = @chmod(FF_DISK_PATH . FF_THEME_DIR . "/" . $theme . "/" . GALLERY_TPL_PATH . $form_path, 0777);
		            }
		        }

		        if($res) {
		            if(@copy(FF_DISK_PATH . FF_THEME_DIR . "/" . THEME_INSET . "/contents/mail/email.tpl", FF_DISK_PATH . FF_THEME_DIR . "/" . $theme . "/" . GALLERY_TPL_PATH . $form_path . "/email.tpl")
            			&& @copy(FF_DISK_PATH . FF_THEME_DIR . "/" . THEME_INSET . "/contents/mail/email.txt", FF_DISK_PATH . FF_THEME_DIR . "/" . $theme . "/" . GALLERY_TPL_PATH . $form_path . "/email.txt")
		            ) {
		                $tpl_email_path = $form_path . "/email.tpl";

		                $res = @mkdir(FF_DISK_PATH . FF_THEME_DIR . "/" . $theme . "/" . GALLERY_TPL_PATH . $form_path . "/images");
		                if($res) {
		                    $res = @chmod(FF_DISK_PATH . FF_THEME_DIR . "/" . $theme . "/" . GALLERY_TPL_PATH . $form_path . "/images", 0777);
		                }
		                if($res) {
		                    foreach (glob(FF_DISK_PATH . FF_THEME_DIR . "/" . THEME_INSET . "/contents/mail/images/*") as $file) { 
		                        $res = @copy($file, FF_DISK_PATH . FF_THEME_DIR . "/" . $theme . "/" . GALLERY_TPL_PATH . $form_path . "/images/" . basename($file));
		                        if($res) {
		                            $res = @chmod(FF_DISK_PATH . FF_THEME_DIR . "/" . $theme . "/" . GALLERY_TPL_PATH . $form_path . "/images/" . basename($file), 0777);
		                        }
		                    }
		                }
		            }
		        } 
			}

			$default_from["name"] = A_FROM_NAME;
		    $default_from["mail"] = A_FROM_EMAIL;

	        $sSql = "INSERT 
	                    INTO `email` 
	                    (
	                        ID
	                        , name
	                        , theme
	                        , tpl_email_path
	                        , from_name
	                        , from_email
	                        , enable_notify
	                        , owner
	                    ) 
	                    VALUES 
	                    (
	                        ''
	                        , " . $dbtemp->toSql($email_name, "Text") . "
	                        , " . $dbtemp->toSql($theme, "Text") . "
	                        , " . $dbtemp->toSql($tpl_email_path, "Text") . "
	                        , " . $dbtemp->toSql($default_from["name"], "Text") . "
	                        , " . $dbtemp->toSql($default_from["mail"], "Text") . "
	                        , " . $dbtemp->toSql($enable_notify, "Number") . "
	                        , " . $dbtemp->toSql(0, "Number") . "
	                    )";
	        $dbtemp->execute($sSql);
	        $ID_mail = $dbtemp->getInsertID(true);

			if(!verifyMailbox(CC_FROM_EMAIL)) {
		        $sSql = "SELECT * FROM `email_address` WHERE email = " . $dbtemp->toSql(CC_FROM_EMAIL, "Text");
		        $dbtemp->query($sSql);
		        if($dbtemp->nextRecord()) {
		            $ID_CC = $dbtemp->getField("ID")->getValue();
		            $default_cc = array(
	            		"name" => $dbtemp->getField("name", "Text", true)
	            		, "mail" => $dbtemp->getField("email", "Text", true)
		            );
		        } else {
		            $sSql = "INSERT 
		                        INTO `email_address` 
		                        (
		                            ID
		                            , name
		                            , email
		                            , uid
		                        ) 
		                        VALUES 
		                        (
		                            ''
		                            , " . $dbtemp->toSql(CC_FROM_NAME, "Text") . "
		                            , " . $dbtemp->toSql(CC_FROM_EMAIL, "Text") . "
		                            , " . $dbtemp->toSql(0, "Number") . "
		                        )";
		            $dbtemp->execute($sSql);
		            $ID_CC = $dbtemp->getInsertID(true);
					$default_cc = array(
	            		"name" => CC_FROM_NAME
	            		, "mail" => CC_FROM_EMAIL
		            );
		        }
		            
        		if($ID_CC) {
			        $sSql = "SELECT * 
			                FROM `email_rel_address` 
			                WHERE 
			                    ID_email = " . $dbtemp->toSql($ID_mail, "Number") . "
			                    AND ID_address = " . $dbtemp->toSql($ID_CC, "Number") . "
			                    AND type = " . $dbtemp->toSql("cc", "Text");
			        $dbtemp->query($sSql);
			        if(!$dbtemp->nextRecord()) {
			            $sSql = "INSERT 
			                        INTO `email_rel_address` 
			                        (
			                            ID
			                            , ID_email
			                            , ID_address
			                            , type
			                        ) 
			                        VALUES 
			                        (
			                            ''
			                            , " . $dbtemp->toSql($ID_mail, "Number") . "
			                            , " . $dbtemp->toSql($ID_CC, "Number") . "
			                            , " . $dbtemp->toSql("cc", "Text") . "
			                        )";
			            $dbtemp->execute($sSql);
			        }
				}
			}   
	        if(!verifyMailbox(BCC_FROM_EMAIL)) {
		        $sSql = "SELECT * FROM `email_address` WHERE email = " . $dbtemp->toSql(BCC_FROM_EMAIL, "Text");
		        $dbtemp->query($sSql);
		        if($dbtemp->nextRecord()) {
		            $ID_BCC = $dbtemp->getField("ID")->getValue();
		            $default_bcc = array(
	            		"name" => $dbtemp->getField("name", "Text", true)
	            		, "mail" => $dbtemp->getField("email", "Text", true)
		            );
		        } else {
		            $sSql = "INSERT 
		                        INTO `email_address` 
		                        (
		                            ID
		                            , name
		                            , email
		                            , uid
		                        ) 
		                        VALUES 
		                        (
		                            ''
		                            , " . $dbtemp->toSql(BCC_FROM_NAME, "Text") . "
		                            , " . $dbtemp->toSql(BCC_FROM_EMAIL, "Text") . "
		                            , " . $dbtemp->toSql(0, "Number") . "
		                        )";
		            $dbtemp->execute($sSql);
		            $ID_BCC = $dbtemp->getInsertID(true);
					$default_bcc = array(
	            		"name" => BCC_FROM_NAME
	            		, "mail" => BCC_FROM_EMAIL
		            );
		        }
		            
        		if($ID_BCC) {
			        $sSql = "SELECT * 
			                FROM `email_rel_address` 
			                WHERE 
			                    ID_email = " . $dbtemp->toSql($ID_mail, "Number") . "
			                    AND ID_address = " . $dbtemp->toSql($ID_BCC, "Number") . "
			                    AND type = " . $dbtemp->toSql("bcc", "Text");
			        $dbtemp->query($sSql);
			        if(!$dbtemp->nextRecord()) {
			            $sSql = "INSERT 
			                        INTO `email_rel_address` 
			                        (
			                            ID
			                            , ID_email
			                            , ID_address
			                            , type
			                        ) 
			                        VALUES 
			                        (
			                            ''
			                            , " . $dbtemp->toSql($ID_mail, "Number") . "
			                            , " . $dbtemp->toSql($ID_BCC, "Number") . "
			                            , " . $dbtemp->toSql("bcc", "Text") . "
			                        )";
			            $dbtemp->execute($sSql);
			        }
				}
	        }
		}
	}

	return array(
		"db" => false
		, "ID" => $ID_mail
		, "theme" => FRONTEND_THEME
		, "default" => array(
			"theme" => THEME_INSET
			, "path" => "/" . GALLERY_TPL_PATH
		)
		, "domain" => DOMAIN_NAME
		, "lang" => LANGUAGE_INSET
		, "mail" => array(
			"smtp" => array(
				"host" => A_SMTP_HOST
				, "auth" => SMTP_AUTH
				, "username" => A_SMTP_USER
				, "password" => A_SMTP_PASSWORD
			)
			, "subject" => ffTemplate::_get_word_by_code("email_" . preg_replace('/[^a-zA-Z0-9]/', '', $email_name) . "_subject")
			, "name" => $email_name
			, "tpl_path" => $tpl_email_path
			, "notify" => $enable_notify
			, "from" => $default_from
			, "cc" => $default_cc
			, "bcc" => $default_bcc
		)
		, "example" => $fields_example
		, "debug" => $debug
	);
}


function verifyMailbox($email, $mailAddress = NULL) {
    $before = microtime();
    $err = false;
    if (!preg_match('/([^\@]+)\@(.+)$/', $email, $matches)) {
        return "wrong email";
    }
    $user = $matches[1]; 
    $domain = $matches[2];
    
	if($mailAddress === null) {
		$mailAddress = "noreply@" . $domain;
	}
    
    
    if(!function_exists('checkdnsrr')) return $err;
    if(!function_exists('getmxrr')) return $err;
    // Get MX Records to find smtp servers handling this domain
    if(getmxrr($domain, $mxhosts, $mxweight)) {
    	$mxs = array();
        for($i=0;$i<count($mxhosts);$i++){
            $mxs[$mxhosts[$i]] = $mxweight[$i];
        }
        asort($mxs);
        $mailers = array_keys($mxs);
    }elseif(checkdnsrr($domain, 'A')) {
        $mailers[0] = gethostbyname($domain);
    }else {
        return "domain not found";
    }
    // Try to send to each mailserver
    $total = count($mailers);
    $ok = 0;
    for($n=0; $n < $total; $n++) {
        $timeout = 5;
        $errno = 0; $errstr = 0;
        if(!($sock = @fsockopen($mailers[$n], 25, $errno , $errstr, $timeout))) {
            continue;
        }
        $response = fgets($sock);
        stream_set_timeout($sock, 5);
        $meta = stream_get_meta_data($sock);
        $cmds = array(
            "HELO localhost",
            "MAIL FROM: <$mailAddress>",
            "RCPT TO: <$email>",
            "QUIT",
        );
        if(!$meta['timed_out'] && !preg_match('/^2\d\d[ -]/', $response)) {
            break;
        }
        $success_ok = 1;
        foreach($cmds as $cmd) {
            fputs($sock, "$cmd\r\n");
            $response = fgets($sock, 4096);

            if(!$meta['timed_out'] && preg_match('/^5\d\d[ -]/', $response)) {
                $success_ok = 0;
                break;
            }
        }
        fclose($sock);
        if($success_ok) {
        	$ip_addr = @gethostbyname($mailers[$n]);

        	if(@gethostbyaddr($ip_addr)) {
        		$not_spam  = false;
			} else { 
				$not_spam = "PTR not set";
			}
            $ok = 1;
            break;
        }
    }
    $after = microtime();
    // Fail on error
    if(!$ok) return $response;
    // Return a positive value on success
    //return $after-$before;
    
    
    return $not_spam;
}

function check_email($email_value, $email_name, $mailbox_error = "") 
{
    $db = ffDB_Sql::factory();
    
    $sSQL = "SELECT * FROM email_address WHERE email = " . $db->toSql($email_value, "Text");
    $db->query($sSQL);
    if(!$db->numRows()) {
        $sSQL = "INSERT INTO email_address 
                    (
                        name
                        , email
                        , error
                    ) 
                    VALUES 
                    ( 
                        " . $db->toSql($email_name, "Text") . "
                        , " . $db->toSql($email_value, "Text") . "
                        , " . $db->toSql($mailbox_error, "Text") . "
                    )";
        $db->execute($sSQL);
    } elseif($db->nextRecord() && strlen($mailbox_error)) {
    	$ID_email = $db->getField("ID", "Number");
    	
        $sSQL = "UPDATE email_address SET
        			error = " . $db->toSql($mailbox_error, "Text") . "
                    WHERE ID = " . $db->toSql($ID_email, "Number");
        $db->execute($sSQL);
	}
}
?>