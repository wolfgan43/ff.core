<?php
// ----------------------------------------
//  		FRAMEWORK FORMS vBeta
//		      PLUGIN DEFINITION (checkgroup)
//			   by Samuele Diella
// ----------------------------------------

class ffWidget_checkgroup
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)
	var $template_file 	 = "ffWidget.html";
	
	var $class			= null;

	var $widget_deps	= array();
	
	var $libraries		= array();
	
    var $js_deps 		= array(
    						"ff.ffField.checkgroup"       => null
    					);
    var $css_deps 		= array();

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
        $oPage = ffPage::getInstance();

		// DO SOME CHECK..
		switch($Field->base_type)
		{
			case "Text":
				switch($Field->grouping_action)
				{
					case "concat":
						if ($Field->grouping_separator === NULL || !strlen($Field->grouping_separator))
							ffErrorHandler::raise("Invalid Grouping Separator with Grouping Action 'concat'", E_USER_ERROR, $this, get_defined_vars());
						if(is_array($Field->recordset)) {
							foreach ($Field->recordset as $tmp_key => $tmp_value)
							{
								if (strpos($tmp_value[0]->getValue(), $Field->grouping_separator) !== FALSE)
									ffErrorHandler::raise("Separator present in values", E_USER_ERROR, $this, get_defined_vars()); 
							}
							reset($Field->recordset);
						}
						break;
						
					default:
						ffErrorHandler::raise("Invalid Grouping Action with base_type 'Text'", E_USER_ERROR, $this, get_defined_vars());
				}
				break;
				
			default:
				ffErrorHandler::raise("Invalid Grouping with base_type different from 'Text'", E_USER_ERROR, $this, get_defined_vars());
		}
		
		// THE REAL STUFF
		if ($Field->parent !== null && strlen($Field->parent[0]->getIDIF()))
		{
			$tpl_id = $Field->parent[0]->getIDIF();
			$prefix = $tpl_id . "_";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
			$this->tpl[$tpl_id]->set_var("component", $tpl_id);
			$this->tpl[$tpl_id]->set_var("container", $prefix);
			//$Field->parent[0]->processed_widgets[$prefix . $id] = "checkgroup";
		}
		else
		{
			$tpl_id = "main";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
		}
			
		$this->tpl[$tpl_id]->set_var("id", $id);
		$this->tpl[$tpl_id]->set_var("separator", $Field->grouping_separator);

		$selected_values = explode($Field->grouping_separator, $value->getValue());

		if(!is_array($Field->properties))
			$Field->properties = array();

		$Field->properties["onchange"] = " ff.ffField.checkgroup.recalc('" . $prefix . $id . "', this); " . $Field->properties["onchange"];
		
		if (count($Field->recordset))
		{
			$this->tpl[$tpl_id]->set_var("SectRow", "");
			$i = 0;
            $data_filled = false;
			foreach ($Field->recordset as $tmp_key => $tmp_value)
			{
				$this->tpl[$tpl_id]->set_var("index", $i);
				$this->tpl[$tpl_id]->set_var("element_value", $tmp_value[0]->getValue());
				$this->tpl[$tpl_id]->set_var("label", ffCommon_specialchars($tmp_value[1]->getValue($Field->multi_app_type, FF_LOCALE)));
				
				$class = $this->class;
				$control_class = $Field->get_control_class("checkbox");
				if (in_array($tmp_value[0]->getValue(), $selected_values)) {
					$this->tpl[$tpl_id]->set_var("checked", "checked=\"checked\"");
					$class = $class . ($class ? " " : "") . "on";
                    $data_filled = true;
				} else {
					$this->tpl[$tpl_id]->set_var("checked", "");
					$class = $class . ($class ? " " : "") . "off";				
				}
				
				$class .= " " . $oPage->frameworkCSS->get("row-padding", "form");
				$class .= " checkbox";

				$this->tpl[$tpl_id]->set_var("class", $class);
                $this->tpl[$tpl_id]->set_var("control_class", $control_class);
				$this->tpl[$tpl_id]->set_var("properties", $Field->getProperties()); 
				
				$this->tpl[$tpl_id]->parse("SectRow", TRUE);
				$i++;
			}
			reset($Field->recordset);

			if($data_filled) {
				$this->tpl[$tpl_id]->set_var("value", ffCommon_specialchars($value->getValue()));
			} else {
				$this->tpl[$tpl_id]->set_var("value", "");
			}
			$this->tpl[$tpl_id]->set_var("length", $i);
			
			$this->tpl[$tpl_id]->parse("SectBinding", TRUE);
			return $this->tpl[$tpl_id]->rpparse("SectControl", FALSE);
		}
		else
		{
			return "No data to select";
		}

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
