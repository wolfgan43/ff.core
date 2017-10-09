<?php

class ffWidget_fullclick extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)

	var $template_file 	 = "ffWidget.html";

	var $class			= "ffWidget_fullclick";

	var $widget_deps	= array();
    var $js_deps = array(
							  "jquery" 			=> null
						);
	var $css_deps = array();
	// PRIVATE VARS

	var $source_path		= null;
	var $oPage				= null;
	var $style_path			= null;
	
	var $tpl 				= null;

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

		$this->tpl[$id]->set_var("site_path", $this->oPage[0]->site_path);
		$this->tpl[$id]->set_var("theme", $this->oPage[0]->getTheme());

		$this->tpl[$id]->set_var("source_path", $this->source_path);

		if ($style_path !== null)
			$this->tpl[$id]->set_var("style_path", $this->style_path);
		elseif ($this->oPage !== null)
			$this->tpl[$id]->set_var("style_path", $this->oPage[0]->getThemePath());
	}

	function process(ffGrid_base $grid)
	{
		$tpl_id = $grid->id;
		if (!isset($this->tpl[$tpl_id]))
			$this->prepare_template($tpl_id);
		
		$this->tpl[$tpl_id]->set_var("component", $grid->id);
		$this->tpl[$tpl_id]->parse("SectIstance", true);
	}

	function get_component_headers($id)
	{
		if ($this->oPage !== NULL) { //code for ff.js
			$this->oPage[0]->tplAddJs("ff.ffGrid.fullclick", "fullclick.js", FF_THEME_DIR . "/responsive/ff/ffGrid/widgets/fullclick"); 
		}

		if (!isset($this->tpl[$id]))
			return;

		return $this->tpl[$id]->rpparse("SectHeaders", false);
	}

	function get_component_footers($id)
	{
		if (!isset($this->tpl[$id]))
			return;

		return $this->tpl[$id]->rpparse("SectFooters", false);
	}

	function process_headers()
	{
		if ($this->oPage !== NULL) { //code for ff.js
			$this->oPage[0]->tplAddJs("ff.ffGrid.fullclick", "fullclick.js", FF_THEME_DIR . "/responsive/ff/ffGrid/widgets/fullclick");
			
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

		return $this->tpl["main"]->rpparse("SectFooters", false);
	}
}
