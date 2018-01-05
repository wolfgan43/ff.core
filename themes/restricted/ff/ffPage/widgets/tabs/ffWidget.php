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
    var $css_deps 		= array(/*
                              "jquery.ui.core"        => array(
                                      "file" => "jquery.ui.core.css"
                                    , "path" => null
                                    , "rel" => "jquery.ui"
                                ), 
                              "jquery.ui.theme"        => array(
                                      "file" => "jquery.ui.theme.css"
                                    , "path" => null
                                    , "rel" => "jquery.ui"
                                ), 
                              "jquery.ui.tabs"        => array( 
                                      "file" => "jquery.ui.tabs.css"
                                    , "path" => null
                                    , "rel" => "jquery.ui"
                                )
    					*/);    
	// PRIVATE VARS
	var $oPage			= null;
	var $source_path	= null;
	var $style_path		= null;

	var $tpl 			= null;

	var $processed_id	= array();

	function __construct(ffPage_base $oPage = null, $source_path = null, $style_path = null)
	{
		$this->get_defaults();

		$this->oPage = array(&$oPage);

		if ($source_path !== null)
			$this->source_path = $source_path;
		elseif ($oPage !== null)
			$this->source_path = $oPage->getThemePath();

		$this->style_path = $style_path;
	}

	function prepare_template($id)
	{
		$this->tpl[$id] = ffTemplate::factory(ffCommon_dirname(__FILE__));
		$this->tpl[$id]->load_file($this->template_file, "main");

		$this->tpl[$id]->set_var("source_path", $this->source_path);

        if ($this->style_path !== null)
			$this->tpl[$id]->set_var("style_path", $this->style_path);
		elseif ($this->oPage !== null)
			$this->tpl[$id]->set_var("style_path", $this->oPage[0]->getThemePath());
	}

	function process($id, &$data, ffPage_base &$oPage, $component = null)
	{
		if ($component !== null)
		{
			$tpl_id = $component;
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
			$oPage->components[$component]->processed_widgets[$id] = "tabs";
		}
		else
		{
			$tpl_id = "main";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
		}

		$this->tpl[$tpl_id]->set_var("site_path", $oPage->site_path);
		$this->tpl[$tpl_id]->set_var("theme", $oPage->getTheme());

		$this->tpl[$tpl_id]->set_var("id", $id);

		$this->tpl[$tpl_id]->set_var("SectElementHead", "");
		$this->tpl[$tpl_id]->set_var("SectElementBody", "");

		$i = 0;
		foreach ($data["contents"] as $subkey => $subvalue)
		{
			if ($subvalue["data"] === null)
				continue;

			if (isset($subvalue["title"]))
				$title = $subvalue["title"];
			else if (is_object($subvalue["data"]))
			{
				if (
						$subvalue["data"] instanceof ffGrid_base
						|| $subvalue["data"] instanceof ffRecord_base
						|| $subvalue["data"] instanceof ffDetails_base
					)
					$title = $subvalue["data"]->title;
				if (
						$subvalue["data"] instanceof ffField_base
					)
					$title = $subvalue["data"]->label;
			}
			else
				$title = $key;

			$this->tpl[$tpl_id]->set_var("element_count", $i);
			$this->tpl[$tpl_id]->set_var("element_title", $title);
			$ret = $oPage->getContentData($subvalue["data"]);
			if (is_array($ret))
			{
				if ($oPage->isXHR())
				{
					$oPage->output_buffer["headers"] .= $ret["headers"];
					$oPage->output_buffer["footers"] .= $ret["footers"];
					$this->tpl[$tpl_id]->set_var("element_content", $ret["html"]);
				}
				else
				{
					$this->tpl[$tpl_id]->set_var("element_content", $ret["headers"] . $ret["html"] . $ret["footers"]);
				}
			}
			else
				$this->tpl[$tpl_id]->set_var("element_content", $ret);

			$this->tpl[$tpl_id]->parse("SectElementHead", true);
			$this->tpl[$tpl_id]->parse("SectElementBody", true);

			$i++;
		}

		$this->tpl[$tpl_id]->parse("SectBinding", true);
		return $this->tpl[$tpl_id]->rpparse("SectIstance", false);
	}

	function get_component_headers($id)
	{
		if ($this->oPage !== NULL) {//code for ff.js 
			$this->oPage[0]->tplAddJs("ff.history", "history.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("ff.ffPage.tabs", "tabs.js", FF_THEME_DIR . "/restricted/ff/ffPage/widgets/tabs");
		}			

		if (!isset($this->tpl[$id]))
			return;

		return $this->tpl[$id]->rpparse("SectHeaders", false);
	}

	function get_component_footers($id)
	{
		if (!isset($this->tpl[$id]))
			return;

		/*if(isset($_REQUEST["XHR_DIALOG_ID"])) 
		{
			$this->tpl[$id]->set_var("SectNoXHRDialogStart", "");
			$this->tpl[$id]->set_var("SectNoXHRDialogEnd", "");
		}
		else
		{
			$this->tpl[$id]->parse("SectNoXHRDialogStart", false);
			$this->tpl[$id]->parse("SectNoXHRDialogEnd", false);
		}*/

		return $this->tpl[$id]->rpparse("SectFooters", false);
	}

	function process_headers()
	{
		if ($this->oPage !== NULL) {//code for ff.js 
			$this->oPage[0]->tplAddJs("ff.history", "history.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("ff.ffPage.tabs", "tabs.js", FF_THEME_DIR . "/restricted/ff/ffPage/widgets/tabs");
			
			//return;
		}			
		
		if (!isset($this->tpl["main"]))
			return;

		return $this->tpl["main"]->rpparse("SectHeaders", false);
	}

	function process_footers()
	{
		if (!isset($this->tpl["main"]))
			return;
			
		/*if(isset($_REQUEST["XHR_DIALOG_ID"])) 
		{
			$this->tpl["main"]->set_var("SectNoXHRDialogStart", "");
			$this->tpl["main"]->set_var("SectNoXHRDialogEnd", "");
		}
		else
		{
			$this->tpl["main"]->parse("SectNoXHRDialogStart", false);
			$this->tpl["main"]->parse("SectNoXHRDialogEnd", false);
		}*/

		return $this->tpl["main"]->rpparse("SectFooters", false);
	}
}
