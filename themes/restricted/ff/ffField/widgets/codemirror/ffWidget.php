<?php
// ----------------------------------------
//  		FRAMEWORK FORMS vAlpha
//		      PLUGIN DEFINITION (tiny_mce)
//			   by Samuele Diella
// ----------------------------------------

class ffWidget_codemirror extends ffCommon
{

	
	
    // ---------------------------------------------------------------
    //  PRIVATE VARS (used by code, don't touch or may be explode! :-)

	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_codemirror";

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
		
		$Field->parent_page[0]->tpl[0]->minify = false;
		
		$this->tpl[$tpl_id]->set_var("id", $id);
		$this->tpl[$tpl_id]->set_var("site_path", $Field->parent_page[0]->site_path);
		$this->tpl[$tpl_id]->set_var("theme", $Field->getTheme());
		$this->tpl[$tpl_id]->set_var("class", $this->class);
		$this->tpl[$tpl_id]->set_var("properties", $Field->getProperties());

        if(strlen($Field->widget_path))
            $this->tpl[$tpl_id]->set_var("widget_path", $Field->widget_path);
        else 
            $this->tpl[$tpl_id]->set_var("widget_path", "/themes/restricted/ff/ffField/widgets/codemirror");
        
        if ($Field->contain_error && $Field->error_preserve)
            $this->tpl[$tpl_id]->set_var("value", ffCommon_specialchars($value->ori_value));
        else
            $this->tpl[$tpl_id]->set_var("value", ffCommon_specialchars($value->getValue($Field->get_app_type(), $Field->get_locale())));
        $this->tpl[$tpl_id]->set_var("properties", $Field->getProperties());

       
        $this->tpl[$tpl_id]->set_var("widget_class", $this->class);
        
        $this->tpl[$tpl_id]->set_var("widget_lang", strtolower(substr(FF_LOCALE, 0, -1)));
        
        
        $this->tpl[$tpl_id]->parse("SectBinding", true);

        return $this->tpl[$tpl_id]->rpparse("SectControl", FALSE);
    }
    
	function get_component_headers($id)
	{
		if ($this->oPage !== NULL) { //code for ff.js
			$this->oPage[0]->tplAddCss("codemirror", "codemirror.css", FF_THEME_DIR . "/library/codemirror/lib", false, false, null, true);
			$this->oPage[0]->tplAddCss("showhint", "show-hint.css", FF_THEME_DIR . "/library/codemirror/addon/hint", false, false, null, true);
                        $this->oPage[0]->tplAddCss("Dialog", "dialog.css", FF_THEME_DIR . "/library/codemirror/addon/dialog", false, false, null, true);
                        $this->oPage[0]->tplAddJs("CodeMirror", "codemirror.js", FF_THEME_DIR . "/library/codemirror/lib", false, false, null, true);
                        $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("ff.ffField.codemirror", "codemirror.js", FF_THEME_DIR . "/restricted/ff/ffField/widgets/codemirror");
			$this->oPage[0]->tplAddJs("closebrackets", "closebrackets.js", FF_THEME_DIR . "/library/codemirror/addon/edit", false, false, null, true);
			$this->oPage[0]->tplAddJs("active-line", "active-line.js", FF_THEME_DIR . "/library/codemirror/addon/selection", false, false, null, true);
                        $this->oPage[0]->tplAddJs("dialog", "dialog.js", FF_THEME_DIR . "/library/codemirror/addon/dialog", false, false, null, true);
			$this->oPage[0]->tplAddJs("show-hint", "show-hint.js", FF_THEME_DIR . "/library/codemirror/addon/hint", false, false, null, true);
			$this->oPage[0]->tplAddJs("javascript-hint", "javascript-hint.js", FF_THEME_DIR . "/library/codemirror/addon/hint", false, false, null, true);
                        $this->oPage[0]->tplAddJs("search", "search.js", FF_THEME_DIR . "/library/codemirror/addon/search", false, false, null, true);
			$this->oPage[0]->tplAddJs("search-cursor", "searchcursor.js", FF_THEME_DIR . "/library/codemirror/addon/search", false, false, null, true);
			$this->oPage[0]->tplAddJs("javascript", "javascript.js", FF_THEME_DIR . "/library/codemirror/mode/javascript", false, false, null, true);
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
			$this->oPage[0]->tplAddCss("codemirror", "codemirror.css", FF_THEME_DIR . "/library/codemirror/lib", false, false, null, true); 
			$this->oPage[0]->tplAddCss("showhint", "show-hint.css", FF_THEME_DIR . "/library/codemirror/addon/hint", false, false, null, true);
                        $this->oPage[0]->tplAddCss("Dialog", "dialog.css", FF_THEME_DIR . "/library/codemirror/addon/dialog", false, false, null, true);
                        $this->oPage[0]->tplAddJs("CodeMirror", "codemirror.js", FF_THEME_DIR . "/library/codemirror/lib", false, false, null, true);
                        $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("closebrackets", "closebrackets.js", FF_THEME_DIR . "/library/codemirror/addon/edit", false, false, null, true);
			$this->oPage[0]->tplAddJs("active-line", "active-line.js", FF_THEME_DIR . "/library/codemirror/addon/selection", false, false, null, true);
                        $this->oPage[0]->tplAddJs("dialog", "dialog.js", FF_THEME_DIR . "/library/codemirror/addon/dialog", false, false, null, true);
			$this->oPage[0]->tplAddJs("show-hint", "show-hint.js", FF_THEME_DIR . "/library/codemirror/addon/hint", false, false, null, true);
			$this->oPage[0]->tplAddJs("javascript-hint", "javascript-hint.js", FF_THEME_DIR . "/library/codemirror/addon/hint", false, false, null, true);
                        $this->oPage[0]->tplAddJs("search", "search.js", FF_THEME_DIR . "/library/codemirror/addon/search", false, false, null, true);
			$this->oPage[0]->tplAddJs("search-cursor", "searchcursor.js", FF_THEME_DIR . "/library/codemirror/addon/search", false, false, null, true);
			$this->oPage[0]->tplAddJs("javascript", "javascript.js", FF_THEME_DIR . "/library/codemirror/mode/javascript", false, false, null, true);
			$this->oPage[0]->tplAddJs("ff.ffField.codemirror", "codemirror.js", FF_THEME_DIR . "/restricted/ff/ffField/widgets/codemirror");
			
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