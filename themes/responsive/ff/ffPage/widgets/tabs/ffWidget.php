<?php

class ffWidget_tabs extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)

	var $template_file 	 = "ffWidget.html";

	var $class			= "ffWidget_tabs";

	var $widget_deps	= array();
    var $js_deps = array(
							  "jquery" 			=> null
						);
    var $css_deps 		= array();    
	// PRIVATE VARS
	var $oPage			= null;
	var $source_path	= null;
	var $style_path		= null;

	var $tpl 			= null;

	var $processed_id	= array();
	var $tab_mode 		= "top"; //top OR left OR right
 	var $framework_css = array(
		"menu" => array(
			"class" => null
			//, "tab" => null //menu OR menu-vertical OR menu-vertical-right
			, "wrap_menu" => null	// null OR array(xs, sm, md, lg)
			, "wrap_pane" => null	// null OR array(xs, sm, md, lg)
		)
		, "menu-item" => array(
			"class" => null
			, "tab" => "menu-item"
		)
		, "pane" => array(
			"class" => null
			, "tab" => "pane"
		)
		, "pane-item" => array(
			"class" => null
			, "tab" => "pane-item-effect" // pane-item-effect OR pane-item
		)
	);

	function __construct(ffPage_base $oPage = null, $source_path = null, $style_path = null)
	{
		$this->get_defaults();

		$this->oPage = array(&$oPage);

		if ($source_path !== null)
			$this->source_path = $source_path;
		elseif ($oPage !== null)
			$this->source_path = $oPage->getThemePath();

		$this->style_path = $style_path;

		if(!$oPage->framework_css["name"])
			$this->js_deps["jquery.ui"] = null;
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

	function process($id, &$data, ffPage_base &$oPage, $component = null)
	{
		if ($component !== null)
		{
			$tpl_id = $component;
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
			$oPage->components[$component]->processed_widgets[$id] = "tabs";
		}
		else
		{
			$tpl_id = "main";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
		}

		if(isset($data["tab_mode"]))
			$this->tab_mode = $data["tab_mode"];

		if(is_array($data["framework_css"]))
			$this->framework_css = array_replace_recursive($this->framework_css, $data["framework_css"]);

		$this->tpl[$tpl_id]->set_var("site_path", $oPage->site_path);
		$this->tpl[$tpl_id]->set_var("theme", $oPage->getTheme());

		$this->tpl[$tpl_id]->set_var("component_id", $id);
		$this->tpl[$tpl_id]->set_var("framework_css_name", $oPage->framework_css["name"]);
		
		$this->tpl[$tpl_id]->set_var("SectHeaderTabRow", "");
		$this->tpl[$tpl_id]->set_var("SectHeaderRowBottom", "");
		$this->tpl[$tpl_id]->set_var("SectBodyRow", "");	
		
		/**
		* Tab init
		*/
		if($this->tab_mode) {
			if($this->tab_mode === true)
				$this->tab_mode = "top";

			switch($this->tab_mode) {
				case "right":
					$wrap_tab_need = cm_getClassByFrameworkCss("menu-vertical-wrap", "tab");
					$this->framework_css["menu"]["tab"] = "menu-vertical-right";
					$tab_position = "Bottom";
					$default_wrap_menu = array(
											"xs" => 4
											, "sm" => 3
											, "md" => 2
											, "lg" => 1
										);	
					$default_wrap_pane = array(
											"xs" => 8
											, "sm" => 9	
											, "md" => 10
											, "lg" => 11
										);
					break;
				case "left":
					$wrap_tab_need = cm_getClassByFrameworkCss("menu-vertical-wrap", "tab");
					$default_wrap_menu = array(
											"xs" => 4
											, "sm" => 3
											, "md" => 3
											, "lg" => 2
										);	
					$default_wrap_pane = array(
											"xs" => 8
											, "sm" => 9	
											, "md" => 9
											, "lg" => 10
										);
					$this->framework_css["menu"]["tab"] = "menu-vertical";
					break;
				case "top":
					$this->framework_css["menu"]["tab"] = "menu";
					break;
				default:
			}
			
			$first_menu_current = cm_getClassByFrameworkCss("menu-current", "tab");
			if(strpos($this->framework_css["pane-item"]["tab"], "effect") === false) {
				$first_pane_current = cm_getClassByFrameworkCss("pane-current", "tab");
			} else {
				$first_pane_current = cm_getClassByFrameworkCss("pane-current-effect", "tab");
			}
		}		
		
		$i = 0;
		foreach ($data["contents"] as $subkey => $subvalue)
		{
			if ($subvalue["data"] === null)
				continue;

			if (isset($subvalue["title"]))
				$title = $subvalue["title"];
			else if (is_object($subvalue["data"]))
			{
				if (
						$subvalue["data"] instanceof ffGrid_base
						|| $subvalue["data"] instanceof ffRecord_base
						|| $subvalue["data"] instanceof ffDetails_base
					)
					$title = $subvalue["data"]->title;
				if (
						$subvalue["data"] instanceof ffField_base
					)
					$title = $subvalue["data"]->label;
			}
			else
				$title = $key;

			$buttons = $subvalue["buttons"];

			$this->tpl[$tpl_id]->set_var("rrow", $i);
			$this->tpl[$tpl_id]->set_var("tab_label", $title);
			$this->tpl[$tpl_id]->set_var("tab_buttons", $buttons);
			
			$ret = $oPage->getContentData($subvalue["data"]);
			if (is_array($ret))
			{
				if ($oPage->isXHR())
				{
					$oPage->output_buffer["headers"] .= $ret["headers"];
					$oPage->output_buffer["footers"] .= $ret["footers"];
					$this->tpl[$tpl_id]->set_var("content", $ret["html"]);
				}
				else
				{
					$this->tpl[$tpl_id]->set_var("content", $ret["headers"] . $ret["html"] . $ret["footers"]);
				}
			}
			else
				$this->tpl[$tpl_id]->set_var("content", $ret);

				
			/**
			* Tab manage rows
			*/
			if($this->tab_mode)
			{
				$this->tpl[$tpl_id]->set_var("tab_pane_properties", cm_getClassByDef($this->framework_css["pane"], null, true) . cm_getClassByFrameworkCss("pane", "data", "tab"));
				$this->tpl[$tpl_id]->set_var("tab_pane_item_properties", cm_getClassByDef($this->framework_css["pane-item"], array("tab-label" => ffCommon_url_rewrite($title), "current" => $first_pane_current), true) . cm_getClassByFrameworkCss("pane-item", "data", "tab"));
				
				$this->tpl[$tpl_id]->set_var("tab_menu_properties", cm_getClassByDef($this->framework_css["menu"], null, true) . cm_getClassByFrameworkCss("menu", "data", "tab"));
				$this->tpl[$tpl_id]->set_var("tab_menu_item_properties", cm_getClassByDef($this->framework_css["menu-item"], array("current" => $first_menu_current), true));
				$this->tpl[$tpl_id]->set_var("tab_menu_link_properties", cm_getClassByFrameworkCss("menu-link", "data", "tab"));
			}

			$first_menu_current = "";
			$first_pane_current = "";				
				
			$this->tpl[$tpl_id]->parse("SectHeaderRow" . $tab_position, true);
			$this->tpl[$tpl_id]->parse("SectBodyRow", true);

			$i++;
		}		

		/**
		* Tab container
		*/
		if($this->tab_mode) {
			if(!$this->framework_css["menu"]["wrap_menu"] && $wrap_tab_need)
				$this->framework_css["menu"]["wrap_menu"] = $default_wrap_menu;

			if(!$this->framework_css["menu"]["wrap_pane"] && $wrap_tab_need)
				$this->framework_css["menu"]["wrap_pane"] = $default_wrap_pane;
			
			if($this->framework_css["menu"]["wrap_menu"]) {
				$this->tpl[$tpl_id]->set_var("tab_menu_wrap_start", '<div class="' . cm_getClassByFrameworkCss($this->framework_css["menu"]["wrap_menu"], "col") . '">');
				$this->tpl[$tpl_id]->set_var("tab_menu_wrap_end", '</div>');
			}
			if($this->framework_css["menu"]["wrap_pane"]) {
				$this->tpl[$tpl_id]->set_var("tab_pane_wrap_start", '<div class="' . cm_getClassByFrameworkCss($this->framework_css["menu"]["wrap_pane"], "col") . '">');
				$this->tpl[$tpl_id]->set_var("tab_pane_wrap_end", '</div>');
			}

	        $this->tpl[$tpl_id]->parse("SectHeaderTab" . $tab_position, false);
		}		
		
		$this->tpl[$tpl_id]->parse("SectBinding", true);

		return $this->tpl[$tpl_id]->rpparse("SectIstance", false);
	}

	function get_component_headers($id)
	{
		if ($this->oPage !== NULL) {//code for ff.js 
			$this->oPage[0]->tplAddJs("ff.ffPage.tabs", "tabs.js", FF_THEME_DIR . "/responsive/ff/ffPage/widgets/tabs");
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
		if ($this->oPage !== NULL) {//code for ff.js 
			$this->oPage[0]->tplAddJs("ff.ffPage.tabs", "tabs.js", FF_THEME_DIR . "/responsive/ff/ffPage/widgets/tabs");
			
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
