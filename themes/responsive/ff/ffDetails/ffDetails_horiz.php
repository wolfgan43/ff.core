<?php
frameworkCSS::extend(array(
			"component" => array(
				"inner_wrap" => false // null OR false OR true OR array(xs, sm, md, lg) OR 'row-default' OR 'row' OR 'row-fluid'
                , "outer_wrap" => false // false OR true OR array(xs, sm, md, lg) OR 'row-default' OR 'row' OR 'row-fluid'
				, "grid" => false		//false OR array(xs, sm, md, lg) OR 'row' OR 'row-fluid'
				, "type" => null		//null OR '' OR "inline"
			)
			, "actions" => array(
				"class" => "actions"
				, "row" => true
				, "util" => "right"
			)
			, "record" => array(
				"row" => true
			)
			, "field" => array(
				"label" => array(
					"col" => null
				)
				, "control" => array(
					"col" => null
				)
			)
			, "field-inline" => array(
				"label" => array(
					"col" => array(
						"xs" => 0
						, "sm" => 0
						, "md" => 12
						, "lg" => 4
					)
				)
				, "control" => array(
					"col" => array(
						"xs" => 12
						, "sm" => 12
						, "md" => 12
						, "lg" => 8
					)
				)
			)
			, "info" => array(
				"class" => "info"
				, "callout" => "info"
			)
			, "error" => array(
				"class" => "error"
				, "callout" => "danger"
			)
	), "ffDetails_horiz");

class ffDetails_horiz extends ffDetails_base
{
    var $framework_css = null;

	var $buttons_options		= array(
                                    "addrow" => array(
                                          "display" => true
                                        , "index"   => 0
                                        , "obj"     => null
                                        , "label"   => null 
                                        , "icon"    => null
                                        , "class"   => null
                                        , "aspect"  => "link"
                                    ),    
									"delete" => array(
										  "display" => true
										, "index" 	=> 0
										, "obj" 	=> null
                                        , "label"   => null 
                                        , "icon"    => null
										, "class" 	=> null
                                        , "aspect"  => "link"
									)
								);
	
	var $id_if					= null;	
	/**
	 * Visualizza le label orizzontalmente
	 * @var Boolean
	 */
	var $horizontal_labels				= true;

	/**
	 * Il template di default
	 * @var String
	 */
	public $template_file	= "ffDetails_horiz.html";

	/**
	 * L'eventuale tab in cui è inserito il dettaglio
	 * @var String
	 */
	public $tab				= null;
	
	/**
	 * Il testo descrittivo da inserire nel template
	 * @var String
	 */
	public $description = null;

	/**
	 * Se le azioni del dettaglio devono essere eseguite in ajax
	 * @var Boolean
	 */
	var $doAjax	= true;
    
    var $display_grid_location  = "Footer"; //Header Footer or Both

  	/**
     * una classe da associare alla riga
     * @var String
     */
    var $row_class        = "";
    /**
     * Un elenco di classi da associare alle righe della grid, ciclate in sequenza
     * @var Array
     */
    var $switch_row_class =  array(
                                "display" => false
                                , "first" => "odd"
                                , "second" => "even"
                            );
    
    /**
     * La classe da assegnare alle colonne intermedie
     * @var String
     */
    var $column_class                = "";
    /**
     * La classe da assegnare alla prima colonna
     * @var String
     */
    var $column_class_first         = "";
    /**
     * La classe da assegnare all'ultima colonna
     * @var String
     */
    var $column_class_last            	= "";
      
      
	var $js_deps = array(
		"ff.ffDetails" => null
	);
	
	function __construct(ffPage_html $page, $disk_path, $theme)
	{
		ffDetails_base::__construct($page, $disk_path, $theme);

        $this->framework_css = frameworkCSS::findComponent("ffDetails_horiz");

        if (ffTheme::RANDOMIZE_COMP_ID) {
            $this->id_if = uniqid();
        }
	}
	
	function getIDIF()
	{
		if ($this->id_if !== null)
			return $this->id_if;
		else
			return $this->id;
	}

	function getPrefix()
	{
		$tmp = $this->getIDIF();
		if (strlen($tmp))
			return $tmp . "_";
	}      
        
	/**
	 * Aggiunge un campo di tipo ffField a ffDetail
	 * @param ffField Il campo da aggiungere
	 * @return L'id del campo
	 */
	function addContent($field)
	{
		parent::addContent($field);
		
		$field->framework_css = array_replace_recursive($this->framework_css["field" . ($this->framework_css["component"]["type"] ? "-" : "") . $this->framework_css["component"]["type"]], $field->framework_css);
		
		return $field->id;
	}

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

		// display labels when horiz
		if ($this->horizontal_labels)
		{
			$col = 0;

            if($this->display_grid_location == "Header" || $this->display_grid_location == "Both")
			    $this->processDetailButtons($col, true, false, "");

			$col++;
			$display_col = 0;
			foreach ($this->form_fields as $key => $value)
			{         
				$this->processDetailButtons($col, true, false, "");
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

                    $class = "";
                    $properties = "";

                    if (strlen($this->form_fields[$key]->container_class))
                        $class = $this->form_fields[$key]->container_class;
                    else
                        $class = $this->column_class;
                    
                    if ($col == 1 && $this->column_class_first)
                        $class .= " " . $this->column_class_first;
                    elseif ($col == $totfields && $this->column_class_last)
                        $class .= " " . $this->column_class_last;
                    
                    $class .= " ffField";

                    if($this->form_fields[$key]->get_app_type() == "Text" && $this->form_fields[$key]->extended_type != "String")
                        $class .= " " . strtolower($this->form_fields[$key]->extended_type);
                    else 
                        $class .= " " . strtolower($this->form_fields[$key]->get_app_type());

                    $class = str_replace("[COL]", $col, $class);
                    $class = str_replace("[ID]", $this->form_fields[$key]->id, $class);
                    $class = trim($class);

					if ($this->form_fields[$key]->required) {
						$class = $class . (strlen($class) ? " " : "") . "required";
					}
					/*
					* se inserito in block modify detail le checkbox si vedono male
					*/

					//$class = $class . (strlen($class) ? " " : "") . $this->form_fields[$key]->get_control_class(null, array("framework_css" => false, "control_type" => false));

                    if (strlen($class))
                        $class = "class=\"" . $class . "\"";

                    $properties = $this->form_fields[$key]->getProperties($this->form_fields[$key]->container_properties);
                    if(strlen($this->form_fields[$key]->width))
                    	$properties .= " width=\"" . $this->form_fields[$key]->width . "\"";

                    $this->tpl[0]->set_var("col_class", $class);
                    $this->tpl[0]->set_var("col_properties", $properties);

					$this->tpl[0]->set_var("SectDetailButtonLabel", "");
					$this->tpl[0]->parse("SectFormFieldLabel", false);  
					$this->tpl[0]->parse("SectLabel", true);  
					
					$display_col++;
				} else {
					$this->tpl[0]->set_var("SectDetailButtonLabel", "");
					$this->tpl[0]->set_var("SectFormFieldLabel", ""); 
					$this->tpl[0]->set_var("SectLabel", "");				
				}

				$col++;
			}
			reset($this->form_fields);
			$this->processDetailButtons($col, true, true, "");
            	
            if($this->display_grid_location == "Footer" || $this->display_grid_location == "Both")
                $this->processDetailButtons(0, true, false, "");
            
            if($display_col) 
            	$this->tpl[0]->parse("SectFormHeader", false);                 
		}

		// display data, if present
		if (count($this->recordset))
		{
			$i = -1;
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

				foreach ($this->fields_relationship as $key => $value)
				{
					$this->tpl[0]->set_var( $value . "_FATHER", $this->main_record[0]->key_fields[$value]->value->getValue());
				}
				reset ($this->fields_relationship);

				foreach ($this->key_fields as $key => $value)
				{
					$this->tpl[0]->set_var("hidden_name", "recordset_ori[" . $i . "][" . $key . "]");
					$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset_ori[$rst_key][$key]->getValue(null, FF_SYSTEM_LOCALE)));
					$this->tpl[0]->parse("SectHidden", true);
					$this->key_fields[$key]->value_ori = $this->recordset_ori[$rst_key][$key];

					if (!isset($this->form_fields[$key]))
					{
						$this->tpl[0]->set_var("hidden_name", "recordset[" . $i . "][" . $key . "]");
						$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset[$rst_key][$key]->getValue(null, FF_SYSTEM_LOCALE)));
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
					$this->getDetailButton("deleterow")->variables[$this->main_record[0]->getIDIF() . "_detailaction"] = $this->getIDIF();
					$this->getDetailButton("deleterow")->variables[$this->getIDIF() . "_delete_row"] = $i;
				}

                if($this->display_grid_location == "Header" || $this->display_grid_location == "Both")
                    $this->processDetailButtons($col, !$this->horizontal_labels, false, $i);

				$col++;
				
				foreach ($this->form_fields as $key => $value)
				{
					$multi_field = false;
					$this->processDetailButtons($col, !$this->horizontal_labels, false, $i);

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
					
					// EVENT HANDLER
					$res = $this->doEvent("on_before_process_field", array(&$this, $rst_val, &$this->form_fields[$key]));

					// if control is a Label, store hidden value
/*					if ($this->form_fields[$key]->control_type == "label")
					{
						$this->tpl[0]->set_var("hidden_name", "recordset[" . $i . "][" . $key . "]");
						$this->tpl[0]->set_var("hidden_value", ffCommon_specialchars($this->recordset[$rst_key][$key]->getValue($this->form_fields[$key]->get_app_type(), $this->form_fields[$key]->get_locale())));
						$this->tpl[0]->parse("SectHidden", true);
					}
*/

					$this->form_fields[$key]->row = $i;
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
				        $class = "";
				        
				        if ($this->form_fields[$key]->container_class)
				            $class = $this->form_fields[$key]->container_class;
				        else
				            $class = $this->column_class;
				            
			 			if($this->form_fields[$key]->get_app_type() == "Text" && $this->form_fields[$key]->extended_type != "String")
					        $class .= " " . strtolower($this->form_fields[$key]->extended_type);
					    else 
					        $class .= " " . strtolower($this->form_fields[$key]->get_app_type());

					    $class .= " ffField";

					    $class = str_replace("[COL]", $col, $class);
					    $class = str_replace("[ID]", $this->form_fields[$key]->id, $class);
					    $class = trim($class);

						if ($this->form_fields[$key]->required) {
							$class = $class . (strlen($class) ? " " : "") . "required";
						}
						/*
						* se inserito in block modify detail le checkbox si vedono male
						*/
						//$class = $class . (strlen($class) ? " " : "") . $this->form_fields[$key]->get_control_class(null, array("framework_css" => false, "control_type" => false));

					    if (strlen($class))
					        $class = "class=\"" . $class . "\"";

					    $this->tpl[0]->set_var("col_class", $class);
					    $this->tpl[0]->set_var("col_properties", $this->form_fields[$key]->getProperties($this->form_fields[$key]->container_properties));
				            
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
						
						if (!$this->horizontal_labels)
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

							//$this->tpl[0]->set_var("column_class", ($this->form_fields[$key]->container_class ? $this->form_fields[$key]->container_class : "textLabel"));
							$this->tpl[0]->set_var("SectDetailButtonLabel", "");
							$this->tpl[0]->parse("SectFormFieldLabel", false);
							$this->tpl[0]->parse("SectLabel", true);
						}

						$this->tpl[0]->set_var("SectDetailButton", "");
						$this->tpl[0]->parse("SectFormField", false);

						$this->tpl[0]->parse("SectCol", true);
						$col++;
					}
				}
				reset($this->form_fields);
				$this->processDetailButtons($col, !$this->horizontal_labels, true, $i);

                if($this->display_grid_location == "Footer" || $this->display_grid_location == "Both")
                    $this->processDetailButtons(0, !$this->horizontal_labels, false, $i);

				if($this->switch_row_class["display"])
                {
                    if(trim($actual_row_class, " ") == $this->switch_row_class["second"] || $actual_row_class == "")
                        $actual_row_class = " " . $this->switch_row_class["first"];
                    else 
                        $actual_row_class = " " . $this->switch_row_class["second"];
                } else 
                    $actual_row_class = "";

				$res = $this->doEvent("on_before_parse_row", array(&$this, $rst_val));

                if ($this->row_class || strlen($actual_row_class))
                {
                    $this->tpl[0]->set_var("row_class", trim($this->row_class . $actual_row_class, " "));
                    $this->tpl[0]->parse("SectRowClass", false);
                }
                else
                {
                    $this->tpl[0]->set_var("SectRowClass", "");
                }
				
				// EVENT HANDLER
				$rc = end($res);
				if (null !== $rc)
				{
					if ($rc === false)
						$this->tpl[0]->parse("SectFormRow", true);
					else if ($rc !== true)
					{
						$this->contain_error = true;
						$this->strError = $rc;
					}
				}
				else
					$this->tpl[0]->parse("SectFormRow", true);
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

	/**
	 * La funzione che carica il template ed imposta le variabili di default
	 */
	protected function tplLoad()
	{
		if ($this->tpl === null)
		{
            $this->tpl[0] = $this->parent[0]->loadTemplate(pathinfo($this->template_file, PATHINFO_FILENAME));
            //$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
			//$this->tpl[0]->load_file($this->template_file, "main");
		}
		
		$this->tpl[0]->set_var("component_id", $this->getIDIF());
		$this->tpl[0]->set_var("main_record_component", $this->main_record[0]->getPrefix());

		$this->tpl[0]->set_var("site_path", $this->site_path);
		$this->tpl[0]->set_var("page_path", $this->page_path);
		$this->tpl[0]->set_var("theme", $this->getTheme());

		$this->tpl[0]->set_var("XHR_CTX_ID", $_SERVER["XHR_CTX_ID"]);
		$this->tpl[0]->set_var("requested_url", ffCommon_specialchars($_SERVER["REQUEST_URI"]));
				
        $component_class["default"] = $this->class;
        if($this->framework_css["component"]["grid"]) {
            if(is_array($this->framework_css["component"]["grid"]))
                $component_class["grid"] = $this->parent[0]->frameworkCSS->get($this->framework_css["component"]["grid"], "col");
            else {
                $component_class["grid"] = $this->parent[0]->frameworkCSS->get("", $this->framework_css["component"]["grid"]);
            }
        }
        $component_class["form"] = $this->parent[0]->frameworkCSS->get("component" . $this->framework_css["component"]["type"], "form");

        $this->tpl[0]->set_var("component_class", implode(" ", array_filter($component_class)));

        if(is_array($this->framework_css["component"]["col"]) && $this->framework_css["component"]["inner_wrap"] === null)
            $this->framework_css["component"]["inner_wrap"] = "row";

        if($this->framework_css["component"]["inner_wrap"]) 
        {
            if(is_array($this->framework_css["component"]["inner_wrap"])) {
                $this->tpl[0]->set_var("inner_wrap_start", '<div class="' . $this->parent[0]->frameworkCSS->get($this->framework_css["component"]["inner_wrap"], "col", "innerWrap") . '">');
            } elseif(is_bool($this->framework_css["component"]["inner_wrap"])) {
                $this->tpl[0]->set_var("inner_wrap_start", '<div class="innerWrap">');
            } else {
                $this->tpl[0]->set_var("inner_wrap_start", '<div class="' . $this->parent[0]->frameworkCSS->get("", $this->framework_css["component"]["inner_wrap"], "innerWrap") . '">');
            }
            $this->tpl[0]->set_var("inner_wrap_end", '</div>');
        }       
           
        if($this->framework_css["component"]["outer_wrap"]) 
        {
            if(is_array($this->framework_css["component"]["outer_wrap"])) {
                $this->tpl[0]->set_var("outer_wrap_start", '<div class="' . $this->parent[0]->frameworkCSS->get($this->framework_css["component"]["outer_wrap"], "col", $this->getIDIF() . "Wrap outerWrap"). '">');
            } elseif(is_bool($this->framework_css["component"]["outer_wrap"])) {
                $this->tpl[0]->set_var("outer_wrap_start", '<div class="' . $this->getIDIF() . 'Wrap outerWrap">');
            } else {
                $this->tpl[0]->set_var("outer_wrap_start", '<div class="' . $this->parent[0]->frameworkCSS->get("", $this->framework_css["component"]["outer_wrap"], $this->getIDIF() . "Wrap outerWrap") . '">');
            }
            $this->tpl[0]->set_var("outer_wrap_end", '</div>');                
        }

		$this->tpl[0]->set_var("SectHiddden", "");

        $this->tpl[0]->set_var("fixed_pre_content", $this->fixed_pre_content);
        $this->tpl[0]->set_var("fixed_post_content", $this->fixed_post_content);
        
        $this->tpl[0]->set_var("fixed_title_content", $this->fixed_title_content);
        $this->tpl[0]->set_var("fixed_heading_content", $this->fixed_heading_content);
		
		$this->tpl[0]->set_var("title", ffCommon_specialchars($this->title));
		
		if ($this->description !== null)
			$this->tpl[0]->set_var("description", $this->description);

		if ($this->tab)
		{
			$this->tpl[0]->set_var("tab_id", $this->main_record[0]->getIDIF());
			$this->tpl[0]->set_var("tab_number", key($this->main_record[0]->tabs[$this->tab]) + 1);
			$this->tpl[0]->parse("SectTabUrl", false);
		}
		else
		{
			$this->tpl[0]->set_var("SectTabUrl", "");
		}

		if ($this->doAjax)
		{
			if (isset($_REQUEST["XHR_CTX_ID"])) {
				$this->tpl[0]->set_var("submit_action", "ff.ajax.ctxDoRequest('" . $_REQUEST["XHR_CTX_ID"] . "', {'action' : '" . $this->main_record[0]->getPrefix() . "detail_addrows', 'component' :'" . $this->getIDIF() . "', 'detailaction' : '" . $this->main_record[0]->getPrefix() . "'})");
			} else {
				$this->main_record[0]->parent[0]->tplAddJs("ff.ajax");

				$this->tpl[0]->set_var("submit_action", "ff.ajax.doRequest({'component' : '" . $this->getIDIF() . "'});");
			}
		}
		else
			$this->tpl[0]->set_var("submit_action", "jQuery(this).closest('form').submit();");

		if ($this->display_new === true) {
            if ($this->tab)
            {
                $this->tpl[0]->set_var("tab_id", $this->main_record[0]->getIDIF());
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
                $this->buttons_options["addrow"]["icon"] = $this->parent[0]->frameworkCSS->get("addrow", "icon-" . $this->buttons_options["addrow"]["aspect"] . "-tag");

            if($this->buttons_options["addrow"]["class"] === null)
                $this->buttons_options["addrow"]["class"] = $this->parent[0]->frameworkCSS->get("addrow", $this->buttons_options["addrow"]["aspect"]);

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
			if($this->rowstoadd_field_default) 
			{
				$this->tpl[0]->set_var("rowstoadd_default_field", $this->rowstoadd_field_default);
				if($this->display_new_location == "Header" || $this->display_new_location == "Both")
					$this->tpl[0]->parse("SectRowToAddHeaderDefault", false);
				if($this->display_new_location == "Footer" || $this->display_new_location == "Both")
					$this->tpl[0]->parse("SectRowToAddFooterDefault", false);
			}

            if($this->display_new_location == "Header" || $this->display_new_location == "Both")
                $this->tpl[0]->parse("SectNewHeader", false);
            if($this->display_new_location == "Footer" || $this->display_new_location == "Both")
                $this->tpl[0]->parse("SectNewFooter", false);

			$this->tpl[0]->parse("SectTitle", false);
		} else {
			$this->tpl[0]->set_var("SectNewHeader", "");
            $this->tpl[0]->set_var("SectNewFooter", "");
			if(strlen($this->title))
				$this->tpl[0]->parse("SectTitle", false);
			else
				$this->tpl[0]->set_var("SectTitle", "");
		}
		
		$this->tpl[0]->set_var("properties", $this->getProperties());

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

		$this->tpl[0]->set_var("fixed_pre_content", $this->fixed_pre_content);
		$this->tpl[0]->set_var("fixed_post_content", $this->fixed_post_content);

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
	function processDetailButtons($col, $display_label = false, $remaining = false, $row)
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
						$this->main_record[0]->parent[0]->tplAddJs("ff.ajax");

						if ($this->buttons_options["deleterow"]["jsaction"])
							$this->detail_buttons[$key]["obj"]->jsaction = str_replace("[ROW]", strval(intval($row)), $this->buttons_options["deleterow"]["jsaction"]);
						else if (isset($_REQUEST["XHR_CTX_ID"]))
							$this->detail_buttons[$key]["obj"]->jsaction = "ff.ajax.ctxDoRequest('" . $_REQUEST["XHR_CTX_ID"] . "', {'action' : '" . $this->main_record[0]->getPrefix() . "detail_delete', 'component' : '" . $this->getIDIF() . "', 'detailaction' : '" . $this->main_record[0]->getPrefix() . "', 'action_param' : " . $row . "});";
						else
							$this->detail_buttons[$key]["obj"]->jsaction = "ff.ajax.doRequest({'component' : '" . $this->getIDIF() . "'});";
					}
					
					//if ($key == "mydelete" && !$display_label) ffErrorHandler::raise("test", E_USER_ERROR, $this, get_defined_vars());

					$this->detail_buttons[$key]["obj"]->jsaction = str_replace("[ROW]", strval(intval($row)), $this->detail_buttons[$key]["obj"]->jsaction);
 					if(!is_array($this->detail_buttons[$key]["obj"]->class)) {
	                    $this->detail_buttons[$key]["obj"]->class = array(
                            "value" => $this->detail_buttons[$key]["obj"]->class
                            , "params" => array("strict" => true)
	                    );
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

		$this->doEvent("on_error", array($this));

		$this->tpl[0]->set_var("SectError", "");
		if (strlen($this->strError))
		{
			$this->tpl[0]->set_var("error_class", $this->parent[0]->frameworkCSS->getClass($this->framework_css["error"]));
			$this->tpl[0]->set_var("strError", $this->strError);
			$this->tpl[0]->parse("SectError", false);
		}

		return $sError;
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
				$tmp->component_action = $this->main_record[0]->getIDIF();
				$this->addContentButton($tmp
										, $this->buttons_options["delete"]["index"]);
			}
		}
	}
	
	public function structProcess($tpl)
	{
		if ($this->id_if !== null)
		{
            $tpl->set_var("prop_name",    "factory_id");
            $tpl->set_var("prop_value",   '"' . $this->id . '"');
            $tpl->parse("SectFFObjProperty",    true);
		}
	}
}
