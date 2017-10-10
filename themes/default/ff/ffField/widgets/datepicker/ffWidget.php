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
class ffWidget_datepicker extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)

	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_datepicker";
	
	var $widget_deps	= array();
    var $js_deps = array(
							  "jquery" 			=> null
							, "jquery.ui" 		=> null
							, "jquery.ui/i18n" 		=> "jquery.ui.datepicker-it.js"
						);		
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
                              "jquery.ui.datepicker"        => array(
                                      "file" => "jquery.ui.datepicker.css"
                                    , "path" => null
                                    , "rel" => "jquery.ui"
                                )
    					);
	// PRIVATE VARS
	
	var $oPage			= null;
	var $source_path	= null;
	var $style_path		= null;

	var $tpl			= null;

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
		$this->tpl[$tpl_id]->set_var("theme", $Field->parent_page[0]->theme);

		$this->tpl[$tpl_id]->set_var("id", $id);
		
		$this->tpl[$tpl_id]->set_var("lang", strtolower(substr(FF_LOCALE, 0, -1)));
		
		if ($Field->contain_error && $Field->error_preserve)
			$this->tpl[$tpl_id]->set_var("value", ffCommon_specialchars($value->ori_value));
		else
			$this->tpl[$tpl_id]->set_var("value", ffCommon_specialchars($value->getValue($Field->get_app_type(), $Field->get_locale())));
		$this->tpl[$tpl_id]->set_var("properties", $Field->getProperties());
		
		if (strlen($Field->class))
			$this->tpl[$tpl_id]->set_var("class", $Field->class);
		else
			$this->tpl[$tpl_id]->set_var("class", $this->class);
		
		if ($Field->get_app_type() == "DateTime")
		{
			$this->tpl[$tpl_id]->parse("SectDateTime", false);
			$this->tpl[$tpl_id]->set_var("SectDate", "");
		}
		else
		{
			$this->tpl[$tpl_id]->set_var("SectDateTime", "");
			$this->tpl[$tpl_id]->parse("SectDate", false);
		}
			
		$this->tpl[$tpl_id]->parse("SectBinding", true);
		return $Field->fixed_pre_content . $this->tpl[$tpl_id]->rpparse("SectControl", FALSE) . $Field->fixed_post_content;
	}
	
	function get_component_headers($id)
	{
		if ($this->oPage !== NULL) { //code for ff.js
			$this->oPage[0]->tplAddJs("jquery.ui.datepicker", "jquery.ui.datepicker-" . strtolower(substr(FF_LOCALE, 0, -1)) . ".js", FF_THEME_DIR . "/library/jquery.ui/i18n");
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
			$this->oPage[0]->tplAddJs("jquery.ui.datepicker", "jquery.ui.datepicker-" . strtolower(substr(FF_LOCALE, 0, -1)) . ".js", FF_THEME_DIR . "/library/jquery.ui/i18n");
			
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
