<?php

class ffWidget_activebuttons extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)

	var $template_file 	 = "ffWidget.html";

	var $class			= "ffWidget_activebuttons";

	var $widget_deps	= array();
    var $js_deps = array(
							  "jquery" 			=> null
							/*, "jquery.ui" 		=> null*/
						);
    //var $css_deps 		= array();
    var $css_deps 		= array();
    /*
    var $css_deps 		= array(
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
                              "jquery.ui.button"        => array(
                                      "file" => "jquery.ui.button.css"
                                    , "path" => null
                                    , "rel" => "jquery.ui"
                                )
    					);*/
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
		
		$this->tpl[0] = ffTemplate::factory(ffCommon_dirname(__FILE__));
		$this->tpl[0]->load_file($this->template_file, "main");

		$this->tpl[0]->set_var("site_path", FF_SITE_PATH);

		if ($source_path !== null)
			$this->source_path = $source_path;
		elseif ($oPage !== null)
			$this->source_path = $oPage->getThemePath();

		$this->tpl[0]->set_var("source_path", $this->source_path);

		if ($style_path !== null)
			$this->tpl[0]->set_var("style_path", $style_path);
		elseif ($oPage !== null)
			$this->tpl[0]->set_var("style_path", $oPage->getThemePath());
	}

	function process($id, &$data, ffPage_base &$oPage)
	{
	}

	function get_component_headers($id)
	{
		if ($this->oPage !== NULL) {//code for ff.js 
			$this->oPage[0]->tplAddJs("ff.ffPage.activebuttons", "activebuttons.js", FF_THEME_DIR . "/responsive/ff/ffPage/widgets/activebuttons"); 
		}			

		if (!isset($this->tpl[$id])) {
			$id = 0;
		}
		if (!isset($this->tpl[$id]))
			return;

		return $this->tpl[$id]->rpparse("SectHeaders", false);
	}

	function get_component_footers($id)
	{
		if (!isset($this->tpl[$id])) {
			$id = 0;
		}
		if (!isset($this->tpl[$id]))
			return;
		
		$this->tpl[$id]->set_var("spinner_class", cm_getClassByFrameworkCss("spinner", "icon", "spin"));
		
		return $this->tpl[$id]->rpparse("SectFooters", false);
	}

	function process_headers()
	{
		if ($this->oPage !== NULL) {//code for ff.js 
			$this->oPage[0]->tplAddJs("ff.ffPage.activebuttons", "activebuttons.js", FF_THEME_DIR . "/responsive/ff/ffPage/widgets/activebuttons");
			
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
		$this->tpl["main"]->set_var("spinner_class", cm_getClassByFrameworkCss("spinner", "icon"));
		
		return $this->tpl["main"]->rpparse("SectFooters", false);
	}
}
