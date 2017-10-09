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
class ffWidget_activecomboex extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)

	var $template_file 	 = "ffWidget.html";
	
	var $class			= "ffWidget_activecomboex";

	var $widget_deps	= array(
		
	);
	
    var $js_deps = array(
							  "jquery"						=> null
							, "jquery.ui"					=> null
/*							, "ff.ffField.activecomboex"	=> array(
									"file" => "activecomboex.js"
									, "path" => "/themes/restricted/ff/ffField/widgets/activecomboex"
								)*/
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
                              "jquery.ui.autocomplete"        => array(
                                      "file" => "jquery.ui.autocomplete.css"
                                    , "path" => null
                                    , "rel" => "jquery.ui"
                                )
    					);

	var $disable_dialog = true;
	
	// PRIVATE VARS
	
	var $innerURL		= null;
	
	var $tpl 			= null;
	var $db				= null;

	var $display_debug	= false;

	var $oPage 			= null;
	var $source_path	= null;
	var $style_path 	= null;
	
	function __construct(ffPage_base $oPage = null, $source_path = null, $style_path = null)
	{
		$this->get_defaults();

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

		if ($this->innerURL === null)
			$this->tpl[$id]->set_var("innerURL", $this->source_path . "/ff/ffField/widgets/activecomboex/parsedata." . FF_PHP_EXT);
		else
			$this->tpl[$id]->set_var("innerURL", $this->innerURL);
	}

	function process($id, &$value, ffField_base &$Field)
	{
		global $plgCfg_ActiveComboEX_UseOwnSession;
        
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

		if (isset($Field->db[0]))
			$db =& $Field->db[0];
		else
			$db =& $this->db[0];

		$this->tpl[$tpl_id]->set_var("SectControl", "");

		$this->tpl[$tpl_id]->set_var("id", $id);
		$this->tpl[$tpl_id]->set_var("name", $Field->id);
		$this->tpl[$tpl_id]->set_var("class", $this->class);
		$this->tpl[$tpl_id]->set_var("site_path", $Field->parent_page[0]->site_path);
		$this->tpl[$tpl_id]->set_var("theme", $Field->getTheme());

        if(strlen($Field->widget_path))
            $this->tpl[$tpl_id]->set_var("widget_path", $Field->widget_path); 
        else 
            $this->tpl[$tpl_id]->set_var("widget_path", "/themes/restricted/ff/ffField/widgets/activecomboex");

		$count_editable = 0;
		if ($Field->actex_update_from_db && $Field->actex_dialog && $Field->actex_dialog_show_add && !$this->disable_dialog && strlen($Field->actex_dialog_url))
		{
			if (strlen($Field->actex_dialog_title))
				$dialog_title = $Field->actex_dialog_title;
			elseif (strlen($Field->label))
				$dialog_title = $Field->label;
			else
				$dialog_title = $Field->id;

			$this->tpl[$tpl_id]->set_var("dialoglink", $Field->parent_page[0]->widgets["dialog"]->process(
					"actex_dlg_" . $prefix . $id
					, array(
							"title"			=> $dialog_title
							, "url"			=> $Field->actex_dialog_url
							, "name"		=> '<img alt="add" src="' . FF_SITE_PATH . '/themes/' . $Field->parent_page[0]->getTheme() . '/images/icons/add.png" />'
							, "callback"	=> (count($Field->resources) ? "ff.ffField.activecomboex.dialog_success('" . $prefix . $id . "', '" . $Field->resources[0] . "')" : "")
							, "tpl_id"		=> $tpl_id
						)
					, $Field->parent_page[0]
				));
			$this->tpl[$tpl_id]->parse("SectDialog", false);
			
			$count_editable++;
		}
		else
		{
			$this->tpl[$tpl_id]->set_var("dialoglink", "");
			$this->tpl[$tpl_id]->set_var("SectDialog", "");
		}

		$edit_url = ($Field->actex_dialog_edit_url ? $Field->actex_dialog_edit_url : $Field->actex_dialog_url);
		$params = $Field->actex_dialog_edit_params;
		if (
				$Field->actex_update_from_db
				&& $Field->actex_dialog
				&& $Field->actex_dialog_show_edit
				&& !$this->disable_dialog
				&& strlen($edit_url)
				&& (
						(is_array($params) && count($params))
						|| (strpos($edit_url, "[[") !== false)
					)
			)
		{
			if (strlen($Field->actex_dialog_edit_title))
				$dialog_edit_title = $Field->actex_dialog_edit_title;
			elseif (strlen($Field->actex_dialog_title))
				$dialog_edit_title = $Field->actex_dialog_title;
			elseif (strlen($Field->label))
				$dialog_edit_title = $Field->label;
			else
				$dialog_edit_title = $Field->id;

			if (strpos($edit_url, "?") === false)
				$edit_url .= "?";
			elseif (substr($edit_url, -1) !== "&")
				$edit_url .= "&";
			foreach ($params as $param_key => $param_value)
			{
				if ($param_value === null)
					$edit_url .= $param_key . "=[[" . $prefix . $id . "]]&";
				else
					$edit_url .= $param_key . "=[[" . $param_value . "]]&";
			}

			$this->tpl[$tpl_id]->set_var("dialogeditlink", $Field->parent_page[0]->widgets["dialog"]->process(
					"actex_dlg_edit_" . $prefix . $id
					, array(
							"title"			=> $dialog_title
							, "url"			=> $edit_url
							, "name"		=> '<img alt="edit" src="' . FF_SITE_PATH . '/themes/' . $Field->parent_page[0]->getTheme() . '/images/icons/edit.png" />'
//							, "callback"	=> "ff.ffField.activecomboex.dialog_success('" . $prefix . $id . "', 'actex_dlg_edit_" . $Field->parent[0]->id . "_" . $Field->id . "')"
							, "tpl_id"		=> $tpl_id
						)
					, $Field->parent_page[0]
				));
			$this->tpl[$tpl_id]->parse("SectDialogEdit", false);
			
			$count_editable++;
		}
		else
		{
			$this->tpl[$tpl_id]->set_var("dialogeditlink", "");
			$this->tpl[$tpl_id]->set_var("SectDialogEdit", "");
		}

		$params = ($Field->actex_dialog_delete_params ? $Field->actex_dialog_delete_params : $Field->actex_dialog_edit_params);
		if (
				$Field->actex_update_from_db
				&& $Field->actex_dialog
				&& $Field->actex_dialog_show_delete
				&& !$this->disable_dialog
				&& strlen($Field->actex_dialog_delete_url)
				&& (
						(is_array($params) && count($params))
						|| (strpos($Field->actex_dialog_delete_url, "[[") !== false)
					)
			)
		{
			if (strlen($Field->actex_dialog_delete_title))
				$dialog_delete_title = $Field->actex_dialog_delete_title;
			elseif (strlen($Field->actex_dialog_title))
				$dialog_delete_title = $Field->actex_dialog_title;
			elseif (strlen($Field->label))
				$dialog_delete_title = $Field->label;
			else
				$dialog_delete_title = $Field->id;

			$delete_url = $Field->actex_dialog_delete_url;
			if (is_array($params) && count($params))
			{
				if (strpos($delete_url, "?") === false)
					$delete_url .= "?";
				elseif (substr($delete_url, -1) !== "&")
					$delete_url .= "&";
				foreach ($params as $param_key => $param_value)
				{
					if ($param_value === null)
						$delete_url .= $param_key . "=[[" . $prefix . $id . "]]&";
					else
						$delete_url .= $param_key . "=[[" . $param_value . "]]&";
				}
			}

			if(method_exists($Field->parent[0], "dialog")) {
				$dialog_delete = $Field->parent[0]->dialog(
													  true
													, "yesno"
													, $dialog_delete_title
													, $Field->actex_dialog_delete_message
													, "[CLOSEDIALOG]"
													, $delete_url
												);
			} else {
				$dialog_delete = ffDialog(true, "yesno", $dialog_delete_title, $Field->actex_dialog_delete_message, "[CLOSEDIALOG]", $delete_url, "/dialog");
			}
			$this->tpl[$tpl_id]->set_var("dialogdeletelink", $Field->parent_page[0]->widgets["dialog"]->process(
					"actex_dlg_delete_" . $prefix . $id
					, array(
							"title"			=> $dialog_delete_title
							, "url"			=> $dialog_delete
							, "name"		=> '<img alt="delete" src="' . FF_SITE_PATH . '/themes/' . $Field->parent_page[0]->getTheme() . '/images/icons/delete.png" />'
//							, "callback"	=> "ff.ffField.activecomboex.dialog_success('" . $prefix . $id . "', 'actex_dlg_delete_" . $Field->parent[0]->id . "_" . $Field->id . "')"
							, "tpl_id"		=> $tpl_id
						)
					, $Field->parent_page[0]
				));
			$this->tpl[$tpl_id]->parse("SectDialogDelete", false);
			
			$count_editable++;
		}
		else
		{
			$this->tpl[$tpl_id]->set_var("dialogdeletelink", "");
			$this->tpl[$tpl_id]->set_var("SectDialogDelete", "");
		}

		if($count_editable) {
			$this->tpl[$tpl_id]->set_var("editable", " editable");
			$this->tpl[$tpl_id]->parse("SectEditable", false);
		} else {
			$this->tpl[$tpl_id]->set_var("editable", "");
			$this->tpl[$tpl_id]->set_var("SectEditable", "");
		}
		
		if (substr($id, -4) == "_src")
			$suffix = "_src";
		
		if ($Field->actex_father === null)
			$father = null;
		else if (isset($Field->cont_array[$Field->actex_father]))
			$father = $Field->cont_array[$Field->actex_father];
		else
			$father = $Field->actex_father[0];
		
		if ($Field->actex_child === null)
			$child = null;
		else if (is_array($Field->actex_child))
		{
			$i = -1;
			foreach ($Field->actex_child as $key => $element)
				{
					$i++;
					if (isset($Field->cont_array[$element]))
						$child[$i] = $Field->cont_array[$element];
					else
						$child[$i] = $Field->actex_child[$key];
				}
			reset($Field->actex_child);
		}
		else if (isset($Field->cont_array[$Field->actex_child]))
		{
			$child[0] = $Field->cont_array[$Field->actex_child];
		}
		else
			ffErrorHandler::raise("Cannot determine child in activecombo!", E_USER_ERROR, $this, get_defined_vars());
		
		if ($father === null)
			$this->tpl[$tpl_id]->set_var("father", "null");
		else
		{
			if ($Field->row === null)
				$this->tpl[$tpl_id]->set_var("father", "\"" . $prefix . $father->id . $suffix . "\"");
			else
				$this->tpl[$tpl_id]->set_var("father", "\"" . $prefix . "recordset[" . $Field->row . "][" . $father->id . "]\"");
		}
			
		$this->tpl[$tpl_id]->set_var("SectChild", "");
        if ($child !== null)
		{
			foreach ($child as $key => $element)
			{
				if ($Field->row === null)
					$this->tpl[$tpl_id]->set_var("child", ($key > 0 ? "," : "") . "\"" . $prefix . $child[$key]->id . $suffix . "\"");
				else
					$this->tpl[$tpl_id]->set_var("child", ($key > 0 ? "," : "") . "\"" . $prefix . "recordset[" . $Field->row . "][" . $child[$key]->id . "]\"");
				$this->tpl[$tpl_id]->set_var("n", $key);
				$this->tpl[$tpl_id]->parse("SectChild", true);
			}
			reset($child);
		}

		$property_set = $Field->properties;
		if (isset($property_set["onchange"]))
		{
			$this->tpl[$tpl_id]->set_var("onchange", str_replace("'", "\'", $property_set["onchange"]));
			unset($property_set["onchange"]);
		}
		
		$this->tpl[$tpl_id]->set_var("properties", str_replace("'", "\'", $Field->getProperties($property_set)));
		
		if ($Field->multi_limit_select)
			$this->tpl[$tpl_id]->set_var("limit_select", "true");
		else
			$this->tpl[$tpl_id]->set_var("limit_select", "false");
		
		if (strlen($Field->properties["disabled"]))
			$this->tpl[$tpl_id]->set_var("disabled", "true");
		else
			$this->tpl[$tpl_id]->set_var("disabled", "false");

        $this->tpl[$tpl_id]->set_var("separator", $Field->grouping_separator);
        
        $this->tpl[$tpl_id]->set_var("control_type", $Field->get_control_type());

        if ($Field->actex_add_plus)
            $this->tpl[$tpl_id]->set_var("add_plus", "true");
        else
            $this->tpl[$tpl_id]->set_var("add_plus", "false");

        if($Field->autocomplete_label) {
        	$this->tpl[$tpl_id]->set_var("autocomplete_label", $Field->autocomplete_label);
        	$this->tpl[$tpl_id]->parse("SectControlLabel", false);
		} else {
        	$this->tpl[$tpl_id]->set_var("SectControlLabel", "");
		}

        
		if ($Field->multi_select_one && !$Field->multi_limit_select)
		{
			if ($Field->multi_select_one_val !== null && !$Field->multi_limit_select)
				$this->tpl[$tpl_id]->set_var("select_one_val", $Field->multi_select_one_val->getValue($Field->get_app_type(), $Field->get_locale()));
			else
				$this->tpl[$tpl_id]->set_var("select_one_val", "");
			$this->tpl[$tpl_id]->set_var("select_one_label", $Field->multi_select_one_label);
			$this->tpl[$tpl_id]->set_var("select_one", "true");
		}
		else
			$this->tpl[$tpl_id]->set_var("select_one", "false");
			
		if ($Field->multi_select_noone && 
							(!$Field->multi_limit_select || 
								($Field->multi_limit_select && $value->getValue($Field->get_app_type(), $Field->get_locale()) == $Field->multi_select_noone_val->getValue($Field->get_app_type(), $Field->get_locale()))
							)
			)
		{
			if ($Field->multi_select_noone_val !== null)
				$this->tpl[$tpl_id]->set_var("select_noone_val", $Field->multi_select_noone_val->getValue($Field->get_app_type(), $Field->get_locale()));
			else
				$this->tpl[$tpl_id]->set_var("select_noone_val", "");
			$this->tpl[$tpl_id]->set_var("select_noone_label", $Field->multi_select_noone_label);
			$this->tpl[$tpl_id]->set_var("select_noone", "true");
		}
		else
			$this->tpl[$tpl_id]->set_var("select_noone", "false");
			
		$this->tpl[$tpl_id]->set_var("SectData", "");
		$this->tpl[$tpl_id]->set_var("data_src", "");
		$no_rec = true;
		
		if (strlen($Field->source_SQL) && $Field->actex_update_from_db)
		{
			if($father === null) {
				$tmp = md5($Field->source_SQL);
			} else {
				$tmp = md5($Field->source_SQL . "-" . $father->getValue());
			}
            
			if (!defined("FF_ACTEX_SESSION_STARTED") && ($plgCfg_ActiveComboEX_UseOwnSession || $Field->actex_use_own_session))
			{
				if (!isset($_COOKIE[session_name()]))
				{
					if (isset($_POST[session_name()]))
						session_id($_POST[session_name()]);
					elseif (isset($_GET[session_name()]))
						session_id($_GET[session_name()]);
				}
				session_start();
				if (!defined("FF_ACTEX_SESSION_STARTED"))
					define("FF_ACTEX_SESSION_STARTED", true);
			}

			$ff = get_session("ff");
			$ff["activecomboex"][$tmp]["sql"] = $Field->source_SQL;
			$ff["activecomboex"][$tmp]["field"] = $Field->actex_related_field;
			$ff["activecomboex"][$tmp]["main_db"] = $Field->actex_use_main_db;
			set_session("ff", $ff);

//			set_session("activecomboex_sql_" . $tmp, $Field->source_SQL);
//			set_session("activecomboex_field_" . $tmp, $Field->actex_related_field);
//			set_session("activecomboex_main_db_" . $tmp, $Field->actex_use_main_db);
			
			$this->tpl[$tpl_id]->set_var("data_src", $tmp);
			$this->tpl[$tpl_id]->set_var("SectData", "");
		}
		else if (strlen($Field->source_SQL))
		{
			$this->tpl[$tpl_id]->set_var("data_src", "");
			$db->query($Field->source_SQL);
			if ($db->nextRecord())
			{
				$n = -1;
				do
				{
					$n++;
					$this->tpl[$tpl_id]->set_var("n", $n);

					if ($n > 0)
						$this->tpl[$tpl_id]->set_var("data_comma", ",");
					else
						$this->tpl[$tpl_id]->set_var("data_comma", "");


					if ($father === null)
						$this->tpl[$tpl_id]->set_var("father_value", "null");
					else
					{
						$tmp = $db->getField($db->fields_names[0], $father->base_type);
						//$tmp = $db->getResult(null, 0, $father->base_type);
						$this->tpl[$tpl_id]->set_var("father_value", "\"" . str_replace('"', '\"', $tmp->getValue($father->get_app_type(), $father->get_locale())) . "\"");
					}
					$tmp = $db->getField($db->fields_names[1], $Field->base_type);
					//$tmp = $db->getResult(null, 1, $Field->base_type);
					$this->tpl[$tpl_id]->set_var("value", str_replace('"', '\"', $tmp->getValue($Field->get_app_type(), $Field->get_locale())));
					$tmp = $db->getField($db->fields_names[2], $Field->multi_base_type);
					//$tmp = $db->getResult(null, 2, $Field->multi_base_type);
					$this->tpl[$tpl_id]->set_var("desc", str_replace('"', '\"', $tmp->getValue($Field->multi_app_type, $Field->get_locale())));

					$this->tpl[$tpl_id]->parse("SectData", true);
				}
				while ($db->nextRecord());
			}
			else
				$this->tpl[$tpl_id]->set_var("SectData", "");
		}
		else if (is_array($Field->multi_pairs) && count($Field->multi_pairs))
		{
			$n = -1;
			foreach($Field->multi_pairs as $key => $item)
			{
				$n++;
				$this->tpl[$tpl_id]->set_var("n", $n);

				if ($n > 0)
					$this->tpl[$tpl_id]->set_var("data_comma", ",");
				else
					$this->tpl[$tpl_id]->set_var("data_comma", "");
				
				if ($father === null) {
					$this->tpl[$tpl_id]->set_var("father_value", "null");
				} else {
					list($item_key, $father_id) = each($item); 					
					$this->tpl[$tpl_id]->set_var("father_value", "\"" . str_replace('"', '\"', $father_id->getValue($father->base_type, $father->get_locale())) . "\"");
				}

				list($item_key, $child_id) = each($item);
				list($item_key, $child_value) = each($item);

				$this->tpl[$tpl_id]->set_var("value", str_replace('"', '\"', $child_id->getValue($Field->get_app_type(), $Field->get_locale())));
				$this->tpl[$tpl_id]->set_var("desc", str_replace('"', '\"', $child_value->getValue($Field->multi_app_type, $Field->get_locale())));

				$this->tpl[$tpl_id]->parse("SectData", true);
			}
			reset($Field->multi_pairs);
		}
		else
			$this->tpl[$tpl_id]->set_var("SectData", "");

		if ($Field->actex_father === null)
			$this->tpl[$tpl_id]->set_var("father_value", "null");
		else
		{
			if ($Field->row === null)
				$this->tpl[$tpl_id]->set_var("father_value", "\"" . $father->value->getValue($father->get_app_type(), $father->get_locale()) . "\"");
			else	
				$this->tpl[$tpl_id]->set_var("father_value", "\"" . $Field->parent[0]->recordset[$father->row][$father->id]->getValue($father->get_app_type(), $father->get_locale()) . "\"");
		}

		if ($value == null)
			$this->tpl[$tpl_id]->set_var("selected_value", "null");
		else
			$this->tpl[$tpl_id]->set_var("selected_value", "\"" . $value->getValue($Field->get_app_type(), $Field->get_locale()) . "\"");

		$this->tpl[$tpl_id]->parse("SectBinding", true);
		
        if ($father === null)
			$this->tpl[$tpl_id]->parse("SectBindingFoot", true);

		if ($this->display_debug)
			$this->tpl[$tpl_id]->parse("SectDebug", false);
		else
			$this->tpl[$tpl_id]->set_var("SectDebug", "");

 		return $Field->fixed_pre_content . $this->tpl[$tpl_id]->rpparse("SectControl", false) . $Field->fixed_post_content;
	}

	function get_component_headers($id)
	{
		if ($this->oPage !== NULL) { //code for ff.js
			$this->oPage[0]->tplAddJs("jquery.blockui", "jquery.blockui.js", FF_THEME_DIR . "/library/plugins/jquery.blockui");
			$this->oPage[0]->tplAddJs("ff.ajax", "ajax.js", FF_THEME_DIR . "/library/ff");
            $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("ff.ffField.activecomboex", "activecomboex.js", FF_THEME_DIR . "/default/ff/ffField/widgets/activecomboex");
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
			$this->oPage[0]->tplAddJs("jquery.blockui", "jquery.blockui.js", FF_THEME_DIR . "/library/plugins/jquery.blockui");
			$this->oPage[0]->tplAddJs("ff.ajax", "ajax.js", FF_THEME_DIR . "/library/ff");
            $this->oPage[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
			$this->oPage[0]->tplAddJs("ff.ffField.activecomboex", "activecomboex.js", FF_THEME_DIR . "/default/ff/ffField/widgets/activecomboex");
			
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
