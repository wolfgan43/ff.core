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
class ffDetails_tabs extends ffDetails_base
{
	public $prefix			= null;
	
    var $template_file         = "ffDetails_tabs.html";
    var $tab_label             = null;
    
    public $description = null;
    
    var $widget_discl_enable = false;
    
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

		$this->preProcessDetailButtons();
		
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
			$i = -1;
			$display_label = true;
			foreach ($this->recordset as $rst_key => $rst_val)
			{
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
                
                if($this->tab_label === null)
                {
                    $this->tpl[0]->set_var("tab_label", "" . $i + 1 . "");
                    $this->tpl[0]->parse("SectTabLabel", false); 
                } else {
                    if(is_array($this->tab_label) && count($this->tab_label)) {
                        $tmp_tab_label = "";
                        $check_tab_label = false;
                        $this->tpl[0]->set_var("SectTabLabel", "");

                        foreach ($this->tab_label as $tab_label_value) {
                            if(isset($rst_val[$tab_label_value])) {
                                $tmp_tab_label = $rst_val[$tab_label_value]->getValue(
                                                                (isset($this->form_fields[$tab_label_value])
                                                                    ? $this->form_fields[$tab_label_value]->get_app_type()
                                                                    : $this->hidden_fields[$tab_label_value]->get_app_type()
                                                                ), 
                                                                (isset($this->form_fields[$tab_label_value])
                                                                    ? $this->form_fields[$tab_label_value]->get_locale()
                                                                    : $this->hidden_fields[$tab_label_value]->get_locale()
                                                                )
                                                            );
                                if(strlen($tmp_tab_label)) {
	                                $this->tpl[0]->set_var("tab_label", $tmp_tab_label);
	                                $this->tpl[0]->parse("SectTabLabel", true);
	                                $check_tab_label = true;
								}
                            }
                            if(!$check_tab_label) {
                                $this->tpl[0]->set_var("tab_label", "" . $i + 1 . "");
                                $this->tpl[0]->parse("SectTabLabel", false);
                            }
                        }
                    } else {
                        if(isset($rst_val[$this->tab_label]) && strlen($rst_val[$this->tab_label]->getValue())) {
                            $this->tpl[0]->set_var("tab_label", $rst_val[$this->tab_label]->getValue(
                                                                                    (isset($this->form_fields[$this->tab_label])
                                                                                        ? $this->form_fields[$this->tab_label]->get_app_type()
                                                                                        : $this->hidden_fields[$this->tab_label]->get_app_type()
                                                                                    ), 
                                                                                    (isset($this->form_fields[$this->tab_label])
                                                                                        ? $this->form_fields[$this->tab_label]->get_locale()
                                                                                        : $this->hidden_fields[$this->tab_label]->get_locale()
                                                                                    )
                                                                                )
                                                                            );
                        } else {
                            $this->tpl[0]->set_var("tab_label", "" . $i + 1 . "");
                        }
                       	$this->tpl[0]->parse("SectTabLabel", false); 
                    }
                }
                
				foreach ($this->fields_relationship as $key => $value)
				{
					$this->tpl[0]->set_var( $value . "_FATHER", $this->main_record[0]->key_fields[$value]->value->getValue());
				}
				reset ($this->fields_relationship);
				
				foreach ($this->key_fields as $key => $value)
				{
					$this->tpl[0]->set_var("hidden_name", "recordset_ori[" . $i . "][" . $key . "]");
					$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset_ori[$rst_key][$key]->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE)));
					$this->tpl[0]->parse("SectHidden", true);
					$this->key_fields[$key]->value_ori = $this->recordset_ori[$rst_key][$key];

					if (!isset($this->form_fields[$key]))
					{
						$this->tpl[0]->set_var("hidden_name", "recordset[" . $i . "][" . $key . "]");
						$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset[$rst_key][$key]->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE)));
						$this->tpl[0]->parse("SectHidden", true);
						$this->key_fields[$key]->value = $this->recordset[$rst_key][$key];
					}

					$this->tpl[0]->set_var("Key_" . $key, $this->recordset[$rst_key][$key]->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE));
					$this->tpl[0]->set_var("encoded_Key_" . $key, rawurlencode($this->recordset[$rst_key][$key]->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE)));
					if (strlen($this->recordset[$rst_key][$key]->ori_value))
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

				foreach ($this->hidden_fields as $key => $value)
				{
					$this->tpl[0]->set_var("hidden_name", "recordset_ori[" . $i . "][" . $key . "]");
					$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset_ori[$rst_key][$key]->getValue(null, FF_SYSTEM_LOCALE)));
					$this->tpl[0]->parse("SectHidden", true);
					$this->hidden_fields[$key]->value_ori = $this->recordset_ori[$rst_key][$key];

					$this->tpl[0]->set_var("hidden_name", "recordset[" . $i . "][" . $key . "]");
					$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset[$rst_key][$key]->getValue(null, FF_SYSTEM_LOCALE)));
					$this->tpl[0]->parse("SectHidden", true);
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

				$this->processDetailButtons($col, false);
				//$col++;
				
				foreach ($this->form_fields as $key => $value)
				{
					$this->processDetailButtons($col, $display_label);

					// store hidden original value
					if (!isset($this->key_fields[$key]) && $this->form_fields[$key]->data_type == "db") /*&& isset($this->recordset_ori[$rst_key][$key])*/
					{
						$this->tpl[0]->set_var("hidden_name", "recordset_ori[" . $i . "][" . $key . "]");
						$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset_ori[$rst_key][$key]->getValue(null, FF_SYSTEM_LOCALE)));
						$this->tpl[0]->parse("SectHidden", true);
					}
					
					// EVENT HANDLER
					$res = $this->doEvent("on_before_process_field", array(&$this, $rst_val, $this->form_fields[$key]));

					// if control is a Label, store hidden value
					if ($this->form_fields[$key]->control_type == "label")
					{
						$this->tpl[0]->set_var("hidden_name", "recordset[" . $i . "][" . $key . "]");
						$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset[$rst_key][$key]->getValue($this->form_fields[$key]->get_app_type(), $this->form_fields[$key]->get_locale())));
						$this->tpl[0]->parse("SectHidden", true);
					}


					$this->form_fields[$key]->row = $i;
					$this->tpl[0]->set_var("width", $this->form_fields[$key]->width);
					if ($this->form_fields[$key]->extended_type == "File")
					{
						$this->form_fields[$key]->file_tmpname = $this->recordset_files[$rst_key][$key]["tmpname"];
					}

					$rc = false;
					
					$rc |= $this->tpl[0]->set_var($key . "_value", $this->recordset[$rst_key][$key]->getValue($this->form_fields[$key]->base_type, FF_SYSTEM_LOCALE));
					$rc |= $this->tpl[0]->set_var($key . "_display_value", $this->form_fields[$key]->getDisplayValue(
							null, null, $this->recordset[$rst_key][$key]
						));
					if ($this->tpl[0]->isset_var($key . "_field"))
						$rc |= $this->tpl[0]->set_var($key . "_field", $this->form_fields[$key]->process(
																								  "recordset[" . $i . "][" . $key . "]"
																								, $this->recordset[$rst_key][$key]
																							));

					if (!$rc)
					{
						$this->tpl[0]->set_var("container_class", $this->form_fields[$key]->container_class);
						$this->tpl[0]->set_var("container_properties", $this->form_fields[$key]->getProperties($this->form_fields[$key]->container_properties));

                        if($this->form_fields[$key]->display)
                        {
                            $this->tpl[0]->set_var("control", $this->form_fields[$key]->process(
                                                                                                      "recordset[" . $i . "][" . $key . "]"
                                                                                                    , $this->recordset[$rst_key][$key]
                                                                                                )
                                                    );
                        }
                        else 
                            $this->tpl[0]->set_var("control", "");

						if ($display_label)
						{
							$this->tpl[0]->set_var("FormFieldLabel", $this->form_fields[$key]->label);
							$this->tpl[0]->parse("SectFormFieldLabel", false);
							$this->tpl[0]->parse("SectLabel", true);
						}

						$this->tpl[0]->parse("SectFormField", true);
						$this->tpl[0]->parse("SectCol", true);
						$col++;
					}
				}
				reset($this->form_fields);
				$this->processDetailButtons($col, $display_label, true);
                
				// EVENT HANDLER
				$res = $this->doEvent("on_before_parse_row", array(&$this, $rst_val));
				$rc = end($res);
				if (null !== $rc)
				{
					if ($rc === false)
					{
                        $this->tpl[0]->parse("SectHeaderRow", true);
						$this->tpl[0]->parse("SectFormRow", true);
                        $this->tpl[0]->set_var("SectFormField", "");
					}
					else if ($rc !== true)
					{
						$this->contain_error = true;
						$this->strError = $rc;
					}
				}
				else
				{
                    $this->tpl[0]->parse("SectHeaderRow", true);
					$this->tpl[0]->parse("SectFormRow", true);
                    $this->tpl[0]->set_var("SectFormField", "");
                }
				//$display_label = false;
			}
			reset($this->recordset);
		}
		else
		{
            $this->tpl[0]->set_var("SectHeaderRow", "");
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
		
		if ($this->description !== null)
			$this->tpl[0]->set_var("description", $this->description);

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
		
	function processDetailButtons($col, $display_label = false, $remaining = false)
	{
		if (is_array($this->detail_buttons) && count($this->detail_buttons))
		{
			$tmp = $this->detail_buttons;
			foreach ($tmp as $key => $value)
			{
				if ($value["index"] == $col || ($remaining && $value["index"] >= $col))
				{
					$this->tpl[0]->set_var(
											"DetailButton"
											, $this->detail_buttons[$key]["obj"]->process(
															ffProcessTags(
																				$this->detail_buttons[$key]["obj"]->url
																				, $this->key_fields
																				, $this->form_fields
																				, "normal"
																				, $this->main_record[0]->parent[0]->get_params()
																				, urlencode($_SERVER['REQUEST_URI'])
																				, $this->parent[0]->get_globals()
																			)
														)
										   );
					$this->tpl[0]->parse("SectDetailButton", false);
					$this->tpl[0]->parse("SectCol", true);

					if ($display_label)
					{
						$this->tpl[0]->set_var("DetailButtonLabel", $this->detail_buttons[$key]["obj"]->label);
						$this->tpl[0]->parse("SectDetailButtonLabel", false);
						$this->tpl[0]->parse("SectLabel", true);
					}
				}
			}
			reset($tmp);
		}
	}
	public function structProcess($tpl)
	{
	}
}
