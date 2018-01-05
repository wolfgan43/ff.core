<?php

class ffWidget_tabs extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)

	var $template_file 	 = "ffWidget.html";

	var $class			= "ffWidget_tabs";
             
	var $widget_deps	= array();
    var $js_deps = array(
							  "jquery" 			=> null
							, "jquery.ui" 		=> null
						);
    var $css_deps 		= array(
    						  "jquery.ui"		=> null
    					);
	// PRIVATE VARS

	var $source_path		= null;
	var $tpl 				= null;

	function __construct(ffPage_html $oPage = null, $source_path = null, $style_path = null)
	{
		$this->get_defaults();

		$this->tpl[0] = ffTemplate::factory(ffCommon_dirname(__FILE__));
		$this->tpl[0]->load_file($this->template_file, "main");

		if ($source_path !== null)
			$this->source_path = $source_path;
		elseif ($oPage !== null)
			$this->source_path = $oPage->getThemePath();

		$this->tpl[0]->set_var("source_path", $this->source_path);

        if ($this->style_path !== null)
			$this->tpl[0]->set_var("style_path", $style_path);
		elseif ($oPage !== null)
			$this->tpl[0]->set_var("style_path", $oPage->getThemePath());
	}

	function process($id, &$data, ffPage_html &$oPage)
	{
		$this->tpl[0]->set_var("site_path", $oPage->site_path);
		$this->tpl[0]->set_var("theme", $oPage->getTheme());

		$this->tpl[0]->set_var("id", $id);

		$this->tpl[0]->set_var("SectElementHead", "");
		$this->tpl[0]->set_var("SectElementBody", "");

		$i = 0;
		foreach ($data["contents"] as $subkey => $subvalue)
		{
			if (isset($subvalue["title"]))
				$title = $subvalue["title"];
			else
				$title = $subkey;

			$this->tpl[0]->set_var("element_count", $i);
			$this->tpl[0]->set_var("element_title", $title);
			$this->tpl[0]->set_var("element_content", $oPage->getContentData($subvalue["data"]));

			$this->tpl[0]->parse("SectElementHead", true);
			$this->tpl[0]->parse("SectElementBody", true);

			$i++;
		}

		$this->tpl[0]->parse("SectBinding", true);
		return $this->tpl[0]->rpparse("SectIstance", false);
	}

	function process_headers()
	{
		return $this->tpl[0]->rpparse("SectHeaders", false);
	}

	function process_footers()
	{
		return $this->tpl[0]->rpparse("SectFooters", false);
	}
}
