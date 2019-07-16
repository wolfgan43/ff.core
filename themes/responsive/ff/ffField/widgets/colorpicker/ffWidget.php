<?php
// ----------------------------------------
//  		FRAMEWORK FORMS vAlpha
//		      PLUGIN DEFINITION (colorpicker)
//			   by Samuele Diella
// ----------------------------------------

class ffWidget_colorpicker extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)
	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_colorpicker";

	var $widget_deps	= array();

	var $libraries = array();
	
    var $js_deps = array(
    						"ff.ffField.colorpicker" => null
						);
    var $css_deps 		= array(
    					);

	// PRIVATE VARS

    /**
     * @var $tpl ffTemplate[]
     */
    private $tpl 			= null;


	function __construct(ffPage_base $oPage = null)
	{
		$this->get_defaults();
	}

	function prepare_template($id)
	{
		$this->tpl[$id] = ffTemplate::factory(__DIR__);
		$this->tpl[$id]->load_file($this->template_file, "main");
	}
	
	function process($id, &$value, ffField_base &$Field)
	{
		// THE REAL STUFF
		if ($Field->parent !== null && strlen($Field->parent[0]->getIDIF()))
		{
			$tpl_id = $Field->parent[0]->getIDIF();
			$prefix = $tpl_id . "_";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
			$this->tpl[$tpl_id]->set_var("component", $tpl_id);
			$this->tpl[$tpl_id]->set_var("container", $prefix);
			//$Field->parent[0]->processed_widgets[$prefix . $id] = "colorpicker";
		}
		else
		{
			$tpl_id = "main";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
		}
			
		$this->tpl[$tpl_id]->set_var("id", $id);

		if ($Field->contain_error && $Field->error_preserve) {
			$this->tpl[$tpl_id]->set_var("value", trim(ffCommon_specialchars($value->ori_value), "#"));
			$value_color = "#" . trim(strtolower($value->ori_value), "#");
		} else {
			$this->tpl[$tpl_id]->set_var("value", trim(ffCommon_specialchars($value->getValue($Field->get_app_type(), $Field->get_locale())), "#"));
			$value_color = "#" . trim(strtolower($value->getValue($Field->get_app_type(), $Field->get_locale())), "#");
		}
 
		$Field->framework_css["fixed_post_content"] = array(2);
		$Field->fixed_post_content ='<input id="' . $prefix . $id . '_color" type="color" value="' .  $value_color . '" onchange="ff.ffField.colorpicker.change(this, \'' . $container . $id . '\');" />';
		$Field->properties["onkeyup"] = "ff.ffField.colorpicker.change(this, '" . $container . $id . "_color');";
		$Field->properties["maxlength"] = '6';
		
		//$this->tpl[$tpl_id]->parse("SectBinding", true);
		
		return;
	}
	
	function get_component_headers($id)
	{
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
