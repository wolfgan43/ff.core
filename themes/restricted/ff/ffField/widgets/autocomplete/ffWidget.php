<?php
// ----------------------------------------
//  		FRAMEWORK FORMS vAlpha
//		      PLUGIN DEFINITION (activecomboex)
//			   by Samuele Diella
// ----------------------------------------

class ffWidget_autocomplete extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)

	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_autocomplete";

	var $widget_deps	= array();
	
	var $libraries		= array();
	
    var $js_deps = array(
                              "ff.ffField.autocomplete"       => null
						);
    var $css_deps 		= array(
                              "jquery-ui.autocomplete"       => null
    					);

	var $disable_dialog = false;
	
	// PRIVATE VARS
	
	var $innerURL		= null;
	
	var $tpl 			= null;
	var $db				= null;

	var $display_debug	= false;

	var $oPage 			= null;
	var $source_path	= null;
	var $style_path 	= null;
	var $theme			= null;
	
	function __construct(ffPage_base $oPage = null, $source_path = null, $style_path = null)
	{
		$this->get_defaults();

		$this->oPage = array(&$oPage);
		
		if ($source_path !== null)
			$this->source_path = $source_path;
		elseif ($oPage !== null)
			$this->source_path = $oPage->getThemePath();

		$this->style_path = $style_path;
		
		$this->db[0] = ffDB_Sql::factory();
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

		/*if ($this->innerURL === null)
			$this->tpl[$id]->set_var("innerURL", $this->source_path . "/ff/ffField/widgets/autocomplete/parsedata." . FF_PHP_EXT);
		else
			$this->tpl[$id]->set_var("innerURL", $this->innerURL);*/
	}

	function process($id, &$value, ffField_base &$Field)
	{
		global $plgCfg_autocomplete_UseOwnSession;
        
		if ($Field->parent !== null && strlen($Field->parent[0]->getIDIF()))
		{
			$tpl_id = $Field->parent[0]->getIDIF();
			$prefix = $tpl_id . "_";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
			$this->tpl[$tpl_id]->set_var("component", $tpl_id);
			$this->tpl[$tpl_id]->set_var("container", $prefix);
			$Field->parent[0]->processed_widgets[$prefix . $id] = "autocomplete";
		}
		else
		{
			$tpl_id = "main";
			if (!isset($this->tpl[$tpl_id]))
				$this->prepare_template($tpl_id);
		}

		if (isset($Field->db[0]))
			$db =& $Field->db[0];
		else
			$db =& $this->db[0];

		$this->tpl[$tpl_id]->set_var("SectControl", "");

		if(strpos($id, "[") === false) {
			$this->tpl[$tpl_id]->set_var("id_encoded", $id);
		} else {
			$this->tpl[$tpl_id]->set_var("id_encoded", str_replace("[", '\\\\[', str_replace("]", '\\\\]', $id)));
		}

        if($Field->actex_father) {  
            $Field->properties["data-father"] = $Field->actex_father;
        }

		$this->tpl[$tpl_id]->set_var("id", $id);
		$this->tpl[$tpl_id]->set_var("class", " " . $this->class . (strlen($Field->class) ? " " . $Field->class : ""));
		$this->tpl[$tpl_id]->set_var("site_path", $Field->parent_page[0]->site_path);
        $this->tpl[$tpl_id]->set_var("properties", $Field->getProperties());
		if($this->theme !== null) 
			$this->tpl[$tpl_id]->set_var("theme", $this->theme);
		else
			$this->tpl[$tpl_id]->set_var("theme", $Field->getTheme());

        if(strlen($Field->widget_path))
            $this->tpl[$tpl_id]->set_var("widget_path", $Field->widget_path);
        else 
            $this->tpl[$tpl_id]->set_var("widget_path", "/themes/restricted/ff/ffField/widgets/autocomplete");

        if($Field->autocomplete_combo) {
        	$this->tpl[$tpl_id]->parse("SectCombo", false);
		} else {
			$this->tpl[$tpl_id]->set_var("SectCombo", "");
		}
		
        if($Field->autocomplete_disabled)
            $this->tpl[$tpl_id]->set_var("disabled", "true");
        else
            $this->tpl[$tpl_id]->set_var("disabled", "false");

        if($Field->autocomplete_readonly) {
			if ($value == null || !($value->getValue($Field->get_app_type(), $Field->get_locale()))) {
				$this->tpl[$tpl_id]->set_var("selected_value", "");
				$this->tpl[$tpl_id]->set_var("selected_id", "");
			} else {
                if($Field->autocomplete_multi) {
                    $arrValue = explode(",", $value->getValue($Field->get_app_type(), $Field->get_locale()));
                    if(is_array($arrValue) && count($arrValue)) {
                        foreach($arrValue AS $value_part) {
                            if(strlen($strValue))
                                $strValue .= ",";

                            $strValue .= $Field->getDisplayValue($Field->get_app_type(), $Field->get_locale(), new ffData($value_part, $Field->get_app_type(), $Field->get_locale()));
                        }
                    }
                }
                if(!strlen($strValue))
                    $strValue = $Field->getDisplayValue();
				$this->tpl[$tpl_id]->set_var("selected_value", $strValue);
				$this->tpl[$tpl_id]->set_var("selected_id", $value->getValue($Field->get_app_type(), $Field->get_locale()));
			}
            $this->tpl[$tpl_id]->set_var("readonly", "true");
            $this->tpl[$tpl_id]->set_var("prefix", "autocomplete_");
            $this->tpl[$tpl_id]->parse("SectReadOnly", false);
        } else {
			if ($value == null || !$value->getValue($Field->get_app_type(), $Field->get_locale()))
				$this->tpl[$tpl_id]->set_var("selected_value", "");
			else
				$this->tpl[$tpl_id]->set_var("selected_value", $value->getValue($Field->get_app_type(), $Field->get_locale()));

			$this->tpl[$tpl_id]->set_var("prefix", "");
            $this->tpl[$tpl_id]->set_var("readonly", "false");
            $this->tpl[$tpl_id]->set_var("SectReadOnly", "");
        }
        
        $this->tpl[$tpl_id]->set_var("minLength", $Field->autocomplete_minLength);
        $this->tpl[$tpl_id]->set_var("delay", $Field->autocomplete_delay);

		if ($Field->autocomplete_service === null)
		{
			$this->tpl[$tpl_id]->set_var("service", "null");
			
			if ($this->innerURL === null)
				$this->tpl[$tpl_id]->set_var("innerURL", $this->source_path . "/ff/ffField/widgets/autocomplete/parsedata." . FF_PHP_EXT);
			else
				$this->tpl[$tpl_id]->set_var("innerURL", $this->innerURL);
		}
		else
			$this->tpl[$tpl_id]->set_var("service", "'" . $Field->autocomplete_service . "'");

        if($Field->autocomplete_multi)
            $this->tpl[$tpl_id]->set_var("multi", "true");
        else
            $this->tpl[$tpl_id]->set_var("multi", "false");

        if($Field->autocomplete_cache)
            $this->tpl[$tpl_id]->set_var("cache", "true");
        else
            $this->tpl[$tpl_id]->set_var("cache", "false");

        if($Field->autocomplete_combo)
            $this->tpl[$tpl_id]->set_var("combo", "true");
        else
            $this->tpl[$tpl_id]->set_var("combo", "false");

        $this->tpl[$tpl_id]->set_var("strip_char", urlencode($Field->autocomplete_strip_char));
        
		$this->tpl[$tpl_id]->set_var("SectData", "");
		$this->tpl[$tpl_id]->set_var("data_src", "");

		if (strlen($Field->source_SQL))
		{
            if($Field->autocomplete_service === null)
            {
			    if($father === null) {
				    $tmp = md5($Field->source_SQL);
			    } else {
				    $tmp = md5($Field->source_SQL . "-" . $father->getValue());
			    }

			    if (!defined("FF_AUTOCOMPLETE_SESSION_STARTED") && ($plgCfg_autocomplete_UseOwnSession || $Field->autocomplete_use_own_session))
			    { //non entra
				    if (!isset($_COOKIE[session_name()]))
				    {
					    if (isset($_POST[session_name()]))
						    session_id($_POST[session_name()]);
					    elseif (isset($_GET[session_name()]))
						    session_id($_GET[session_name()]);
				    }
				    session_start();
				    if (!defined("FF_AUTOCOMPLETE_SESSION_STARTED"))
					    define("FF_AUTOCOMPLETE_SESSION_STARTED", true);
			    }

			    $ff = get_session("ff");
			    $ff["autocomplete"][$tmp]["sql"]						= $Field->source_SQL;
			    $ff["autocomplete"][$tmp]["main_db"]					= $Field->autocomplete_use_main_db;
			    $ff["autocomplete"][$tmp]["hide_result_on_query_empty"] = $Field->autocomplete_hide_result_on_query_empty;
                
                $ff["autocomplete"][$tmp]["field"]          = $Field->actex_related_field;                                                                                
			    $ff["autocomplete"][$tmp]["compare"]		= $Field->autocomplete_compare;
			    $ff["autocomplete"][$tmp]["compare_having"] = $Field->autocomplete_compare_having;
			    $ff["autocomplete"][$tmp]["operation"]		= $Field->autocomplete_operation;

			    set_session("ff", $ff);
			    
    //			set_session("autocomplete_sql_" . $tmp, $Field->source_SQL);
    //			set_session("autocomplete_main_db_" . $tmp, $Field->actex_use_main_db);
			    $this->tpl[$tpl_id]->set_var("data_src", $tmp);
			    $this->tpl[$tpl_id]->set_var("SectData", "");
            }
		}
		else if (is_array($Field->multi_pairs) && count($Field->multi_pairs))
		{
			$n = -1;
			foreach($Field->multi_pairs as $key => $item)
			{
				if(!(count($item) > 0)) 
                    continue;

                $n++;

				if ($n > 0)
					$this->tpl[$tpl_id]->set_var("data_comma", ",");
				else
					$this->tpl[$tpl_id]->set_var("data_comma", "");
				
                if(count($item) == 1) {
                    list($item_key, $elem_id) = each($item);
                    $this->tpl[$tpl_id]->set_var("value", str_replace('"', '\"', $elem_id->getValue($Field->get_app_type(), $Field->get_locale())));
                    $this->tpl[$tpl_id]->set_var("label", str_replace('"', '\"', $elem_id->getValue($Field->get_app_type(), $Field->get_locale())));
                    $this->tpl[$tpl_id]->set_var("cat", "");
                } else {
                    list($item_key, $elem_id) = each($item);
                    $this->tpl[$tpl_id]->set_var("value", str_replace('"', '\"', $elem_id->getValue($Field->get_app_type(), $Field->get_locale())));
                    
				    if(count($item) >= 2) {
                        list($item_key, $elem_value) = each($item);
                        $this->tpl[$tpl_id]->set_var("label", str_replace('"', '\"', $elem_value->getValue($Field->get_app_type(), $Field->get_locale())));
                    }
                    if(count($item) >= 3) {
                    list($item_key, $elem_cat) = each($item);
                        $this->tpl[$tpl_id]->set_var("cat", str_replace('"', '\"', $elem_cat->getValue($Field->multi_app_type, $Field->get_locale())));
                    } else {
                        $this->tpl[$tpl_id]->set_var("cat", "");
                    }
                }

				$this->tpl[$tpl_id]->parse("SectData", true);
			}
			reset($Field->multi_pairs);
		}
		else
			$this->tpl[$tpl_id]->set_var("SectData", "");

		$this->tpl[$tpl_id]->parse("SectBinding", true);

		if ($this->display_debug)
			$this->tpl[$tpl_id]->parse("SectDebug", false);
		else
			$this->tpl[$tpl_id]->set_var("SectDebug", "");

        if($Field->autocomplete_multi) {
            $this->tpl[$tpl_id]->parse("SectControlMulti", false);
            $this->tpl[$tpl_id]->set_var("SectControlMono", "");
        } else {
            $this->tpl[$tpl_id]->set_var("SectControlMulti", "");
            $this->tpl[$tpl_id]->parse("SectControlMono", false);
        }
        return $Field->fixed_pre_content . $this->tpl[$tpl_id]->rpparse("SectControl", false) . $Field->fixed_post_content;	

/*        if(array_key_exists("autocomplete", $Field->parent_page[0]->widgets)) {
        	return $Field->fixed_pre_content . $this->tpl[$tpl_id]->rpparse("SectControl", false) . $Field->fixed_post_content;	
		} else {
			return $this->get_component_headers($tpl_id) 
					. $Field->fixed_pre_content 
					. $this->tpl[$tpl_id]->rpparse("SectControl", false) 
					. $Field->fixed_post_content 
					. $this->get_component_footers($tpl_id);
		}*/
        
 		
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
/*       ff.ffField.activecomboex.addAutocomplete({
            "id"            : "{container}{id}",
            "data" : {
                "data_src" : "{data_src}"
                <!--BeginSectData-->
                , "source" : [
                    {data_comma} { value: "{value}", label: "{label}", category: "{cat}" } 
                ]
                <!--EndSectData-->
            }
        });
 */
