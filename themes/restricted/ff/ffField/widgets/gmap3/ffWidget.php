<?php
class ffWidget_gmap3
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)
	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_gmap3"; 

	var $widget_deps	= array();

	var $libraries		= array();
	
    var $js_deps		= array();
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

		$cm = cm::getInstance();
		if ($cm->oPage->compact_js)
			$cm->oPage->tplAddJs("ff.ffField.gmap3.async");
		else
			$cm->oPage->tplAddJs("ff.ffField.gmap3.sync");
	}
	
	function process($id, &$value, ffField_base &$Field)
	{
		if ($Field->parent !== null && strlen($Field->parent[0]->getIDIF()))
		{
			$tpl_id = $Field->parent[0]->getIDIF();
			$prefix = $tpl_id . "_";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
			$this->tpl[$tpl_id]->set_var("component", $tpl_id);
			$this->tpl[$tpl_id]->set_var("container", $prefix);
			//$Field->parent[0]->processed_widgets[$prefix . $id] = "gmap3";
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

        if(strlen($Field->widget_path))
            $this->tpl[$tpl_id]->set_var("widget_path", $Field->widget_path);
        else 
            $this->tpl[$tpl_id]->set_var("widget_path", "/themes/restricted/ff/ffField/widgets/gmap3");

		$this->tpl[$tpl_id]->set_var("key", $Field->gmap_key);
		$this->tpl[$tpl_id]->set_var("sensor", (0 ? "true" : "false"));
		
		$this->tpl[$tpl_id]->set_var("region", (strlen($Field->gmap_region) ? $Field->gmap_region : ""));
		
		
/*
		if ($Field->gmap_draggable)
			$this->tpl[$tpl_id]->set_var("draggable", "true");
		else
			$this->tpl[$tpl_id]->set_var("draggable", "false");
         
		$know_point = true;
*/
		$know_point = true;
		//ffErrorHandler::raise("asdf", E_USER_ERROR, null,get_defined_vars());
		if(is_array($value))
		{
			if (!strlen($value["lat"]->getValue()))
			{
				$this->tpl[$tpl_id]->set_var("lat", $Field->gmap_start_lat);
				if(!$Field->gmap_start_lat)
					$know_point = false;
			}
			else
				$this->tpl[$tpl_id]->set_var("lat", $value["lat"]->getValue());

			if (!strlen($value["lng"]->getValue()))
			{
				$this->tpl[$tpl_id]->set_var("lng", $Field->gmap_start_lng);
				if(!$Field->gmap_start_lng)
					$know_point = false;
			}
			else
				$this->tpl[$tpl_id]->set_var("lng", $value["lng"]->getValue());

			
			if (!strlen($value["title"]->getValue()))
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
				if($Field->gmap_force_search && strlen($value["title"]->getValue())) 
					$this->tpl[$tpl_id]->set_var("force_search", "true");
				else
					$this->tpl[$tpl_id]->set_var("force_search", "false");
			}
			
			if (!strlen($value["zoom"]->ori_value))
				$this->tpl[$tpl_id]->set_var("zoom", $Field->gmap_start_zoom);
			else
				$this->tpl[$tpl_id]->set_var("zoom", $value["zoom"]->getValue());
		} else {
			$know_point = false;
			$this->tpl[$tpl_id]->set_var("title", "");
			$this->tpl[$tpl_id]->set_var("force_search", "false");
			$this->tpl[$tpl_id]->set_var("zoom", $Field->gmap_start_zoom);
		}
		
		
		if(!$know_point) {
			$this->tpl[$tpl_id]->set_var("lat", $Field->gmap_start_lat);
			$this->tpl[$tpl_id]->set_var("lng", $Field->gmap_start_lng);
			$this->tpl[$tpl_id]->set_var("zoom", $Field->gmap_start_zoom);
		}
		//controllo dello zoom (ci deve essere o no)
		if (!strlen($Field->gmap3_zoom_control))
		{
			$this->tpl[$tpl_id]->set_var("zoom_control", "false");
		} else
		{
			$this->tpl[$tpl_id]->set_var("zoom_control", "true");
		}
		//dove posizionare il controllo dello zoom
		$this->tpl[$tpl_id]->set_var("zoom_control_position", $Field->gmap3_zoom_control_position);
		//quanto deve essere grande
		$this->tpl[$tpl_id]->set_var("zoom_control_style", $Field->gmap3_zoom_control_style);
		
		if (strlen($Field->gmap3_marker_icon))
		{
			$this->tpl[$tpl_id]->set_var("image", $Field->gmap3_marker_icon);
		}
		
		$this->tpl[$tpl_id]->set_var("marker_limit", $Field->gmap3_marker_limit);
		
//stile mappa
		//	controllo delle possibilità di scegliere il tipo di mappa(satellite, strada, ecc)
		if (!strlen($Field->gmap3_map_type_control))
		{
			$this->tpl[$tpl_id]->set_var("map_type_control", "false");
		} else
		{
			$this->tpl[$tpl_id]->set_var("map_type_control", "true");
		}
		//	aspetto del tipe control
		$this->tpl[$tpl_id]->set_var("map_type_control_options", $Field->gmap3_map_type_control_options);
		
		if(strlen($Field->gmap3_personal_style))
		{
                    $this->tpl[$tpl_id]->set_var("personalized_style", "true");
                    $this->tpl[$tpl_id]->set_var("text_style", $Field->gmap3_personal_style_text);
		} else
                {
                    $this->tpl[$tpl_id]->set_var("personalized_style", "false");
                }
		
//pan control
		//	attivare/disattivare pan control
		if (!strlen($Field->gmap3_pan_control))
		{
			$this->tpl[$tpl_id]->set_var("pan_control", "false");
		} else
		{
			$this->tpl[$tpl_id]->set_var("pan_control", "true");
		}
		//	dove posizionare pan control
		$this->tpl[$tpl_id]->set_var("pan_control_position", $Field->gmap3_pan_control_position);
		
//scale control
		//	attivare/disattivare scale control
		if (!strlen($Field->gmap3_scale_control))
		{
			$this->tpl[$tpl_id]->set_var("scale_control", "false");
		} else
		{
			$this->tpl[$tpl_id]->set_var("scale_control", "true");
		}
		//	dove posizionare scale control
		$this->tpl[$tpl_id]->set_var("scale_control_position", $Field->gmap3_scale_control_position);
		
//streetview
		//	attivare/disattivare streetview control
		if (!strlen($Field->gmap3_streetview_control))
		{
			$this->tpl[$tpl_id]->set_var("streetview_control", "false");
		} else
		{
			$this->tpl[$tpl_id]->set_var("streetview_control", "true");
		}
		//	dove posizionare streetview control
		$this->tpl[$tpl_id]->set_var("streetview_control_position", $Field->gmap3_streetview_control_position);
		
		
		

		
        
 	
		if(strlen($Field->gmap_update_class)) {
			$this->tpl[$tpl_id]->set_var("style_search", 'style="display:none;"');
			$this->tpl[$tpl_id]->set_var("style_search_bt", 'style="display:none;"');
		} else {
			$this->tpl[$tpl_id]->set_var("style_search", '');
			$this->tpl[$tpl_id]->set_var("style_search_bt", '');
		}
        $this->tpl[$tpl_id]->set_var("update_class", $Field->gmap_update_class);
        $this->tpl[$tpl_id]->set_var("update_class_prefix", $Field->gmap_update_class_prefix);
	
		
/*
		$this->tpl[$tpl_id]->set_var("set_marker", "false");
		if ($know_point)
			$this->tpl[$tpl_id]->set_var("set_marker", "true");
*/
		$this->tpl[$tpl_id]->parse("SectBinding", true);

		return $this->tpl[$tpl_id]->rpparse("SectControl", false);
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