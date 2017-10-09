<?php

if (mod_security_check_session(false)) {
    $permission = check_recruitment_permission();
    if ($permission !== true && !(is_array($permission) && count($permission))) {
        ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
    }
}
if (!isset($_REQUEST["ret_url"])) {
    if ($cm->router->named_rules["recruitment_submit_cv"]->reverse == $cm->oPage->page_path) {
        $_REQUEST["ret_url"] = "/";
    } else {
        $_REQUEST["ret_url"] = $cm->oPage->site_path . str_replace($cm->router->named_rules["recruitment_submit_cv"]->reverse, "", $cm->oPage->page_path);
    }
}

$arrLangLevel = array(
    array(new ffData("A1"), new ffData(ffTemplate::_get_word_by_code("A1"))),
    array(new ffData("A2"), new ffData(ffTemplate::_get_word_by_code("A2"))),
    array(new ffData("B1"), new ffData(ffTemplate::_get_word_by_code("B1"))),
    array(new ffData("B2"), new ffData(ffTemplate::_get_word_by_code("B2"))),
    array(new ffData("C1"), new ffData(ffTemplate::_get_word_by_code("C1"))),
    array(new ffData("C2"), new ffData(ffTemplate::_get_word_by_code("C2")))
);

$db = ffDB_Sql::factory();

$default_value = false;
$string_city = "";
$UserNID = get_session("UserNID");

if (isset($_REQUEST["scv"]) && $_REQUEST["scv"] > 0)
    $cv_requested = $_REQUEST["scv"];

if (isset($_REQUEST["setcv"]) && $_REQUEST["setcv"] && $UserNID) {
    $sSQL = "SELECT ID
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_user = " . $db->toSql($UserNID, "Number") . "
                    AND " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_advertisement = 
                        (
                            SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID
                            FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                            WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.smart_url = " . $db->toSql(basename($cm->real_path_info), "Text") . "
                        )";
    $db->query($sSQL);
    if($db->nextRecord()) {
        $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit SET
                        " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_cv = " . $db->toSql($_REQUEST["keys"]["ID"], "Number") . "
                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_user = " . $db->toSql($UserNID, "Number") . "
                        AND " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_advertisement = 
                            (
                                SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID
                                FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.smart_url = " . $db->toSql(basename($cm->real_path_info), "Text") . "
                            )";
        $db->execute($sSQL);
    } else {
        $sSQL = "INSERT INTO " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit
                    (
                            ID_user
                            , ID_advertisement
                            , ID_cv
                    )
                    (
                            SELECT 
                                    " . $db->toSql($UserNID, "Number") . "
                                    , " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID
                                    , " . $db->toSql($_REQUEST["keys"]["ID"], "Number") . "
                                FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.smart_url = " . $db->toSql(basename($cm->real_path_info), "Text") . "
                    )";
        $db->execute($sSQL);
    }
    die(ffCommon_jsonenc(array("url" => $_REQUEST["ret_url"], "close" => false, "refresh" => true, "doredirects" => false), true));
    //ffRedirect($_REQUEST["ret_url"]);
}

if (!$_REQUEST["keys"]["ID"] > 0 && strlen($cm->real_path_info)) {
    $real_path_info = $cm->real_path_info;
    //$cv_subcategory = str_replace('/', "','", trim($real_path_info, "/"));
    $array = explode("/", trim($real_path_info, "/"));
    foreach ($array AS $key => $value) {
        if (strlen($value)) {
            if (strlen($cv_subcategory))
                $cv_subcategory .= ",";
            $cv_subcategory .= $db->toSql($value, "Text");
        }
    }


    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_category.ID AS ID_category
                ,  " . CM_TABLE_PREFIX . "mod_recruitment_category.smart_url AS category_smart_url
                , " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID AS ID_subcategory 
                ,  " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.smart_url AS subcategory_smart_url
                ,  " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.name AS subcategory_name
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_category
                    LEFT JOIN " . CM_TABLE_PREFIX . "mod_recruitment_subcategory ON " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID_category = " . CM_TABLE_PREFIX . "mod_recruitment_category.ID
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.smart_url IN (" . $cv_subcategory . ")";
    $db->query($sSQL);
    if ($db->nextRecord()) {
        do {
            $ID_category = $db->getField("ID_category", "Number", true);
            $ID_subcategory = $db->getField("ID_subcategory", "Number", true);
            if (strlen($subcategory_string))
                $subcategory_string .= " - ";
            $subcategory_string .= $db->getField("subcategory_name", "Text", true);
            $cv_ambito["ID_category"][$ID_category] = $ID_category;
            $cv_ambito["ID_subcategory"][$ID_subcategory] = $ID_subcategory;
        } while ($db->nextRecord());
        $category = implode(",", $cv_ambito["ID_category"]);
        $subcategory = implode(",", $cv_ambito["ID_subcategory"]);
    }else {
        $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv.*
                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv
                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv.smart_url = " . $db->toSql(basename($cm->real_path_info)) . "
                    AND " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_user = " . $db->toSql($UserNID, "Number", false);
        $db->query($sSQL);
        if ($db->nextRecord()) {
            $_REQUEST["keys"]["ID"] = $db->getField("ID", "Number", true);
            $smart_url = $db->getField("smart_url", "Text", true);
        }
    }
} else if ($_REQUEST["keys"]["ID"] > 0) {
    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv.*
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID = " . $db->toSql($_REQUEST["keys"]["ID"]);
    $db->query($sSQL);
    if ($db->nextRecord()) {
        $_REQUEST["keys"]["ID"] = $db->getField("ID", "Number", true);
        $smart_url = $db->getField("smart_url", "Text", true);
    }
}

if ($UserNID > 0) {
    $information = mod_security_getAllUserInfo($UserNID);
    if (is_array($information) && count($information))
        $default_value = true;
}

if (!$default_value && isset($_REQUEST["su"]) && strlen($_REQUEST["su"])) {
    $sSQL = "SELECT " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".smart_url
                FROM " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . "
                    INNER JOIN " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement ON " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.city = " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".ID
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.smart_url = " . $db->toSql($_REQUEST["su"], "Text");
    $db->query($sSQL);
    if ($db->nextRecord()) { 
        $string_city = $db->getField("smart_url", "Text", true);
    }
}

$ID_cv = $_REQUEST["keys"]["ID"];
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "cvModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("recruitment_curriculum_modify");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_recruitment_cv";
$oRecord->populate_edit_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv.*
                                        , " . CM_TABLE_PREFIX . "mod_security_users.*
                                    FROM  " . CM_TABLE_PREFIX . "mod_recruitment_cv
                                        INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users ON " . CM_TABLE_PREFIX . "mod_security_users.ID = " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_user 
                                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID = " . $db->toSql($ID_cv, "Number") . "
                                        AND  " . CM_TABLE_PREFIX . "mod_security_users.ID = " . $db->toSql($UserNID, "Number");
$oRecord->auto_populate_edit = true;
if ($cv_requested) {
    $oRecord->populate_insert_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv.*
                                        , " . CM_TABLE_PREFIX . "mod_security_users.*
                                    FROM  " . CM_TABLE_PREFIX . "mod_recruitment_cv
                                        INNER JOIN " . CM_TABLE_PREFIX . "mod_security_users ON " . CM_TABLE_PREFIX . "mod_security_users.ID = " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_user 
                                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID = " . $db->toSql($cv_requested, "Number") . "
                                        AND  " . CM_TABLE_PREFIX . "mod_security_users.ID = " . $db->toSql($UserNID, "Number");
    $oRecord->auto_populate_insert = true;
}
$oRecord->buttons_options["delete"]["display"] = false;
$oRecord->insert_additional_fields["ID_user"] = new ffData($UserNID, "Number");
$oRecord->update_additional_fields["last_update"] = new ffData(time(), "Number");
$oRecord->insert_additional_fields["created"] = new ffData(time(), "Number");
$oRecord->addEvent("on_done_action", "cvModify_on_done_action");
$oRecord->addEvent("on_do_action", "cvModify_on_do_action");
if (strlen($category)) {
    $oRecord->user_vars["category"] = $category;
}
if (strlen($subcategory)) {
    $oRecord->user_vars["subcategory"] = $subcategory;
}
if (strlen($subcategory_string)) {
    $oRecord->user_vars["subcategory_string"] = $subcategory_string;
}

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

if ($ID_cv || strlen($category)) {
    $oField = ffField::factory($cm->oPage);
    $oField->id = "avatar";
    $oField->container_class = "avatar";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_modify_avatar");
    $oField->base_type = "Text";
    $oField->extended_type = "File";
    $oField->file_storing_path = FF_DISK_PATH . FF_UPDIR . "/recruitment/cv/[ID_VALUE]"; // DA METTERE
    $oField->file_temp_path = FF_DISK_PATH . FF_UPDIR . "/recruitment/cv"; // DA METTERE
    //$oField->file_max_size = MAX_UPLOAD;
    $oField->file_full_path = true;
    $oField->file_check_exist = true;
    $oField->file_normalize = true;
    $oField->file_show_preview = true;
    $oField->file_saved_view_url = FF_SITE_PATH . constant("CM_SHOWFILES") . "/[_FILENAME_]";
    $oField->file_saved_preview_url = FF_SITE_PATH . constant("CM_SHOWFILES") . "/thumb/[_FILENAME_]";
    //$oField->file_temp_view_url = FF_SITE_PATH . constant("CM_SHOWFILES") . "[_FILENAME_]";
    //$oField->file_temp_preview_url = FF_SITE_PATH . constant("CM_SHOWFILES") . "/thumb[_FILENAME_]";
    $oField->control_type = "file";
    $oField->file_show_delete = true;
    $oField->widget = "uploadify";
    if (check_function("set_field_uploader")) {
        $oField = set_field_uploader($oField);
    }
    $oField->store_in_db = false;
    if ($default_value && array_key_exists("avatar", $information)) {
        $oField->default_value = new ffData($information["avatar"], "Text");
    }
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "name";
    $oField->container_class = "name";
    $oField->store_in_db = false;
    $oField->label = ffTemplate::_get_word_by_code("recruitment_user_name");
    $oField->required = true;
    $oField->data_type = "";
    if ($default_value && array_key_exists("firstname", $information)) {
        $oField->default_value = new ffData($information["firstname"], "Text");
    }
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "surname";
    $oField->container_class = "surname";
    $oField->store_in_db = false;
    $oField->label = ffTemplate::_get_word_by_code("recruitment_user_surname");
    $oField->required = true;
    $oField->data_type = "";
    if ($default_value && array_key_exists("lastname", $information)) {
        $oField->default_value = new ffData($information["lastname"], "Text");
    }
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "billaddress";
    $oField->container_class = "address";
    $oField->store_in_db = false;
    $oField->label = ffTemplate::_get_word_by_code("recruitment_user_address");
    $oField->data_type = "";
    if ($default_value && array_key_exists("address", $information)) {
        $oField->default_value = new ffData($information["address"], "Text");
    }
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "billtown";
    $oField->container_class = "town";
    $oField->store_in_db = false;
    $oField->widget = "activecomboex";
    $oField->source_SQL = "SELECT " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".smart_url, " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".name
                            FROM " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . "
                            WHERE 1
                            ORDER BY name";
    $oField->actex_update_from_db = true;
    $oField->data_type = "";
    if ($default_value && array_key_exists("city", $information)) {
        $oField->default_value = new ffData($information["city"], "Text");
    } elseif (strlen($string_city))
        $oField->default_value = new ffData($string_city, "Text");
    $oField->label = ffTemplate::_get_word_by_code("recruitment_user_town");
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "tel";
    $oField->container_class = "telephone";
    $oField->store_in_db = false;
    $oField->label = ffTemplate::_get_word_by_code("recruitment_user_telephone");
    $oField->data_type = "";
    if ($default_value && array_key_exists("tel", $information)) {
        $oField->default_value = new ffData($information["tel"], "Text");
    }
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "email";
    $oField->container_class = "email";
    $oField->required = true;
    $oField->label = ffTemplate::_get_word_by_code("recruitment_user_email");
    $oField->store_in_db = false;
    $oField->data_type = "";
    if ($default_value && array_key_exists("email", $information)) {
        $oField->default_value = new ffData($information["email"], "Text");
    }
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "cellular";
    $oField->container_class = "cellular";
    $oField->store_in_db = false;
    $oField->label = ffTemplate::_get_word_by_code("recruitment_user_callular");
    $oField->data_type = "";
    if ($default_value && array_key_exists("cell", $information)) {
        $oField->default_value = new ffData($information["cell"], "Text");
    }
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "data_nascita";
    $oField->store_in_db = false;
    $oField->widget = "datechooser";
    $oField->base_type = "Date";
    $oField->extended_type = "Date";
    $oField->app_type = "Date";
    $oField->data_type = "";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_user_date");
    if ($default_value && array_key_exists("data_nascita", $information)) {
        $oField->default_value = new ffData($information["data_nascita"], "Date", FF_LOCALE);
    }
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "sesso";
    $oField->container_class = "sesso";
    $oField->store_in_db = false;
    $oField->extended_type = "Selection";
    $oField->data_type = "";
    $oField->multi_pairs = array(
        array(new ffData("M"), new ffData(ffTemplate::_get_word_by_code("uomo"))),
        array(new ffData("F"), new ffData(ffTemplate::_get_word_by_code("donna")))
    );
    $oField->label = ffTemplate::_get_word_by_code("recruitment_user_sesso");
    if ($default_value && array_key_exists("sesso", $information)) {
        $oField->default_value = new ffData($information["sesso"], "Text");
    }
    $oRecord->addContent($oField);
    /*
      if($category_smart_url !== "generico")
      {
      $oField = ffField::factory($cm->oPage);
      $oField->id = "ID_subcategory";
      $oField->container_class = "subcategory";
      $oField->base_type = "Number";
      $oField->source_SQL = "SELECT ID, name
      FROM " . CM_TABLE_PREFIX . "mod_recruitment_subcategory
      WHERE " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID_category = " . $db->toSql($ID_category, "Number") . "
      ORDER BY name";
      $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_subcategory");
      $oField->widget = "activecomboex";
      $oField->actex_update_from_db = true;
      $oField->resources[] = "SubCategoryModify";
      $oField->actex_dialog_url = $cm->oPage->site_path . VG_SITE_RESTRICTED  . "/recruitment/subcategory/modify?category=" . $category_smart_url;
      //$oField->actex_dialog_edit_params = array("keys[anagraph-ID]" => null);
      $oRecord->addContent($oField);
      }
     */
    $oField = ffField::factory($cm->oPage);
    $oField->id = "ambizione";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_ambizione");
    $oRecord->addContent($oField);

    if ($ID_cv > 0) {
        $oGrid = ffGrid::factory($cm->oPage);
        $oGrid->title = ffTemplate::_get_word_by_code("recruitment_job_experience");
        $oGrid->full_ajax = true;
        $oGrid->id = "Job";
        $oGrid->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv_job.*
	                        FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_job
	                        WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_job.ID_cv = " . $db->toSql($ID_cv, "Number") . "
	                        [AND] [WHERE] 
	                        [HAVING]
	                        [ORDER]";
        $oGrid->order_default = "ID";
		if(MOD_RECRUITMENT_WANT_DIALOG)
			$oGrid->record_url = $cm->oPage->site_path . MOD_RECRUITMENT_PATH . $cm->router->named_rules["recruitment_submit_cv"]->reverse . "/jobs/modify/" . $smart_url;
		else
			$oGrid->record_url = $cm->oPage->site_path . "/restricted/recruitment/cv/jobs/modify/" . $smart_url;
        $oGrid->record_id = "JobModify";
        $oGrid->resources[] = $oGrid->record_id;
        $oGrid->buttons_options["export"]["display"] = false;
        $oGrid->display_new = true;
        $oGrid->display_edit_url = true;
        $oGrid->display_delete_bt = true;
        $oGrid->user_vars["ID_cv"] = $ID_cv;
        $oGrid->use_search = false;
        $oGrid->use_paging = false;
        $oGrid->addEvent("on_before_parse_row", "Job_on_before_parse_row");

        // Campi chiave
        $oField = ffField::factory($cm->oPage);
        $oField->id = "ID";
        $oField->base_type = "Number";
        $oGrid->addKeyField($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "data_fine";
        $oField->container_class = "data-fine";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_job_end");
        $oField->base_type = "Text";
        $oGrid->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "data_inizio";
        $oField->container_class = "data-inizio";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_job_begin");
        $oField->base_type = "Timestamp";
        $oField->app_type = "Date";
        $oGrid->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "nome_azienda";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_job_name");
        $oGrid->addContent($oField);

        $oRecord->addContent($oGrid);
        $cm->oPage->addContent($oGrid);
    } else {
        $oDetail = ffDetails::factory($cm->oPage);
        $oDetail->id = "Job";
        $oDetail->title = ffTemplate::_get_word_by_code("recruitment_job_experience");
        $oDetail->src_table = CM_TABLE_PREFIX . "mod_recruitment_cv_job";
        $oDetail->order_default = "ID";
        if ($cv_requested) {
            $oDetail->populate_insert_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv_job.*
                                                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_job
                                                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_job.ID_cv = " . $db->toSql($cv_requested, "Number");
            $oDetail->auto_populate_insert = true;
        }
        $oDetail->fields_relationship = array("ID_cv" => "ID");

        $oField = ffField::factory($cm->oPage);
        $oField->id = "ID";
        $oField->data_source = "ID";
        $oField->base_type = "Number";
        $oDetail->addKeyField($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "data_fine";
        $oField->container_class = "data-fine";
        $oField->widget = "datepicker";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_job_end");
        $oField->base_type = "Text";
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "data_inizio";
        $oField->container_class = "data-inizio";
        $oField->widget = "datepicker";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_job_begin");
        $oField->base_type = "Timestamp";
        $oField->app_type = "Date";
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "nome_azienda";
        $oField->required = true;
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_job_nome_azienda");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "telefono";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_job_telefono");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "email";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_job_email");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "incarico";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_job_role");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "datore_lavoro";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_job_datore");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "citta";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_job_citta");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "descrizione";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_job_descrizione");
        $oField->extended_type = "Text";
        $oField->base_type = "Text";
        $oDetail->addContent($oField);

        $oRecord->addContent($oDetail);
        $cm->oPage->addContent($oDetail);
    }
    if ($ID_cv > 0) {
        $oGrid = ffGrid::factory($cm->oPage);
        $oGrid->title = ffTemplate::_get_word_by_code("recruitment_school_experience");
        $oGrid->full_ajax = true;
        $oGrid->id = "Studies";
        $oGrid->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv_formation.*
	                        FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_formation
	                        WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_formation.ID_cv = " . $db->toSql($ID_cv, "Number") . "
	                        [AND] [WHERE] 
	                        [HAVING]
	                        [ORDER]";
        $oGrid->order_default = "ID";
		if(MOD_RECRUITMENT_WANT_DIALOG)
			$oGrid->record_url = $cm->oPage->site_path . MOD_RECRUITMENT_PATH . $cm->router->named_rules["recruitment_submit_cv"]->reverse . "/studies/modify/" . $smart_url;
		else
			$oGrid->record_url = $cm->oPage->site_path . "/restricted/recruitment/cv/studies/modify/" . $smart_url;
        $oGrid->record_id = "StudiesModify";
        $oGrid->resources[] = $oGrid->record_id;
        $oGrid->buttons_options["export"]["display"] = false;
        $oGrid->display_new = true;
        $oGrid->display_edit_bt = false;
        $oGrid->display_edit_url = true;
        $oGrid->display_delete_bt = true;
        $oGrid->use_search = false;
        $oGrid->use_paging = false;
        $oGrid->addEvent("on_before_parse_row", "Studies_on_before_parse_row");

        // Campi chiave
        $oField = ffField::factory($cm->oPage);
        $oField->id = "ID";
        $oField->base_type = "Number";
        $oGrid->addKeyField($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "data_fine";
        $oField->container_class = "data-fine";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_studies_end");
        $oField->base_type = "Text";
        $oGrid->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "data_inizio";
        $oField->container_class = "data-inizio";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_studies_begin");
        $oField->base_type = "Timestamp";
        $oField->app_type = "Date";
        $oGrid->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "nome_scuola";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_school_name");
        $oGrid->addContent($oField);

        $oRecord->addContent($oGrid);
        $cm->oPage->addContent($oGrid);
    } else {
        $oDetail = ffDetails::factory($cm->oPage);
        $oDetail->id = "Studies";
        $oDetail->title = ffTemplate::_get_word_by_code("recruitment_school_experience");
        $oDetail->src_table = CM_TABLE_PREFIX . "mod_recruitment_cv_formation";
        $oDetail->order_default = "ID";
        if ($cv_requested) {
            $oDetail->populate_insert_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv_formation.*
                                                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_formation
                                                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_formation.ID_cv = " . $db->toSql($cv_requested, "Number");
            $oDetail->auto_populate_insert = true;
        }
        $oDetail->fields_relationship = array("ID_cv" => "ID");

        $oField = ffField::factory($cm->oPage);
        $oField->id = "ID";
        $oField->data_source = "ID";
        $oField->base_type = "Number";
        $oDetail->addKeyField($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "data_fine";
        $oField->container_class = "data-fine";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_studies_end");
        $oField->base_type = "Text";
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "data_inizio";
        $oField->container_class = "data-inizio";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_studies_begin");
        $oField->base_type = "Timestamp";
        $oField->app_type = "Date";
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "nome_scuola";
        $oField->required = true;
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_school_nome_scuola");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "telefono";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_school_telefono");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "email";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_school_email");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "esito";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_school_esito");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "descrizione";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_school_descrizione");
        $oField->extended_type = "Text";
        $oField->base_type = "Text";
        $oDetail->addContent($oField);

        $oRecord->addContent($oDetail);
        $cm->oPage->addContent($oDetail);
    }

    $oField = ffField::factory($cm->oPage);
    $oField->id = "lingua_madre";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_lang_prefer");
    $oRecord->addContent($oField);

    if ($ID_cv > 0) {
        $oGrid = ffGrid::factory($cm->oPage);
        $oGrid->title = ffTemplate::_get_word_by_code("recruitment_lang_title");
        $oGrid->full_ajax = true;
        $oGrid->id = "Lang";
        $oGrid->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv_lang.*
	                        FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_lang
	                        WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_lang.ID_cv = " . $db->toSql($ID_cv, "Number") . "
	                        [AND] [WHERE] 
	                        [HAVING]
	                        [ORDER]";
        $oGrid->order_default = "ID";
        if(MOD_RECRUITMENT_WANT_DIALOG)
			$oGrid->record_url = $cm->oPage->site_path . MOD_RECRUITMENT_PATH . $cm->router->named_rules["recruitment_submit_cv"]->reverse . "/lang/modify/" . $smart_url;
		else
			$oGrid->record_url = $cm->oPage->site_path . "/restricted/recruitment/cv/lang/modify/" . $smart_url;
        $oGrid->record_id = "LangModify";
        $oGrid->resources[] = $oGrid->record_id;
        $oGrid->buttons_options["export"]["display"] = false;
        $oGrid->display_new = true;
        $oGrid->display_edit_bt = false;
        $oGrid->display_edit_url = true;
        $oGrid->display_delete_bt = true;
        $oGrid->use_search = false;
        $oGrid->use_paging = false;

        $oField = ffField::factory($cm->oPage);
        $oField->id = "ID";
        $oField->base_type = "Number";
        $oGrid->addKeyField($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "lang_name";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang");
        $oField->display_label = false;
        $oGrid->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "reading";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_reading");
        $oGrid->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "listening";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_reading");
        $oGrid->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "speaking";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_speaking");
        $oGrid->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "writing";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_writing");
        $oGrid->addContent($oField);

        $oRecord->addContent($oGrid);
        $cm->oPage->addContent($oGrid);
    } else {
        $oDetail = ffDetails::factory($cm->oPage);
        $oDetail->id = "Lang";
        $oDetail->title = ffTemplate::_get_word_by_code("recruitment_lang_title");
        $oDetail->src_table = CM_TABLE_PREFIX . "mod_recruitment_cv_lang";
        $oDetail->order_default = "ID";
        if ($cv_requested) {
            $oDetail->populate_insert_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv_lang.*
                                                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_lang
                                                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_lang.ID_cv = " . $db->toSql($cv_requested, "Number");
            $oDetail->auto_populate_insert = true;
        }
        $oDetail->fields_relationship = array("ID_cv" => "ID");

        $oField = ffField::factory($cm->oPage);
        $oField->id = "ID";
        $oField->data_source = "ID";
        $oField->base_type = "Number";
        $oDetail->addKeyField($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "lang_name";
        $oField->required = true;
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_name");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "reading";
        $oField->extended_type = "Selection";
        $oField->multi_pairs = $arrLangLevel;
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_reading");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "listening";
        $oField->extended_type = "Selection";
        $oField->multi_pairs = $arrLangLevel;
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_listening");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "speaking";
        $oField->extended_type = "Selection";
        $oField->multi_pairs = $arrLangLevel;
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_speaking");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "writing";
        $oField->extended_type = "Selection";
        $oField->multi_pairs = $arrLangLevel;
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_writing");
        $oDetail->addContent($oField);

        $oField = ffField::factory($cm->oPage);
        $oField->id = "certification";
        $oField->extended_type = "Text";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_certification");
        $oDetail->addContent($oField);

        $oRecord->addContent($oDetail);
        $cm->oPage->addContent($oDetail);
    }
    $oField = ffField::factory($cm->oPage);
    $oField->id = "competenze_comunicative";
    $oField->container_class = "description";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_competenze_comunicative");
    $oField->base_type = "Text";
    $oField->extended_type = "Text";
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "competenze_organizzative";
    $oField->container_class = "description";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_competenze_organizzative");
    $oField->base_type = "Text";
    $oField->extended_type = "Text";
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "competenze_professionali";
    $oField->container_class = "description";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_competenze_professionali");
    $oField->base_type = "Text";
    $oField->extended_type = "Text";
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "competenze_informatiche";
    $oField->container_class = "description";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_competenze_informatiche");
    $oField->base_type = "Text";
    $oField->extended_type = "Text";
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "ulteriori_informazioni";
    $oField->container_class = "description";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_ulteriori_informazioni");
    $oField->base_type = "Text";
    $oField->extended_type = "Text";
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->id = "allegato";
    $oField->container_class = "allegato";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_modify_allegato");
    $oField->base_type = "Text";
    $oField->extended_type = "File";
    $oField->file_storing_path = FF_DISK_PATH . FF_UPDIR . "/recruitment/cv/allegato/[ID_VALUE]"; // DA METTERE
    $oField->file_temp_path = FF_DISK_PATH . FF_UPDIR . "/recruitment/cv/allegato"; // DA METTERE
    //$oField->file_max_size = MAX_UPLOAD;
    $oField->file_full_path = true;
    $oField->file_check_exist = true;
    $oField->file_normalize = true;
    $oField->file_show_preview = true;
    $oField->file_saved_view_url = FF_SITE_PATH . constant("CM_SHOWFILES") . "/[_FILENAME_]";
    $oField->file_saved_preview_url = FF_SITE_PATH . constant("CM_SHOWFILES") . "/thumb/[_FILENAME_]";
    //$oField->file_temp_view_url = FF_SITE_PATH . constant("CM_SHOWFILES") . "[_FILENAME_]";
    //$oField->file_temp_preview_url = FF_SITE_PATH . constant("CM_SHOWFILES") . "/thumb[_FILENAME_]";
    $oField->control_type = "file";
    $oField->file_show_delete = true;
    $oField->widget = "uploadify";
    if (check_function("set_field_uploader")) {
        $oField = set_field_uploader($oField);
    }
    $oRecord->addContent($oField);
} else {
    if ($UserNID) {
        $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID, " . CM_TABLE_PREFIX . "mod_recruitment_cv.subcategory_string
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_user = " . $db->toSql($UserNID, "Number");
        $db->query($sSQL);
        if($db->nextRecord()) {
            $oField = ffField::factory($cm->oPage);
            $oField->id = "cv_base";
            $oField->base_type = "Number";
            $oField->widget = "activecomboex";
            $oField->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID, " . CM_TABLE_PREFIX . "mod_recruitment_cv.subcategory_string
                                        FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv
                                        WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_user = " . $db->toSql($UserNID, "Number");
            $oField->actex_update_from_db = true;
            $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_cv_base");
            $oRecord->addContent($oField);
        }
    }


    if (isset($_REQUEST["su"]) && strlen($_REQUEST["su"])) {
        $oRecord->user_vars["smart_url"] = $_REQUEST["su"];
        $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID_subcategory
                        FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                        WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.smart_url = " . $db->toSql($_REQUEST["su"]);
        $db->query($sSQL);
        if ($db->nextRecord()) {
            $ID_subcategry = $db->getField("ID_subcategory", "Number", true);
        }
    }

    $oField = ffField::factory($cm->oPage);
    $oField->id = "ID_subcategory";
    $oField->container_class = "subcategory";
    $oField->extended_type = "Selection";
    $oField->widget = "autocompletetoken";
    $oField->autocompletetoken_minLength = 0;
    $oField->autocompletetoken_theme = "";
    $oField->autocompletetoken_not_found_label = ffTemplate::_get_word_by_code("autocompletetoken_not_found");
    $oField->autocompletetoken_init_label = ffTemplate::_get_word_by_code("autocompletetoken_init");
    $oField->autocompletetoken_searching_label = ffTemplate::_get_word_by_code("autocompletetoken_searching");
    $oField->autocompletetoken_label = ffTemplate::_get_word_by_code("autocompletetoken_label");
    $oField->autocompletetoken_combo = true;
    $oField->autocompletetoken_compare_having = "name";
    $oField->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID
                                        , " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.name AS display_name
                                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_category
                                       INNER JOIN  " . CM_TABLE_PREFIX . "mod_recruitment_subcategory ON " . CM_TABLE_PREFIX . "mod_recruitment_category.ID = " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID_category
                                    WHERE 1
                                    [AND] [WHERE]
                                    [HAVING]
                                    ORDER BY display_name"/* . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID NOT IN (
      SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_subcategory
      FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv
      WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_user = " . $db->toSql($UserNID, "Number", false) . "
      ) . "
      ORDER BY `group`" */;

    $oField->resources[] = "SubCategoryModify";
    if ($ID_subcategry > 0)
        $oField->default_value = new ffData($ID_subcategry, "Number");
    $oField->label = ffTemplate::_get_word_by_code("recruitment_cv_category");
    $oField->required = true;
    $oRecord->addContent($oField);
}

if (is_file(FF_DISK_PATH . "/themes/comune.info/css/recruitment.css")) {
    $cm->oPage->tplAddCss("recruitment-css", "recruitment.css", "/themes/comune.info/css");
}
$cm->oPage->addContent($oRecord);

function Job_on_before_parse_row($oGrid) {
    //ffErrorHandler::raise("asd", E_USER_ERROR, null, get_defined_vars());
    $db = ffDB_Sql::factory();
    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv_job.*
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_job
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_job.ID = " . $db->toSql($oGrid->key_fields["ID"]->getValue("Number"), "Number");
    $db->query($sSQL);
    if ($db->nextRecord()) {
        $in_corso = $db->getField("in_corso", "Number", true);
        $date_end = $db->getField("data_fine", "Number", true);
    }
    if ($in_corso) {
        $oGrid->grid_fields["data_fine"]->value->setValue("in corso");
    } elseif ($date_end != 0) {
        $oGrid->grid_fields["data_fine"]->value->setValue(date("j/n/Y", $date_end));
    } else {
        $oGrid->grid_fields["data_fine"]->value->setValue("");
    }
}

function Studies_on_before_parse_row($oGrid) {
    $db = ffDB_Sql::factory();
    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv_formation.*
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_formation
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_formation.ID = " . $db->toSql($oGrid->key_fields["ID"]->getValue("Number"), "Number");
    $db->query($sSQL);
    if ($db->nextRecord()) {
        $in_corso = $db->getField("in_corso", "Number", true);
        $date_end = $db->getField("data_fine", "Number", true);
    }

    if ($in_corso) {
        $oGrid->grid_fields["data_fine"]->value->setValue("in corso");
    } elseif ($date_end != 0) {
        $oGrid->grid_fields["data_fine"]->value->setValue(date("j/n/Y", $date_end));
    } else {
        $oGrid->grid_fields["data_fine"]->value->setValue("");
    }
}

function cvModify_on_do_action($component, $action) {
    $db = ffDB_Sql::factory();
    $UserNID = get_session("UserNID");
    if (strlen($action)) {
        switch ($action) {
            case "insert":
                if (isset($component->form_fields["ID_subcategory"]) && $component->form_fields["ID_subcategory"]->getValue() > 0) {
                    if (isset($component->form_fields["cv_base"]) && $component->form_fields["cv_base"]->getValue())
                        $selected_cv = $component->form_fields["cv_base"]->getValue();

                    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.smart_url AS subcategory_smart_url
                                FROM " . CM_TABLE_PREFIX . "mod_recruitment_subcategory
                                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID IN (" . $db->toSql($component->form_fields["ID_subcategory"]->getValue(), "Number") . ")";
                    $db->query($sSQL);
                    if ($db->nextRecord()) {
                        do {
                            $cv_subcategory .= "/" . $db->getField("subcategory_smart_url", "Text", true);
                        } while ($db->nextRecord());

                        if (isset($_REQUEST["XHR_DIALOG_ID"])) {
                            die(ffCommon_jsonenc(array("url" => $component->parent[0]->site_path . $component->parent[0]->page_path . "/modify" . $cv_subcategory . "?ret_url=" . ((isset($_REQUEST["ret_url"]) && isset($component->user_vars["smart_url"])) ? urlencode($_REQUEST["ret_url"]) : urlencode($_REQUEST["ret_url"])) . (isset($component->user_vars["smart_url"]) ? "&su=" . $component->user_vars["smart_url"] : "") . ($selected_cv ? "&scv=" . $selected_cv : ""), "close" => true, "refresh" => true, "doredirects" => true), true));
                        } else {
                            ffRedirect($component->parent[0]->site_path . $component->parent[0]->page_path . "/modify" . $cv_subcategory . "?ret_url=" . ((isset($_REQUEST["ret_url"]) && isset($component->user_vars["smart_url"])) ? urlencode($_REQUEST["ret_url"]) : urlencode($_REQUEST["ret_url"])) . (isset($component->user_vars["smart_url"]) ? "&su=" . $component->user_vars["smart_url"] : "") . ($selected_cv ? "&scv=" . $selected_cv : ""));
                        }
                    }
                }

                if (!$UserNID && isset($component->form_fields["email"]) && strlen($component->form_fields["email"]->getValue())) {
                    $unknow_user = mod_recruitment_verify_existing_user($component->form_fields["email"]->getValue());
                    if ($unknow_user["ID"] > 0) {
                        ffRedirect("/login?username=" . urlencode($unknow_user["username"]) . "&ret_url=" . (isset($_REQUEST["ret_url"]) ? urlencode($_REQUEST["ret_url"]) : "/"));
                    }
                }
                break;
            default:
        }
    }
}

function cvModify_on_done_action($component, $action) {
    $UserNID = get_session("UserNID");

    if (!MOD_SEC_MULTIDOMAIN_EXTERNAL_DB || mod_security_is_admin())
        $db = mod_security_get_main_db();
    else
        $db = mod_security_get_db_by_domain(null);


    if (strlen($action)) {
        if ($action == "insert" || $action == "update") {
            foreach ($component->form_fields AS $key => $value)
                $field[$key] = $component->form_fields[$key]->getValue();

            $UserNID = mod_security_setAllUserInfo($field, $UserNID);

            $name = $field["name"] . " " . $field["surname"];

            $smart_url = "curriculum-vitae " . $name . $component->key_fields["ID"]->getValue();

            switch ($action) {
                case "insert":
                    if ($UserNID > 0) {
                        if (isset($_REQUEST["su"]) && strlen(($_REQUEST["su"]))) {
                            $sSQL = "SELECT ID
                                        FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit
                                        WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_user = " . $db->toSql($UserNID, "Number") . "
                                            AND " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_advertisement = 
                                                (
                                                    SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID
                                                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                                                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.smart_url = " . $db->toSql($_REQUEST["su"], "Text") . "
                                                )";
                            $db->query($sSQL);
                            if($db->nextRecord()) {
                                $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit SET
                                                " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_cv = " . $db->toSql($component->key_fields["ID"]->getValue(), "Number") . "
                                            WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_user = " . $db->toSql($UserNID, "Number") . "
                                                AND " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit.ID_advertisement = 
                                                    (
                                                        SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID
                                                        FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                                                        WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.smart_url = " . $db->toSql($_REQUEST["su"], "Text") . "
                                                    )";
                                $db->execute($sSQL);
                            } else {
                                $sSQL = "INSERT INTO " . CM_TABLE_PREFIX . "mod_recruitment_cv_submit
                                            (
                                                    ID_user
                                                    , ID_advertisement
                                                    , ID_cv
                                            )
                                            (
                                                    SELECT 
                                                            " . $db->toSql($UserNID, "Number") . "
                                                            , " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID
                                                            , " . $db->toSql($component->key_fields["ID"]->getValue(), "Number") . "
                                                        FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                                                        WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.smart_url = " . $db->toSql($_REQUEST["su"], "Text") . "
                                            )";
                                $db->execute($sSQL);
                            }
                        }

                        $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_recruitment_cv
                                    SET " . CM_TABLE_PREFIX . "mod_recruitment_cv.smart_url = " . $db->toSql(ffCommon_url_rewrite($smart_url)) . "
                                     , " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_user = " . $db->toSql($UserNID, "Number") . "
                                        " . (isset($component->user_vars["category"]) ? " , " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_category = " . $db->toSql($component->user_vars["category"]) : ""
                                ) . "
                                        " . (isset($component->user_vars["subcategory"]) ? " , " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_subcategory = " . $db->toSql($component->user_vars["subcategory"]) : ""
                                ) . "
                                        " . (isset($component->user_vars["subcategory_string"]) ? " , " . CM_TABLE_PREFIX . "mod_recruitment_cv.subcategory_string = " . $db->toSql($component->user_vars["subcategory_string"]) : ""
                                ) . "
                                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID = " . $db->toSql($component->key_fields["ID"]->getValue(), "Number");
                        $db->execute($sSQL);
                    }

                    break;
                case "update":
                    $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_recruitment_cv
                                SET " . CM_TABLE_PREFIX . "mod_recruitment_cv.smart_url = " . $db->toSql(ffCommon_url_rewrite($smart_url)) . "
                            WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID = " . $db->toSql($component->key_fields["ID"]->getValue(), "Number");
                    $db->execute($sSQL);


                    break;
                default:
            }
        }
    }
}

?>