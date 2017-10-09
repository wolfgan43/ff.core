<?php
/**
 * @package theme_default
 * @subpackage components
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

/**
 * @package theme_default
 * @subpackage components
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */
class ffGrid_html extends ffGrid_base 
{
	var $display_edit_bt		= true;					// display edit record button
	var $display_delete_bt		= true;					// display delete record button. This cause use of dialog.
	var $visible_edit_bt		= true;					// display edit record button record per record
	var $visible_delete_bt		= true;					// display delete record button record per record
	
	var $row_class		= "";
	var $switch_row_class =  array(
								"display" => false
								, "first" => "odd"
								, "second" => "even"
							);

	var $column_class				= "";
	var $column_class_first 		= "";
	var $column_class_last			= "";

	var $label_class				= "";
	var $label_selected_class		= "";
	var $label_selected_class_first	= "";
	var $label_selected_class_last	= "";
	
	var $encode_title = true;
	
	var $widget_discl_enable = false;
	var $display_edit_url = false;
	
	var $symbol_valuta          = "&euro; ";
	
	protected function tplLoad()
	{
		$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
		$this->tpl[0]->load_file($this->template_file, "main");

		$this->tpl[0]->set_var("site_path", $this->site_path);
		$this->tpl[0]->set_var("page_path", $this->page_path);
		$this->tpl[0]->set_var("theme", $this->getTheme());
		$this->tpl[0]->set_var("class", $this->class);

		$this->tpl[0]->set_var("fixed_pre_content", $this->fixed_pre_content);
		$this->tpl[0]->set_var("fixed_post_content", $this->fixed_post_content);

		$this->tpl[0]->set_var("SectHiddenField", "");

		if (strlen($this->id))
			$this->prefix = $this->id . "_";
		$this->tpl[0]->set_var("component", $this->prefix);
		
		if ($this->encode_title)
			$this->tpl[0]->set_var("title", ffCommon_specialchars($this->title));
		else
			$this->tpl[0]->set_var("title", $this->title);

		if ($this->parent !== NULL)
			$this->tpl[0]->set_var("ret_url", $this->parent[0]->ret_url);


		$this->setProperties();

		if (is_array($this->fixed_vars) && count($this->fixed_vars))
		{
			foreach ($this->fixed_vars as $key => $value)
			{
				$this->tpl[0]->set_var($key, $value);
			}
			reset($this->fixed_vars);
		}

		$res = $this->doEvent("on_load_template", array(&$this, $this->tpl[0]));
	}

	public function tplParse($output_result)
	{
		$res = $this->doEvent("on_tplParse", array($this, $this->tpl[0]));

		if ($this->parent !== NULL) //code for ff.js
			$this->parent[0]->tplAddJs("ff.ffGrid", "ffGrid.js", FF_THEME_DIR . "/library/ff");

		if ($output_result)
		{
			$this->tpl[0]->pparse("main", false);
			return true;
		}
		else
		{
			return $this->tpl[0]->rpparse("main", false);
		}
	}

	public function process_grid()
	{
		if ($this->display_grid == "never" || ($this->display_grid == "search" && !strlen($this->searched)))
		{
			$this->tpl[0]->set_var("SectGrid", "");
			return;
		}

		parent::process_grid();
		
		// Manage various buttons
		$totfields = count($this->grid_fields);
		if ($this->display_edit_bt)
			$totfields++;
		else
			$this->tpl[0]->set_var("SectEditButton", "");

		if ($this->display_delete_bt)
			$totfields++;
		else
			$this->tpl[0]->set_var("SectDeleteButton", "");

		if (is_array($this->grid_buttons) && count($this->grid_buttons))
			$totfields += count($this->grid_buttons);

		$this->tpl[0]->set_var("maxspan", $totfields);

		$this->process_navigator(); // done at this time due to maxspan and $this->db[0]->numRows()

		$col_class = $this->column_class;
		$col_class_f = ($this->column_class_first ? $this->column_class_first : $col_class);
		$col_class_l = ($this->column_class_last ? $this->column_class_last : $col_class);

		if ($this->db[0]->query_id && $this->db[0]->nextRecord())
		{
			$this->tpl[0]->set_var("SectNoRecords", "");

			if ($this->use_paging)
				$this->db[0]->jumpToPage($this->page, $this->records_per_page);
			$i = 0;
			$break_while = false;
			do
			{
				$this->tpl[0]->set_var("row", $i);
				$this->tpl[0]->set_var("rrow", $i + 1);

				/* Step 1: retrieve values (done in 2 steps due to events needs) */

				$keys = "";
				if (count($this->key_fields))
				{
					// find global recordset corrispondency (if one)
					$aKeys = array();
					foreach($this->key_fields as $key => $FormField)
					{
						$this->key_fields[$key]->value = $this->db[0]->getField($this->key_fields[$key]->get_data_source(), $this->key_fields[$key]->base_type);
						$aKeys[$key] = $this->key_fields[$key]->value->getValue(null, FF_SYSTEM_LOCALE);
						$keys .= "keys[" . $this->key_fields[$key]->id . "]=" . $this->key_fields[$key]->value->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE) . "&";

						$this->tpl[0]->set_var("Key_" . $key, $this->key_fields[$key]->getValue($this->key_fields[$key]->get_app_type(), $this->key_fields[$key]->get_locale()));
						$this->tpl[0]->set_var("encoded_Key_" . $key, rawurlencode($this->key_fields[$key]->getValue($this->key_fields[$key]->get_app_type(), $this->key_fields[$key]->get_locale())));
						if (strlen($this->key_fields[$key]->value->ori_value))
						{
							$this->tpl[0]->parse("SectSetKey" . $key, false);
							$this->tpl[0]->set_var("SectNotSetKey" . $key, "");
						}
						else
						{
							$this->tpl[0]->set_var("SectSetKey" . $key, "");
							$this->tpl[0]->parse("SectNotSetKey" . $key, false);
						}
					}
					reset($this->key_fields);

					$recordset_key = array_search($aKeys, $this->recordset_keys);
					// if not exists, add new
					if ($recordset_key === false)
					{
						$recordset_key = count($this->recordset_keys);
						$this->recordset_keys[$recordset_key] = $aKeys;
					}
					$this->displayed_keys[$recordset_key] = $aKeys;


					// display hidden key fields to managed displayed values properly
					if ($this->use_fields_params)
					{
						foreach($this->key_fields as $key => $FormField)
						{
							$this->tpl[0]->set_var("id", "displayed_keys[" . $recordset_key . "][" . $this->key_fields[$key]->id . "]");
							$this->tpl[0]->set_var("value",   ffCommon_specialchars($this->key_fields[$key]->value->getValue(null, FF_SYSTEM_LOCALE)));
							$this->tpl[0]->parse("SectHiddenField", true);
						}
						reset($this->key_fields);
					}
				}

				$keys .= $this->parent[0]->get_keys($this->key_fields);

				foreach($this->grid_fields as $key => $FormField)
				{
					if ($this->grid_fields[$key]->control_type != "" && isset($this->recordset_values[$recordset_key][$key]))
					{
						$this->grid_fields[$key]->value = new ffData($this->recordset_values[$recordset_key][$key], $this->grid_fields[$key]->base_type, FF_SYSTEM_LOCALE);
						if (isset($this->recordset_ori_values[$recordset_key][$key]))
						{
							$this->tpl[0]->set_var("id", "recordset_ori_values[" . $recordset_key . "][" . $this->grid_fields[$key]->id . "]");
							$this->tpl[0]->set_var("value",   ffCommon_specialchars($this->recordset_ori_values[$recordset_key][$key]));
							$this->tpl[0]->parse("SectHiddenField", true);
						}
					}
					else
					{
						switch ($this->grid_fields[$key]->data_type)
						{
							case "db":
								$this->grid_fields[$key]->value = $this->db[0]->getField($this->grid_fields[$key]->get_data_source(), $this->grid_fields[$key]->base_type);
								break;

							case "callback":
								$this->grid_fields[$key]->value = call_user_func($this->grid_fields[$key]->get_data_source(), $this->grid_fields, $key);
								break;

							default:
								if (isset($this->recordset_values[$recordset_key][$key]))
									$this->grid_fields[$key]->value = new ffData($this->recordset_values[$recordset_key][$key], $this->grid_fields[$key]->base_type, FF_SYSTEM_LOCALE);
								else
									$this->grid_fields[$key]->value = $this->grid_fields[$key]->getDefault(array(&$this));
						}
						if ($this->grid_fields[$key]->control_type != "" && !$this->force_no_field_params)
						{
							$this->tpl[0]->set_var("id", "recordset_ori_values[" . $recordset_key . "][" . $this->grid_fields[$key]->id . "]");
							$this->tpl[0]->set_var("value", ffCommon_specialchars($this->grid_fields[$key]->value->getValue($this->grid_fields[$key]->base_type, FF_SYSTEM_LOCALE)));
							$this->tpl[0]->parse("SectHiddenField", true);
						}
					}
				}
				reset($this->grid_fields);

				/* Step 2: display values */

				// EVENT HANDLER
				$res = $this->doEvent("on_before_parse_row", array(&$this, $i));
				$rc = end($res);

				if ($rc === null)
				{
					$col = 0;
					
					$this->tpl[0]->set_var("keys", $keys); // Useful for Fixed Fields Templates

					if (strlen($this->bt_edit_url))
					{
						$modify_url = $this->bt_edit_url;
					}
					else
					{
						$modify_url = $this->record_url . "?" . $keys .
									$this->parent[0]->get_globals() .
									"ret_url=" . urlencode($_SERVER['REQUEST_URI']) .
									"&" . $this->addit_record_param;
					}
					$modify_url = ffProcessTags($modify_url, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), urlencode($_SERVER['REQUEST_URI']), $this->parent[0]->get_globals());

					$cancelurl = $_SERVER['REQUEST_URI'];
					if (strlen($this->bt_delete_url))
					{
						$confirmurl = $this->bt_delete_url;
					}
					else
					{
						$confirmurl = $this->record_url . "?" . $keys .
									$this->parent[0]->get_globals() .
									$this->record_id . "_frmAction=confirmdelete&" .
									"ret_url=" . urlencode($_SERVER['REQUEST_URI']) .
									"&" . $this->addit_record_param;
					}
					$confirmurl = ffProcessTags($confirmurl, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), urlencode($_SERVER['REQUEST_URI']), $this->parent[0]->get_globals());

					$delete_url = $this->dialog(
												  true
												, "yesno"
												, $this->parent[0]->title
												, $this->label_delete
												, $cancelurl
												, $confirmurl
												);

					foreach($this->grid_fields as $key => $FormField)
					{
						$buffer = "";
						if (
								$this->use_fixed_fields == true
								&& $this->grid_fields[$key]->skip_if_empty
								&& $this->grid_fields[$key]->value->ori_value == ""
							)
							continue;

/*											if ($this->grid_fields[$key]->id == "tipologia"){
							print "<pre>"; print_r($this->grid_fields[$key]); die();
						}
*/

            			if (!is_subclass_of($this->grid_fields[$key], "ffField_base"))
							ffErrorHandler::raise("Wrong Grid Field: must be a ffField", E_USER_ERROR, $this, get_defined_vars());

						//---------------------------------------------------------------------
						//---------------------------------------------------------------------
						$col++;
						$class = "";
						if ($col == 1 && $col_class_f)
							$class .= " " . str_replace("[COL]", $col, $col_class_f);
						elseif ($col == $totfields && $col_class_l)
							$class .= " " . str_replace("[COL]", $col, $col_class_l);
						if ($col_class_l)
							$class .= " " . str_replace("[COL]", $col, $col_class);
						if (strlen($this->grid_fields[$key]->container_class))
							$class .= " " . str_replace("[COL]", $col, $this->grid_fields[$key]->container_class);
						if (strlen($class))
							$class = "class=\"" . $class . "\"";
						$this->tpl[0]->set_var("col_class", $class);
						//---------------------------------------------------------------------
						//---------------------------------------------------------------------
						
						$this->tpl[0]->set_var("container_class", strtolower($this->grid_fields[$key]->get_app_type()) . " " . $this->grid_fields[$key]->container_class);
						$this->tpl[0]->set_var("container_properties", $this->grid_fields[$key]->getProperties($this->grid_fields[$key]->container_properties));

						if ($this->grid_fields[$key]->control_type === "")
						{
							$this->grid_fields[$key]->pre_process(true);
							$buffer = $this->grid_fields[$key]->getDisplayValue();
						}
						else
						{
							$buffer = $this->grid_fields[$key]->process(
										"recordset_values[$recordset_key][" . $this->grid_fields[$key]->id . "]");
						}

/*											if (!$this->use_fixed_fields)
							{
								$this->tpl[0]->set_var("Value", $buffer);
								$this->tpl[0]->parse("SectField", true);
							}
						else
							{
								//ffErrorHandler::raise("DEBUG", E_USER_ERROR, $this, get_defined_vars());
								if (
										   false === array_search($key . "_display_value", $this->tpl[0]->DVars["main"])
										&& false === array_search($key . "_value", $this->tpl[0]->DVars["main"])
										&& false === array_search("Field_" . $key, $this->tpl[0]->DVars["main"])
										&& false === array_search("EncodedField_" . $key, $this->tpl[0]->DVars["main"])
									)
								{
									$this->tpl[0]->set_var("Value", $buffer);
									$this->tpl[0]->parse("SectField", true);
								}
								else
								{
									$this->tpl[0]->set_var($key . "_display_value", $buffer);
									$this->tpl[0]->set_var($key . "_value", $this->grid_fields[$key]->value->getValue());

									$this->tpl[0]->set_var("Field_" . $key, $buffer);
									$this->tpl[0]->set_var("EncodedField_" . $key, rawurlencode(ffCommon_url_normalize($buffer)));
								}
								if (strlen($this->grid_fields[$key]->value->ori_value))
								{
									$this->tpl[0]->parse("SectSetField_$key", false);
									$this->tpl[0]->set_var("SectNotSetField_$key", "");
								}
								else
								{
									$this->tpl[0]->set_var("SectSetField_$key", "");
									$this->tpl[0]->parse("SectNotSetField_$key", false);
								}
							}
*/
						$this->tpl[0]->set_var($key . "_display_value", $buffer);
						$this->tpl[0]->set_var($key . "_value", $this->grid_fields[$key]->value->getValue());
						$this->tpl[0]->set_var("url_" . $key, ffCommon_url_rewrite($this->grid_fields[$key]->value->getValue()));
						$this->tpl[0]->set_var("url_" . $key . "_display_value", ffCommon_url_rewrite($buffer));
						$this->tpl[0]->set_var("ent_" . $key . "_display_value", ffCommon_specialchars($buffer));
						$this->tpl[0]->set_var("ent_" . $key . "_value", ffCommon_specialchars($this->grid_fields[$key]->value->getValue()));

						if ($this->grid_fields[$key]->url === null)
						{
							$this->tpl[0]->set_var("field_url", $modify_url);
						}
						elseif ($this->grid_fields[$key]->url === false)
						{
							$this->tpl[0]->set_var("field_url", "");
						}
						else
						{
							$field_url = ffProcessTags($this->grid_fields[$key]->url, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), urlencode($_SERVER['REQUEST_URI']), $this->parent[0]->get_globals());
							$this->tpl[0]->set_var("field_url", $field_url);
						}

						if ($this->grid_fields[$key]->url === "" || !$this->display_edit_bt || !$this->visible_edit_bt)
						{
							$this->tpl[0]->set_var("SectUrlBefore", "");
							$this->tpl[0]->set_var("SectUrlAfter", "");
						}
						else
						{
							$this->tpl[0]->parse("SectUrlBefore", false);
							$this->tpl[0]->parse("SectUrlAfter", false);
						}
						
						//ffErrorHandler::raise("DEBUG", E_USER_ERROR, $this, get_defined_vars());
						if (
								/*	FALSE === array_search($key . "_display_value", $this->tpl[0]->DVars["main"])
								&& FALSE === array_search($key . "_value", $this->tpl[0]->DVars["main"])
								&&*/ FALSE === array_search("Field_" . $key, $this->tpl[0]->DVars["main"])
								&& FALSE === array_search("EncodedField_" . $key, $this->tpl[0]->DVars["main"])
								//&& FALSE === array_search("url_" . $key, $this->tpl[0]->DVars["main"])
							)
						{
							if (!$this->use_fixed_fields)
							{
								$this->tpl[0]->set_var("Value", $buffer);
								$this->tpl[0]->parse("SectField", true);
							}
						}
						else
						{
							$this->tpl[0]->set_var("Field_" . $key, $buffer);
							$this->tpl[0]->set_var("EncodedField_" . $key, rawurlencode(ffCommon_url_normalize($buffer)));
						}

						if (strlen($this->grid_fields[$key]->value->ori_value))
						{
							$this->tpl[0]->parse("SectSetField_$key", false);
							$this->tpl[0]->set_var("SectNotSetField_$key", "");
						}
						else
						{
							$this->tpl[0]->set_var("SectSetField_$key", "");
							$this->tpl[0]->parse("SectNotSetField_$key", false);
						}

						if ($this->grid_fields[$key]->extended_type == "Selection")
						{
							$this->tpl[0]->set_regexp_var("/SectSet_" . $key . "_.+/", "");
							$this->tpl[0]->parse("SectSet_" . $key . "_" . $this->grid_fields[$key]->value->getValue($this->grid_fields[$key]->base_type, FF_SYSTEM_LOCALE), false);
						}

						$this->tpl[0]->parse("Sect_$key", false);
					}
					reset($this->grid_fields);

					if ($this->visible_edit_bt)
					{
						$this->tpl[0]->set_var("modify_url", ffCommon_specialchars($modify_url));
						$this->tpl[0]->parse("SectEditButtonVisible", false);
					}
					else
						$this->tpl[0]->set_var("SectEditButtonVisible", "");

					if ($this->display_edit_bt)
					{
						//---------------------------------------------------------------------
						//---------------------------------------------------------------------
						$col++;
						$class = "";
						if ($col == 1 && $col_class_f)
							$class .= " " . $col_class_f;
						elseif ($col == $totfields && $col_class_l)
							$class .= " " . $col_class_l;
						if (strlen($class))
							$class = "class=\"" . $class . "\"";
						$this->tpl[0]->set_var("col_class", $class);
						//---------------------------------------------------------------------
						//---------------------------------------------------------------------
						
						$this->tpl[0]->parse("SectEditButton", false);
					}
					else
						$this->tpl[0]->set_var("SectEditButton", "");

					if ($this->visible_delete_bt)
					{
						$this->tpl[0]->set_var("delete_url", ffCommon_specialchars($delete_url));
						$this->tpl[0]->parse("SectDeleteButtonVisible", false);
					}
					else
						$this->tpl[0]->set_var("SectDeleteButtonVisible", "");

					if ($this->display_delete_bt)
					{
						//---------------------------------------------------------------------
						//---------------------------------------------------------------------
						$col++;
						$class = "";
						if ($col == 1 && $col_class_f)
							$class .= " " . $col_class_f;
						elseif ($col == $totfields && $col_class_l)
							$class .= " " . $col_class_l;
						if (strlen($class))
							$class = "class=\"" . $class . "\"";
						$this->tpl[0]->set_var("col_class", $class);
						//---------------------------------------------------------------------
						//---------------------------------------------------------------------

						$this->tpl[0]->parse("SectDeleteButton", false);
					}
					else
						$this->tpl[0]->set_var("SectDeleteButton", "");

					if (is_array($this->grid_buttons) && count($this->grid_buttons))
					{
						foreach($this->grid_buttons as $key => $FormButton)
						{
							//---------------------------------------------------------------------
							//---------------------------------------------------------------------
							$col++;
							$class = "";
							if ($col == 1 && $col_class_f)
								$class .= " " . $col_class_f;
							elseif ($col == $totfields && $col_class_l)
								$class .= " " . $col_class_l;
							if (strlen($class))
								$class = "class=\"" . $class . "\"";
							$this->tpl[0]->set_var("col_class", $class);
							//---------------------------------------------------------------------
							//---------------------------------------------------------------------
							
							$this->tpl[0]->set_var(	  "GridButton"
													, $this->grid_buttons[$key]->process(
																/*ffProcessTags($FormButton->url, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), urlencode($_SERVER['REQUEST_URI']), $this->parent[0]->get_globals())*/
																)
												   );
							$this->tpl[0]->parse("SectGridButton", true);
						}
						reset($this->grid_buttons);
					}
					else
						$this->tpl[0]->set_var("SectGridButton", "");

					$i++;
					if ($this->use_paging && $i >= $this->records_per_page)
						$break_while = true;
					$this->tpl[0]->set_var("RowNumber", $i);
					$this->tpl[0]->set_var("RecordNumber", ($i + ($this->page - 1 ) * $this->records_per_page));

					$res = $this->doEvent("on_before_parse_record", array(&$this));
					$rc = end($res);
					if ($rc === null)
						$this->tpl[0]->parse("SectRecord", true);

					$this->tpl[0]->set_var("SectField", "");
					$this->tpl[0]->set_var("SectGridButton", "");
				}
			} while ($this->db[0]->nextRecord() && !$break_while);
			$this->tpl[0]->parse("SectGridData", false);
		}
		else
		{
			$this->tpl[0]->set_var("SectRecord", "");
			$this->tpl[0]->set_var("SectGridData", "");
			$this->tpl[0]->parse("SectNoRecords", false);
		}

		// store value of fields in hidden section, but only for not displayed records

		if (is_array($this->recordset_keys) && count($this->recordset_keys) && $this->use_fields_params)
		{
			// cycle records
			foreach ($this->recordset_keys as $rst_key => $rst_value)
			{
				$aKeys = array();
				// cycle keys
				foreach($this->key_fields as $key => $value)
				{
					$this->tpl[0]->set_var("id", "recordset_keys[" . $rst_key . "][" . $key . "]");
					$this->tpl[0]->set_var("value", ffCommon_specialchars($this->recordset_keys[$rst_key][$key]));
					$this->tpl[0]->parse("SectHiddenField", true);
					$aKeys[$key] = $this->recordset_keys[$rst_key][$key];
				}
				reset($this->key_fields);

				$displayed = array_search($aKeys, $this->displayed_keys);
				// if not displayed, store hidden
				if ($displayed === false)
				{
					// cycle fields
					while(list($key, $value) = each($this->grid_fields))
					{
						if ($this->grid_fields[$key]->control_type !== "")
						{
							$this->tpl[0]->set_var("id", "recordset_ori_values[" . $rst_key . "][" . $key . "]");
							$this->tpl[0]->set_var("value", ffCommon_specialchars($this->recordset_ori_values[$rst_key][$key]));
							$this->tpl[0]->parse("SectHiddenField", true);

							$this->tpl[0]->set_var("id", "recordset_values[" . $rst_key . "][" . $key . "]");
							$this->tpl[0]->set_var("value", ffCommon_specialchars($this->recordset_values[$rst_key][$key]));
							$this->tpl[0]->parse("SectHiddenField", true);
						}
					}
					reset($this->grid_fields);
				}
			}
			reset($this->recordset_keys);
		}

		$res = $this->doEvent("on_after_process_grid", array(&$this, $this->tpl[0]));

		$this->tpl[0]->parse("SectGrid", false);
	}

	function process_labels()
	{
		if (!$this->display_labels)
			{
				$this->tpl[0]->set_var("SectLabels", "");
				return;
			}

		if ($this->display_edit_bt)
			$this->tpl[0]->parse("SectEditLabel", false);
		else
			$this->tpl[0]->set_var("SectEditLabel", "");

		if ($this->display_delete_bt)
			$this->tpl[0]->parse("SectDeleteLabel", false);
		else
			$this->tpl[0]->set_var("SectDeleteLabel", "");

		$tot_labels = 0;
		$tot_labels += (is_array($this->grid_buttons) ? count($this->grid_buttons) : 0);
		$tot_labels += (is_array($this->grid_fields) ? count($this->grid_fields) : 0);

		$col_class = $this->column_class;
		$col_class_f = ($this->column_class_first ? $this->column_class_first : $col_class);
		$col_class_l = ($this->column_class_last ? $this->column_class_last : $col_class);

		$selected_class = $this->label_selected_class;
		$selected_class_f = ($this->label_selected_class_first ? $this->label_selected_class_first : $selected_class);
		$selected_class_l = ($this->label_selected_class_last ? $this->label_selected_class_last : $selected_class);

		$col = 0;

		if (is_array($this->grid_buttons) && count($this->grid_buttons))
		{
			foreach($this->grid_buttons as $key => $FormButton)
			{
				$col++;
				$class = str_replace("[COL]", $col, $this->label_class);

				if ($col == 1 && $col_class_f)
					$class .= " " . $col_class_f;
				elseif ($col == $tot_labels && $col_class_l)
					$class .= " " . $col_class_l;

				$this->tpl[0]->set_var("label_class", $class);

				$this->tpl[0]->parse("SectButtonLabel", true);
			}
			reset($this->grid_buttons);
		}
		else
			$this->tpl[0]->set_var("SectButtonLabel", "");

		foreach($this->grid_fields as $key => $FormField)
		{
			$col++;
			$class = str_replace("[COL]", $col, $this->label_class);

			if ($col == 1 && $col_class_f)
				$class .= " " . $col_class_f;
			elseif ($col == $tot_labels && $col_class_l)
				$class .= " " . $col_class_l;
			if (strlen($this->grid_fields[$key]->label_class))
				$class .= " " . str_replace("[COL]", $col, $this->grid_fields[$key]->container_class);

			if ($this->order_method != "none" && $this->grid_fields[$key]->allow_order)
			{
				$this->tpl[0]->set_var("Label", ffCommon_specialchars($this->grid_fields[$key]->label));
				$this->tpl[0]->set_var("order", $this->grid_fields[$key]->id);
				$this->tpl[0]->set_var("width", ffCommon_specialchars($this->grid_fields[$key]->width));
				if ($this->order == $this->grid_fields[$key]->id)
				{
					if ($col == 1 && $selected_class_f)
						$class .= " " . $selected_class_f;
					elseif ($col == $tot_labels && $selected_class_l)
						$class .= " " . $selected_class_l;
					else
						$class .= " " . $selected_class;

					if ($this->direction == "ASC")
						$this->tpl[0]->set_var("direction", "DESC");
					else
						$this->tpl[0]->set_var("direction", "ASC");
				}
				else
				{
					$this->tpl[0]->set_var("label_class", "");
					$this->tpl[0]->set_var("direction", $this->grid_fields[$key]->order_dir);
				}

				if ($this->order_method == "labels" || $this->order_method == "both")
				{
					$this->tpl[0]->parse("SectOrderLabel", false);
					$this->tpl[0]->set_var("SectNoOrder", "");
				}
				else
				{
					$this->tpl[0]->set_var("SectOrderLabel", "");
					$this->tpl[0]->parse("SectNoOrder", false);
				}

				if ($this->order_method == "icons" || $this->order_method == "both")
					$this->tpl[0]->parse("SectOrderIcons", false);
				else
					$this->tpl[0]->set_var("SectOrderIcons", "");

				$this->tpl[0]->parse("SectOrder", false);
			}
			else
			{
				$this->tpl[0]->set_var("SectOrder", "");
				$this->tpl[0]->set_var("Label", ffCommon_specialchars($FormField->label));
				$this->tpl[0]->set_var("Name", $FormField->id);

				$this->tpl[0]->parse("SectNoOrder", false);
			}

			if (strlen($class))
				$class = "class=\"" . $class . "\"";
			$this->tpl[0]->set_var("label_class", $class);

			if (!$this->use_fixed_fields)
				$this->tpl[0]->parse("SectLabel", true);
			else
				$this->tpl[0]->parse("SectLabel" . $FormField->id, false);
		}
		reset($this->grid_fields);
		$this->tpl[0]->parse("SectLabels", false);
	}

	function process_navigator()
	{
		$this->tpl[0]->set_var("page_parname", $this->navigator[0]->page_parname);
		$this->tpl[0]->set_var("records_per_page_parname", $this->navigator[0]->records_per_page_parname);

		if (!$this->display_navigator || !$this->use_paging)
		{
			$this->tpl[0]->set_var("SectPageNavigator", "");
			$this->tpl[0]->set_var("SectPaginatorTop", "");
			$this->tpl[0]->set_var("SectPaginatorBottom", "");
			return;
		}

		$this->navigator[0]->parent[0] = $this;

		$this->navigator[0]->page = $this->page;
		$this->navigator[0]->records_per_page = $this->records_per_page;
		if ($this->db[0]->query_id)
			$this->navigator[0]->num_rows = $this->db[0]->numRows();

		$this->tpl[0]->set_var("PageNavigator", $this->navigator[0]->process());
		$this->tpl[0]->parse("SectPageNavigator", false);

		if ($this->navigator_orientation == "both" || $this->navigator_orientation == "top")
			$this->tpl[0]->parse("SectPaginatorTop", false);
		else
			$this->tpl[0]->set_var("SectPaginatorTop", "");

		if ($this->navigator_orientation == "both" || $this->navigator_orientation == "bottom")
			$this->tpl[0]->parse("SectPaginatorBottom", false);
		else
			$this->tpl[0]->set_var("SectPaginatorBottom", "");
	}
	
	public function structProcess($tpl)
	{
	}
}