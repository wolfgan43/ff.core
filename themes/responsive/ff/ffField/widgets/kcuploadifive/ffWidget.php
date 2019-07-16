<?php
// ----------------------------------------
//  		FRAMEWORK FORMS vAlpha
//		      PLUGIN DEFINITION (jscalendar)
//			   by Samuele Diella
// ----------------------------------------

class ffWidget_kcuploadifive extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)
	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_kcuploadifive";

	var $widget_deps	= array();
    var $js_deps = array(
							  "jquery"						=> null
							, "jquery.uploadifive"			=> "/plugins/jquery.uploadifive"
						);
    var $css_deps 		= array(
    						  "uploadifive"		=> array(
									                      "file" => "uploadifive.css"
									                    , "path" => null
									                    , "rel" => "plugins/jquery.uploadifive"
									                ) 
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
		global $plgCfg_kcuploadifive_UseOwnSession;
		//$Field->parent_page[0]->tplAddCss("jquery.uploadifive", "uploadifive.css", FF_SITE_PATH	 . "/themes/library/plugins/jquery.uploadifive");
		
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
		$this->tpl[$tpl_id]->set_var("id_kc", $id . $suffix_target);
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

        $this->tpl[$tpl_id]->set_var("browse_class", Cms::getInstance("frameworkcss")->get("search", "icon", "lg"));

        if(strlen($Field->widget_path))
            $this->tpl[$tpl_id]->set_var("widget_path", $Field->widget_path);
        else 
            $this->tpl[$tpl_id]->set_var("widget_path", "/themes/responsive/ff/ffField/widgets/kcuploadifive");  
        
		$css_deps["uploadifive"]        = array(
                      "file" => "uploadifive.css"
                    , "path" => null
                    , "rel" => "plugins/jquery.uploadifive"
                );

		
		if($Field->uploadifive_showfile_plugin && is_file(FF_DISK_PATH . FF_THEME_DIR . "/library/plugins/jquery." . $Field->uploadifive_showfile_plugin . "/jquery." . $Field->uploadifive_showfile_plugin . ".js")) {
			if($Field->uploadifive_showfile_plugin && is_file(FF_DISK_PATH . FF_THEME_DIR . "/library/plugins/jquery." . $Field->uploadifive_showfile_plugin . "/jquery." . $Field->uploadifive_showfile_plugin . ".css")) {
				$css_deps[$Field->uploadifive_showfile_plugin] = array(
		                      "file" => "jquery." . $Field->uploadifive_showfile_plugin . ".css"
		                    , "path" => null
		                    , "rel" => "plugins/jquery." . $Field->uploadifive_showfile_plugin
		                );
		        $this->tpl[$tpl_id]->set_var("uploadifive_plugin_name", $Field->uploadifive_showfile_plugin);
		        $this->tpl[$tpl_id]->set_var("uploadifive_plugin_css", FF_SITE_PATH . FF_THEME_DIR . "/library/plugins/jquery." . $Field->uploadifive_showfile_plugin . "/jquery." . $Field->uploadifive_showfile_plugin . ".css");
		        $this->tpl[$tpl_id]->parse("SectPluginCss", false);
			}
			$js_deps["jquery.fn." . $Field->uploadifive_showfile_plugin] = array(
 						"file" => "jquery." . $Field->uploadifive_showfile_plugin . ".js"
				        , "path" => FF_THEME_DIR . "/library/plugins/jquery." . $Field->uploadifive_showfile_plugin
					);
			

	        $this->tpl[$tpl_id]->set_var("uploadifive_plugin_name", "jquery." . $Field->uploadifive_showfile_plugin);
	        $this->tpl[$tpl_id]->set_var("uploadifive_plugin_js", FF_SITE_PATH . FF_THEME_DIR . "/library/plugins/jquery." . $Field->uploadifive_showfile_plugin . "/jquery." . $Field->uploadifive_showfile_plugin . ".js");
	        $this->tpl[$tpl_id]->parse("SectPluginJs", false);
	        $this->tpl[$tpl_id]->set_var("SectNoPlugin", "");
		} else {
			$this->tpl[$tpl_id]->set_var("SectPluginCss", "");
			$this->tpl[$tpl_id]->set_var("SectPluginJs", "");
			$this->tpl[$tpl_id]->parse("SectNoPlugin", false);
		
		}

		if($Field->uploadifive_showfile_plugin) {
			$this->tpl[$tpl_id]->set_var("showfile_plugin", "'" . $Field->uploadifive_showfile_plugin . "'");
		} else {
			$this->tpl[$tpl_id]->set_var("showfile_plugin", "undefined");
		}
		
		//e necessario perche il widgetlaod viene caricato prima del process
		if(is_array($js_deps) && count($js_deps)) {
			foreach($js_deps AS $js_key => $js_value) {
				$Field->parent_page[0]->tplAddJs($js_key, $js_value["file"], $js_value["path"]);
			}
		}		

		if(is_array($css_deps) && count($css_deps)) {
			foreach($css_deps AS $css_key => $css_value) {
				$rc = $Field->parent_page[0]->widgetResolveCss($css_key, $css_value, $Field->parent_page[0]);

				$this->tpl[$tpl_id]->set_var(preg_replace('/[^0-9a-zA-Z]+/', "", $css_key), $rc["path"] . "/" . $rc["file"]);
				$Field->parent_page[0]->tplAddCss(preg_replace('/[^0-9a-zA-Z]+/', "", $css_key), $rc["file"], $rc["path"], "stylesheet", "text/css", false, false, null, false, "bottom");
			}
		}

        $base_path = $Field->getFileBasePath();
        $storing_path = $Field->getFilePath();
		$folder = str_replace($base_path, "", $storing_path);
		/*if(strlen($Field->file_normalize)) {
			$arrFolder = explode("/", trim($folder, "/"));
			if(is_array($arrFolder) && count($arrFolder)) {
				foreach($arrFolder AS $part_folder) {
					$folder_normalized .= "/" . ffCommon_url_rewrite($part_folder);
				}
			}
			$folder = $folder_normalized;
		}*/		
		if(!strlen($folder))
			$folder = "/";

		if(Auth::isLogged()) { //if(session_status() == PHP_SESSION_NONE) {
			if ($plgCfg_kcuploadifive_UseOwnSession || $Field->actex_use_own_session) 
				session_start();
			$ff = get_session("ff");

	        $tmp = MD5($folder . "-" . $base_path . "-" . $Field->file_multi);
		}
		
        if($Field->extended_type == "File") {
        	//$this->tpl[$tpl_id]->set_var("base_url", $folder);
			if(Auth::isLogged()) {//if(session_status() == PHP_SESSION_NONE) {
				$ff["uploadifive"][$tmp]["folder"] = $folder;
				$ff["uploadifive"][$tmp]["base_path"] = $base_path;
				
				$this->tpl[$tpl_id]->set_var("data_src", $tmp);
			}			

			$this->tpl[$tpl_id]->set_var("folder", $folder);
			$this->tpl[$tpl_id]->set_var("size_limit", $Field->file_max_size);
			
			$file_ext = "";
			if(is_array($Field->file_allowed_mime) && count($Field->file_allowed_mime)) 
			{
				foreach($Field->file_allowed_mime AS $file_allowed_mime_value) 
				{
					if(strlen($file_ext))
						$file_ext .= "|";
					if (strpos($file_allowed_mime_value, "/"))
						$file_ext .= ffMedia::getMimeTypeByExtension(substr($file_allowed_mime_value, strpos($file_allowed_mime_value, "/") + 1));
					else
						$file_ext .= ffMedia::getMimeTypeByExtension($file_allowed_mime_value);
				}
			}
			if(strlen($file_ext))
				$this->tpl[$tpl_id]->set_var("file_ext", "'" . $file_ext . "'");
			else
				$this->tpl[$tpl_id]->set_var("file_ext", "null");

			if(strlen($Field->file_normalize))
				$this->tpl[$tpl_id]->set_var("file_normalize", "true");
			else
				$this->tpl[$tpl_id]->set_var("file_normalize", "false");
			
			if($Field->file_widget_preview) {
				$this->tpl[$tpl_id]->set_var("preview_js", "true");
			} else {
				$this->tpl[$tpl_id]->set_var("preview_js", "false");
			}
            
            if($Field->file_writable) {
                $this->tpl[$tpl_id]->set_var("writable", "true");
            } else {
                $this->tpl[$tpl_id]->set_var("writable", "false");
			}
			
			$base_path = $Field->getFileBasePath();
        	$storing_path = $Field->getFilePath(false);
        	if($base_path && $storing_path) {
        		$base_url = str_replace($base_path, "", $storing_path);
        		if(!strlen($base_url))
        			$base_url = "/";
        			
        		$this->tpl[$tpl_id]->set_var("base_url_kc", $base_url);
			} else {
				$this->tpl[$tpl_id]->set_var("base_url_kc", "/");
			}
			$this->tpl[$tpl_id]->set_var("resource_type", basename($base_path));			

		} else {
			$this->tpl[$tpl_id]->set_var("preview_js", "false");
			$this->tpl[$tpl_id]->set_var("writable", "true");
			$this->tpl[$tpl_id]->set_var("size_limit", 0);
			$this->tpl[$tpl_id]->set_var("file_ext", "null");
		}
		$this->tpl[$tpl_id]->set_var("cancel_class", Cms::getInstance("frameworkcss")->get("cancel", "icon"));
        $this->tpl[$tpl_id]->set_var("aviary_class", Cms::getInstance("frameworkcss")->get("crop", "icon"));  
        $this->tpl[$tpl_id]->set_var("upload_class", Cms::getInstance("frameworkcss")->get("upload", "icon"));
        $this->tpl[$tpl_id]->set_var("upload_icon", Cms::getInstance("frameworkcss")->get("upload", "icon-tag", "lg"));
        
		if($Field->file_multi) {
			$this->tpl[$tpl_id]->set_var("multi", "true");
		} else {
			$this->tpl[$tpl_id]->set_var("multi", "false");
		}

		if($Field->file_modify_path) {
			$this->tpl[$tpl_id]->set_var("showfile_path", "'" . $Field->file_modify_path . "'");
		} else {
			$this->tpl[$tpl_id]->set_var("showfile_path", "undefined");
		}

		if($Field->file_modify_dialog) {
			$this->tpl[$tpl_id]->set_var("showfile_dialog", "'" . $Field->file_modify_dialog . "'");
		} else {
			$this->tpl[$tpl_id]->set_var("showfile_dialog", "undefined");
		}

		if($Field->uploadifive_sort_path) {
			$this->tpl[$tpl_id]->set_var("showfile_sort", "'" . $Field->uploadifive_sort_path . "'");
		} else {
			$this->tpl[$tpl_id]->set_var("showfile_sort", "undefined");
		}

		$this->tpl[$tpl_id]->set_var("thumb_model", $Field->uploadifive_model_thumb);

		$this->tpl[$tpl_id]->set_var("width", $Field->file_thumb["width"]);
		$this->tpl[$tpl_id]->set_var("height", $Field->file_thumb["height"]);

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


		$this->tpl[$tpl_id]->set_var("aviary", "null");
		if ($Field->file_show_edit) {
			if(strlen($Field->file_edit_type)) {
				if(is_array($Field->file_edit_params) 
					&& array_key_exists($Field->file_edit_type, $Field->file_edit_params)
					&& is_array($Field->file_edit_params[$Field->file_edit_type])
					&& count($Field->file_edit_params[$Field->file_edit_type])
					&& $Field->file_edit_type == "Aviary"
				) {
					if(Auth::isLogged()) {//if(session_status() == PHP_SESSION_NONE) {
						$ff["aviary"][$tmp]["folder"] = $folder;
						$ff["aviary"][$tmp]["base_path"] = $base_path;
					}					
					
					$str_aviary = "'" . "img_hash" . "' : '" . $tmp . "'";
					foreach($Field->file_edit_params[$Field->file_edit_type] AS $params_key => $params_value) {
						if(strlen($str_aviary ))
							$str_aviary .= ", ";

						$str_aviary .= "'" . "" . $params_key . "' : '" . $params_value . "'";
					}

					
					
					$this->tpl[$tpl_id]->set_var("aviary", "{" . $str_aviary . "}");
				}
			}
		}
		
		if(Auth::isLogged())//if(session_status() == PHP_SESSION_NONE)
			set_session("ff", $ff);

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
			$this->oPage[0]->tplAddJs("jquery.uploadifive", "jquery.uploadifive.js", FF_THEME_DIR . "/library/plugins/jquery.uploadifive");
			$this->oPage[0]->tplAddJs("jquery.cookie", "jquery.cookie.js", FF_THEME_DIR . "/library/plugins/jquery.cookie");
            $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("ff.ffField.kcuploadifive", "kcuploadifive.js", FF_THEME_DIR . "/responsive/ff/ffField/widgets/kcuploadifive");
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
			$this->oPage[0]->tplAddJs("jquery.uploadifive", "jquery.uploadifive.js", FF_THEME_DIR . "/library/plugins/jquery.uploadifive");
			$this->oPage[0]->tplAddJs("jquery.cookie", "jquery.cookie.js", FF_THEME_DIR . "/library/plugins/jquery.cookie");
            $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("ff.ffField.kcuploadifive", "kcuploadifive.js", FF_THEME_DIR . "/responsive/ff/ffField/widgets/kcuploadifive");
			
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