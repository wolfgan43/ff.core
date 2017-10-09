<?php

/**
 * @package theme_default
 * @subpackage base
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

/**
 * @package theme_default
 * @subpackage base
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */
class ffPage_html extends ffPage_base 
{
	var $layer 					= "empty";					/* The layer suffix. If set, cause Forms to enclose all contents
															into a HTML template named layer_$layer.html located under a
															dir named "layers" located under theme dir. */
	var $layer_dir				= null;
	var $sections 				= array();
	/*var $sections				= array(
										  "navbar" => array(
															  "dir" => null
															, "name" => ""
															, "tpl" => null
															, "is_php" => false
															, "on_load_template" => null
															, "on_process" => null
														)
										, "topbar" => array(
															  "dir" => null
															, "name" => ""
															, "tpl" => null
															, "is_php" => false
															, "on_load_template" => null
															, "on_process" => null
														)
										);*/

	var $navbar_dir				= null;
	var $navbar 				= "";					/* The navbar suffix. If set, cause Forms to include into the
															layer a HTML template named navbar_$navbar.html located under
															a dir named "layers" located under theme dir.

															NB: in order to use this, you must set $layer. */

	var $navbar_isphp			= false;

	var $topbar_dir				= null;
	var $topbar 				= "";					/* The topbar suffix. If set, cause Forms to include into the
															layer a HTML template named topbar_$topbar.html located under
															a dir named "layers" located under theme dir.

															NB: in order to use this, you must set $layer. */

	var $topbar_isphp			= false;

	var $use_own_form			= null;				/* if main form on FormsPage must be used or not.
														WARNING: all forms superclasses use this. disable only
														if you are using ffPage like a container for your
														own code. */
	var $use_own_js 			= true;
    var $form_id                = "frmMain";
    var $form_name              = "frmMain";
	var $form_method			= "";					/* method to use with form.
															leave blank for auto-select, otherwise set to GET or POST. */
	var $form_action			= "";
	var $form_enctype			= "";					/* enctype to use with form.
															leave blank for auto-select, otherwise select proper value. */
	var $jquery_ui_theme 		= "base";
	var $jquery_ui_force_theme 	= null;
	var $json_result = array();
	
	public $template_file 			= "ffPage.html";
	public $fixed_vars 				= array();
	
	public $compact_css 		   = false;
	public $css_buffer 			   = array();
    public $override_css           = array();
	public $page_css			   = array(); /*
											"main" => array(
																  "file" => "example.css"
																, "path" => null
															)
										);*/
	public $compact_js 			   = false;
	public $js_buffer 			   = array();
    public $override_js            = array();
    public $page_js                = array(); /*
                                            "main" => array(
                                                                  "tag" => example
                                                                , "file" => "example.js"
                                                                , "path" => null
                                                                
                                                            )
                                        );*/
    public $page_meta              = array(); 
	public $page_html_attr         = array();    /*
                                            "main" => array(
                                                                  "name" => "language"
                                                                , "content" => "it,en"
                                                            )
                                        );*/
    public $class_body               = null;
	
	public $compress			   = false;
	public $strip_extra_newlines   = true;
	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)

	var $tpl_layer				= null;
	
	var $browser = null;
	
	public function __construct($site_path, $disk_path, $page_path, $theme)
	{
		parent::__construct($site_path, $disk_path, $page_path, $theme);
		ffPage::doEvent("html_load_defaults", array(&$this));

		//if($_REQUEST["frmAction"])
		
		$this->tplAddJs("ff.ffPage", "ffPage.js", FF_THEME_DIR . "/library/ff"); 
			

		$registry = ffGlobals::getInstance("_registry_");
		if (isset($registry->themes[$this->theme]->default_css) && count($registry->themes[$this->theme]->default_css->children()))
		{
			foreach ($registry->themes[$this->theme]->default_css->children() as $key => $value)
			{
				$path = (string)$value->path;
				$tag = $key;
				$file = (string)$value->file;
				$priority = (string)$value->priority;
				if(!isset($value->exclude_compact) ||
					(isset($value->exclude_compact) && (string)$value->exclude_compact == "false")
				) {
					$exclude_compact = false;
				} else {
					$exclude_compact = true;	
				}

				if (!strlen($path))
					$path = null;
				if (!strlen($file))
					$file = null;
				if (!strlen($priority))
					$priority = "top";

				$this->tplAddCss($tag, $file, $path, "stylesheet", "text/css", true, false, null, $exclude_compact, $priority);
			}
		}

		if (isset($registry->themes[$this->theme]->default_js) && count($registry->themes[$this->theme]->default_js->children()))
		{
			foreach ($registry->themes[$this->theme]->default_js->children() as $key => $value)
			{
				$path = (string)$value->path;
				$tag = $key;
				$file = (string)$value->file;
				$priority = (string)$value->priority;
				if(!isset($value->exclude_compact) ||
					(isset($value->exclude_compact) && (string)$value->exclude_compact == "false")
				) {
					$exclude_compact = false;
				} else {
					$exclude_compact = true;	
				}
				
				if (!strlen($path))
					$path = null;
				if (!strlen($file))
					$file = null;
				if (!strlen($priority))
					$priority = "top";

				$this->tplAddJs($tag, $file, $path, true, false, null, $exclude_compact, $priority);
			}
		}
        if (isset($registry->themes[$this->theme]->default_cdn) && count($registry->themes[$this->theme]->default_cdn->children()))
        {
            foreach ($registry->themes[$this->theme]->default_cdn->children() as $key => $value)
            {
                $url = (string)$value->url;
                $name = $key;
                $type = (string)$value->type;

                if($type == "css")
                    $this->override_css[$key] = $url;
                elseif($type == "js")
                    $this->override_js[$key] = $url;
            }
        }
	}

	function getLayerDir($layout_file = null)
	{
		$res = $this->doEvent("getLayerDir", array($this, $layout_file));
		$last_res = end($res);
		if ($last_res === null)
		{
			if ($this->layer_dir === null)
				return $this->disk_path . "/themes/" . $this->getTheme() . "/layouts";
			else
				return $this->layer_dir;
		}
		else
		{
			return $last_res;
		}
	}

	function getLayoutDir($layout_file = null)
	{
		$res = $this->doEvent("getLayoutDir", array($this, $layout_file));
		$last_res = end($res);
		if ($last_res === null)
		{
			return $this->disk_path . "/themes/" . $this->getTheme() . "/layouts";
		}
		else
		{
			return $last_res;
		}
	}

	public function tplAddCss($tag, $file = null, $path = null, $css_rel = "stylesheet", $css_type = "text/css", $overwrite = false, $async = false, $css_media = null, $exclude_compact = false, $priority = "top")
	{
        static $last_top = 0;
        static $bottom_exist = false;
        $found = false;

        if (!$allow_duplicates)
		{
			foreach ($this->page_css AS $css_key => $css_value)
			{
				if(
						$css_value["path"] == $path
						&& $css_value["file"] == $file
						&& $css_value["path"] !== null
						&& $css_value["file"] !== null
					)
				{
					$found = $css_key;
					break;
				}
			}
			reset($this->page_css);
		}
		
		if (!$found && isset($this->page_css[strtolower($tag)]))
			$found = strtolower($tag);

        if(!$found)
        {
			if($priority == "top" && $bottom_exist) {
				$tmp_css_top = array_slice($this->page_css, 0, $last_top, true);
				$tmp_css_bottom = array_slice($this->page_css, $last_top, count($this->page_css) - $last_top, true);
				$tmp_css_top[strtolower($tag)] = array(
															  "path" => $path
															, "file" => $file
	                                                        , "rel"  => $css_rel
	                                                        , "type" => $css_type
	                                                        , "async" => $async
	                                                        , "media" => $css_media
	                                                        , "exclude_compact" => $exclude_compact
														);
	            $this->page_css = array_merge($tmp_css_top, $tmp_css_bottom);

	            $last_top++;
			} else {
			    $this->page_css[strtolower($tag)] = array(
															  "path" => $path
															, "file" => $file
	                                                        , "rel"  => $css_rel
	                                                        , "type" => $css_type
	                                                        , "async" => $async
	                                                        , "media" => $css_media
	                                                        , "exclude_compact" => $exclude_compact
														);
	            if(!$bottom_exist)
	            	$last_top++;
	            
	            $bottom_exist = true;
			}			
        }
		elseif ($overwrite)
		{
		    $this->page_css[$found] = array(
														  "path" => $path
														, "file" => $file
                                                        , "rel"  => $css_rel
                                                        , "type" => $css_type
                                                        , "async" => $async
                                                        , "media" => $css_media
                                                        , "exclude_compact" => $exclude_compact
													);
		}
		else
			return false;

		return true;
	}

	

    public function tplAddJs($tag, $file = null, $path = null, $overwrite = false, $async = false, $embed = null, $exclude_compact = false, $priority = "top")
    {
    	static $last_top = 0;
    	static $bottom_exist = false;
        $found = false;

        foreach ($this->page_js AS $js_key => $js_value)
        {
            if(
            		   $js_value["path"] == $path
            		&& $js_value["file"] == $file
            		&& $js_value["path"] !== null
            		&& $js_value["file"] !== null
            	)
            {
                $found = $js_key;
                break;
            }
        }
        reset($this->page_js);

		if (!$found && isset($this->page_js[strtolower($tag)]))
			$found = strtolower($tag);

        if (!$found)
        {
			if($priority == "top" && $bottom_exist) {
				$tmp_js_top = array_slice($this->page_js, 0, $last_top, true);
				$tmp_js_bottom = array_slice($this->page_js, $last_top, count($this->page_js) - $last_top, true);
				$tmp_js_top[strtolower($tag)] = array(
	                                                          "path" => $path
	                                                        , "file" => $file
	                                                        , "async" => $async
	                                                        , "embed" => $embed
	                                                        , "exclude_compact" => $exclude_compact
	                                                    );
	            $this->page_js = array_merge($tmp_js_top, $tmp_js_bottom);

	            $last_top++;
			} else {
	            $this->page_js[strtolower($tag)] = array(
	                                                          "path" => $path
	                                                        , "file" => $file
	                                                        , "async" => $async
	                                                        , "embed" => $embed
	                                                        , "exclude_compact" => $exclude_compact
	                                                    );
	            if(!$bottom_exist)
	            	$last_top++;
	            
	            $bottom_exist = true;
			}
        }
        elseif ($overwrite)
        {
            $this->page_js[$found] = array(
                                                          "path" => $path
                                                        , "file" => $file
                                                        , "async" => $async
                                                        , "embed" => $embed
                                                        , "exclude_compact" => $exclude_compact
                                                    );
        }
		else
			return false;

		return true;
    }

    public function tplAddMeta($name, $content, $overwrite = false, $type = "name")
    {
		if (!isset($this->page_meta[$name]) || $overwrite)
		{
			$this->page_meta[$name] = array(
										"name" => $name
										, "content" => $content
										, "type" => $type
									);
			return true;
		}
		else
			return false;
    }
	
	public function tplAddHtmlAttr($content, $overwrite = false, $type = "xmlns")
    {
		if (!isset($this->page_html_attr[$content]) || $overwrite)
		{
			$this->page_html_attr[$content] = array(
										"content" => $content
										, "type" => $type
									);
			return true;
		}
		else
			return false;
    }
	
	function process($output_result = true)
	{
		$this->output_buffer = "";

		if($this->use_own_form !== false)
			$this->addHiddenField("frmAction", $_REQUEST["frmAction"]);

		if (!$this->params_processed)
			$this->process_params();

		$this->tplLoad();

		if (is_array($this->components) && count($this->components))
		{
			// First of all, do a preprocess to retrieve params
			foreach ($this->components as $key => $item)
			{
				$this->components[$key]->pre_process();

				if ($this->form_method == "" && is_subclass_of($this->components[$key], "ffRecord_base"))
					$this->form_method = "post";
			}
			reset($this->components);

			if ($this->form_method == "")
				$this->form_method = "get";

			if ($this->form_enctype == "")
			{
				if (strtolower($this->form_method) == "get")
					$this->form_enctype = "application/x-www-form-urlencoded";
				else if (strtolower($this->form_method) == "post")
					$this->form_enctype = "multipart/form-data";
			}

			$this->doEvent("on_page_process", array($this));

			$components_keys = array_keys($this->components);

			// After params, process page contents (without parsing templates)
			foreach ($components_keys as $key => $item)
			{
				if (is_array($this->components[$item]->widget_deps) && count($this->components[$item]->widget_deps))
				{
					foreach ($this->components[$item]->widget_deps as $subkey => $subvalue)
					{
						if ($subvalue["options"])
							call_user_func_array(array($this->widgets[$subvalue["name"]], "process"), $subvalue["options"]);
						else
							$this->widgets[$subvalue["name"]]->process($this->components[$item]);
					}
					reset($this->components[$item]->widget_deps);
				}
				$success = false;
				if (FF_ENABLE_MEM_PAGE_CACHING && isset($this->components[$item]->cache_get_resources) && count($this->components[$item]->cache_get_resources))
					$res = $this->cache->get($this->request_key . "_" . $item, $success);
				if ($success)
				{
					$this->components_buffer[$item] = $res;
				}
				else
					$this->components[$item]->process();
			}
			reset($components_keys);

			// After processing, retrieve output
			foreach ($components_keys as $key => $item)
			{
				if (!is_subclass_of($this->components[$item], "ffDetails_base") && $this->components[$item]->display !== false)
				{
					if ($this->components_buffer[$item] === null)
					{
						$this->components_buffer[$item] = $this->components[$item]->process_interface();
						$ret = $this->componentWidgetsProcess($item);
						$headers = $ret["headers"];
						$footers = $ret["footers"];

						if (is_subclass_of($this->components[$item], "ffRecord_base") && count($this->components[$item]->detail))
						{
							foreach ($this->components[$item]->detail as $subkey => $subvalue)
							{
								$ret = $this->componentWidgetsProcess($this->components[$item]->detail[$subkey]->id);
								$headers .= $ret["headers"];
								$footers .= $ret["footers"];
							}
							reset($this->components[$item]->detail);
						}

						if (is_array($this->components_buffer[$item]))
							$this->components_buffer[$item]["html"] = $headers . $this->components_buffer[$item]["html"] . $footers;
						else
							$this->components_buffer[$item] = $headers . $this->components_buffer[$item] . $footers;

						if (FF_ENABLE_MEM_PAGE_CACHING && isset($this->components[$item]->cache_get_resources) && count($this->components[$item]->cache_get_resources))
						{
							call_user_func_array(array($this->cache, "set"),
									array_merge(
										array(
												$this->request_key . "_" . $item
												, null
												, $this->components_buffer[$item]
										),
										$this->components[$item]->cache_get_resources
									)
								);
						}
					}
				}
			}
			reset($components_keys);
		}

		$this->tplProcessBounceComponents();

		$rc = $this->doEvent("on_after_process_components", array($this));

		$this->tplProcess();
		if (strlen($this->layer))
			$this->tplProcessLayout();
		
		$this->widgetsProcess();

		return $this->tplParse($output_result);
	}

	public function tplLoad()
	{
		if ($this->template_loaded)
		    return;

		$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
		$this->tpl[0]->load_file($this->template_file, "main");

		$this->tpl[0]->strip_extra_newlines = $this->strip_extra_newlines;
        
		// ff.js
		$this->tpl[0]->set_var("phpsession_name", session_name());
		
        $this->doEvent("on_tpl_load", array($this, $this->tpl));		
		
        $this->tplProcessVars($this->tpl);
		
		$this->tpl[0]->set_var("SectFormHidden", "");
		if (is_array($this->hidden_fields) && count($this->hidden_fields))
		{
			foreach ($this->hidden_fields as $key => $value)
			{
				$this->tpl[0]->set_var("varname", $key);

				if (is_object($value["field"]))
				{
					switch (get_class($value["field"]))
					{
						case "ffField":
						case "ffData":
							$this->tpl[0]->set_var("varvalue", $value["field"]->getValue($value["type"], $value["locale"]));
							break;
					}
				}
				else
					$this->tpl[0]->set_var("varvalue", $value["field"]);
				
				$this->tpl[0]->parse("SectFormHidden", true);
				$this->tpl[0]->parse("SectHiddenFields", false);
			}
			reset($this->hidden_fields);
		}
		
		if (is_array($this->globals) && count($this->globals))
		{
			foreach ($this->globals as $key => $value)
			{
				if (!$value["display_hidden"])
					continue;
				
				$this->tpl[0]->set_var("varname", $key);
				$this->tpl[0]->set_var("varvalue", $this->retrieve_global($key));
				$this->tpl[0]->parse("SectFormHidden", true);
				$this->tpl[0]->parse("SectHiddenFields", false);
			}
			reset($this->globals);
		}
			
		$this->doEvent("on_tpl_loaded", array($this, $this->tpl));

		// LAYER SECTION
		$this->tpl_layer[0] = ffTemplate::factory($this->getLayerDir("layer_" . $this->layer  . ".html"));
		$this->tpl_layer[0]->load_file("layer_" . $this->layer  . ".html", "main");

		$this->tpl_layer[0]->strip_extra_newlines = $this->strip_extra_newlines;
        
        $res = $this->doEvent("on_tpl_layer_load", array($this, $this->tpl_layer[0]));

		$this->tplProcessVars($this->tpl_layer);
		$this->tplSetGlobals($this->tpl_layer);
		
		$res = $this->doEvent("on_tpl_layer_loaded", array($this, $this->tpl_layer[0]));
			
		// SECTIONS
		if (strlen($this->navbar))
		{
			ffErrorHandler::raise("Obsolete use of ->navbar, use ->sections[\"navbar\"] instead", E_USER_ERROR, $this, get_defined_vars());
		}
		
		if (strlen($this->topbar))
		{
			ffErrorHandler::raise("Obsolete use of ->topbar, use ->sections[\"topbar\"] instead", E_USER_ERROR, $this, get_defined_vars());
		}
		
		if (is_array($this->sections) && count($this->sections))
		{
			foreach ($this->sections as $key => $value)
			{
				if (strlen($value["name"]))
				{
					if ($value["is_php"])
					{
						ob_start();
						require($value["name"]);
						$this->tpl[0]->set_var($key, ob_get_contents());
						$this->tpl_layer[0]->set_var($key, ob_get_contents());
						ob_end_clean();
					}
					else
					{
						if ($value["dir"] === null)
							$this->sections[$key]["tpl"] = ffTemplate::factory($this->disk_path . "/themes/" . $this->theme . "/layouts");
						else
							$this->sections[$key]["tpl"] = ffTemplate::factory($value["dir"]);

						$this->sections[$key]["tpl"]->load_file($key . "_" . $value["name"] . ".html", "main");
						
						$this->sections[$key]["tpl"]->strip_extra_newlines = $this->strip_extra_newlines;
						
						$this->tplProcessVars(array(&$this->sections[$key]["tpl"]));
						$this->tplSetGlobals(array(&$this->sections[$key]["tpl"]));
						
						if ($this->sections[$key]["events"] === null)
							$this->sections[$key]["events"] = new ffEvents();
						else
							$this->sections[$key]["events"]->doEvent("on_load_template", array(&$this, &$this->sections[$key]["tpl"]));
					}
				}
			}
			reset($this->sections);
		}
                  
		// END OF LOADING
		$this->template_loaded = true;
	}

	protected function tplProcessBounceComponents()
	{
		$varcount = FALSE;
		if (is_array($this->bounce_components) && count($this->bounce_components))
		{
			foreach ($this->bounce_components as $key => $value)
			{
				if (isset($this->params[$key]) && is_array($this->params[$key]) && count($this->params[$key]))
				{
					$varcount = true;
					foreach ($this->params[$key] as $subkey => $subvalue)
					{
						if (is_array($subvalue))
						{
							$this->tplProcessBounceArray($subvalue, $key, $subkey);
						}
						elseif ($subkey !== "frmAction")
						{
							$this->tpl[0]->set_var("varname", $key . "_" . $subkey);
							$this->tpl[0]->set_var("varvalue", $subvalue);
							$this->tpl[0]->parse("SectFormHidden", true);
						}
					}
					reset($this->params[$key]);
				}
			}
			reset($this->bounce_components);
		}
		return;
	}
		
	protected function tplProcessBounceArray($array, $prefix, $arrayname, $arraykeys = "")
	{
		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				$this->tplProcessBounceArray($value, $prefix, $arrayname, $arraykeys . "[" . $key . "]");
			}
			else
			{
				$this->tpl[0]->set_var("varname", $prefix . "_" . $arrayname . $arraykeys . "[" . $key . "]");
				$this->tpl[0]->set_var("varvalue", $value);
				$this->tpl[0]->parse("SectFormHidden", true);
			}
		}
		reset($array);
	}
		
	protected function tplProcessVars($tpl)
	{
		$tpl[0]->set_var("site_path", $this->site_path);
		$tpl[0]->set_var("language", FF_LOCALE);

		$tpl[0]->set_var("title", $this->title);
        if($this->class_body)
            $tpl[0]->set_var("class_body", "class=\"" . $this->class_body . "\"");
        
		$tpl[0]->set_var("theme", $this->theme);
		$tpl[0]->set_var("layer", $this->layer);
		
		$tpl[0]->set_var("encoded_this_url", rawurlencode($_SERVER['REQUEST_URI']));

		foreach ($this->global_params as $key => $value)
		{
			if (is_array($value))
			{
				$tmp = "";
				foreach ($value as $subkey => $subvalue)
				{
					if (!is_array($subvalue))
				    {
				        $tpl[0]->set_var("[VAR_$key[$subkey]]", $subvalue);
				        $tpl[0]->set_var("[VAR_URL_$key[$subkey]]", urlencode($subvalue));
				        $tmp .= $key . "[" . $subkey . "]=" . urlencode($subvalue) . "&";
					}
				}
				reset($value);
				$tpl[0]->set_var("[VAR_URL_$key]", $tmp);
			}
			else
			{
				$tpl[0]->set_var("[VAR_$key]", $value);
				$tpl[0]->set_var("[VAR_URL_$key]", urlencode($value));
			}
		}
		reset($this->global_params);

		$tpl[0]->set_var("query_string", $this->get_script_params());
		
		if (is_array($this->keys) && count($this->keys))
		{
			foreach ($this->keys as $key => $value)
			{
				$tpl[0]->set_var("_key_name", $key);
				$tpl[0]->set_var("_key_value", $value);
				$tpl[0]->parse("SectFormKeys", true);
			}
			reset($this->keys);
		}
		else
			$tpl[0]->set_var("SectFormKeys", "");

		if (is_array($this->fixed_vars) && count($this->fixed_vars))
		{
			foreach ($this->fixed_vars as $key => $value)
			{
				$tpl[0]->set_var($key, $value);
			}
			reset($this->fixed_vars);
		}
	}
	
	protected function tplParse($output_result)
	{
		$this->doEvent("on_tpl_parse", array($this));
		
        $this->parse_css();
        $this->parse_js();
        $this->parse_meta(); 
        $this->parse_html_attr();       

        if($this->use_own_js)
			$this->tpl[0]->parse("SectFFJS", false);
		else
			$this->tpl[0]->set_var("SectFFJS", "");

        $this->doEvent("on_tpl_parsed_header", array($this));

		$this->tpl[0]->set_var("content", $this->output_buffer);
        
        $this->doEvent("on_tpl_parsed", array($this));

		if ($output_result)
		{
			$this->tpl[0]->pparse("main", false);
			return true;
		}
		else
			return $this->tpl[0]->rpparse("main", false);
	}
			
	function tplSetGlobals($tpl)
	{
		if (is_array($this->globals) && count($this->globals))
		{
			foreach ($this->globals as $key => $value)
			{
				$keyval = $this->retrieve_global($key);
				$tpl[0]->set_var($key, $keyval);
			}
			reset($this->globals);
			$tpl[0]->set_var("globals", $this->get_globals());
		}
	}

	protected function tplProcess()
	{
		if ($this->struct_process())
			$this->tpl[0]->parse("SectHeaders", false);
		else
			$this->tpl[0]->set_var("SectHeaders", "");
		
		$this->doEvent("on_tpl_process", array($this, $this->tpl_layer[0]));

		if ($this->use_own_form !== false)
		{
            $this->tpl[0]->set_var("form_id", $this->form_id);
            $this->tpl[0]->set_var("form_name", $this->form_name);
			$this->tpl[0]->set_var("form_method", (strlen($this->form_method) ? strtolower($this->form_method) : "get"));
			$this->tpl[0]->set_var("form_enctype", $this->form_enctype);
			$this->tpl[0]->set_var("form_action", $this->form_action);
			$this->tpl[0]->set_var("script_name", $this->get_script_name() . "?" . $this->get_script_params());

			$this->tpl[0]->parse("SectFormHeader", false);
			$this->tpl[0]->parse("SectFormFooter", false);
		}
		else
		{
			$this->tpl[0]->set_var("SectFormHeader", "");
			$this->tpl[0]->set_var("SectFormFooter", "");
		}

		foreach ($this->contents as $key => $content)
		{
			if ($content["group"] === true)
			{
				if (!count($this->groups[$key]["contents"]))
					continue;

				foreach ($this->groups[$key]["contents"] as $subkey => $subvalue)
				{
					$this->output_buffer .= $this->getContentData($subvalue["data"]);
				}
			}
			else
			{
				$this->output_buffer .= $this->getContentData($content["data"]);
			}
		}
		reset ($this->contents);

		$rc = $this->doEvent("on_fixed_process_before", array($this));
		$this->output_buffer = $this->fixed_pre_content . $this->output_buffer . $this->fixed_post_content;
	}

	function addSection($sName)
	{
		if (!isset($this->sections[$sName]))
		{
			$this->sections[$sName] = array(
											  "dir" => null
											, "name" => ""
											, "tpl" => null
											, "is_php" => false
											, "events" => new ffEvents()
										);
		}
	}

	protected function tplProcessLayout()
	{
		$this->doEvent("on_tpl_layer_process", array($this, $this->tpl_layer[0]));
		
		// process components buffer
		foreach($this->components as $key => $item)
		{
			if ($this->components[$key]->use_own_location && $this->components[$key]->display !== FALSE)
			{
				if ($this->components[$key]->location_name === null)
					$this->tpl_layer[0]->set_var($key, $this->components_buffer[$key]);
				else
					$this->tpl_layer[0]->set_var($this->components[$key]->location_name, $this->components_buffer[$key]);
			}
				
			if (get_class($this->components[$key]) == "ffGrid_html" && $this->components[$key]->search_container !== null)
			{
				$this->sections[$this->components[$key]->search_container]["content"] .= $this->components[$key]->search_container_buffer;
			}
		}
		reset($this->components);
		
		if (is_array($this->sections) && count($this->sections))
		{
			foreach ($this->sections as $key => $value)
			{
				if ($value["tpl"] === null)
					continue;
		
				$this->sections[$key]["events"]->doEvent("on_process", array($this, $this->sections[$key]["tpl"]));
				
				// process components buffer
				foreach ($this->components as $subkey => $item)
				{
					if ($this->components[$subkey]->use_own_location && $this->components[$subkey]->display !== FALSE)
					{
						if ($this->components[$subkey]->location_name === null)
							$value["tpl"]->set_var($subkey, $this->components_buffer[$subkey]);
						else
							$value["tpl"]->set_var($this->components[$subkey]->location_name, $this->components_buffer[$subkey]);
					}
				}
				reset($this->components);
				$value["tpl"]->set_var("content", $value["content"]);
				
				$this->tpl_layer[0]->set_var($key, $value["tpl"]->rpparse("main", false));
			}
			reset($this->sections);
		}
		
		$this->tpl_layer[0]->set_var("content", $this->output_buffer);
		$this->output_buffer = $this->tpl_layer[0]->rpparse("main", false);
	}

    public function parse_css() 
    {
        $this->tpl[0]->set_var("SectCss", "");
        $this->tpl[0]->set_var("SectAsyncCssPlugin", "");
        
        if (is_array($this->page_css) && count($this->page_css))
        {
        	if($this->browser === null)
        		$this->browser = $this->getBrowser();

            foreach ($this->page_css as $key => $value)
            {
            	$tmp_path = "";
            	$tmp_file = "";

                if(isset($this->override_css[$key]) && strlen($this->override_css[$key])) {
                    $tmp_path = ffcommon_dirname($this->override_css[$key]) . "/";
                    $tmp_file = basename($this->override_css[$key]);
                } else {
            	    $res = $this->doEvent("on_css_parse", array($this, $key, $value["path"], $value["file"]));
            	    $rc = end($res);

            	    if ($rc === null)
            	    {
					    if ($value["path"] === null)
						    $tmp_path = $this->site_path . "/themes/" . $this->theme . "/css";
					    elseif (strlen($value["path"])) {
                            if (
                                substr(strtolower($value["path"]), 0, 7) == "http://"
                                || substr(strtolower($value["path"]), 0, 8) == "https://"
                            )
						        $tmp_path = $value["path"];
                    	    elseif($this->compact_css)
                    		    $tmp_path = $value["path"];
						    else
							    $tmp_path = $this->site_path . $value["path"];
                        }
					    if ($value["file"] === null)
						    $tmp_file = $key . ".css";
					    else
					    {
						    $tmp_file = $value["file"];
					    }
				    }
				    else
				    {
                    	if($this->compact_css)
                    		$tmp_path = $rc["path"];
						else
							$tmp_path = $this->site_path . $rc["path"];

					    $tmp_file = $rc["file"];
				    }

                    $tmp_path = rtrim($tmp_path, "/") . "/";
                    $tmp_file = ltrim($tmp_file, "/");

				    if($value["path"] === null && file_exists($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . strtolower($this->browser["name"]) . $this->browser["majorver"] . "/" . $tmp_file)) {
    					$tmp_path = FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . strtolower($this->browser["name"]) . $this->browser["majorver"] . "/";
					}
                }

				if($value["async"]) 
				{
	                $this->tpl[0]->set_var("css_path", $tmp_path);
	                $this->tpl[0]->set_var("css_file", $tmp_file);
	                $this->tpl[0]->set_var("css_rel", $value["rel"]);
	                $this->tpl[0]->set_var("css_type", $value["type"]);
	                $this->tpl[0]->parse("SectAsyncCssPlugin", true);
				} 
				else 
				{
                    if( 
                        $this->compact_css 
                        && !$value["exclude_compact"]
                        && $value["rel"] == "stylesheet"
                        && $value["type"] == "text/css"
                    ) {
                        if($value["media"] === null)
                            $tmp_media = "default";
                        else
                            $tmp_media = $value["media"];

                        if (
                            substr(strtolower($tmp_path), 0, 7) == "http://"
                            || substr(strtolower($tmp_path), 0, 8) == "https://"
                        )
                            $this->css_buffer[$tmp_media][]["path"] = $tmp_path . $tmp_file;
                        else
                            $this->css_buffer[$tmp_media][]["path"] = FF_DISK_PATH . $tmp_path . $tmp_file;

                    } else {
                        if (
                            substr(strtolower($tmp_path), 0, 7) == "http://"
                            || substr(strtolower($tmp_path), 0, 8) == "https://"
                            || (strlen(FF_SITE_PATH) && strpos($tmp_path, FF_SITE_PATH) === 0) 
                        ) {
                            $this->tpl[0]->set_var("css_path", $tmp_path);
                        } else {
                            $this->tpl[0]->set_var("css_path", FF_SITE_PATH . $tmp_path);
                        }
                        $this->tpl[0]->set_var("css_file", $tmp_file);
                        $this->tpl[0]->set_var("css_rel", $value["rel"]);
                        $this->tpl[0]->set_var("css_type", $value["type"]);
                        if($value["media"] !== null) {
                            $this->tpl[0]->set_var("css_media", $value["media"]);
                            $this->tpl[0]->parse("SectCssMedia", false);
                        } else {
                            $this->tpl[0]->set_var("SectCssMedia", "");
                        }
                        $this->tpl[0]->parse("SectCss", true);
                    }
				}

                /*$this->tpl[0]->set_var("key", $key);
                $this->tpl[0]->parse("SectLoadedCSS", true);*/
            } 
            reset($this->page_css);
        }
        else
        {
            $this->tpl[0]->set_var("SectCss", "");
            $this->tpl[0]->set_var("SectAsyncCssPlugin", "");
		}
    }
    
    public function parse_js() 
    {
        $this->tpl[0]->set_var("SectJs", "");
        $this->tpl[0]->set_var("SectAsyncJsPlugin", "");

		if (is_array($this->page_js) && count($this->page_js))
        {
        	if($this->browser === null)
        		$this->browser = $this->getBrowser();

            foreach ($this->page_js as $key => $value)
            {
            	$tmp_path = "";
            	$tmp_file = "";
            	
                if($value["embed"])
                {
                    if($this->compact_js) {
						$this->js_buffer[]["content"] = $value["embed"];
					} else {
	                    $this->tpl[0]->set_var("js_embed", $value["embed"]);
	                    $this->tpl[0]->set_var("SectJsSrc", "");
	                    $this->tpl[0]->parse("SectJs", true);
					}
                } 
                else 
                {
                    $this->tpl[0]->set_var("js_embed", "");

                    if(isset($this->override_js[$key]) && strlen($this->override_js[$key])) {
                        $tmp_path = ffcommon_dirname($this->override_js[$key]) . "/";
                        $tmp_file = basename($this->override_js[$key]);
                    } else {
            	        $res = $this->doEvent("on_js_parse", array($this, $key, $value["path"], $value["file"]));
            	        $rc = end($res);
            	        
            	        if ($rc === null)
            	        {
	                        if ($value["path"] === null)
	                            $tmp_path = $this->site_path . "/themes/" . $this->theme . "/javascript/";
                            elseif (strlen($value["path"])) {
							    if (
								    substr(strtolower($value["path"]), 0, 7) == "http://"
								    || substr(strtolower($value["path"]), 0, 8) == "https://"
							    )
                            	    $tmp_path = $value["path"] . "/";
                                elseif($this->compact_js)
                            	    $tmp_path = $value["path"] . "/";
                                else
								    $tmp_path = $this->site_path . $value["path"] . "/";
						    
						    }

	                        if ($value["file"] === null)
	                            $tmp_file = $key . ".js";
	                        else
	                        {
	                            $tmp_file = $value["file"];
	                        }
				        }
				        else
				        {
                            if($this->compact_js)
                            	$tmp_path = $rc["path"];
                            else
								$tmp_path = $this->site_path . $rc["path"];

					        $tmp_file = $rc["file"];
				        }
				        
					    if($value["path"] === null && file_exists($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . strtolower($this->browser["name"]) . $this->browser["majorver"] . "/" . $tmp_file)) {
    						$tmp_path = FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . strtolower($this->browser["name"]) . $this->browser["majorver"] . "/";
						}
                    }
                    
				    if($value["async"])
				    { 
	                    $this->tpl[0]->set_var("js_tag", $key);
	                    $this->tpl[0]->set_var("js_path", $tmp_path);
	                    $this->tpl[0]->set_var("js_file", $tmp_file);
	                    $this->tpl[0]->parse("SectAsyncJsPlugin", true);
				    }
				    else 
				    {
	                    if( 
	                    	$this->compact_js 
	                    	&& !$value["exclude_compact"]
                            && !(substr(strtolower($tmp_path), 0, 7) == "http://"
                                || substr(strtolower($tmp_path), 0, 8) == "https://")
						) {
							$this->js_buffer[]["path"] = FF_DISK_PATH . $tmp_path . $tmp_file;
						} else {
                            if (
                                substr(strtolower($tmp_path), 0, 7) == "http://"
                                || substr(strtolower($tmp_path), 0, 8) == "https://"
                                || (strlen(FF_SITE_PATH) && strpos($tmp_path, FF_SITE_PATH) === 0) 
                            ) {
                                $this->tpl[0]->set_var("js_path", $tmp_path);
                            } else {
                                $this->tpl[0]->set_var("js_path", FF_SITE_PATH . $tmp_path);
                            }

		                    $this->tpl[0]->set_var("js_file", $tmp_file);
	                        $this->tpl[0]->parse("SectJsSrc", false);
		                    $this->tpl[0]->parse("SectJs", true);
						}
				    }
                }
                /*$this->tpl[0]->set_var("key", $key);
                $this->tpl[0]->parse("SectLoadedJS", true);*/
            }
            reset($this->page_js);
        }
        else
        {
            $this->tpl[0]->set_var("SectJs", "");
            $this->tpl[0]->set_var("SectAsyncJsPlugin", "");
		}
	}

    public function parse_meta() 
    {
        $this->tpl[0]->set_var("SectMeta", "");
        if (is_array($this->page_meta) && count($this->page_meta))
        {
            foreach ($this->page_meta as $key => $value)
            {
            	$this->tpl[0]->set_var("meta_type", $value["type"]);
                $this->tpl[0]->set_var("meta_content", $value["content"]);
                $this->tpl[0]->set_var("meta_name", $value["name"]);
                $this->tpl[0]->parse("SectMeta", true);
            }
            reset($this->page_meta);
        }
        else
            $this->tpl[0]->set_var("SectMeta", "");
    }

    public function parse_html_attr() 
    {   
        $this->tpl[0]->set_var("SectHtmlAttr", "");
        if (is_array($this->page_html_attr) && count($this->page_html_attr))
        {
            foreach ($this->page_html_attr as $key => $value)
            {
            	$this->tpl[0]->set_var("attr_type", $value["type"]);
                $this->tpl[0]->set_var("attr_content", $value["content"]);
                $this->tpl[0]->parse("SectHtmlAttr", true);
            }
            reset($this->page_html_attr);
        }
        else
            $this->tpl[0]->set_var("SectHtmlAttr", "");
    }   
     
	function widgetLoad($name, $path = null, &$ref = null)
	{
		parent::widgetLoad($name, $path, $ref);
		
		if(is_array($this->widgets[$name]->js_deps) && count($this->widgets[$name]->js_deps))
		{
			foreach ($this->widgets[$name]->js_deps AS $js_key => $js_value)
			{
				if (is_array($js_value))
				{
					$this->tplAddJs($js_key, $js_value["file"], $js_value["path"]);
				}
				elseif (is_null($js_value))
				{
					if(file_exists(FF_DISK_PATH . $this->getThemePath(false) . "/javascript/" . $js_key . ".js"))
						$this->tplAddJs($js_key, $js_key . ".js", $this->getThemePath(false) . "/javascript");
					elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library/" . $js_key . "/" . $js_key . ".js"))
						$this->tplAddJs($js_key, $js_key . ".js", FF_THEME_DIR . "/library/" . $js_key);
				}
				else
				{
					if(file_exists(FF_DISK_PATH . $this->getThemePath(false) . "/javascript" . $js_value))
						$this->tplAddJs($js_key, null, $this->getThemePath(false) . "/javascript" . $js_value);
					elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library" . $js_value))
						$this->tplAddJs($js_key, null, FF_THEME_DIR . "/library" . $js_value);
					//$this->tplAddJs($js_key, $js_value, null);
				}
			}
			reset($this->widgets[$name]);
		}
		
		if(is_array($this->widgets[$name]->css_deps) && count($this->widgets[$name]->css_deps))
		{
			foreach ($this->widgets[$name]->css_deps AS $css_key => $css_value)
			{
				if(is_array($css_value))
				{
					if(!is_null($css_value["path"])) {
						//da gestire se sara necessario un giorno
					} else {
                        if($this->jquery_ui_force_theme !== null && strpos($oPage->jquery_ui_force_theme, "/") === 0 && file_exists(FF_DISK_PATH . $this->jquery_ui_force_theme . "/" . $css_value["file"]))
                            $this->tplAddCss($css_key, $css_value["file"], $this->jquery_ui_force_theme);
						elseif($this->jquery_ui_force_theme !== null && file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library/" . $css_key . "/themes/" . $this->jquery_ui_force_theme . "/" . $css_value["file"]))
							$this->tplAddCss($css_key, $css_value["file"], FF_THEME_DIR . "/library/" . $css_key . "/themes/" . $this->jquery_ui_force_theme);
						elseif(file_exists(FF_DISK_PATH . $this->getThemePath(false) . "/css/" . $css_key . "/" . $css_value["file"]))
							$this->tplAddCss($css_key, $css_value["file"], $this->getThemePath(false) . "/css/" . $css_key);
						elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library/" . $css_key . "/" . $css_value["file"]))
							$this->tplAddCss($css_key, $css_value["file"], FF_THEME_DIR . "/library/" . $css_key);
						elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library/" . $css_key . "/themes/" . $this->jquery_ui_theme . "/" . $css_value["file"]))
							$this->tplAddCss($css_key, $css_value["file"], FF_THEME_DIR . "/library/" . $css_key . "/themes/" . $this->jquery_ui_theme);
					}
				}
				elseif (is_null($css_value))
				{
					if(file_exists(FF_DISK_PATH . $this->getThemePath(false) . "/css/" . $css_key . ".css"))
						$this->tplAddCss($css_key, null, $this->getThemePath(false) . "/css");
					elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library/" . $css_key . "/" . $css_key . ".css"))
						$this->tplAddCss($css_key, null, FF_THEME_DIR . "/library/" . $css_key);
				} 
				else
				{
					if(file_exists(FF_DISK_PATH . $this->getThemePath(false) . "/css" . $css_value))
						$this->tplAddCss($css_key, $this->getThemePath(false) . "/css" . $css_value, "");
					elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library" . $css_value))
						$this->tplAddCss($css_key, FF_THEME_DIR . "/library" . $css_value, "");
				}					
				/*if (!is_null($css_value))
					$this->tplAddCss($css_key, $this->getThemePath() . "/css" . $css_value, "");
				else
				{
					$this->tplAddCss($css_key, null, $this->getThemePath() . "/css");
				}*/
			}
			reset($this->widgets[$name]);
		}
	}
	
	function getBrowser()
	{
	    $u_agent = $_SERVER['HTTP_USER_AGENT'];
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version= "";

	    //First get the platform?
	    if (preg_match('/linux/i', $u_agent)) {
	        $platform = 'linux';
	    }
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        $platform = 'mac';
	    }
	    elseif (preg_match('/windows|win32/i', $u_agent)) {
	        $platform = 'windows';
	    }
	   
	    // Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/iPad/i',$u_agent))
	    {
	        $bname = 'Ipad';
	        $ub = "Ipad";
	    }
		elseif(preg_match('/iPhone/i',$u_agent))
	    {
	        $bname = 'iPhone';
	        $ub = "iPhone";
	    }
		elseif(preg_match('/iPod/i',$u_agent))
	    {
	        $bname = 'Ipod';
	        $ub = "Ipod";
	    }
	    elseif(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
	    {
	        $bname = 'Internet Explorer';
	        $ub = "MSIE";
	    }
	    elseif(preg_match('/Firefox/i',$u_agent))
	    {
	        $bname = 'Mozilla Firefox';
	        $ub = "Firefox";
	    }
	    elseif(preg_match('/Chrome/i',$u_agent))
	    {
	        $bname = 'Google Chrome';
	        $ub = "Chrome";
	    }
	    elseif(preg_match('/Safari/i',$u_agent))
	    {
	        $bname = 'Apple Safari';
	        $ub = "Safari";
	    }
	    elseif(preg_match('/Opera/i',$u_agent))
	    {
	        $bname = 'Opera';
	        $ub = "Opera";
	    }
	    elseif(preg_match('/Netscape/i',$u_agent))
	    {
	        $bname = 'Netscape';
	        $ub = "Netscape";
	    }

	    // finally get the correct version number
/*	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>' . join('|', $known) .
	    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!preg_match_all($pattern, $u_agent, $matches)) {
	        // we have no matching number just continue
	    }
*/	   
	    // see how many we have
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        //we will have two since we are not using 'other' argument yet
	        //see if version is before or after the name
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
	            $version= $matches['version'][0];
	        }
	        else {
	            $version= $matches['version'][1];
	        }
	    }
	    else {
	        $version= $matches['version'][0];
	    }
	   
	    // check if we have a number
	    if ($version==null || $version=="") {$version="?";}
	   
	    return array(
	        'userAgent' 	=> $u_agent
	        , 'extendname'  => $bname
	        , 'name'		=> $ub
	        , 'majorver'   	=> (strpos($version, ".") === false ? $version : substr($version, 0, strpos($version, ".")))
	        , 'lowerver'   	=> (strpos($version, ".") === false ? $version : substr($version, strpos($version, ".") + 1))
	        , 'platform'  	=> $platform
	        , 'pattern'    	=> $pattern
	    );
	}
}