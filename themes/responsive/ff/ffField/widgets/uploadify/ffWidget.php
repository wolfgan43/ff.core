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
							, "jquery.uploadify"			=> "/plugins/jquery.uploadify"
						);
    var $css_deps 		= array(
    						 /* "jquery.uploadify"		=> "/plugins/jquery.uploadify/uploadify.css"*/
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

		if ($style_path !== null)
			$this->tpl[$id]->set_var("style_path", $this->style_path);
		elseif ($this->oPage !== null)
			$this->tpl[$id]->set_var("style_path", $this->oPage[0]->getThemePath());

	}
	
	function process($id, &$value, ffField_base &$Field)
	{
		global $plgCfg_uploadify_UseOwnSession;
		//$Field->parent_page[0]->tplAddCss("jquery.uploadify", "uploadify.css", FF_SITE_PATH	 . "/themes/library/plugins/jquery.uploadify");
		
		switch($Field->get_control_type())
		{
			case "picture":
			case "picture_no_link":
				//$this->process_picture($id, $value);
				//break;
			case "file_label":
			case "file":
				if($Field->file_show_filename)
                	$Field->file_show_filesize = true;

				$Field->process_file($id, $value);
				if (count($Field->parent) && is_subclass_of($Field->parent[0], "ffDetails_base")) {
					$suffix_start = "";
					$suffix_target = "[name]";
					$suffix_tmpname = "[tmpname]";
					$suffix_delete = "[delete]";
				} else {
					$suffix_start = "_file";
					$suffix_target = "";
					$suffix_tmpname = "_tmpname";
					$suffix_delete = "_delete";
				}
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
		$this->tpl[$tpl_id]->set_var("suffix_start", $suffix_start);
		$this->tpl[$tpl_id]->set_var("suffix_target", $suffix_target);
		$this->tpl[$tpl_id]->set_var("suffix_tmpname", $suffix_tmpname);
		$this->tpl[$tpl_id]->set_var("suffix_delete", $suffix_delete);
		$this->tpl[$tpl_id]->set_var("site_path", $Field->parent_page[0]->site_path);
		$this->tpl[$tpl_id]->set_var("theme", $Field->getTheme());
		$this->tpl[$tpl_id]->set_var("class", $this->class);
		$this->tpl[$tpl_id]->set_var("properties", $Field->getProperties());
		
		$this->tpl[$tpl_id]->set_var("fixed_pre_content", $Field->fixed_pre_content);
		$this->tpl[$tpl_id]->set_var("fixed_post_content", $Field->fixed_post_content);
        
        if(strlen($Field->widget_path))
            $this->tpl[$tpl_id]->set_var("widget_path", $Field->widget_path);
        else 
            $this->tpl[$tpl_id]->set_var("widget_path", "/themes/responsive/ff/ffField/widgets/uploadify"); 
		
		$css_deps 		= array(
              	"uploadify"        => array(
                      "file" => "uploadify.css"
                    , "path" => null
                    , "rel" => "plugins/jquery.uploadify"
                ) 
    	);		

		if(is_array($css_deps) && count($css_deps)) {
			foreach($css_deps AS $css_key => $css_value) {
				$rc = $Field->parent_page[0]->widgetResolveCss($css_key, $css_value, $Field->parent_page[0]);

				$this->tpl[$tpl_id]->set_var(preg_replace('/[^0-9a-zA-Z]+/', "", $css_key), $rc["path"] . "/" . $rc["file"]);
				$Field->parent_page[0]->tplAddCss(preg_replace('/[^0-9a-zA-Z]+/', "", $css_key), $rc["file"], $rc["path"], "stylesheet", "text/css", false, false, null, false, "bottom");
			}
		}
		
        if($Field->extended_type == "File") {
        	$base_path = $Field->getFileBasePath();
        	$storing_path = $Field->getFilePath();
			$folder = str_replace($base_path, "", $storing_path);
			
			if(!strlen($folder))
				$folder = "/";
        			
			//$this->tpl[$tpl_id]->set_var("base_url", $folder);

			$tmp = MD5($folder . "-" . $base_path);
			if (!defined("FF_UPLOADIFY_SESSION_STARTED") && ($plgCfg_uploadify_UseOwnSession || $Field->uploadify_use_own_session))
			{
				if (isset($_POST[session_name()]))
					session_id($_POST[session_name()]);
				elseif (isset($_GET[session_name()]))
					session_id($_GET[session_name()]);
				elseif (isset($_COOKIE[session_name()]))
					session_id($_COOKIE[session_name()]);
				session_start();
				if (!defined("FF_UPLOADIFY_SESSION_STARTED"))
					define("FF_UPLOADIFY_SESSION_STARTED", true);
			}

			$ff = get_session("ff");
			$ff["uploadify"][$tmp]["folder"] = $folder;
			$ff["uploadify"][$tmp]["base_path"] = $base_path;
			set_session("ff", $ff);
			
			$this->tpl[$tpl_id]->set_var("data_src", $tmp);
			
			$this->tpl[$tpl_id]->set_var("size_limit", $Field->file_max_size);
			
			$file_ext = "";
			$file_desc = "";
			if(is_array($Field->file_allowed_mime) && count($Field->file_allowed_mime)) 
			{
				foreach($Field->file_allowed_mime AS $file_allowed_mime_value) 
				{
					if(strlen($file_ext))
					{
						$file_ext .= "; ";
						$file_desc .= ", ";
					}
					if (strpos($file_allowed_mime_value, "/"))
					{
						$ext = "*." . substr($file_allowed_mime_value, strpos($file_allowed_mime_value, "/") + 1);
						$file_ext .= $ext;
						$file_desc .= $file_allowed_mime_value . " (" . $ext . ")";
					}
					else
					{
						$file_ext .= "*." . $file_allowed_mime_value;
						$file_desc .= $file_allowed_mime_value . " (" . "*." . $file_allowed_mime_value . ")";
					}
				}
				//die($file_ext);
			}
			if(strlen($file_ext))
			{
				$this->tpl[$tpl_id]->set_var("file_ext", "'" . $file_ext . "'");
				$this->tpl[$tpl_id]->set_var("file_desc", "'" . $file_desc . "'");
			}
			else
			{
				$this->tpl[$tpl_id]->set_var("file_ext", "null");
				$this->tpl[$tpl_id]->set_var("file_desc", "null");
			}
			
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

			$view_url = ffProcessTags($view_url, $Field->getKeysArray(), $Field->getDataArray(), "normal");
			//$this->tpl[$tpl_id]->set_var("view_url", rtrim(str_replace("[_FILENAME_]", "", $view_url), "/"));
			$this->tpl[$tpl_id]->set_var("view_url", rtrim(str_replace("[_FILENAME_]", "", ffProcessTags($Field->file_temp_view_url, $Field->getKeysArray(), $Field->getDataArray(), "normal")), "/"));
			
			$view_url = str_replace("[_FILENAME_]", $filename, $view_url);
			$view_url = ffProcessTags($view_url, $Field->getKeysArray(), $Field->getDataArray(), "normal");
			$view_query_string = ffProcessTags($view_query_string, $Field->getKeysArray(), $Field->getDataArray(), "normal");
			
			$preview_url = ffProcessTags($preview_url, $Field->getKeysArray(), $Field->getDataArray(), "normal");
			//$this->tpl[$tpl_id]->set_var("preview_url", rtrim(str_replace("[_FILENAME_]", "", $preview_url), "/"));
			$this->tpl[$tpl_id]->set_var("preview_url", rtrim(str_replace("[_FILENAME_]", "", ffProcessTags($Field->file_temp_preview_url, $Field->getKeysArray(), $Field->getDataArray(), "normal")), "/"));

			$preview_url = str_replace("[_FILENAME_]", $filename, $preview_url);
			$preview_url = ffProcessTags($preview_url, $Field->getKeysArray(), $Field->getDataArray(), "normal");
			$preview_query_string = ffProcessTags($preview_query_string, $Field->getKeysArray(), $Field->getDataArray(), "normal");

			if($Field->file_show_preview && @is_file($Field->getFileBasePath() . "/" . ltrim($Field->getValue(), "/"))) {
				$this->tpl[$tpl_id]->set_var("show_file", str_replace($Field->getValue(), "", $preview_url));
				if($Field->file_widget_preview)
					$this->tpl[$tpl_id]->set_var("preview_js", "true");
				else
					$this->tpl[$tpl_id]->set_var("preview_js", "false"); 

				//$this->tpl[$tpl_id]->parse("SectPreview", false);
			} elseif($Field->file_widget_preview) {
				$this->tpl[$tpl_id]->set_var("preview_js", "true");
			} else {
				$this->tpl[$tpl_id]->set_var("preview_js", "false");
			}
            
            if($Field->file_writable) {
                $this->tpl[$tpl_id]->set_var("writable", "true");
            } else {
                $this->tpl[$tpl_id]->set_var("writable", "false");
            }
		} else {
			$this->tpl[$tpl_id]->set_var("preview_js", "false");
			$this->tpl[$tpl_id]->set_var("writable", "true");
			$this->tpl[$tpl_id]->set_var("size_limit", 0);
			$this->tpl[$tpl_id]->set_var("file_ext", "null");
		}

        $this->tpl[$tpl_id]->set_var("cancel_class", Cms::getInstance("frameworkcss")->get("deleterow", "icon"));
		
		$this->tpl[$tpl_id]->set_var("type_model", $Field->uploadify_model);
		$this->tpl[$tpl_id]->set_var("thumb_model", $Field->uploadify_model_thumb);
		
		if($Field->file_show_filename) {
			$this->tpl[$tpl_id]->set_var("show_file", "true");
		} else {
			$this->tpl[$tpl_id]->set_var("show_file", "false");
		}

        if($Field->file_full_path) {
            $this->tpl[$tpl_id]->set_var("full_path", "true");
        } else {
            $this->tpl[$tpl_id]->set_var("full_path", "false");
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
			$this->oPage[0]->tplAddJs("jquery.cookie", "jquery.cookie.js", FF_THEME_DIR . "/library/plugins/jquery.cookie");
            $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("ff.ffField.uploadify", "uploadify.js", FF_THEME_DIR . "/responsive/ff/ffField/widgets/uploadify");
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
			$this->oPage[0]->tplAddJs("jquery.cookie", "jquery.cookie.js", FF_THEME_DIR . "/library/plugins/jquery.cookie");
            $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("ff.ffField.uploadify", "uploadify.js", FF_THEME_DIR . "/responsive/ff/ffField/widgets/uploadify");
			
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
