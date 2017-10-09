<?php
/**
 * @package theme_default
 * @subpackage components
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

function ffDetails_drag_update_order($detail, $row, $fields)
{
	$tmp = $detail->recordset;
	$i = -1;
	foreach ($tmp as $key => $value)
	{
		$i++;
		if ($key == $row)
			break;
	}
	
	if (strlen($fields))
		$fields .= ", ";
	$fields .= $detail->drag_order_field . " = " . $detail->db[0]->toSql($i);
}

function ffDetails_drag_insert_order($detail, $row, $fields, $values)
{
	if (!strlen($fields))
		return;
		
	$tmp = $detail->recordset;
	$i = -1;
	foreach ($tmp as $key => $value)
	{
		$i++;
		if ($key == $row)
			break;
	}
	
	if (strlen($fields))
		$fields .= ", ";
	$fields .= $detail->drag_order_field;
	
	if (strlen($values))
		$values .= ", ";
	$values .= $detail->db[0]->toSql($i);
	
	return;
}

function ffDetails_drag_on_loaded_data($detail)
{
	$recordset_avl_ori = $detail->parent[0]->retrieve_param($detail->id, "recordset_avl_ori");
	if (!is_array($recordset_avl_ori))
		$recordset_avl_ori = array();
	
	foreach ($recordset_avl_ori as $key => $value)
	{
		$found = false;
		$slice = array();
		
		$tmp = $detail->key_fields;
		foreach ($tmp as $subkey => $subval)
		{
			if (strlen($value[$subkey]))
			{
				$slice[$subkey] = new ffData($value[$subkey], $subval->base_type, FF_SYSTEM_LOCALE);
			}
		}
		
		if (count($slice))
			$detail->deleted_keys[] = $slice;
	}
}

/**
 * @package theme_default
 * @subpackage components
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */
class ffDetails_drag extends ffDetails_base
{
	public $prefix			= null;
	var $template_file 		= "ffDetails_drag.html";
	
	var $drag_records_avaiable = null;
	var $drag_order_field = array();
	
	function __construct(ffPage_base $page, $disk_path, $theme)
	{
		ffDetails_base::__construct($page, $disk_path, $theme);
		
		$this->addEvent("on_before_record_update", "ffDetails_drag_update_order", ffEvent::PRIORITY_HIGH);
		$this->addEvent("on_before_record_insert", "ffDetails_drag_insert_order", ffEvent::PRIORITY_HIGH);
		$this->addEvent("on_loaded_data", "ffDetails_drag_on_loaded_data", ffEvent::PRIORITY_HIGH);

	}
	
	public function display_rows()
	{
		// First of all, set hidden fields to handle deleted keys 
		if (is_array($this->deleted_keys) && count($this->deleted_keys))
		{
			for ($i = 0; $i < count($this->deleted_keys); $i++)
			{
				foreach ($this->deleted_keys[$i] as $key => $value)
				{
					$this->tpl[0]->set_var("hidden_name", "deleted_keys[$i][$key]");
					$this->tpl[0]->set_var("hidden_value", $value->getValue(null, FF_SYSTEM_LOCALE));
					$this->tpl[0]->parse("SectHidden", true);
				}
				reset($this->deleted_keys[$i]);
			}

			for ($i = 0; $i < count($this->deleted_values); $i++)
			{
				foreach ($this->deleted_values[$i] as $key => $value)
				{
					$this->tpl[0]->set_var("hidden_name", "deleted_values[$i][$key]");
					$this->tpl[0]->set_var("hidden_value", $value->getValue(null, FF_SYSTEM_LOCALE));
					$this->tpl[0]->parse("SectHidden", true);
				}
				reset($this->deleted_values[$i]);
			}
		}

		$totfields = count($this->form_fields);
		if ($this->display_delete)
			$totfields++;

		$this->tpl[0]->set_var("maxspan", $totfields);

		//$this->preProcessDetailButtons();
		
		//------------------------------------------
		// Manage Avaiable Records
		//------------------------------------------

		// retrieve records
		
		$recordset_avaiable = array();
		
		if (is_string($this->drag_records_avaiable))
		{
			$this->db[0]->query($this->drag_records_avaiable);
			if ($this->db[0]->nextRecord())
			{
				do
				{
					$each_tmp = $this->form_fields;
					$slice = array();
					foreach ($each_tmp as $key => $value)
					{
						$slice[$key] = $this->db[0]->getField($key, $this->form_fields[$key]->base_type);
					}
					
					if (ffCommon_partial_in_array($slice, $this->recordset) === false)
						$recordset_avaiable[] = $slice;

				} while ($this->db[0]->nextRecord());
			}
		}
		
		foreach ($recordset_avaiable as $key => $value)
		{
			$this->tpl[0]->set_var("SectFormField", "");
			$this->tpl[0]->set_var("SectFieldHidden", "");
			foreach ($value as $subkey => $subvalue)
			{
				//ffErrorHandler::raise("", E_USER_ERROR, $this, get_defined_vars());
				$this->tpl[0]->set_var("label", $this->form_fields[$subkey]->label);
				$this->tpl[0]->set_var("control", $this->form_fields[$subkey]->process("recordset_avl[" . $key . "][" . $subkey . "]", $subvalue));

				$this->tpl[0]->set_var("hidden_name", "recordset_avl_ori[" . $key . "][" . $subkey . "]");
				$this->tpl[0]->set_var("hidden_value", "");
				$this->tpl[0]->parse("SectFieldHidden", true);

				$this->tpl[0]->parse("SectFormField", true);
			}
			$this->tpl[0]->parse("SectAvaiableRow", true);
		}
		reset($recordset_avaiable);
		
		// pre-order detail buttons
	    $tmp_detail_buttons = $this->detail_buttons;
	    if (is_array($tmp_detail_buttons) && count($tmp_detail_buttons))
	    {
	        $rc = usort($tmp_detail_buttons, "ffCommon_IndexOrder");
	        if (!$rc)
	            ffErrorHandler::raise("UNABLE TO ORDER DETAIL BUTTONS", E_USER_ERROR, $this, get_defined_vars());
	    }

		// display data, if present
		if (count($this->recordset))
		{
			$i = count($recordset_avaiable) - 1;
			$display_label = true;
			foreach ($this->recordset as $rst_key => $rst_val)
			{
				$this->tpl[0]->set_var("SectSelFormField", "");
				$this->tpl[0]->set_var("SectSelFieldHidden", "");

				// EVENT HANDLER
				$res = $this->doEvent("on_before_process_row", array(&$this, $rst_val));
				$rc = end($res);
				if (null !== $rc)
				{
					if ($rc === true)
						continue;
					if ($rc !== false)
					{
						$this->contain_error = true;
						$this->strError = $rc;
					}
				}

				$i++;
				$this->tpl[0]->set_var("row", $i);
				$this->tpl[0]->set_var("rrow", $i + 1);

				foreach ($this->key_fields as $key => $value)
				{
					$this->tpl[0]->set_var("hidden_name", "recordset_ori[" . $i . "][" . $key . "]");
					$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset_ori[$rst_key][$key]->getValue(null, FF_SYSTEM_LOCALE)));
					$this->tpl[0]->parse("SectSelFieldHidden", true);
					$this->key_fields[$key]->value_ori = $this->recordset_ori[$rst_key][$key];

					if (!isset($this->form_fields[$key]))
					{
						$this->tpl[0]->set_var("hidden_name", "recordset[" . $i . "][" . $key . "]");
						$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset[$rst_key][$key]->getValue(null, FF_SYSTEM_LOCALE)));
						$this->tpl[0]->parse("SectSelFieldHidden", true);
						$this->key_fields[$key]->value = $this->recordset[$rst_key][$key];
					}
				}
				reset($this->key_fields);

				foreach ($this->hidden_fields as $key => $value)
				{
					$this->tpl[0]->set_var("hidden_name", "recordset_ori[" . $i . "][" . $key . "]");
					$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset_ori[$rst_key][$key]->getValue(null, FF_SYSTEM_LOCALE)));
					$this->tpl[0]->parse("SectSelFieldHidden", true);
					$this->hidden_fields[$key]->value_ori = $this->recordset_ori[$rst_key][$key];

					$this->tpl[0]->set_var("hidden_name", "recordset[" . $i . "][" . $key . "]");
					$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset[$rst_key][$key]->getValue(null, FF_SYSTEM_LOCALE)));
					$this->tpl[0]->parse("SectSelFieldHidden", true);
					$this->hidden_fields[$key]->value = $this->recordset[$rst_key][$key];
				}
				reset($this->hidden_fields);

				foreach ($this->form_fields as $key => $value)
				{
					$this->form_fields[$key]->value_ori = $this->recordset_ori[$rst_key][$key];
					$this->form_fields[$key]->value = $this->recordset[$rst_key][$key];
				}
				reset($this->form_fields);
				
				$res = $this->doEvent("on_after_process_row", array(&$this, $rst_key));
				
				$col = 0;
				$this->tpl[0]->set_var("SectCol", "");
				
				if ($this->display_delete && $this->buttons_options["delete"]["display"])
				{
					$this->getDetailButton("detail_delete")->variables[$this->main_record[0]->id . "_detailaction"] = $this->id;
					$this->getDetailButton("detail_delete")->variables[$this->id . "_delete_row"] = $i;
				}

				//$this->processDetailButtons($col, $display_label);
				$col++;
				
				foreach ($this->form_fields as $key => $value)
				{
					//$this->processDetailButtons($col, $display_label);

					// store hidden original value
					if (!isset($this->key_fields[$key]) && $this->form_fields[$key]->data_type == "db") /*&& isset($this->recordset_ori[$rst_key][$key])*/
					{
						$this->tpl[0]->set_var("hidden_name", "recordset_ori[" . $i . "][" . $key . "]");
						$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset_ori[$rst_key][$key]->getValue(null, FF_SYSTEM_LOCALE)));
						$this->tpl[0]->parse("SectSelFieldHidden", true);
					}
					
					// EVENT HANDLER
					$res = $this->doEvent("on_before_process_field", array(&$this, $rst_val, $this->form_fields[$key]));

					// if control is a Label, store hidden value
/*					if ($this->form_fields[$key]->control_type == "label")
					{
						$this->tpl[0]->set_var("hidden_name", "recordset[" . $i . "][" . $key . "]");
						$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset[$rst_key][$key]->getValue($this->form_fields[$key]->get_app_type(), $this->form_fields[$key]->get_locale())));
						$this->tpl[0]->parse("SectSelFieldHidden", true);
					}
*/

					$this->form_fields[$key]->row = $i;
					$this->tpl[0]->set_var("width", $this->form_fields[$key]->width);
					if ($this->form_fields[$key]->extended_type == "File")
					{
						$this->form_fields[$key]->file_tmpname = $this->recordset_files[$rst_key][$key]["tmpname"];
					}

           			if (!$this->use_fixed_fields)
						$tmp_loc = "control";
					else
						$tmp_loc = $key;

					$this->tpl[0]->set_var("container_class", $this->form_fields[$key]->container_class);
					$this->tpl[0]->set_var("container_properties", $this->form_fields[$key]->getProperties($this->form_fields[$key]->container_properties));

					if (!$this->use_fixed_fields) {
                        if($this->form_fields[$key]->display)
                        {
                            $this->tpl[0]->set_var($tmp_loc, $this->form_fields[$key]->process(
                                                                                                      "recordset[" . $i . "][" . $key . "]"
                                                                                                    , $this->recordset[$rst_key][$key]
                                                                                                )
                                                );
                        }
                        else 
                            $this->tpl[0]->set_var("control", "");
                    }
					else
					{
						$this->tpl[0]->set_var($tmp_loc . "_value",
														$this->form_fields[$key]->get_encoded($this->recordset[$rst_key][$key]->getValue($this->form_fields[$key]->get_app_type(), $this->form_fields[$key]->get_locale()))
											);
						$this->tpl[0]->set_var($tmp_loc . "_field", $this->form_fields[$key]->process(
																											  "recordset[" . $i . "][" . $key . "]"
																											, $this->recordset[$rst_key][$key]
																										)
											);
					}

					$this->tpl[0]->parse("SectSelFormField", true);
					
					$col++;
				}
				reset($this->form_fields);
				//$this->processDetailButtons($col, $display_label, true);
				
				// EVENT HANDLER
				$res = $this->doEvent("on_before_parse_row", array(&$this, $rst_val));
				$rc = end($res);
				if (null !== $rc)
				{
					if ($rc === false)
						$this->tpl[0]->parse("SectSelectedRow", true);
					else if ($rc !== true)
					{
						$this->contain_error = true;
						$this->strError = $rc;
					}
				}
				else
					$this->tpl[0]->parse("SectSelectedRow", true);
				
				$display_label = false;
			}
			reset($this->recordset);
		}
		else
		{
			$this->tpl[0]->set_var("SectFormRow", "");
		}

		if ($this->display_delete)
		{
			$this->cols++;
			$this->tpl[0]->parse("SectDeleteLabel", false);
		}
		else
			$this->tpl[0]->set_var("SectDeleteLabel", "");
			
		$this->tpl[0]->set_var("rows", $this->rows);
		$this->tpl[0]->set_var("maxspan", $this->cols);
		return;
	}
	
	protected function tplLoad()
	{
		$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
		$this->tpl[0]->load_file($this->template_file, "main");
		
		if (strlen($this->id))
			$this->prefix = $this->id . "_";
		$this->tpl[0]->set_var("component", $this->prefix);
		$this->tpl[0]->set_var("componentid", $this->id);

		$this->tpl[0]->set_var("main_record_component", $this->main_record[0]->prefix);

		$this->tpl[0]->set_var("site_path", $this->site_path);
		$this->tpl[0]->set_var("page_path", $this->page_path);
		$this->tpl[0]->set_var("theme", $this->getTheme());
		$this->tpl[0]->set_var("class", $this->class);
		$this->tpl[0]->set_var("SectHiddden", "");
		
		$this->tpl[0]->set_var("requested_url", ffCommon_specialchars($_SERVER["REQUEST_URI"]));

		$this->tpl[0]->set_var("title", $this->title);
		
		if ($this->display_new == true)
			$this->tpl[0]->parse("SectNew", false);
		else
			$this->tpl[0]->set_var("SectNew", "");
	}

	public function tplParse($output_result)
	{
		$res = $this->doEvent("on_tpl_parse", array(&$this, $this->tpl[0]));

		if ($this->main_record !== NULL && $this->main_record[0]->parent !== NULL) //code for ff.js
			$this->main_record[0]->parent[0]->tplAddJs("ff.ffDetails", "ffDetails.js", FF_THEME_DIR . "/library/ff");

		if ($output_result)
		{
			echo $this->fixed_pre_content;
			$this->tpl[0]->pparse("main", false);
			echo $this->fixed_post_content;
			return true;
		}
		else
		{
			return $this->fixed_pre_content . $this->tpl[0]->rpparse("main", false) . $this->fixed_post_content;
		}
	}
	public function structProcess($tpl)
	{
	}
}
