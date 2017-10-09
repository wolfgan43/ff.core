<?php
/**
 *  PARAMETERS (trough ffGlobals::getInstance("mod_recruitment")
 * 
 *  ->slug_city
 *  ->subpath
 *  ->base_path
 */

//mod_security_check_session();

$globals = ffGlobals::getInstance("mod_recruitment");

if (!strlen($globals->slug_city) && !strlen($globals->subpath))
{
	if ($globals->do_404)
		$cm->responseCode(404);
	else
		return;
}

$db = ffDB_Sql::factory();
$active = 1; // DA CONTROLLARE.. DECIDERE DOVE METTERE QUESTO FLAG
$genere = array("F" => "Donna"
	, "M" => "Uomo");
//$UserNID = get_session("UserNID"); // UNUSED

if (is_file(FF_DISK_PATH . "/themes/" . $cm->oPage->getTheme() . "/css/recruitment.css"))
	$cm->oPage->tplAddCss("recruitment-css", "recruitment.css", "/themes/comune.info/css");

$arrPathInfo = array();

if (strlen(global_settings("MOD_CV_CLASS_BASE_PATH"))) {
	$path_info = $cm->path_info . $cm->real_path_info;
	$arrPathInfo = explode(global_settings("MOD_CV_CLASS_BASE_PATH"), $path_info);
	$smart_url = basename($arrPathInfo[1]);
}

if (strlen($globals->subpath)) {
	$arrPathInfo = array();
	//if (strlen(global_settings("MOD_ADVERTISEMENT_CLASS_BASE_PATH"))) {
		$arrPathInfo = mb_split("/", trim($globals->subpath, "/"));
		if (count($arrPathInfo) > 1)
		{
			if ($globals->do_404)
				$cm->responseCode(404);
			else
				return;
		}
		
		$element_url = end($arrPathInfo);
	//}

	$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv.*
				FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv
				WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv.smart_url = " . $db->toSql($element_url);
	$db->query($sSQL);
	if ($db->nextRecord()) {
		$experience = false;
		$user_other_information = false;
		$cv_found = true;
		$ID_cv = $db->getField("ID", "Number", true);

		$ID_user = $db->getField("ID_user", "Number", true);

		$user = getAllUserInfo($ID_user);
		//print_r($user);
		$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/recruitment/applets/cv/detail.html", $cm->oPage->theme, false);
		if ($filename === null)
			$filename = cm_moduleCascadeFindTemplate(CM_MODULES_ROOT . "/recruitment/themes", "/applets/cv/detail.html", $cm->oPage->theme);

		$tpl = ffTemplate::factory(ffCommon_dirname($filename));
		$tpl->load_file("detail.html", "main");

		if ($active) {
			$tpl->set_var("hide", "");
		} else {
			$tpl->set_var("hide", "blocked");
		}

		$tpl->set_var("theme", $cm->oPage->theme);
		$tpl->set_var("site_path", $cm->oPage->site_path);

		$tpl->set_var("subcategory_string", $db->getField("subcategory_string", "Text", true));

		$tpl->set_var("name", $user["firstname"] . " " . $user["lastname"]);

		if (strlen($user["address"])) {
			if ($active) {
				$tpl->set_var("address", $user["address"]);
			} else {
				$tpl->set_var("address", ffTemplate::_get_word_by_code("hidden_field"));
			}
			$tpl->parse("SezUserAddress", false);
		} else {
			$tpl->set_var("SezUserAddress", "");
		}

		$tpl->set_var("city", $user["city"]);

		if ($active) {
			$tpl->set_var("e-mail", $user["email"]);
		} else {
			$tpl->set_var("e-mail", ffTemplate::_get_word_by_code("hidden_field"));
		}

		if (strlen($user["tel"])) {
			if ($active) {
				$tpl->set_var("telephone", $user["tel"]);
			} else {
				$tpl->set_var("telephone", ffTemplate::_get_word_by_code("hidden_field"));
			}
			$tpl->parse("SezUserTelephone", false);
		} else {
			$tpl->set_var("SezUserTelephone", "");
		}

		if (strlen($user["data_nascita"])) { //DA GESTIRE
			if ($active) {
				$tpl->set_var("birthday", $user["data_nascita"]);
			} else {
				$tpl->set_var("birthday", ffTemplate::_get_word_by_code("hidden_field"));
			}
			$tpl->parse("SezUserBirthday", false);
		} else {
			$tpl->set_var("SezUserBirthday", "");
		}

		if (strlen($user["sesso"])) { //DA GESTIRE
			if ($active) {
				$tpl->set_var("sesso", $genere[$user["sesso"]]);
			} else {
				$tpl->set_var("sesso", ffTemplate::_get_word_by_code("hidden_field"));
			}
			$tpl->parse("SezUserSesso", false);
		} else {
			$tpl->set_var("SezUserSesso", "");
		}

		if (strlen($db->getField("ambicious", "Text", true))) {
			$tpl->set_var("ambicious", $db->getField("ambizione", "Text", true));
			$tpl->parse("SezUserAmbicious", false);
		} else {
			$tpl->set_var("SezUserAmbicious", "");
		}

		if (strlen($db->getField("lingua_madre", "Text", true))) {
			$tpl->set_var("preferred_lang", $db->getField("lingua_madre", "Text", true));
			$tpl->parse("SezUserPreferredLang", false);
		} else {
			$tpl->set_var("SezUserPreferredLang", "");
		}

		if (strlen($db->getField("competenze_comunicative", "Text", true))) {
			$user_other_information = true;
			$tpl->set_var("competenze_comunicative", $db->getField("competenze_comunicative", "Text", true));
			$tpl->parse("SezCompetenzeComunicative", false);
		} else {
			$tpl->set_var("SezCompetenzeComunicative", "");
		}
		if (strlen($db->getField("competenze_organizzative", "Text", true))) {
			$user_other_information = true;
			$tpl->set_var("competenze_organizzative", $db->getField("competenze_organizzative", "Text", true));
			$tpl->parse("SezCompetenzeOrganizzative", false);
		} else {
			$tpl->set_var("SezCompetenzeOrganizzative", "");
		}
		if (strlen($db->getField("competenze_professionali", "Text", true))) {
			$user_other_information = true;
			$tpl->set_var("competenze_professionali", $db->getField("competenze_professionali", "Text", true));
			$tpl->parse("SezCompetenzeProfessionali", false);
		} else {
			$tpl->set_var("SezCompetenzeProfessionali", "");
		}
		if (strlen($db->getField("competenze_informatiche", "Text", true))) {
			$user_other_information = true;
			$tpl->set_var("competenze_informatiche", $db->getField("competenze_informatiche", "Text", true));
			$tpl->parse("SezCompetenzeInformatiche", false);
		} else {
			$tpl->set_var("SezCompetenzeInformatiche", "");
		}
		if (strlen($db->getField("ulteriori_informazioni", "Text", true))) {
			$user_other_information = true;
			$tpl->set_var("ulteriori_informazioni", $db->getField("ulteriori_informazioni", "Text", true));
			$tpl->parse("SezUlterioriInformazioni", false);
		} else {
			$tpl->set_var("SezUlterioriInformazioni", "");
		}

		if ($user_other_information)
			$tpl->parse("SezUserOtherInformation", false);
		else
			$tpl->set_var("SezUserOtherInformation", "");


		$tpl->set_var("published_at", $db->getField("time_inserted", "Timestamp")->getValue("DateTime", FF_LOCALE));

		$tpl->set_var("recruitment_detail_back", $arrPathInfo[0] . global_settings("MOD_CV_CLASS_BASE_PATH"));

		if ($ID_cv > 0) {
			$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv_job.*
						FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_job
						WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_job.ID_cv = " . $db->toSql($ID_cv);
			$db->query($sSQL);
			if ($db->nextRecord()) {
				$experience = true;
				do {
					$azienda_reference = false;
					$tpl->set_var("work_end", $db->getField("data_fine", "Date", true));
					$tpl->set_var("work_begin", $db->getField("data_inizio", "Date", true));

					if ($active) {
						$tpl->set_var("azienda_name", $db->getField("nome_azienda", "Text", true));
					} else {
						$tpl->set_var("azienda_name", ffTemplate::_get_word_by_code("hidden_field"));
					}

					if (strlen($db->getField("incarico", "Text", true))) {
						$tpl->set_var("azienda_role", $db->getField("incarico", "Text", true));
						$tpl->parse("SezJobExperienceItemAziendaRole", false);
					} else {
						$tpl->set_var("SezJobExperienceItemAziendaRole", "");
					}

					if (strlen($db->getField("descrizione", "Text", true))) {
						$tpl->set_var("azienda_role_description", $db->getField("descrizione", "Text", true));
						$tpl->parse("SezJobExperienceItemAziendaRoleDescription", false);
					} else {
						$tpl->set_var("SezJobExperienceItemAziendaRoleDescription", "");
					}

					if (strlen($db->getField("datore_lavoro", "Text", true))) {
						$azienda_reference = true;
						if ($active) {
							$tpl->set_var("azienda_reference", $db->getField("datore_lavoro", "Text", true));
						} else {
							$tpl->set_var("azienda_reference", ffTemplate::_get_word_by_code("hidden_field"));
						}
						$tpl->parse("SezAziendaInfoReference", false);
					} else {
						$tpl->set_var("SezAziendaInfoReference", "");
					}

					if (strlen($db->getField("telefono", "Text", true))) {
						$azienda_reference = true;
						if ($active) {
							$tpl->set_var("azienda_telephone", $db->getField("telefono", "Text", true));
						} else {
							$tpl->set_var("azienda_telephone", ffTemplate::_get_word_by_code("hidden_field"));
						}
						$tpl->parse("SezAziendaInfoTelephone", false);
					} else {
						$tpl->set_var("SezAziendaInfoTelephone", "");
					}

					if (strlen($db->getField("email", "Text", true))) {
						$azienda_reference = true;
						if ($active) {
							$tpl->set_var("azienda_email", $db->getField("email", "Text", true));
						} else {
							$tpl->set_var("azienda_email", ffTemplate::_get_word_by_code("hidden_field"));
						}
						$tpl->parse("SezAziendaInfoEmail", false);
					} else {
						$tpl->set_var("SezAziendaInfoEmail", "");
					}

					if (strlen($db->getField("email", "Text", true))) {
						$azienda_reference = true;
						if ($active) {
							$tpl->set_var("azienda_city", $db->getField("citta", "Text", true));
						} else {
							$tpl->set_var("azienda_city", ffTemplate::_get_word_by_code("hidden_field"));
						}
						$tpl->parse("SezAziendaInfoCity", false);
					} else {
						$tpl->set_var("SezAziendaInfoCity", "");
					}

					if ($azienda_reference)
						$tpl->parse("SezAziendaInfo", false);
					else
						$tpl->set_var("SezAziendaInfo", "");

					$tpl->parse("SezJobExperienceItem", true);
				} while ($db->nextRecord());
				$tpl->parse("SezJobExperience", false);
			} else {
				$tpl->set_var("SezJobExperience", "");
			}

			$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv_formation.*
						FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_formation
						WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_formation.ID_cv = " . $db->toSql($ID_cv);
			$db->query($sSQL);
			if ($db->nextRecord()) {
				$experience = true;
				do {
					$school_other_information = false;
					$tpl->set_var("school_end", $db->getField("data_fine", "Date", true));
					$tpl->set_var("school_begin", $db->getField("data_inizio", "Date", true));

					if ($active) {
						$tpl->set_var("school_name", $db->getField("nome_scuola", "Text", true));
					} else {
						$tpl->set_var("school_name", ffTemplate::_get_word_by_code("hidden_field"));
					}

					if (strlen($db->getField("esito", "Text", true))) {
						$tpl->set_var("school_esito", $db->getField("esito", "Text", true));
						$tpl->parse("SezSchoolExperienceItemEsito", false);
					} else {
						$tpl->set_var("SezSchoolExperienceItemEsito", "");
					}

					if (strlen($db->getField("descrizione", "Text", true))) {
						$tpl->set_var("school_description", $db->getField("descrizione", "Text", true));
						$tpl->parse("SezSchoolExperienceItemDescription", false);
					} else {
						$tpl->set_var("SezSchoolExperienceItemDescription", "");
					}

					if (strlen($db->getField("telefono", "Text", true))) {
						$school_other_information = true;
						if ($active) {
							$tpl->set_var("school_telephone", $db->getField("telefono", "Text", true));
						} else {
							$tpl->set_var("school_telephone", ffTemplate::_get_word_by_code("hidden_field"));
						}
						$tpl->parse("SezSchoolExperienceItemTelephone", false);
					} else {
						$tpl->set_var("SezSchoolExperienceItemTelephone", "");
					}

					if (strlen($db->getField("email", "Text", true))) {
						$school_other_information = true;
						if ($active) {
							$tpl->set_var("school_email", $db->getField("email", "Text", true));
						} else {
							$tpl->set_var("school_email", ffTemplate::_get_word_by_code("hidden_field"));
						}
						$tpl->parse("SezSchoolExperienceItemEmail", false);
					} else {
						$tpl->set_var("SezSchoolExperienceItemEmail", "");
					}

					if ($school_other_information)
						$tpl->parse("SezSchoolExperienceItemInfoAggiuntive", false);
					else
						$tpl->set_var("SezSchoolExperienceItemInfoAggiuntive", "");

					$tpl->parse("SezSchoolExperienceItem", true);
				} while ($db->nextRecord());
				$tpl->parse("SezSchoolExperience", false);
			} else {
				$tpl->set_var("SezSchoolExperience", "");
			}

			$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv_lang.*
						FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_lang
						WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_lang.ID_cv = " . $db->toSql($ID_cv);
			$db->query($sSQL);
			if ($db->nextRecord()) {
				$experience = true;
				do {
					$tpl->set_var("lang_name", $db->getField("lang_name", "Text", true));
					$tpl->set_var("lang_reading", $db->getField("reading", "Text", true));

					$tpl->set_var("lang_listening", $db->getField("listening", "Text", true));
					$tpl->set_var("lang_speaking", $db->getField("speaking", "Text", true));

					$tpl->set_var("lang_writing", $db->getField("writing", "Text", true));

					$tpl->set_var("lang_certification", $db->getField("certification", "Text", true));

					$tpl->parse("SezLangExperienceItem", true);
				} while ($db->nextRecord());
				$tpl->parse("SezLangExperience", false);
			} else {
				$tpl->set_var("SezLangExperience", "");
			}
		}
		if ($experience) {
			$tpl->parse("SezUserExperience", false);
		} else {
			$tpl->set_var("SezUserExperience", "");
		}
		
		$out_buffer = $tpl->rpparse("main", false);
	}
	else
	{
		if ($globals->do_404)
			$cm->responseCode(404);
		else
			return;
	}
} else {
	$sSQL = "SELECT 
					" . CM_TABLE_PREFIX . "mod_recruitment_cv.*
				FROM 
					" . CM_TABLE_PREFIX . "mod_recruitment_cv
				WHERE 
					" . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_user IN (
						SELECT DISTINCT " . CM_TABLE_PREFIX . "mod_security_users_fields.ID_users
							FROM " . CM_TABLE_PREFIX . "mod_security_users_fields
							WHERE " . CM_TABLE_PREFIX . "mod_security_users_fields.field = " . $db->toSql("city") . "
								AND " . CM_TABLE_PREFIX . "mod_security_users_fields.value = " . $db->toSql($globals->slug_city) . "
					)
		";
	$db->query($sSQL);
	if ($db->nextRecord()) {
		$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/recruitment/applets/cv/index.html", $cm->oPage->theme, false);
		if ($filename === null)
			$filename = cm_moduleCascadeFindTemplate(CM_MODULES_ROOT . "/recruitment/themes", "/applets/cv/index.html", $cm->oPage->theme);

		$tpl = ffTemplate::factory(ffCommon_dirname($filename));
		$tpl->load_file("index.html", "main");

		$tpl->set_var("theme", $cm->oPage->theme);
		$tpl->set_var("site_path", $cm->oPage->site_path);

		do {
			$ID_user = $db->getField("ID_user", "Number", true);
			$subcategory_list = $db->getField("subcategory_string", "Text", true);

			if (!isset($user[$ID_user]))
				$user[$ID_user] = getAllUserInfo($ID_user);
			$firstname = $user[$ID_user]["firstname"];
			$lastname = $user[$ID_user]["lastname"];

			if ($active) {
				$tpl->set_var("hide", "");
			} else {
				$tpl->set_var("hide", "blocked");
			}
			if (strlen($db->getField("smart_url", "Text", true))) {
				$smart_url = $db->getField("smart_url", "Text", true);
			} else {
				$smart_url = ffCommon_url_rewrite("curriculum-vitae" . " " . $firstname . " " . $lastname . $db->getField("ID", "Number", true));
			}
			if ($active)
				$tpl->set_var("title", $firstname . " " . $lastname . " => " . $subcategory_list);
			else
				$tpl->set_var("title", ffTemplate::_get_word_by_code("hidden_field"));


			if (/*global_settings("MOD_CV_CLASS_BASE_PATH") && */$smart_url) {
				$tpl->set_var("smart_url", $globals->base_path . "/" . $smart_url . "?" . $cm->oPage->get_globals() . "ret_url=" . rawurlencode($_SERVER["REQUEST_URI"]));
				$tpl->parse("SectCvTitleUrl", false);
				$tpl->set_var("SectCvTitleNoUrl", "");
			} else {
				$tpl->set_var("SectCvTitleUrl", "");
				$tpl->parse("SectCvTitleNoUrl", false);
			}

			$tpl->set_var("published_at", $db->getField("time_inserted", "Timestamp")->getValue("DateTime", FF_LOCALE));

			$tpl->parse("SectCv", true);
		} while ($db->nextRecord());
		
		$out_buffer = $tpl->rpparse("main", false);
	} else {
		if ($globals->do_404)
			$cm->responseCode(404);
		else
			return;
	}
}
