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

$arrLangLevel = array (
                        array(new ffData("A1"), new ffData(ffTemplate::_get_word_by_code("A1"))),
                        array(new ffData("A2"), new ffData(ffTemplate::_get_word_by_code("A2"))),
                        array(new ffData("B1"), new ffData(ffTemplate::_get_word_by_code("B1"))),
                        array(new ffData("B2"), new ffData(ffTemplate::_get_word_by_code("B2"))),
                        array(new ffData("C1"), new ffData(ffTemplate::_get_word_by_code("C1"))),
                        array(new ffData("C2"), new ffData(ffTemplate::_get_word_by_code("C2")))
                );

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
$oRecord->id = "LangModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("recruitment_job_lang_title");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_recruitment_cv_lang";
$oRecord->insert_additional_fields["ID_cv"] =  $ID_cv;

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "lang_name";
$oField->required = true;
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_name");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "reading";
$oField->extended_type = "Selection";
$oField->multi_pairs = $arrLangLevel;
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_reading");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "listening";
$oField->extended_type = "Selection";
$oField->multi_pairs = $arrLangLevel;
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_listening");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "speaking";
$oField->extended_type = "Selection";
$oField->multi_pairs = $arrLangLevel;
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_speaking");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "writing";
$oField->extended_type = "Selection";
$oField->multi_pairs = $arrLangLevel;
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_writing");
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "certification";
$oField->extended_type = "Text";
$oField->label = ffTemplate::_get_word_by_code("recruitment_experience_lang_certification");
$oRecord->addContent($oField);

if(is_file(FF_DISK_PATH . "/themes/comune.info/css/recruitment.css"))
			$cm->oPage->tplAddCss("recruitment-css", "recruitment.css", "/themes/comune.info/css");

$cm->oPage->addContent($oRecord);