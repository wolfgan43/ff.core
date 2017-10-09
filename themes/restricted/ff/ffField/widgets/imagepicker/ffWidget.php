<?php
// ----------------------------------------
//  		FRAMEWORK FORMS vAlpha
//		      PLUGIN DEFINITION (imagepicker)
//			   by Samuele Diella
// ----------------------------------------

class ffWidget_imagepicker extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)

	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_imagepicker";
	
	var $widget_deps	= array();
    var $js_deps = array(
							  "jquery" 			=> null
						);		
    var $css_deps 		= array();
	// PRIVATE VARS
	
	var $oPage			= null;
	var $source_path	= null;
	var $style_path		= null;

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

		$this->tpl[$id]->set_var("source_path", $this->source_path);

        if ($this->style_path !== null)
			$this->tpl[$id]->set_var("style_path", $this->style_path);
		elseif ($this->oPage !== null)
			$this->tpl[$id]->set_var("style_path", $this->oPage[0]->getThemePath());
	}

	function process($id, &$value, ffField_html &$Field)
	{
		if ($Field->parent !== null && strlen($Field->parent[0]->id))
		{
			$tpl_id = $Field->parent[0]->id;
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
			$this->tpl[$tpl_id]->set_var("container", $Field->parent[0]->id . "_");
		}
		else
		{
			$tpl_id = "main";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
		}
		
		$this->tpl[$tpl_id]->set_var("site_path", $Field->parent_page[0]->site_path);
		$this->tpl[$tpl_id]->set_var("theme", $Field->getTheme());

        if(strlen($Field->widget_path))
            $this->tpl[$tpl_id]->set_var("widget_path", $Field->widget_path);
        else 
            $this->tpl[$tpl_id]->set_var("widget_path", "/themes/restricted/ff/ffField/widgets/imagepicker");
		
		$this->tpl[$tpl_id]->set_var("imagepicker_field", str_replace("[", '\\\\[', str_replace("]", '\\\\]', $id)));
		if(strpos($id, "[") === false) {
			$this->tpl[$tpl_id]->set_var("title_field", $Field->imagepicker_title_field);
		} else {
			$this->tpl[$tpl_id]->set_var("title_field", str_replace("[", '\\\\[', str_replace("]", '\\\\]', str_replace("[" . $Field->id . "]", "", $id) . "[" . $Field->imagepicker_title_field . "]")));
		}
		$this->tpl[$tpl_id]->parse("SectBinding", true);
		return null;
	}
	
	function get_component_headers($id)
	{
		if ($this->oPage !== NULL) { //code for ff.js
                    $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
                    $this->oPage[0]->tplAddCss("image-picker-css", "jquery.image-picker.css", FF_THEME_DIR . "/library/plugins/jquery.image-picker"); 
                    $this->oPage[0]->tplAddJs("image-picker","jquery.image-picker.min.js",FF_THEME_DIR . "/library/plugins/jquery.image-picker");
                    $this->oPage[0]->tplAddJs("ff.ffField.imagepicker", "imagepicker.js", FF_THEME_DIR . "/restricted/ff/ffField/widgets/imagepicker");
		
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
            $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
            $this->oPage[0]->tplAddCss("image-picker-css", "jquery.image-picker.css", FF_THEME_DIR . "/library/plugins/jquery.image-picker"); 
                    $this->oPage[0]->tplAddJs("image-picker","jquery.image-picker.min.js",FF_THEME_DIR . "/library/plugins/jquery.image-picker");
                    $this->oPage[0]->tplAddJs("ff.ffField.imagepicker", "imagepicker.js", FF_THEME_DIR . "/restricted/ff/ffField/widgets/imagepicker");
			
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
