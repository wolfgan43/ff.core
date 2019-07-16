<?php
/**
 * VGallery: CMS based on FormsFramework
 * Copyright (C) 2004-2015 Alessandro Stucchi <wolfgan@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @package VGallery
 *  @subpackage core
 *  @author Alessandro Stucchi <wolfgan@gmail.com>
 *  @copyright Copyright (c) 2004, Alessandro Stucchi
 *  @license http://opensource.org/licenses/gpl-3.0.html
 *  @link https://github.com/wolfgan43/vgallery
 */

// ----------------------------------------
//  		FRAMEWORK FORMS vBeta
//		      PLUGIN DEFINITION (listgroup)
//			   by Samuele Diella
// ----------------------------------------

class ffWidget_listgroup extends ffCommon
{
	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)
	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_listgroup";

	var $widget_deps	= array();
	
	var $libraries		= array();
	
    var $js_deps	= array(
                              "ff.ffField.listgroup"       => null
		);
    var $css_deps 	= array(
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
			//$Field->parent[0]->processed_widgets[$prefix . $id] = "listgroup";
		}
		else
		{
			$tpl_id = "main";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
		}
			
		$this->tpl[$tpl_id]->set_var("id", $id);
        $this->tpl[$tpl_id]->set_var("add_class", cm_getClassByFrameworkCss("plus", "icon"));
        $this->tpl[$tpl_id]->set_var("remove_class", cm_getClassByFrameworkCss("minus", "icon"));

		$this->tpl[$tpl_id]->set_var("value", ffCommon_specialchars($value->getValue()));
		$this->tpl[$tpl_id]->set_var("separator", $Field->grouping_separator);
        
        $selected_values = explode($Field->grouping_separator, $value->getValue());
        if (count($selected_values))
        {
            $this->tpl[$tpl_id]->set_var("SectRow", "");
            foreach ($selected_values as $tmp_key => $tmp_value)
            {
                if(strlen($tmp_value)) {
                    $this->tpl[$tpl_id]->set_var("listgroup_value", $tmp_value);
                    $this->tpl[$tpl_id]->parse("SectRow", TRUE);
                }
            }
            reset($selected_values);
            
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
