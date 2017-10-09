<?php
/**
 * @package theme_default
 * @subpackage widgets
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

/**
 * @package theme_default
 * @subpackage widgets
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */
class ffWidget_ckeditor extends ffCommon
{

	
	
    // ---------------------------------------------------------------
    //  PRIVATE VARS (used by code, don't touch or may be explode! :-)

	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_ckeditor";

	var $widget_deps	= array();
    var $js_deps 		= array();
    var $css_deps 		= array();

	// PRIVATE VARS
	
	var $tpl 			= null;
	var $db				= null;

	var $oPage = null;
	var $source_path	= null;
	var $style_path 	= null;

	function __construct(ffPage_base $oPage = null, $source_path = null, $style_path = null)
	{
		//$this->get_defaults();

		$this->oPage = array(&$oPage);
		
		if ($source_path !== null)
			$this->source_path = $source_path;
		elseif ($oPage !== null)
			$this->source_path = $oPage->getThemePath();

		$this->style_path = $style_path;
		
		$this->db[0] = ffDb_Sql::factory();

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
        
        if ($Field->contain_error && $Field->error_preserve)
            $this->tpl[$tpl_id]->set_var("value", ffCommon_specialchars($value->ori_value));
        else
            $this->tpl[$tpl_id]->set_var("value", ffCommon_specialchars($value->getValue($Field->get_app_type(), $Field->get_locale())));
        $this->tpl[$tpl_id]->set_var("properties", $Field->getProperties());

        if($Field->ckeditor_group_by_auth && MOD_SEC_GROUPS) {
            $user_permission = get_session("user_permission");
            $this->tpl[$tpl_id]->set_var("widget_group", preg_replace('/[^a-zA-Z0-9]/', '', strtolower($user_permission["primary_gid_name"])));
        } else {
            $this->tpl[$tpl_id]->set_var("widget_group", $Field->ckeditor_group);
        }

        $this->tpl[$tpl_id]->set_var("widget_class", $this->class);
        $this->tpl[$tpl_id]->set_var("widget_theme", $Field->ckeditor_theme);
        $this->tpl[$tpl_id]->set_var("widget_skin", $Field->ckeditor_skin);
        $this->tpl[$tpl_id]->set_var("widget_lang", strtolower(substr(FF_LOCALE, 0, -1)));
        
        $this->tpl[$tpl_id]->set_var("SectBinding", "");

        return $this->tpl[$tpl_id]->rpparse("SectControl", FALSE);
    }
    
	function get_component_headers($id)
	{
        if ($this->oPage !== NULL) { //code for ff.js
            $this->oPage[0]->tplAddJs("ff.lib.ckeditor", "ckeditor.js", FF_THEME_DIR . "/library/ckeditor", false, false, null, true);
            $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
            $this->oPage[0]->tplAddJs("ff.ffField.ckeditor", "ckeditor.js", FF_THEME_DIR . "/restricted/ff/ffField/widgets/ckeditor");
        }
        
		if (!isset($this->tpl[$id]))
			return;

		return $this->tpl[$id]->rpparse("SectHeaders", false);
	}
	
	function get_component_footers($id)
	{
		//ffErrorHandler::raise("sad", E_USER_ERROR, null, get_defined_vars());
		if (!isset($this->tpl[$id]))
			return;

		return $this->tpl[$id]->rpparse("SectFooters", false);
	}
	
	function process_headers()
	{
        if ($this->oPage !== NULL) { //code for ff.js
            $this->oPage[0]->tplAddJs("ff.lib.ckeditor", "ckeditor.js", FF_THEME_DIR . "/library/ckeditor", false, false, null, true);
            $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
            $this->oPage[0]->tplAddJs("ff.ffField.ckeditor", "ckeditor.js", FF_THEME_DIR . "/restricted/ff/ffField/widgets/ckeditor");
            
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
