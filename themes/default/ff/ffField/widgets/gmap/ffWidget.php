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
class ffWidget_gmap
{
	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)
	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_gmap";

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

		$this->tpl[$tpl_id]->set_var("key", $Field->gmap_key);

		if ($Field->gmap_draggable)
			$this->tpl[$tpl_id]->set_var("draggable", "true");
		else
			$this->tpl[$tpl_id]->set_var("draggable", "false");
         
		$know_point = true;

		if (!strlen($value["lat"]->ori_value))
		{
			$this->tpl[$tpl_id]->set_var("start_lat", $Field->gmap_start_lat);
			if(!$Field->gmap_start_lat)
				$know_point = false;
		}
		else
			$this->tpl[$tpl_id]->set_var("start_lat", $value["lat"]->getValue());

		if (!strlen($value["lng"]->ori_value))
		{
			$this->tpl[$tpl_id]->set_var("start_lng", $Field->gmap_start_lng);
			if(!$Field->gmap_start_lng)
				$know_point = false;
		}
		else
			$this->tpl[$tpl_id]->set_var("start_lng", $value["lng"]->getValue());

        if (!strlen($value["title"]->ori_value))
        {
        	if(strlen($value["title"]->getValue())) {
            	$this->tpl[$tpl_id]->set_var("title", $value["title"]->getValue());
            	$this->tpl[$tpl_id]->set_var("force_search", "true");
			} else {
            	$this->tpl[$tpl_id]->set_var("title", "");
            	$this->tpl[$tpl_id]->set_var("force_search", "false");
			}
        }
        else
        {
            $this->tpl[$tpl_id]->set_var("title", $value["title"]->getValue());
            $this->tpl[$tpl_id]->set_var("force_search", "false");
        }

		if (!strlen($value["zoom"]->ori_value))
			$this->tpl[$tpl_id]->set_var("start_zoom", $Field->gmap_start_zoom);
		else
			$this->tpl[$tpl_id]->set_var("start_zoom", $value["zoom"]->getValue());

		$this->tpl[$tpl_id]->set_var("set_marker", "false");
		if ($know_point)
			$this->tpl[$tpl_id]->set_var("set_marker", "true");

		$this->tpl[$tpl_id]->parse("SectBinding", true);

		return $this->tpl[$tpl_id]->rpparse("SectControl", false);
	}

	function get_component_headers($id)
	{
		if ($this->oPage !== NULL) { //code for ff.js
            $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("ff.ffField.gmap", "gmap.js", FF_THEME_DIR . "/default/ff/ffField/widgets/gmap");
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
				$this->oPage[0]->tplAddJs("ff.ffField.gmap", "gmap.js", FF_THEME_DIR . "/default/ff/ffField/widgets/gmap");
				
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
	
	function init($Field)
	{
		if (!is_array($Field[0]->multi_fields) || !count($Field[0]->multi_fields))
			$Field[0]->multi_fields = array(
					"lat" 		=> array("type" => "Text")
					, "lng" 	=> array("type" => "Text")
					, "title" 	=> array("type" => "Text")
					, "zoom" 	=> array("type" => "Text")
				);
	}
}
