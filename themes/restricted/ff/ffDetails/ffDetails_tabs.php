<?php
class ffDetails_tabs extends ffDetails_base
{
	/**
	 * Il prefisso di ogni oggetto nel template HTML
	 * @var String
	 */
	public $prefix			= null;
	
	/**
	 * Il template di default
	 * @var String
	 */
    var $template_file         = "ffDetails_tabs.html";

	/**
	 * le etichette delle schede
	 * @var Array
	 */
    var $tab_label	= null;

	/**
	 * L'id Del tab
	 * @var String
	 */
	var $tab		= null;
    
	/**
	 * Il testo descrittivo da inserire nel template
	 * @var String
	 */
	public $description = null;

	/**
	 * Se dev'essere utilizzata la widget "disclosure panels"
	 * @var Boolean
	 */
	var $widget_discl_enable = false;
	/**
	 * Se il pannello dev'essere aperto di default
	 * @var Boolean
	 */
	var $widget_def_open = false;
	
	/**
	 * Il tipo di widget che deve essere caricata
	 * TODO: va eliminato! c'è widget_deps
	 * @var Array
	 */
	var $widget_need = array("tabs");	//only widget page compatibles
	
	/**
	 * Forza il dettaglio ad agire usando le funzioni Ajax
	 * @var Boolean
	 */
	var $doAjax	= true;

	var $widget_tab_mode = "both";
	/**
	 * Visualizza il contenuto del dettaglio, riga per riga
	 */
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
				$res = $this->doEvent("on_before_process_row", array(&$this, &$rst_val, $i + 1)); 
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
					$this->getDetailButton("deleterow")->variables[$this->main_record[0]->id . "_detailaction"] = $this->id;
					$this->getDetailButton("deleterow")->variables[$this->id . "_delete_row"] = $i;
				}

				$this->processDetailButtons($col, false, false, $i);
				//$col++;
				
				foreach ($this->form_fields as $key => $value)
				{
					$multi_field = false;
					$this->processDetailButtons($col, $display_label, false, $i);

					// store hidden original value
					if (!isset($this->key_fields[$key]) && $this->form_fields[$key]->data_type == "db") /*&& isset($this->recordset_ori[$rst_key][$key])*/
					{
						if (is_array($this->form_fields[$key]->multi_fields) && count($this->form_fields[$key]->multi_fields))
						{
							$multi_field = true;
							foreach ($this->form_fields[$key]->multi_fields as $subkey => $subvalue)
							{
								$this->tpl[0]->set_var("hidden_name", "recordset_ori[" . $i . "][" . $key . "][" . $subkey . "]");
								$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset_ori[$rst_key][$key][$subkey]->getValue(null, FF_SYSTEM_LOCALE)));
								$this->tpl[0]->parse("SectHidden", true);

								$this->tpl[0]->set_var("hidden_name", "recordset[" . $i . "][" . $key . "][" . $subkey . "]");
								$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset[$rst_key][$key][$subkey]->getValue(null, FF_SYSTEM_LOCALE)));
								$this->tpl[0]->parse("SectHidden", true);

								$this->form_fields[$key]->multi_values[$subkey] = $this->recordset[$rst_key][$key][$subkey];
								$this->form_fields[$key]->multi_values_ori[$subkey] = $this->recordset_ori[$rst_key][$key][$subkey];
							}
							reset($this->form_fields[$key]->multi_fields);
						}
						else
						{
							$this->tpl[0]->set_var("hidden_name", "recordset_ori[" . $i . "][" . $key . "]");
							$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset_ori[$rst_key][$key]->getValue(null, FF_SYSTEM_LOCALE)));
							$this->tpl[0]->parse("SectHidden", true);
						}
					}
					
					$tmp_source_SQL = "";
					if(strlen($this->form_fields[$key]->source_SQL)) {
						$tmp_source_SQL = $this->form_fields[$key]->source_SQL;
						$this->form_fields[$key]->source_SQL = ffProcessTags(
							$this->form_fields[$key]->source_SQL
							, $this->key_fields
							, $this->form_fields
							, "normal"
							, $this->main_record[0]->parent[0]->get_params()
							, urlencode($_SERVER['REQUEST_URI'])
							, $this->parent[0]->get_globals()
							, $this->hidden_fields
						);
					}

					// EVENT HANDLER
					$res = $this->doEvent("on_before_process_field", array(&$this, $rst_val, &$this->form_fields[$key]));

					if(!$this->form_fields[$key]->display && !$this->form_fields[$key]->display_label) 
						continue;

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

					if (!$multi_field)
					{
						$rc |= $this->tpl[0]->set_var($key . "_value", $this->recordset[$rst_key][$key]->getValue($this->form_fields[$key]->base_type, FF_SYSTEM_LOCALE));
						$rc |= $this->tpl[0]->set_var($key . "_display_value", $this->form_fields[$key]->getDisplayValue(
								null, null, $this->recordset[$rst_key][$key]
							));
					}
					if ($this->tpl[0]->isset_var($key . "_field"))
						$rc |= $this->tpl[0]->set_var($key . "_field", $this->form_fields[$key]->process(
																								  "recordset[" . $i . "][" . $key . "]"
																								, $this->recordset[$rst_key][$key]
																							));
					if (strlen($this->recordset[$rst_key][$key]->ori_value))
					{
						$rc |= $this->tpl[0]->parse("SectSet_$key", false);
						$rc |= $this->tpl[0]->set_var("SectNotSet_$key", "");
					}
					else
					{
						$rc |= $this->tpl[0]->set_var("SectSet_$key", "");
						$rc |= $this->tpl[0]->parse("SectNotSet_$key", false);
					}

					if ($this->form_fields[$key]->extended_type == "Selection")
					{
						$rc |= $this->tpl[0]->set_regexp_var("/SectSet_" . $key . "_.+/", "");
						$rc |= $this->tpl[0]->parse("SectSet_" . $key . "_" . $this->recordset[$rst_key][$key]->getValue($this->form_fields[$key]->base_type, FF_SYSTEM_LOCALE), false);
					}

					$rc |= $this->tpl[0]->parse("Sect_$key", false);

					if (!$rc)
					{
						$class = $this->form_fields[$key]->container_class;

						if ($this->form_fields[$key]->required) {
						    $class = $class . (strlen($class) ? " " : "") . "required";
						} 
						$class = $class . (strlen($class) ? " " : "") . $this->form_fields[$key]->get_control_class(null, null, array("framework_css" => false, "control_type" => false));
						$class = $class . (strlen($class) ? " " : "") . cm_getClassByFrameworkCss("", "row-default");
						if(strlen($class)) {
							$this->tpl[0]->set_var("container_class", trim($class));
						} else {
							$this->tpl[0]->set_var("container_class", "");
						}

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

						if ($this->form_fields[$key]->display_label)
						{
							if ($this->form_fields[$key]->description !== null) 
							{       
								$this->tpl[0]->set_var("description", $this->form_fields[$key]->description);
								$this->tpl[0]->parse("SectDescriptionLabel", false);
							}	
							else
							{
								$this->tpl[0]->set_var("description", "");
								$this->tpl[0]->set_var("SectDescriptionLabel", "");
							}

						    if ($this->form_fields[$key]->required) {
							    $this->tpl[0]->parse("SectRequiredSymbol", false);
						    } else {
							    $this->tpl[0]->set_var("SectRequiredSymbol", "");
						    }
							if($this->form_fields[$key]->encode_label) 
								$this->tpl[0]->set_var("FormFieldLabel", ffCommon_specialchars($this->form_fields[$key]->label));
							else
								$this->tpl[0]->set_var("FormFieldLabel", $this->form_fields[$key]->label);

							$this->tpl[0]->parse("SectFormFieldLabel", false);
							$this->tpl[0]->set_var("colspan", "");
							$this->tpl[0]->parse("SectLabel", true);
						} else {
							$this->tpl[0]->set_var("colspan", "colspan=\"2\"");
							$this->tpl[0]->set_var("SectFormFieldLabel", ""); 
							$this->tpl[0]->set_var("SectLabel", "");
						}

						$this->tpl[0]->parse("SectFormField", true);
						$this->tpl[0]->parse("SectCol", true);
						$col++;
					}

					if(strlen($tmp_source_SQL))
						 $this->form_fields[$key]->source_SQL = $tmp_source_SQL;
					
				}
				reset($this->form_fields);
				$this->processDetailButtons($col, $display_label, true, $i);

                $this->tpl[0]->set_var("group_class", "box2 " . ($rst_val[$this->tab_label] 
                													? $rst_val[$this->tab_label]->getValue(
                                                                        (isset($this->form_fields[$this->tab_label])
                                                                            ? $this->form_fields[$this->tab_label]->get_app_type()
                                                                            : $this->hidden_fields[$this->tab_label]->get_app_type()
                                                                        ), 
                                                                        (isset($this->form_fields[$this->tab_label])
                                                                            ? $this->form_fields[$this->tab_label]->get_locale()
                                                                            : $this->hidden_fields[$this->tab_label]->get_locale()
                                                                        )
                                                                    )
                                                                    : ""
                                                                )
                                                            );
                
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
			$this->tpl[0]->set_var("tab_mode", $this->widget_tab_mode);
            $this->tpl[0]->parse("SectHeaderTab", false);
		}
		else
		{
            $this->tpl[0]->set_var("SectHeaderRow", "");
			$this->tpl[0]->set_var("SectFormRow", "");
			$this->tpl[0]->set_var("SectHeaderTab", "");
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

	/**
	 * La funzione che carica il template ed imposta le variabili di default
	 */
	protected function tplLoad()
	{
		if ($this->tpl === null)
		{
			$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
			$this->tpl[0]->load_file($this->template_file, "main");
		}

		if(is_array($this->widget_need) && count($this->widget_need)) {
			foreach($this->widget_need AS $widget) {
				$this->parent[0]->widgetLoad($widget);
			}
		}
		if (strlen($this->id))
			$this->prefix = $this->id . "_";
		$this->tpl[0]->set_var("component_id", $this->id);

		$this->tpl[0]->set_var("main_record_component", $this->main_record[0]->prefix);

		$this->tpl[0]->set_var("site_path", $this->site_path);
		$this->tpl[0]->set_var("page_path", $this->page_path);
		$this->tpl[0]->set_var("theme", $this->getTheme());

        $component_class["default"] = $this->class;
        if($this->framework_css["component"]["grid"]) {
            if(is_array($this->framework_css["component"]["grid"]))
                $component_class["grid"] = cm_getClassByFrameworkCss($this->framework_css["component"]["grid"], "col");
            else {
                $component_class["grid"] = cm_getClassByFrameworkCss("", $this->framework_css["component"]["grid"]);
            }
        }
        $component_class["form"] = cm_getClassByFrameworkCss("component" . $this->framework_css["component"]["type"], "form");

        $this->tpl[0]->set_var("component_class", implode(" ", array_filter($component_class)));

        if(is_array($this->framework_css["component"]["col"]) && $this->framework_css["component"]["inner_wrap"] === null)
            $this->framework_css["component"]["inner_wrap"] = "row";

        if($this->framework_css["component"]["inner_wrap"]) 
        {
            if(is_array($this->framework_css["component"]["inner_wrap"])) {
                $this->tpl[0]->set_var("inner_wrap_start", '<div class="' . cm_getClassByFrameworkCss($this->framework_css["component"]["inner_wrap"], "col", "innerWrap") . '">');
            } elseif(is_bool($this->framework_css["component"]["inner_wrap"])) {
                $this->tpl[0]->set_var("inner_wrap_start", '<div class="innerWrap">');
            } else {
                $this->tpl[0]->set_var("inner_wrap_start", '<div class="' . cm_getClassByFrameworkCss("", $this->framework_css["component"]["inner_wrap"], "innerWrap") . '">');
            }
            $this->tpl[0]->set_var("inner_wrap_end", '</div>');
        }       
           
        if($this->framework_css["component"]["outer_wrap"]) 
        {
            if(is_array($this->framework_css["component"]["outer_wrap"])) {
                $this->tpl[0]->set_var("outer_wrap_start", '<div class="' . cm_getClassByFrameworkCss($this->framework_css["component"]["outer_wrap"], "col", $this->id . "Wrap outerWrap"). '">');
            } elseif(is_bool($this->framework_css["component"]["outer_wrap"])) {
                $this->tpl[0]->set_var("outer_wrap_start", '<div class="' . $this->id . 'Wrap outerWrap">');
            } else {
                $this->tpl[0]->set_var("outer_wrap_start", '<div class="' . cm_getClassByFrameworkCss("", $this->framework_css["component"]["outer_wrap"], $this->id . "Wrap outerWrap") . '">');
            }
            $this->tpl[0]->set_var("outer_wrap_end", '</div>');                
        }
        
		$this->tpl[0]->set_var("SectHiddden", "");

        $this->tpl[0]->set_var("fixed_pre_content", $this->fixed_pre_content);
        $this->tpl[0]->set_var("fixed_post_content", $this->fixed_post_content);
        
        $this->tpl[0]->set_var("fixed_title_content", $this->fixed_title_content);
        $this->tpl[0]->set_var("fixed_heading_content", $this->fixed_heading_content);

		$this->tpl[0]->set_var("XHR_DIALOG_ID", $_REQUEST["XHR_DIALOG_ID"]);
		$this->tpl[0]->set_var("requested_url", ffCommon_specialchars($_SERVER["REQUEST_URI"]));

		$this->tpl[0]->set_var("title", ffCommon_specialchars($this->title));

		if ($this->description !== null)
			$this->tpl[0]->set_var("description", $this->description);

		if ($this->tab)
		{
			$this->tpl[0]->set_var("tab_id", $this->main_record[0]->id);
			$this->tpl[0]->set_var("tab_number", key($this->main_record[0]->tabs[$this->tab]) + 1);
			$this->tpl[0]->parse("SectTabUrl", false);
		}
		else
		{
			$this->tpl[0]->set_var("SectTabUrl", "");
		}

		if ($this->doAjax)
		{
			if (isset($_REQUEST["XHR_DIALOG_ID"])) {
				$this->tpl[0]->set_var("submit_action", "ff.ffPage.dialog.doRequest('" . $_REQUEST["XHR_DIALOG_ID"] . "', {'action' : '" . $this->main_record[0]->prefix . "detail_addrows', 'component' :'" . $this->id . "', 'detailaction' : '" . $this->main_record[0]->prefix . "'})");
			} else {
				if ($this->main_record !== NULL && $this->main_record[0]->parent !== NULL) {//code for ff.js
					//$this->main_record[0]->parent[0]->tplAddJs("jquery.blockui", "jquery.blockui.js", FF_THEME_DIR . "/library/plugins/jquery.blockui");
					$this->main_record[0]->parent[0]->tplAddJs("ff.ajax", "ajax.js", FF_THEME_DIR . "/library/ff");
				}

				$this->tpl[0]->set_var("submit_action", "ff.ajax.doRequest({'component' : '" . $this->id . "'});");
			}
		}
		else
			$this->tpl[0]->set_var("submit_action", "document.getElementById('frmMain').submit();");

        if ($this->display_new === true) {
            if ($this->tab)
            {
                $this->tpl[0]->set_var("tab_id", $this->main_record[0]->id);
                $this->tpl[0]->set_var("tab_number", key($this->main_record[0]->tabs[$this->tab]) + 1);
                $this->tpl[0]->parse("SectHeaderTabUrl", false);
                $this->tpl[0]->parse("SectFooterTabUrl", false);
            }
            else
            {
                $this->tpl[0]->set_var("SectHeaderTabUrl", "");
                $this->tpl[0]->set_var("SectFooterTabUrl", "");
            }

            if($this->buttons_options["addrow"]["label"] === null)
                $this->buttons_options["addrow"]["label"] = ffTemplate::_get_word_by_code("ffDetail_addrow");
            
            if($this->buttons_options["addrow"]["icon"] === null)
                $this->buttons_options["addrow"]["icon"] = cm_getClassByFrameworkCss("addrow", "icon-" . $this->buttons_options["addrow"]["aspect"] . "-tag");

            if($this->buttons_options["addrow"]["class"] === null)
                $this->buttons_options["addrow"]["class"] = cm_getClassByFrameworkCss("addrow", $this->buttons_options["addrow"]["aspect"]);        

            $this->tpl[0]->set_var("addrow_label", $this->buttons_options["addrow"]["label"]);
            $this->tpl[0]->set_var("addrow_class", $this->buttons_options["addrow"]["class"]);  
            $this->tpl[0]->set_var("addrow_icon", $this->buttons_options["addrow"]["icon"]);  

            if($this->display_rowstoadd) {
                if($this->display_new_location == "Header" || $this->display_new_location == "Both")
                    $this->tpl[0]->parse("SectNewHeaderQta", false);
                if($this->display_new_location == "Footer" || $this->display_new_location == "Both")
                    $this->tpl[0]->parse("SectNewFooterQta", false);
            } else {
                $this->tpl[0]->set_var("hidden_name", "rowstoadd");
                $this->tpl[0]->set_var("hidden_value", "1");
                $this->tpl[0]->parse("SectHidden", true);
                $this->tpl[0]->set_var("SectNewHeaderQta", "");
                $this->tpl[0]->set_var("SectNewFooterQta", "");
            }

            if($this->display_new_location == "Header" || $this->display_new_location == "Both")
                $this->tpl[0]->parse("SectNewHeader", false);
            if($this->display_new_location == "Footer" || $this->display_new_location == "Both")
                $this->tpl[0]->parse("SectNewFooter", false);

            $this->tpl[0]->parse("SectTitle", false);
        } else {
            $this->tpl[0]->set_var("SectNewHeader", "");
            $this->tpl[0]->set_var("SectNewFooter", "");
            if(strlen($this->title) || $this->widget_discl_enable)
                $this->tpl[0]->parse("SectTitle", false);
            else
                $this->tpl[0]->set_var("SectTitle", "");
        }
	}

	/**
	 * Restituisce l'output eseguendo il processing finale
	 * @param Boolean $output_result Se il contenuto dev'essere restituito o "visualizzato"
	 * @return Boolean Il risultato del processing od un flag per confermare l'avvenuta esecuzione
	 */
	public function tplParse($output_result)
	{
		$res = ffDetails::doEvent("on_tplParse", array($this, $this->tpl[0]));
		$res = $this->doEvent("on_tpl_parse", array(&$this, $this->tpl[0]));

		if ($output_result === true)
		{
			$this->tpl[0]->pparse("main", false);
			return true;
		}
		elseif ($output_result === false)
		{
			return $this->tpl[0]->rpparse("main", false);
		}
	}

	function process_headers()
	{
		if ($this->main_record !== NULL && $this->main_record[0]->parent !== NULL) //code for ff.js
			$this->main_record[0]->parent[0]->tplAddJs("ff.ffDetails", "ffDetails.js", FF_THEME_DIR . "/library/ff");

		if (!isset($this->tpl[0]))
			return;

		return $this->tpl[0]->rpparse("SectHeaders", false);
	}

	function process_footers()
	{
		if (!isset($this->tpl[0]))
			return;

		return $this->tpl[0]->rpparse("SectFooters", false);
	}
	/**
	 * Accoda al template i pulsanti basandosi sul numero di colonna
	 * @param Int $col il numero di colonna
	 * @param Boolean $display_label se deve visualizzare l'etichetta
	 * @param Boolean $remaining se deve processare tutti i pulsanti che rimangono (da li in poi)
	 * @param Int $row il numero della riga
	 */
	function processDetailButtons($col, $display_label = false, $remaining = false, $row = "")
	{
		if (is_array($this->detail_buttons) && count($this->detail_buttons))
		{
			$tmp = $this->detail_buttons;
			foreach ($tmp as $key => $value)
			{
				if ($value["index"] == $col || ($remaining && $value["index"] >= $col))
				{
					if ($key == "deleterow")
					{
						if (isset($_REQUEST["XHR_DIALOG_ID"])) {
							$this->detail_buttons[$key]["obj"]->jsaction = "ff.ffPage.dialog.doRequest('" . $_REQUEST["XHR_DIALOG_ID"] . "', {'action' : '" . $this->main_record[0]->id . "_detail_delete', 'component' : '" . $this->id . "', 'detailaction' : '" . $this->main_record[0]->id . "_', 'action_param' : " . $row . "});";
						} else {
							if ($this->main_record !== NULL && $this->main_record[0]->parent !== NULL) {//code for ff.js
								//$this->main_record[0]->parent[0]->tplAddJs("jquery.blockui", "jquery.blockui.js", FF_THEME_DIR . "/library/plugins/jquery.blockui");
								$this->main_record[0]->parent[0]->tplAddJs("ff.ajax", "ajax.js", FF_THEME_DIR . "/library/ff");
							}

							$this->detail_buttons[$key]["obj"]->jsaction = "ff.ajax.doRequest({'component' : '" . $this->id . "'});";
						}
					}
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
														, false
														, $key . "_" . $row
																	)
										   );
					$this->tpl[0]->parse("SectDetailButton", false);
					$this->tpl[0]->set_var("SectFormField", "");
					$this->tpl[0]->parse("SectCol", true);

					if ($display_label)
					{
						$this->tpl[0]->set_var("DetailButtonLabel", ffCommon_specialchars($this->detail_buttons[$key]["obj"]->label));
						$this->tpl[0]->parse("SectDetailButtonLabel", false);
						$this->tpl[0]->set_var("SectFormFieldLabel", "");
						$this->tpl[0]->parse("SectLabel", true);
					}
				}
			}
			reset($tmp);
		}
	}
	/**
	 * elabora la sezione relativa alla visualizzazione dell'errore nel template
	 * da richiamare ogniqualvolta si aggiorna l'errore
	 */
	function displayError($sError = null)
	{
		if ($sError !== null)
			$this->strError = $sError;

		if (strlen($this->strError))
		{
			$this->tpl[0]->set_var("strError", $this->strError);
			$this->tpl[0]->parse("SectError", false);
		}
		else
			$this->tpl[0]->set_var("SectError", "");

		return $sError;
	}
	/**
	 * Elabora l'azione. L'azione viene ereditata dall'oggetto record padre
	 * @return Mixed Il risultato del processing
	 */
	function process_action()
	{
		if ($this->frmAction == "detail_addrows")
			$this->widget_def_open = true;

		return parent::process_action();
	}

	/**
	 * Prepara i pulsanti standard del dettaglio
	 * al momento l'unico pulsante di default è quello di cancellazione
	 */
	function preProcessDetailButtons()
	{
		if (!$this->doAjax)
			return parent::preProcessDetailButtons();

		// PREPARE DEFAULT BUTTONS
		if ($this->display_delete && $this->buttons_options["delete"]["display"])
		{
			if ($this->buttons_options["delete"]["obj"] !== null)
			{
				$this->addContentButton($this->buttons_options["delete"]["obj"]
										, $this->buttons_options["delete"]["index"]);
			}
			else
			{
				$tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->getTheme());
				$tmp->id 			= "deleterow";
				$tmp->frmAction		= "detail_delete";
                $tmp->label         = $this->buttons_options["delete"]["label"];
                $tmp->icon          = $this->buttons_options["delete"]["icon"];
                $tmp->class         = $this->buttons_options["delete"]["class"];
                $tmp->aspect        = $this->buttons_options["delete"]["aspect"];
				$tmp->action_type 	= "submit";
				$tmp->component_action = $this->main_record[0]->id;
				$this->addContentButton($tmp
										, $this->buttons_options["delete"]["index"]);
			}
		}
	}
	public function structProcess($tpl)
	{
	}
}
