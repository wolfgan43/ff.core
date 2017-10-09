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
//$UserNID = get_session("UserNID"); //UNUSED
$UserID = get_session("UserID");

if(is_file(FF_DISK_PATH . "/themes/" . $cm->oPage->getTheme() . "/css/recruitment.css"))
	$cm->oPage->tplAddCss("recruitment-css", "recruitment.css", "/themes/comune.info/css");

if (strlen($globals->subpath)) {
	$arrPathInfo = array();
	//if (strlen(global_settings("MOD_ADVERTISEMENT_CLASS_BASE_PATH"))) {
		$arrPathInfo = mb_split("/", trim($globals->subpath, "/"));
		$element_url = end($arrPathInfo);
	//}

	$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.*
						, " . CM_TABLE_PREFIX . "mod_recruitment_category.name AS category_name
						, " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.name AS subcategory_name
						FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
							INNER JOIN " . CM_TABLE_PREFIX . "mod_recruitment_category ON " . CM_TABLE_PREFIX . "mod_recruitment_category.ID = " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID_category
							LEFT JOIN " . CM_TABLE_PREFIX . "mod_recruitment_subcategory ON " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID = " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID_subcategory
							WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.smart_url = " . $db->toSql($element_url);
	$db->query($sSQL);
	if ($db->nextRecord()) {
		$other_information = false;

		$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/recruitment/applets/job-advertisement/detail.html", $cm->oPage->theme, false);
		if ($filename === null)
			$filename = cm_moduleCascadeFindTemplate(CM_MODULES_ROOT . "/recruitment/themes", "/applets/job-advertisement/detail.html", $cm->oPage->theme);

		$tpl = ffTemplate::factory(ffCommon_dirname($filename));
		$tpl->load_file("detail.html", "main");

		if ($active) {
			$tpl->set_var("hide", "");
		} else {
			$tpl->set_var("hide", "blocked");
		}

		$cm->oPage->widgetLoad("dialog");
		$cm->oPage->widgets["dialog"]->process(
				"SubmitCv"
				, array(
			"tpl_id" => null
			//"name" => "myTitle"
			, "url" => FF_SITE_PATH . $arrPathInfo[0] . "/" . $cm->router->named_rules["recruitment_submit_cv"]->reverse . "/" . $smart_url . "?insertcv&ret_url=" . urlencode($cm->oPage->site_path . $cm->oPage->page_path)
			, "title" => ffTemplate::_get_word_by_code("recruitment_cv_insert_title")
			, "callback" => ""
			, "class" => ""
			, "params" => array(
			)
			, "resizable" => true
			, "position" => "center"
			, "draggable" => true
			, "doredirects" => false
				)
				, $cm->oPage
		);

		$tpl->set_var("theme", $cm->oPage->theme);
		$tpl->set_var("site_path", $cm->oPage->site_path);

		$tpl->set_var("title", $db->getField("title", "Text", true));
		if ($active) {
			$tpl->set_var("azienda_name", $db->getField("nome_azienda", "Text", true));
		} else {
			$tpl->set_var("azienda_name", ffTemplate::_get_word_by_code("hidden_field"));
		}
		$tpl->set_var("category", $db->getField("category_name", "Text", true));
		$tpl->set_var("subcategory", $db->getField("subcategory_name", "Text", true));
		$tpl->set_var("published_at", $db->getField("time_inserted", "Timestamp")->getValue("DateTime", FF_LOCALE));
		$tpl->set_var("role", $db->getField("role", "Text", true));
		$tpl->set_var("required_workers", $db->getField("required_workers", "Text", true));
		$tpl->set_var("description", $db->getField("description", "Text", true));

		if (strlen($db->getField("required_studies", "Text", true))) {
			$other_information = true;
			$tpl->set_var("required_studies", $db->getField("required_studies", "Text", true));
			$tpl->parse("SezRequiredStudies", false);
		} else {
			$tpl->set_var("SezRequiredStudies", "");
		}

		if (strlen($db->getField("required_experience", "Text", true))) {
			$other_information = true;
			$tpl->set_var("required_experience", $db->getField("required_experience", "Text", true));
			$tpl->parse("SezRequiredExperience", false);
		} else {
			$tpl->set_var("SezRequiredExperience", "");
		}

		if (strlen($db->getField("contract_type", "Text", true))) {
			$other_information = true;
			$tpl->set_var("contract_type", $db->getField("contract_type", "Text", true));
			$tpl->parse("SezContractType", false);
		} else {
			$tpl->set_var("SezContractType", "");
		}

		if (strlen($db->getField("contract_durata", "Text", true))) {
			$other_information = true;
			$tpl->set_var("contract_durata", $db->getField("contract_durata", "Text", true));
			$tpl->parse("SezContractDurata", false);
		} else {
			$tpl->set_var("SezContractDurata", "");
		}

		if (strlen($db->getField("day_type", "Text", true))) {
			$other_information = true;
			$tpl->set_var("day_type", $db->getField("day_type", "Text", true));
			$tpl->parse("SezDayType", false);
		} else {
			$tpl->set_var("SezDayType", "");
		}

		if (strlen($db->getField("day_timing", "Text", true))) {
			$other_information = true;
			$tpl->set_var("day_timing", $db->getField("day_timing", "Text", true));
			$tpl->parse("SezDayTiming", false);
		} else {
			$tpl->set_var("SezDayTiming", "");
		}

		if (strlen($db->getField("stipendio", "Text", true))) {
			$other_information = true;
			$tpl->set_var("stipendo", $db->getField("stipendio", "Text", true));
			$tpl->parse("SezStipendio", false);
		} else {
			$tpl->set_var("SezStipendio", "");
		}

		if ($other_information)
			$tpl->parse("SezOtherInformation", false);
		else
			$tpl->set_var("SezOtherInformation", "");

		$tpl->set_var("cv_submit", "ff.ffPage.dialog.doOpen('SubmitCv');");
		$tpl->set_var("recruitment_detail_back", (isset($_REQUEST["ret_url"]) ? $_REQUEST["ret_url"] : $arrPathInfo[0] . global_settings("MOD_ADVERTISEMENT_CLASS_BASE_PATH")));

		$out_buffer = $tpl->rpparse("main", false);
		return;
	} else {
		$subcategory_slug = "";
		for ($i = 0; $i < count($arrPathInfo); $i++) {
			$subcategory_slug .= ($i ? ", " : "") . $db->toSql($arrPathInfo[$i]);
		}
		$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.*
								, " . CM_TABLE_PREFIX . "mod_recruitment_category.name AS category_name
							FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
								LEFT JOIN " . CM_TABLE_PREFIX . "mod_recruitment_category ON " . CM_TABLE_PREFIX . "mod_recruitment_category.ID = " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID_category 
							WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.city = (SELECT " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".ID 
																										FROM " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . " 
																										WHERE " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".slug = " . $db->toSql($globals->slug_city) . "
																									)
								AND " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID_subcategory IN (SELECT " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID 
																										FROM  " . CM_TABLE_PREFIX . "mod_recruitment_subcategory
																										WHERE  " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.smart_url IN (" . $subcategory_slug . "))
							";
		$db->query($sSQL);
		if ($db->nextRecord()) {
			$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/recruitment/applets/job-advertisement/index.html", $cm->oPage->theme, false);
			if ($filename === null)
				$filename = cm_moduleCascadeFindTemplate(CM_MODULES_ROOT . "/recruitment/themes", "/applets/job-advertisement/index.html", $cm->oPage->theme);

			$tpl = ffTemplate::factory(ffCommon_dirname($filename));
			$tpl->load_file("index.html", "main");

			$tpl->set_var("theme", $cm->oPage->theme);
			$tpl->set_var("site_path", $cm->oPage->site_path);
			do {
				if ($active) {
					$tpl->set_var("hide", "");
				} else {
					$tpl->set_var("hide", "blocked");
				}

				$tpl->set_var("title", $db->getField("title", "Text", true));
				$smart_url = $db->getField("smart_url", "Text", true);
				if (/*global_settings("MOD_ADVERTISEMENT_CLASS_BASE_PATH") && */strlen($smart_url)) {
					$tpl->set_var("smart_url", $globals->base_path . "/" . $smart_url . "?" . $cm->oPage->get_globals() . "ret_url=" . rawurlencode($_SERVER["REQUEST_URI"]));
					$tpl->parse("SectJobAdvertisementTitleUrl", false);
					$tpl->set_var("SectJobAdvertisementTitleNoUrl", "");
				} else {
					$tpl->set_var("SectJobAdvertisementTitleUrl", "");
					$tpl->parse("SectJobAdvertisementTitleNoUrl", false);
				}

				if ($active) {
					$tpl->set_var("azienda_name", $db->getField("nome_azienda", "Text", true));
				} else {
					$tpl->set_var("azienda_name", ffTemplate::_get_word_by_code("hidden_field"));
				}

				$tpl->set_var("category", $db->getField("category_name", "Text", true));

				$tpl->set_var("published_at", $db->getField("time_inserted", "Timestamp")->getValue("DateTime", FF_LOCALE));
				$tpl->set_var("azienda_city", $db->getField("city", "Text", true));
				$tpl->set_var("description", $db->getField("description", "Text", true));
				$tpl->parse("SectJobAdvertisement", true);
			} while ($db->nextRecord());
			
			$out_buffer = $tpl->rpparse("main", false);
			return;
		} else {
			if ($globals->do_404)
				$cm->responseCode(404);
			else
				return;
		}
	}
} else {
	$sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.*
						, " . CM_TABLE_PREFIX . "mod_recruitment_category.name AS category_name
						, " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".name AS city
						FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
							LEFT JOIN " . CM_TABLE_PREFIX . "mod_recruitment_category ON " . CM_TABLE_PREFIX . "mod_recruitment_category.ID = " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID_category 
							LEFT JOIN " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . " ON " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".ID = " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.city
						WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.city = 
							(
								SELECT " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".ID 
								FROM " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . " 
								WHERE " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".slug = " . $db->toSql($globals->slug_city) . "
							)";
	$db->query($sSQL);
	if ($db->nextRecord()) {
		$filename = cm_moduleCascadeFindTemplate(FF_THEME_DISK_PATH, "/modules/recruitment/applets/job-advertisement/index.html", $cm->oPage->theme, false);
		if ($filename === null)
			$filename = cm_moduleCascadeFindTemplate(CM_MODULES_ROOT . "/recruitment/themes", "/applets/job-advertisement/index.html", $cm->oPage->theme);

		$tpl = ffTemplate::factory(ffCommon_dirname($filename));
		$tpl->load_file("index.html", "main");

		$tpl->set_var("theme", $cm->oPage->theme);
		$tpl->set_var("site_path", $cm->oPage->site_path);

		do {
			if ($active) {
				$tpl->set_var("hide", "");
			} else {
				$tpl->set_var("hide", "blocked");
			}

			$tpl->set_var("title", $db->getField("title", "Text", true));
			$smart_url = $db->getField("smart_url", "Text", true);
			if (/*global_settings("MOD_ADVERTISEMENT_CLASS_BASE_PATH") && */strlen($smart_url)) {
				$tpl->set_var("smart_url", $globals->base_path . "/" . $smart_url . "?" . $cm->oPage->get_globals() . "ret_url=" . rawurlencode($_SERVER["REQUEST_URI"]));
				$tpl->parse("SectJobAdvertisementTitleUrl", false);
				$tpl->set_var("SectJobAdvertisementTitleNoUrl", "");
			} else {
				$tpl->set_var("SectJobAdvertisementTitleUrl", "");
				$tpl->parse("SectJobAdvertisementTitleNoUrl", false);
			}

			if ($active) {
				$tpl->set_var("azienda_name", $db->getField("nome_azienda", "Text", true));
			} else {
				$tpl->set_var("azienda_name", ffTemplate::_get_word_by_code("hidden_field"));
			}

			$tpl->set_var("category", $db->getField("category_name", "Text", true));

			$tpl->set_var("published_at", $db->getField("time_inserted", "Timestamp")->getValue("DateTime", FF_LOCALE));
			$tpl->set_var("azienda_city", $db->getField("city", "Text", true));
			$tpl->set_var("description", $db->getField("description", "Text", true));
			$tpl->parse("SectJobAdvertisement", true);
		} while ($db->nextRecord());
		$tpl->set_var("recruitment_detail_back", (isset($_REQUEST["ret_url"]) ? $_REQUEST["ret_url"] : $arrPathInfo[0]));
		
		$out_buffer = $tpl->rpparse("main", false);
		return;
	}
	else
	{
		if ($globals->do_404)
			$cm->responseCode(404);
		else
			return;
	}
}

