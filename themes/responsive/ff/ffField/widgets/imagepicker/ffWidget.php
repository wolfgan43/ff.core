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
	
	var $libraries = array();
	
    var $js_deps = array(
    						"ff.ffField.imagepicker" => null
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

	function process($id, &$value, ffField_html &$Field)
	{
		if ($Field->parent !== null && strlen($Field->parent[0]->getIDIF()))
		{
			$tpl_id = $Field->parent[0]->getIDIF();
			$prefix = $tpl_id . "_";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
			$this->tpl[$tpl_id]->set_var("component", $tpl_id);
			$this->tpl[$tpl_id]->set_var("container", $prefix);
			//$Field->parent[0]->processed_widgets[$prefix . $id] = "imagepicker";
		}
		else
		{
			$tpl_id = "main";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
		}
		
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
