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




if($_REQUEST["importcsv"] == "continue") {
	$js = '
		jQuery(function() { 
			get_importcsv();
		});	

		function get_importcsv(res) {
			if(res === undefined || res["count"] != 0) {
				jQuery.getJSON("' . FF_SITE_PATH . $cm->oPage->page_path . "/step2?" . $cm->oPage->get_globals() . "importcsv=continue" . '", function(data) { 
					jQuery(".progress .count").text((res === undefined ? ' . get_session("importcsvlineprocessed") . ' : 0 ) + parseInt(jQuery(".progress .count").text()) + parseInt(data["count"])); 
					get_importcsv(data);
				}); 
			} else {
				window.location.href = res["url"];
			}
		}	
	';
	
	
	$cm->oPage->addContent('<div class="progress"><span class="count">' . get_session("importcsvlineprocessed") . '</span> / <span class="total">' . get_session("importcsvlinetotal") . '</span></div><script type="text/javascript">' . $js . '</script>');
	importcsv_open();
	importcsv_exec();
} else {
	set_session("importcsvlineprocessed", "0");
	set_session("importcsvpage", 1);
	
	importcsv_open();
	// ----------------------------------
	//  BREADCRUMB
	if(is_file(FF_DISK_PATH . "/themes/" . $cm->oPage->getTheme() . "/contents/importcsv/breadcrumb.html")) {
		$tpl_bread = ffTemplate::factory(FF_DISK_PATH . "/themes/" . $cm->oPage->getTheme() . "/contents/importcsv");
		$tpl_bread->load_file("breadcrumb.html", "main");
		$tpl_bread->set_var("site_path", FF_SITE_PATH);
		$tpl_bread->set_var("theme", $cm->oPage->getTheme());
		$tpl_bread->set_var("query_string", $_SERVER["QUERY_STRING"]);

		$tpl_bread->set_var("selected_2", "wizbread_selected");

		$cm->oPage->addContent($tpl_bread);
	}
	// ----------------------------------



	$arrData = get_importcsv_fields($globals->import_fields[0]);

	$oRecord = ffRecord::factory($cm->oPage);
	$oRecord->id = "ImportCSV";
	$oRecord->title = ffTemplate::_get_word_by_code("scelta-campi");
	$oRecord->description = ffTemplate::_get_word_by_code("wizcsv_step1_format_description");
	$oRecord->buttons_options["cancel"]["display"] = false;
	$oRecord->buttons_options["insert"]["display"] = false;
	$oRecord->skip_action = true;

	//$oRecord->buttons_options["insert"]["label"] = ffTemplate::_get_word_by_code("wizcsv_step3");
	//$oRecord->buttons_options["insert"]["class"] = "next";
	$oRecord->addEvent("on_do_action", "Step2_on_do_action");

	$oField = ffField::factory($cm->oPage);
	$oField->id = "skip_first_row";
	$oField->label = "Salta la prima riga";
	$oField->base_type = "Number";
	$oField->extended_type = "Boolean";
	$oField->control_type = "checkbox";
	$oField->checked_value = new ffData("1", "Number", FF_SYSTEM_LOCALE);
	$oField->unchecked_value = new ffData("0", "Number", FF_SYSTEM_LOCALE);
	$oField->default_value = new ffData($arrData["skip_first_col"], "Number", FF_SYSTEM_LOCALE);
	$oRecord->addContent($oField);

	$bt = ffButton::factory($cm->oPage);
	$bt->id = "step1";
	$bt->label = ffTemplate::_get_word_by_code("wizcsv_step1");
	$bt->class = "prev ";
	$bt->action_type = "gotourl";
	$bt->url = FF_SITE_PATH . $cm->oPage->page_path . "/step1?" . $_SERVER["QUERY_STRING"];
	$oRecord->addActionButton($bt);

	$bt = ffButton::factory($cm->oPage);
	$bt->id = "step3";
	$bt->label = ffTemplate::_get_word_by_code("wiz_csv_step3") . " (" . get_session("importcsvlinetotal") . ")";
	$bt->class = "next ";
	$bt->action_type = "submit";
	$bt->frmAction = "insert";
	$oRecord->addActionButton($bt);

	$email_validator = ffValidator::getInstance("email");

	for ($c = 0; $c < count($globals->import_fields[0]); $c++)
	{
		$oField = ffField::factory($cm->oPage);
		$oField->id = "field_" . $c;
		$oField->label = "Campo #" . ($c + 1);
		$oField->extended_type = "Selection";
		$oField->multi_pairs = $arrData["field"];
		$oField->multi_select_one_label = "Non importare";
		$oField->fixed_post_content = "<p>Esempi di contenuto: ";
		$is_field_email = false;
		for ($v = ((int) $arrData["skip_first_col"]); $v < (3 + ((int) $arrData["skip_first_col"])); $v++)
		{
			if (strlen($globals->import_fields[$v][$c]))
			{
				if (false === $email_validator->checkValue(new ffData($globals->import_fields[$v][$c]), "", array()))
					$is_field_email = true;
				if ($v > ((int) $arrData["skip_first_col"]))
					$oField->fixed_post_content .= ", ";
				$oField->fixed_post_content .= $globals->import_fields[$v][$c];
			}
		}

		if ($is_field_email) {
			$oField->default_value = new ffData("email");
		} else {
			$oField->default_value = new ffData(ffCommon_url_rewrite($globals->import_fields[0][$c]));
		}

		$oField->fixed_post_content .= "</p>";

		$oRecord->addContent($oField);
	}
	$cm->oPage->addContent($oRecord);
}
function Step2_on_do_action($oRecord, $frmAction)
{
	switch ($frmAction)
	{
		case "insert":
			foreach ($oRecord->form_fields as $key => $value)
			{
				if(strpos($key, "field_") !== false) {
					$csv_rel_field[] = $value->getValue();
				}
			}
			$res = importcsv_exec($csv_rel_field, $oRecord->form_fields["skip_first_row"]->getValue());
			if(strlen($res)) {
				$oRecord->tplDisplayError($res);
				return true;
			}		
	}
}