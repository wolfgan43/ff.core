<?php
// ----------------------------------------
//  		FRAMEWORK FORMS vAlpha
//		      PLUGIN DEFINITION (jscalendar)
//			   by Samuele Diella
// ----------------------------------------

class ffWidget_uploadify extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)
	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_uploadify";

	var $widget_deps	= array();
    var $js_deps = array(
							  "jquery"						=> null
							, "swfobject"					=> null
							, "jquery.uploadify"				=> "/plugins/jquery.uploadify"
						);
    var $css_deps 		= array(
    						  "jquery.uploadify"		=> "/plugins/jquery.uploadify/uploadify.css"
    					);

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
		//$Field->parent_page[0]->tplAddCss("jquery.uploadify", "uploadify.css", FF_SITE_PATH	 . "/themes/library/plugins/jquery.uploadify");
		
		switch(strtolower($Field->control_type))
		{
			case "picture":
			case "picture_no_link":
				//$this->process_picture($id, $value);
				//break;
			case "file_label":
			case "file":
				$Field->process_file($id, $value);
				$suffix_file = "_file";
				break;
			default:
				$Field->process_label($id, $value);
		}

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
		$this->tpl[$tpl_id]->set_var("suffix_file", $suffix_file);
		$this->tpl[$tpl_id]->set_var("site_path", $Field->parent_page[0]->site_path);
		$this->tpl[$tpl_id]->set_var("theme", $Field->getTheme());
		$this->tpl[$tpl_id]->set_var("class", $this->class);
		$this->tpl[$tpl_id]->set_var("properties", $Field->getProperties());

        if($Field->extended_type == "File") {
        	$base_path = FF_DISK_PATH . "/uploads";
        	$storing_path = $Field->getFileTempPath(false);
        	
        	if($base_path && $storing_path) {
        		$this->tpl[$tpl_id]->set_var("base_url", str_replace($base_path, "", str_replace("/[_FILENAME_]", "", $storing_path)));
			} else {
				$this->tpl[$tpl_id]->set_var("base_url", "/");
			}
			
			$this->tpl[$tpl_id]->set_var("size_limit", $Field->file_max_size);
			
			if(strlen($Field->file_tmpname)) {
				$view_url				= $Field->file_temp_view_url;
				$view_query_string		= $Field->file_temp_view_query_string;

				$preview_url			= $Field->file_temp_preview_url;
				$preview_query_string	= $Field->file_temp_preview_query_string;
			} else {
				$view_url				= $Field->file_saved_view_url;
				$view_query_string		= $Field->file_saved_view_query_string;

				$preview_url			= $Field->file_saved_preview_url;
				$preview_query_string	= $Field->file_saved_preview_query_string;
			}
			
			$filename = $Field->getValue();
			
			$view_url = ffProcessTags($view_url, $Field->keysarray, $Field->dataarray, "normal");
			$view_url = str_replace("[_FILENAME_]", $filename, $view_url);
			$view_url = ffProcessTags($view_url, $Field->keysarray, $Field->dataarray, "normal");
			$view_query_string = ffProcessTags($view_query_string, $Field->keysarray, $Field->dataarray, "normal");
			
			$preview_url = ffProcessTags($preview_url, $Field->keysarray, $Field->dataarray, "normal");
			$preview_url = str_replace("[_FILENAME_]", $filename, $preview_url);
			$preview_url = ffProcessTags($preview_url, $Field->keysarray, $Field->dataarray, "normal");
			$preview_query_string = ffProcessTags($preview_query_string, $Field->keysarray, $Field->dataarray, "normal");
			
			if($Field->file_show_preview  && $Field->getValue()) {
				$this->tpl[$tpl_id]->set_var("show_file", str_replace($Field->getValue(), "", $preview_url));
				$this->tpl[$tpl_id]->set_var("preview_js", "true");
				$this->tpl[$tpl_id]->parse("SectPreview", false);
			} elseif($Field->file_widget_preview) {
				$this->tpl[$tpl_id]->set_var("preview_js", "true");
			} else {
				$this->tpl[$tpl_id]->set_var("preview_js", "false");
			}						
		} 

        if ($Field->contain_error && $Field->error_preserve)
            $this->tpl[$tpl_id]->set_var("value", ffCommon_specialchars($value->ori_value));
        else
            $this->tpl[$tpl_id]->set_var("value", ffCommon_specialchars($value->getValue($Field->get_app_type(), $Field->get_locale())));
        
        //$this->tpl[0]->set_var("properties", $Field->getProperties());

        $this->tpl[$tpl_id]->parse("SectBinding", true);

        //$Field->tpl[0]->set_var("id", $id);
        $this->tpl[$tpl_id]->set_var("control", $Field->tpl[0]->rpparse("main", false));
        return $this->tpl[$tpl_id]->rpparse("SectControl", FALSE);
    }
	
	function get_component_headers($id)
	{
		if ($this->oPage !== NULL) { //code for ff.js
			$this->oPage[0]->tplAddJs("swfobject", "swfobject.js", FF_THEME_DIR . "/library/swfobject");
			$this->oPage[0]->tplAddJs("jquery.uploadify", "jquery.uploadify.js", FF_THEME_DIR . "/library/plugins/jquery.uploadify");
            $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("ff.ffField.uploadify", "uploadify.js", FF_THEME_DIR . "/default/ff/ffField/widgets/uploadify");
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
			$this->oPage[0]->tplAddJs("swfobject", "swfobject.js", FF_THEME_DIR . "/library/swfobject");
			$this->oPage[0]->tplAddJs("jquery.uploadify", "jquery.uploadify.js", FF_THEME_DIR . "/library/plugins/jquery.uploadify");
            $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("ff.ffField.uploadify", "uploadify.js", FF_THEME_DIR . "/default/ff/ffField/widgets/uploadify");
			
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
?>