<?php
require_once(ffCommon_dirname(__FILE__) . "/common." . FF_PHP_EXT);
// -------------------------
// check key & form url
$globals = ffGlobals::getInstance("wizard");
$globals->var_prefix = "wizcsv_";

$globals->transit_params = $cm->oPage->get_globals() . "ret_url=" . rawurlencode($_REQUEST["ret_url"]);
// -------------------------

$cm->oPage->form_method = "POST";
$db = ffDB_Sql::factory();

// ----------------------------------
//  BREADCRUMB
if(is_file(FF_DISK_PATH . "/themes/" . $cm->oPage->getTheme() . "/contents/importcsv/breadcrumb.html")) {
	$tpl_bread = ffTemplate::factory(FF_DISK_PATH . "/themes/" . $cm->oPage->getTheme() . "/contents/importcsv");
	$tpl_bread->load_file("breadcrumb.html", "main");
	$tpl_bread->set_var("site_path", FF_SITE_PATH);
	$tpl_bread->set_var("theme", $cm->oPage->getTheme());
	$tpl_bread->set_var("query_string", $_SERVER["QUERY_STRING"]);

	$tpl_bread->set_var("selected_1", "wizbread_selected");

	$cm->oPage->addContent($tpl_bread);
}
// ----------------------------------

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "ImportCSV";
$oRecord->title = ffTemplate::_get_word_by_code("import-csv");
$oRecord->description = ffTemplate::_get_word_by_code("wizcsv_step1_format_description");
$oRecord->addEvent("on_do_action", "ImportCSV_on_do_action");
$oRecord->buttons_options["cancel"]["display"] = false;
$oRecord->buttons_options["insert"]["label"] = ffTemplate::_get_word_by_code("wizcsv_step2");

$oField = ffField::factory($cm->oPage);
$oField->id = "upload";
$oField->label  = "Upload";
$oField->required = true;
$oField->file_show_delete = false;

$oField->extended_type = "File";
$oField->control_type = "file";
$oField->file_temp_path = FF_DISK_PATH . "/uploads/importcsv";
$oField->file_storing_path = FF_DISK_PATH . "/uploads/importcsv";
$oField->file_full_path = false;
$oField->file_check_exist = false;
$oField->file_saved_view_url		= FF_SITE_PATH . "/cm/showfiles." . FF_PHP_EXT . "/uploads/importcsv/[_FILENAME_]";
$oField->file_saved_preview_url		= FF_SITE_PATH . "/cm/showfiles." . FF_PHP_EXT . "/uploads/importcsv/thumb/[_FILENAME_]";
$oField->file_temp_view_url			= FF_SITE_PATH . "/cm/showfiles." . FF_PHP_EXT . "/uploads/importcsv/[_FILENAME_]";
$oField->file_temp_preview_url		= FF_SITE_PATH . "/cm/showfiles." . FF_PHP_EXT . "/uploads/importcsv/thumb[_FILENAME_]";
$oField->widget = "uploadify";
$oField->file_max_size = "10000000";
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->id = "sep_field";
$oField->label  = "Campo delimitato da";
$oField->extended_type = "Selection";
$oField->multi_pairs = array(
                                  array( new ffData("\t"),  new ffData("Tabulazione"))
                                , array( new ffData(";"),  new ffData("Punto e virgola"))
                                , array( new ffData(","),  new ffData("virgola"))
                            );
$oField->default_value = new ffData("\t");
$oField->required = true;
$oRecord->addContent($oField);


$cm->oPage->addContent($oRecord);

function ImportCSV_on_do_action($oRecord, $frmAction)
{
	if (strlen($frmAction))
	{
		$cm = cm::getInstance();

		$tmpfile = $oRecord->form_fields["upload"]->file_tmpname;

		if (ffMimeType(FF_DISK_PATH . "/uploads/importcsv/" . $tmpfile) != "text/plain"
			&& strpos(ffMimeType(FF_DISK_PATH . "/uploads/importcsv/" . $tmpfile), "text/x-c") === false
		) {
			
			$oRecord->strError = "Il tipo del file non è corretto, sono permessi solo file testo";
			return true;
		}

		set_session("importcsv", $oRecord->form_fields["upload"]->file_tmpname);
        set_session("importcsvsep", $oRecord->form_fields["sep_field"]->getValue());
        set_session("importcsvpage", 1);
        set_session("importcsvlimit", 10);
        set_session("importcsvref", time());
        set_session("importcsvlinetotal", 0);
        set_session("importcsvlineprocessed", 0);

		ffRedirect(FF_SITE_PATH . $cm->oPage->page_path . "/step2?" . $cm->oPage->get_globals());
	}
}
