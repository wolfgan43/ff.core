<?php
if(mod_security_check_session(false))
{
    $permission = check_recruitment_permission();
    if($permission !== true && !(is_array($permission) && count($permission))) {
        ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
    }
}

$UserNID = get_session("UserNID");
$db = ffDB_Sql::factory();

if(strlen(basename($cm->real_path_info))) {
    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_cv
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_cv.smart_url = " . $db->toSql(basename($cm->real_path_info)) . "
                AND " . CM_TABLE_PREFIX . "mod_recruitment_cv.ID_user = " . $db->toSql($UserNID, "Number");
    $db->query($sSQL);
    if($db->nextRecord()) {
        $ID_cv = $db->getField("ID", "Number", true);
    }
}

if(!$ID_cv > 0) {
	ffRedirect($_REQUEST["ret_url"]);
}

$oRecord = ffRecord::factory($cm->oPage); 
$oRecord->id = "StudiesModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("recruitment_job_studies_title");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_recruitment_cv_formation";
$oRecord->insert_additional_fields["ID_cv"] =  $ID_cv;
$cm->oPage->tplAddJs("experience-modify", "experience-modify.js", "/modules/recruitment/themes/javascript", false, true);

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "in_corso";
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_in_corso");
$oField->container_class = "in-corso";
$oField->base_type = "Number";
$oField->extended_type = "Boolean";
$oField->control_type = "checkbox";
$oField->checked_value = new ffData("1", "Number");
$oField->unchecked_value = new ffData("0", "Number");
$oField->default_value = new ffData("1", "Number");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "data_fine";
$oField->widget = "datepicker";
$oField->container_class = "data-fine";
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_studies_end");
$oField->base_type = "Timestamp";
$oField->app_type = "Date";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "data_inizio";
$oField->widget = "datepicker";
$oField->container_class = "data-inizio";
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_studies_begin");
$oField->base_type = "Timestamp";
$oField->app_type = "Date";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "nome_scuola";
$oField->required = true;
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_school_nome_scuola");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "telefono";
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_school_telefono");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "email";
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_school_email");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "esito";
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_school_esito");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "descrizione";
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_school_descrizione");
$oField->extended_type = "Text";
$oField->base_type = "Text";
$oRecord->addContent($oField);

if(is_file(FF_DISK_PATH . "/themes/comune.info/css/recruitment.css"))
			$cm->oPage->tplAddCss("recruitment-css", "recruitment.css", "/themes/comune.info/css");
$cm->oPage->addContent($oRecord);