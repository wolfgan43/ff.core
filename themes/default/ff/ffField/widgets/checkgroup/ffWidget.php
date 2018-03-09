<?php
/**
 * @package theme_default
 * @subpackage widgets
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

/**
 * @package theme_default
 * @subpackage widgets
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */
class ffWidget_checkgroup
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)
	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_checkgroup";

	var $widget_deps	= array();
    var $js_deps = array();
    var $css_deps 		= array();

	// PRIVATE VARS
	
	var $tpl 			= null;
	var $db				= null;

	var $oPage = null;
	var $source_path	= null;
	var $style_path = null;
	
	
	function __construct(ffPage_base $oPage = null, $source_path = null, $style_path = null)
	{
		//$this->get_defaults();

		$this->oPage = array(&$oPage);
		
		if ($source_path !== null)
			$this->source_path = $source_path;
		elseif ($oPage !== null)
			$this->source_path = $oPage->getThemePath();

		$this->style_path = $style_path;
		
		$this->db[0] = ffDB_Sql::factory();

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
	
	function process($id, &$value, ffField_base &$Field)
	{
		// DO SOME CHECK..
		switch($Field->base_type)
		{
			case "Text":
				switch($Field->grouping_action)
				{
					case "concat":
						if ($Field->grouping_separator === NULL || !strlen($Field->grouping_separator))
							ffErrorHandler::raise("Invalid Grouping Separator with Grouping Action 'concat'", E_USER_ERROR, $this, get_defined_vars());
						
						foreach ($Field->recordset as $tmp_key => $tmp_value)
						{
							if (strpos($tmp_value[0]->getValue(), $Field->grouping_separator) !== FALSE)
								ffErrorHandler::raise("Separator present in values", E_USER_ERROR, $this, get_defined_vars()); 
						}
						reset($Field->recordset);
						break;
						
					default:
						ffErrorHandler::raise("Invalid Grouping Action with base_type 'Text'", E_USER_ERROR, $this, get_defined_vars());
				}
				break;
				
			default:
				ffErrorHandler::raise("Invalid Grouping with base_type different from 'Text'", E_USER_ERROR, $this, get_defined_vars());
		}
		
		// THE REAL STUFF
		if ($Field->parent !== null && strlen($Field->parent[0]->id))
		{
			$tpl_id = $Field->parent[0]->id;
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
			$this->tpl[$tpl_id]->set_var("container", $Field->parent[0]->id . "_");
			$prefix = $Field->parent[0]->id . "_";
		}
		else
		{
			$tpl_id = "main";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
		}
			
		$this->tpl[$tpl_id]->set_var("id", $id);
		$this->tpl[$tpl_id]->set_var("site_path", $Field->parent_page[0]->site_path);
		$this->tpl[$tpl_id]->set_var("theme", $Field->getTheme());
		$this->tpl[$tpl_id]->set_var("class", $this->class);
		$this->tpl[$tpl_id]->set_var("properties", $Field->getProperties());
		$this->tpl[$tpl_id]->set_var("value", ffCommon_specialchars($value->getValue()));			
		$this->tpl[$tpl_id]->set_var("separator", $Field->grouping_separator);

		$selected_values = explode($Field->grouping_separator, $value->getValue());
		
		if (count($Field->recordset))
		{
			$this->tpl[$tpl_id]->set_var("SectRow", "");
			$i = 0;
			foreach ($Field->recordset as $tmp_key => $tmp_value)
			{
				$this->tpl[$tpl_id]->set_var("index", $i);
				$this->tpl[$tpl_id]->set_var("element_value", $tmp_value[0]->getValue());
				$this->tpl[$tpl_id]->set_var("label", ffCommon_specialchars($tmp_value[1]->getValue($Field->multi_app_type, FF_LOCALE)));

				if (in_array($tmp_value[0]->getValue(), $selected_values))
					$this->tpl[$tpl_id]->set_var("checked", "checked=\"checked\"");
				else
					$this->tpl[$tpl_id]->set_var("checked", "");

				$this->tpl[$tpl_id]->parse("SectRow", TRUE);
				$i++;
			}
			reset($Field->recordset);
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
		if ($this->oPage !== NULL) { //code for ff.js
            $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("ff.ffField.checkgroup", "checkgroup.js", FF_THEME_DIR . "/default/ff/ffField/widgets/checkgroup");
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
				$this->oPage[0]->tplAddJs("ff.ffField.checkgroup", "checkgroup.js", FF_THEME_DIR . "/default/ff/ffField/widgets/checkgroup");
				
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
