<?php
class ffRecord_preview extends ffRecord_base
{
	/**
	 * Il template da utilizzare per il rendering
	 * @var String
	 */
	public $template_file		= "ffRecord.html";
	
	/**
	 * L'oggetto ffTemplate usato per il rendering, sotto forma di array
	 * @var Array
	 */
	public $tpl					= null;				// Internal ffTemplate() object
	
	/**
	 * Il prefisso per tutti gli oggetti del template
	 * @var String
	 */
	public $prefix				= null;

	/**
	 * Una collezione dei tabs presenti nella pagina
	 * @var Array
	 */
	public $tabs				= array();

	/**
	 * Per compatibilità con il tema dialog
	 * @var Array
	 */
	public $tabs_data			= null;

	/**
	 * Un testo descrittivo da associare all'oggetto
	 * @var String
	 */
	public $description			= null;

	/**
	 * Se abilitare o meno la widget dei disclosure panels
	 * @var Boolean
	 */
	var $widget_discl_enable	= false;

	/**
	 * Se i disclosures panels devono essere aperti di defaults
	 * @var Boolean
	 */
	var $widget_def_open		= true;
	
	var $widget_activebt_enable	= false;
	
	/**
	 * Variabile ad uso interno che conteggia il numero di campi visualizzati
	 * @var Int
	 */
	var $displayed_fields		= 0;

	var $js_deps = array(
		"ff.ffRecord" => null
	);
	
	/**
	 * Aggiunge un nuovo Tab con un tag specifico
	 * @param String $name 
	 */
	public function addTab($name)
	{
		if (!isset($this->tabs[$name]))
			$this->tabs[$name] = array();
	}

	/**
	 * Imposta il titolo di un tab
	 * @param String $name il tag del tab
	 * @param String $title
	 */
	public function setTabTitle($name, $title)
	{
		$this->tabs[$name]["title"] = $title;
	}

	/**
	 * Funzione di rendering principale
	 */
	public function tplDisplay()
	{
		$this->tplDisplayContents();

		// display selected record controls (delete, insert..)
		$this->tplDisplayControls();

		// display error
		$this->tplDisplayError();
	}

	/**
	 * Carica il template per il rendering, ed esegue le inizializzazioni sul template
	 */
	protected function tplLoad()
	{
		if ($this->tpl === null)
		{
			$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
			$this->tpl[0]->load_file($this->template_file, "main");
		}
		
		if (strlen($this->id))
			$this->prefix = $this->id . "_";
		$this->tpl[0]->set_var("component", $this->prefix);
		$this->tpl[0]->set_var("component_id", $this->id);

		$this->tpl[0]->set_var("site_path", $this->site_path);
		$this->tpl[0]->set_var("page_path", $this->page_path);
		$this->tpl[0]->set_var("theme", $this->getTheme());
		$this->tpl[0]->set_var("class", $this->class);

		$this->tpl[0]->set_var("title", $this->title);

		if ($this->description !== null)
		{
			$this->tpl[0]->set_var("description", $this->description);
			$this->tpl[0]->parse("SectDescription", false);
		} else {
			$this->tpl[0]->set_var("SectDescription", "");	
		}

		$this->tpl[0]->set_var("ret_url", ffCommon_specialchars($this->parent[0]->ret_url));
		
		$this->tplSetFixedVars();
		
		// EVENT HANDLER
		$res = $this->doEvent("on_process_template", array(&$this, $this->tpl[0]));
	}
	
	/**
	 * Imposta nel template le variabili statiche definite dall'utente
	 */
	public function tplSetFixedVars()
	{
		if (is_array($this->fixed_vars) && count($this->fixed_vars))
		{
			foreach ($this->fixed_vars as $key => $value)
			{
				if (get_class($value) == "ffData")
				{
					$this->tpl[0]->set_var($key, $value->getValue());
					if (strlen($value->ori_value))
						$this->tpl[0]->parse("SectSet_" . $key, false);
					else
						$this->tpl[0]->set_var("SectSet_" . $key, "");
				}
				else
				{
					$this->tpl[0]->set_var($key, $value);
					if (strlen($value))
						$this->tpl[0]->parse("SectSet_" . $key, false);
					else
						$this->tpl[0]->set_var("SectSet_" . $key, "");
				}
			}
			reset($this->fixed_vars);
		}
	}

	/**
	 * Elabora il template
	 * @param Boolean $output_result se dev'essere visualizzato immediatamente
	 * @return Mixed true nel caso venga visualizzato, il risultato nel caso opposto
	 */
	public function tplParse($output_result)
	{
		$res = ffRecord::doEvent("on_tplParse", array(&$this, $this->tpl[0]));
		$res = $this->doEvent("on_tpl_parse", array(&$this, $this->tpl[0]));

		$output_buffer = "";

		$this->tpl[0]->set_var("fixed_pre_content", $this->fixed_pre_content);
		$this->tpl[0]->set_var("fixed_post_content", $this->fixed_post_content);

		$this->tplSetFixedVars();

		if (count($this->tabs))
		{
			$this->tpl[0]->set_var("SectForm", $this->parent[0]->widgets["tabs"]->process($this->id, $this->tabs_data, $this->parent[0], $this->id));
		}
		elseif ($this->displayed_fields)
		{
			$this->tpl[0]->parse("SectForm", false);
		}

		$output_buffer = $this->tpl[0]->rpparse("main", false);

		if ($output_result)
		{
			echo $output_buffer;
			return true;
		}
		else
			return $output_buffer;
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
	 * Funzione principale di visualizzazione contenuti
	 */
	protected function tplDisplayContents()
	{
		foreach($this->key_fields as $key => $FormField)
		{
			if (!isset($this->form_fields[$key]) && strlen($this->key_fields[$key]->value->getValue()))
			{
				$this->tpl[0]->set_var("id", "keys[" . $key . "]");
				$this->tpl[0]->set_var("value", ffCommon_specialchars($this->key_fields[$key]->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE)));
				$this->tpl[0]->parse("SectHiddenField", true);

				$this->tpl[0]->set_var("Key_" . $key, $this->key_fields[$key]->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE));
				$this->tpl[0]->set_var("encoded_Key_" . $key, rawurlencode($this->key_fields[$key]->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE)));
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
		}
		reset($this->key_fields);

		if ($this->display_required && $this->display_required_note)
		{
			$this->tpl[0]->set_var("required_symbol", $this->required_symbol);
			$this->tpl[0]->parse("SectRequiredNote", false);
		}
		else
			$this->tpl[0]->set_var("SectRequiredNote", "");

		if (count($this->tabs))
		{
			$this->parent[0]->widgetLoad("tabs", null, $this->parent[0]);
			$this->tabs_data = array();
			$this->tabs_data["contents"]["_main_"]["title"] = ffTemplate::_get_word_by_code(preg_replace('/[^a-zA-Z0-9]/', "", $this->id) . "_main"); //Dati Generici
			$this->tabs_data["contents"]["_main_"]["data"] = null;
		}
		else
		{
			if(strlen($this->title))
				$this->tpl[0]->parse("SectTitle", false);
			else
				$this->tpl[0]->set_var("SectTitle", "");
		}

		$col = 0;
		$this->displayed_fields = 0;
		foreach($this->contents as $key => $content)
		{
			if ($content["group"])
				continue;

			if (is_subclass_of($content["data"], "ffField_base"))
			{
				// "display" hidden original values
				if (!isset($this->key_fields[$content["data"]->id])) // displayed key fields are threated previously
				{
					$multi_field = (is_array($this->form_fields[$content["data"]->id]->multi_fields) && count($this->form_fields[$content["data"]->id]->multi_fields));
					if ($multi_field)
					{
						foreach ($this->form_fields[$content["data"]->id]->multi_fields as $mul_subkey => $mul_subvalue)
						{
							$this->tpl[0]->set_var("id", $content["data"]->id . "[" . $mul_subkey . "]");
							$this->tpl[0]->set_var("value", ffCommon_specialchars($this->form_fields[$content["data"]->id]->value[$mul_subkey]->getValue($mul_subvalue["type"], FF_SYSTEM_LOCALE)));
							$this->tpl[0]->parse("SectHiddenField", true);
                            if($this->record_exist) {
							    $this->tpl[0]->set_var("id", $content["data"]->id . "_ori[" . $mul_subkey . "]");
							    $this->tpl[0]->set_var("value", ffCommon_specialchars($this->form_fields[$content["data"]->id]->value_ori[$mul_subkey]->getValue($mul_subvalue["type"], FF_SYSTEM_LOCALE)));
							    $this->tpl[0]->parse("SectHiddenField", true);
                            }
						}
						reset($this->form_fields[$content["data"]->id]->multi_fields);
					}
					else
					{
                        if($this->record_exist) {
                            $this->tpl[0]->set_var("id", $content["data"]->id . "_ori");
						    $this->tpl[0]->set_var("value", ffCommon_specialchars($this->form_fields[$content["data"]->id]->value_ori->getValue($this->form_fields[$content["data"]->id]->base_type, FF_SYSTEM_LOCALE)));
						    $this->tpl[0]->parse("SectHiddenField", true);
                        }
					}
				}
				
				if (
						(isset($this->form_fields[$content["data"]->id]->group) && strlen($this->form_fields[$content["data"]->id]->group))
						|| (/*!$this->use_fixed_fields && ($this->display_values &&*/ $this->form_fields[$content["data"]->id]->skip_if_empty && !strlen($this->form_fields[$content["data"]->id]->value->ori_value))
						|| $this->form_fields[$content["data"]->id]->manual_display
						|| !$this->form_fields[$content["data"]->id]->display
						|| $this->form_fields[$content["data"]->id]->use_own_location
					)
					continue;

				// EVENT HANDLER
				$res = $this->doEvent("on_process_field", array(&$this, $content["data"]->id));

				$rc = false;

				if (!$multi_field)
				{
					$rc |= $this->tpl[0]->set_var($content["data"]->id . "_value", $this->form_fields[$content["data"]->id]->getValue());
					$rc |= $this->tpl[0]->set_var($content["data"]->id . "_display_value", $this->form_fields[$content["data"]->id]->getDisplayValue());
				}
				if ($this->tpl[0]->isset_var($content["data"]->id . "_field"))
					$rc |= $this->tpl[0]->set_var($content["data"]->id . "_field", $this->form_fields[$content["data"]->id]->process());

				if (!$rc)
				{

					$this->tpl[0]->set_var("container_properties", $this->form_fields[$content["data"]->id]->getProperties($this->form_fields[$content["data"]->id]->container_properties));

					$class = $this->form_fields[$key]->container_class;
					if(!strlen($class))
				    	$class = $this->form_fields[$key]->class;

					if($this->form_fields[$content["data"]->id]->display_label)
					{
                        $label_set = false;
						if ($this->form_fields[$content["data"]->id]->description !== null) {
							$this->tpl[0]->set_var("description", $this->form_fields[$content["data"]->id]->description);
                            $this->tpl[0]->parse("SectDescriptionLabel", false);
                            
                            $label_set = true;
						} 
						else 
						{
							$this->tpl[0]->set_var("description", "");
                            $this->tpl[0]->set_var("SectDescriptionLabel", "");
                        }
                        if(strlen($this->form_fields[$content["data"]->id]->label)) 
                        {
						    if($this->form_fields[$content["data"]->id]->encode_label) 	
							    $this->tpl[0]->set_var("label", ffCommon_specialchars($this->form_fields[$content["data"]->id]->label));
						    else
							    $this->tpl[0]->set_var("label", $this->form_fields[$content["data"]->id]->label);
                                
                            $label_set = true;
                        }							
						
                        if($this->form_fields[$content["data"]->id]->label_properties)
                        {
                            $this->tpl[0]->set_var("label_properties", " " . $this->form_fields[$content["data"]->id]->getProperties($this->form_fields[$content["data"]->id]->label_properties));
                            $label_set = true;
                        }

					    if ($this->display_required && $this->form_fields[$key]->required) {
						    $this->tpl[0]->parse("SectRequiredSymbol", false);
						    $class = $class . (strlen($class) ? " " : "") . "required";
                            
                            $label_set = true;
					    } else {
						    $this->tpl[0]->set_var("SectRequiredSymbol", "");
					    }
				    	if($label_set) {
						    $this->tpl[0]->parse("SectLabel", false);
                        } else {
                            $this->tpl[0]->set_var("SectLabel", "");
                        }
					} 
					else 
					{
						$this->tpl[0]->set_var("SectLabel", "");
					}

					$this->tpl[0]->set_var("container_class", $class);
					
					if (!$this->display_values || strlen($this->form_fields[$content["data"]->id]->control_type))
					{
						if ($this->tpl[0]->isset_var("content"))
							$this->tpl[0]->set_var("content", $this->form_fields[$content["data"]->id]->process());
					}
					elseif (!$multi_field)
						$this->tpl[0]->set_var("content", $this->form_fields[$content["data"]->id]->getDisplayValue());

					$this->displayed_fields++;
				}

				if (strlen($this->form_fields[$content["data"]->id]->value->ori_value))
				{
					$this->tpl[0]->parse("SectSet_" . $content["data"]->id, false);
					$this->tpl[0]->set_var("SectNotSet_" . $content["data"]->id, "");
				}
				else
				{
					$this->tpl[0]->set_var("SectSet_" . $content["data"]->id, "");
					$this->tpl[0]->parse("SectNotSet_" . $content["data"]->id, false);
				}

				if ($this->form_fields[$content["data"]->id]->extended_type == "Selection")
				{
					$this->tpl[0]->set_regexp_var("/SectSet_" . $content["data"]->id . "_.+/", "");
					$this->tpl[0]->parse("SectSet_" . $content["data"]->id . "_" . $this->form_fields[$content["data"]->id]->value->getValue($this->form_fields[$content["data"]->id]->base_type, FF_SYSTEM_LOCALE), false);
				}

				$this->tpl[0]->parse("Sect_" . $content["data"]->id, false);
			}
			elseif (get_class($content["data"]) == "ffTemplate" || is_subclass_of($content["data"], "ffDetails_base") || is_subclass_of($content["data"], "ffGrid_base") || is_subclass_of($content["data"], "ffRecord_base"))
			{
				$this->tpl[0]->set_var("SectLabel", "");
				if (get_class($content["data"]) == "ffTemplate")
					$this->tpl[0]->set_var("content", $content["data"]->rpparse("main", false));
				else
					$this->tpl[0]->set_var("content", "{{" . $content["data"]->id . "}}");
				
				$this->displayed_fields++;
				
				$this->tpl[0]->set_var("container_class", "");
			}
			$this->tplSetFixedVars();
			$this->tpl[0]->parse("SectFormCol", false);
			$this->tpl[0]->parse("SectFormRow", true);
		}
		reset($this->contents);

		if (count($this->tabs) && $this->displayed_fields)
		{
			$this->fixed_pre_content .= $this->tpl[0]->rpparse("SectForm", false);
			//$this->tabs_data["contents"]["_main_"]["data"] = $this->tpl[0]->rpparse("SectForm", false);
		}
		
		$groups_tabs = array(); // conteggio gruppi per tab
		foreach ($this->contents as $key => $content)
		{
			if (!$content["group"])
				continue;

			if ($this->groups[$key]["tab"])
				$groups_tabs[$this->groups[$key]["tab"]]++;
		}
		reset($this->contents);


		foreach ($this->contents as $key => $content)
		{ 
			if (!$content["group"])
				continue;

			$suffix = "_" . $key;
			
			$this->tpl[0]->set_var("group_class", $key . (strlen($this->groups[$key]["class"]) ? " " . $this->groups[$key]["class"] : ""));
			$this->tpl[0]->set_var("GroupTitle", ($this->groups[$key]["title"] ? $this->groups[$key]["title"] : $key));
			if ($this->groups[$key]["hide_title"])
				$this->tpl[0]->set_var("SectGroupTitle", "");
			else
				$this->tpl[0]->parse("SectGroupTitle", false);
				
			if(isset($this->groups[$key]["description"]) && strlen($this->groups[$key]["description"])) {
				$this->tpl[0]->set_var("GroupDescription", $this->groups[$key]["description"]);
				$this->tpl[0]->parse("SectGroupDescription", false);
			} else {
				$this->tpl[0]->set_var("SectGroupDescription", "");
			}
			$cols = $this->groups[$key]["cols"];
			if ($cols < 1)
				$cols = 1;
			$this->tpl[0]->set_var("maxspan", $cols);

			$col = 0;
			$i = 0;
			$this->tpl[0]->set_var("SectGroupRow", "");
			$this->tpl[0]->set_var("SectGroupCol", "");
			$this->tpl[0]->set_var("SectGroupRow" . $suffix, "");
			$this->tpl[0]->set_var("SectGroupCol" . $suffix, "");

			if (is_array($this->groups[$key]["contents"]) && count($this->groups[$key]["contents"]))
			{
				foreach ($this->groups[$key]["contents"] as $subkey => $subvalue)
				{
					if (get_class($subvalue["data"]) == "ffTemplate" || is_subclass_of($subvalue["data"], "ffDetails_base") || is_subclass_of($subvalue["data"], "ffGrid_base") || is_subclass_of($subvalue["data"], "ffRecord_base"))
					{
						$i++;
						$oldspan = $span;
						$span = $cols;

						if ($i > 1)
						{
							if ($col + $span > $cols)
							{
								$this->tpl[0]->set_var("colspan", $cols - ($col - $oldspan));

								$rc = false;
								$rc = $this->tpl[0]->parse("SectGroupCol" . $suffix, true);
								if (!$rc) $this->tpl[0]->parse("SectGroupCol", true);
								$rc = $this->tpl[0]->parse("SectGroupRow" . $suffix, true);
								if (!$rc) $this->tpl[0]->parse("SectGroupRow", true);
								$rc = $this->tpl[0]->set_var("SectGroupCol" . $suffix, "");
								if (!$rc) $this->tpl[0]->set_var("SectGroupCol", "");

								$col = 0;
							}
							else
							{
								$rc = $this->tpl[0]->parse("SectGroupCol" . $suffix, true);
								if (!$rc) $this->tpl[0]->parse("SectGroupCol", true);
							}
						}

						$this->tpl[0]->set_var("colspan", $span);
						$col += $span;

						$this->tpl[0]->set_var("SectGroupLabel", "");
						if (get_class($subvalue["data"]) == "ffTemplate")
							$this->tpl[0]->set_var("content", $subvalue["data"]->rpparse("main", false));
						else
							$this->tpl[0]->set_var("content", "{{" . $subvalue["data"]->id . "}}");
							
						$this->tpl[0]->set_var("container_class", "");
					}
					elseif (is_subclass_of($subvalue["data"], "ffField_base"))
					{
						if (
								(/*!$this->use_fixed_fields && $this->display_values && */$this->form_fields[$subkey]->skip_if_empty && !strlen($this->form_fields[$subkey]->value->ori_value))
							)
							continue;

						if (!isset($this->key_fields[$subvalue["data"]->id])) // displayed key fields are threated previously
						{
							$multi_field = (is_array($this->form_fields[$subvalue["data"]->id]->multi_fields) && count($this->form_fields[$subvalue["data"]->id]->multi_fields));
							if ($multi_field)
							{
								foreach ($this->form_fields[$subvalue["data"]->id]->multi_fields as $mul_subkey => $mul_subvalue)
								{
									$this->tpl[0]->set_var("id", $subvalue["data"]->id . "[" . $mul_subkey . "]");
									$this->tpl[0]->set_var("value", ffCommon_specialchars($this->form_fields[$subvalue["data"]->id]->value[$mul_subkey]->getValue($mul_subvalue["type"], FF_SYSTEM_LOCALE)));
									$this->tpl[0]->parse("SectHiddenField", true);

                                    if($this->record_exist) {
									    $this->tpl[0]->set_var("id", $subvalue["data"]->id . "_ori[" . $mul_subkey . "]");
									    $this->tpl[0]->set_var("value", ffCommon_specialchars($this->form_fields[$subvalue["data"]->id]->value_ori[$mul_subkey]->getValue($mul_subvalue["type"], FF_SYSTEM_LOCALE)));
									    $this->tpl[0]->parse("SectHiddenField", true);
                                    }
								}
								reset($this->form_fields[$subvalue["data"]->id]->multi_fields);
							}
							else
							{
                                if($this->record_exist) {
								    $this->tpl[0]->set_var("id", $subvalue["data"]->id . "_ori");
								    $this->tpl[0]->set_var("value", ffCommon_specialchars($this->form_fields[$subvalue["data"]->id]->value_ori->getValue($this->form_fields[$subvalue["data"]->id]->base_type, FF_SYSTEM_LOCALE)));
								    $this->tpl[0]->parse("SectHiddenField", true);
                                }
							}
						}
							
						$i++;

						$oldspan = $span;
						$span = $this->groups[$key]["contents"][$subkey]->span;
						if ($span > $cols)
							$span = $cols;
						else if ($span < 1)
							$span = 1;

						// completo la colonna precedente, se esiste
						if ($i > 1)
						{
							// se abbiamo raggiunto il limite, adatto la precedente e passo alla riga successiva
							if ($col + $span > $cols)
							{
								$this->tpl[0]->set_var("colspan", $cols - ($col - $oldspan));

								$rc = $this->tpl[0]->parse("SectGroupCol" . $suffix, true);
								if (!$rc) $this->tpl[0]->parse("SectGroupCol", true);
								$rc = $this->tpl[0]->parse("SectGroupRow" . $suffix, true);
								if (!$rc) $this->tpl[0]->parse("SectGroupRow", true);
								$rc = $this->tpl[0]->set_var("SectGroupCol" . $suffix, "");
								if (!$rc) $this->tpl[0]->set_var("SectGroupCol", "");

								$col = 0;
							}
							else
							{
								$rc = $this->tpl[0]->parse("SectGroupCol" . $suffix, true);
								if (!$rc) $this->tpl[0]->parse("SectGroupCol", true);
							}
						}

						$this->tpl[0]->set_var("colspan", $span);
						$col += $span;

						// EVENT HANDLER
						$res = $this->doEvent("on_process_field", array(&$this, $subkey));

						// container
						$this->tpl[0]->set_var("container_properties", $this->form_fields[$subkey]->getProperties($this->form_fields[$subkey]->container_properties));

						$class = $this->form_fields[$subkey]->container_class;
						if(!strlen($class))
				    		$class = $this->form_fields[$subkey]->class;

						// LABEL
						if($this->form_fields[$subkey]->display_label)
						{
                            $label_set = false;
							if ($this->form_fields[$subkey]->description !== null) 
							{       
								$this->tpl[0]->set_var("description", $this->form_fields[$subkey]->description);
								$this->tpl[0]->parse("SectDescriptionLabel", false);
                                
                                $label_set = true;
							}	
							else
							{
								$this->tpl[0]->set_var("description", "");
								$this->tpl[0]->set_var("SectDescriptionLabel", "");
							}
                            
                            if(strlen($this->form_fields[$subkey]->label)) 
                            {
							    if($this->form_fields[$subkey]->encode_label) 	
								    $this->tpl[0]->set_var("label", ffCommon_specialchars($this->form_fields[$subkey]->label));
							    else
								    $this->tpl[0]->set_var("label", $this->form_fields[$subkey]->label);
                                
                                $label_set = true; 
                            }

                            if($this->form_fields[$subkey]->label_properties)
                            {
							    $this->tpl[0]->set_var("label_properties", " " . $this->form_fields[$subkey]->getProperties($this->form_fields[$subkey]->label_properties));
                                $label_set = true;
                            }
                            
						    if ($this->display_required && $this->form_fields[$subkey]->required) {
							    $this->tpl[0]->parse("SectRequiredSymbol", false);
							    $class = $class . (strlen($class) ? " " : "") . "required";
                                
                                $label_set = true;
						    } else {
							    $this->tpl[0]->set_var("SectRequiredSymbol", "");
						    }
                            
                            if($label_set)
							    $this->tpl[0]->parse("SectGroupLabel", false);
                            else
                                $this->tpl[0]->set_var("SectGroupLabel", "");
						} 
						else 
						{
							$this->tpl[0]->set_var("SectGroupLabel", "");
						}
						
						$this->tpl[0]->set_var("container_class", $class);
						// CONTROL/VALUE SECTION
						$rc = false;
						$multi_field = (is_array($this->form_fields[$key]->multi_fields) && count($this->form_fields[$key]->multi_fields));

						if ($multi_field)
						{
							$rc |= $this->tpl[0]->set_var($subkey . "_value", $this->form_fields[$subkey]->getValue());
							$rc |= $this->tpl[0]->set_var($subkey . "_display_value", $this->form_fields[$subkey]->getDisplayValue());
						}
						if ($this->tpl[0]->isset_var($subkey . "_field"))
							$rc |= $this->tpl[0]->set_var($subkey . "_field", $this->form_fields[$subkey]->process());

						if ($multi_field)
						{
							$rc |= $this->tpl[0]->set_var("FIELD_value", $this->form_fields[$subkey]->getValue());
							$rc |= $this->tpl[0]->set_var("FIELD_display_value", $this->form_fields[$subkey]->getDisplayValue());
						}
						if ($this->tpl[0]->isset_var("FIELD_field"))
							$rc |= $this->tpl[0]->set_var("FIELD_field", $this->form_fields[$subkey]->process());

						if (!$rc)
						{
							if (!$this->display_values || strlen($this->form_fields[$subkey]->control_type))
							{
								if ($this->tpl[0]->isset_var("content"))
									$this->tpl[0]->set_var("content", $this->form_fields[$subkey]->process());
							}
							else
								$this->tpl[0]->set_var("content", $this->form_fields[$subkey]->getDisplayValue());
						}

						$fieldset = false;

						switch ($this->form_fields[$subkey]->extended_type)
						{
							case "Selection":
								switch ($this->form_fields[$subkey]->get_control_type())
								{
									case "radio":
										$fieldset = true;
										break;
								}
								break;
						}

						if ($fieldset)
						{
							$this->tpl[0]->set_var("SectField", "");
							$this->tpl[0]->parse("SectFieldSet", false);
						}
						else
						{
							$this->tpl[0]->parse("SectField", false);
							$this->tpl[0]->set_var("SectFieldSet", "");
						}

						// Manage Fixed Sections
						if(!is_array($this->form_fields[$subkey]->value->ori_value))
						{
							if (strlen($this->form_fields[$subkey]->value->ori_value))
							{
								$this->tpl[0]->parse("SectSet_$subkey", false);
								$this->tpl[0]->set_var("SectNotSet_$subkey", "");
							}
							else
							{
								$this->tpl[0]->set_var("SectSet_$subkey", "");
								$this->tpl[0]->parse("SectNotSet_$subkey", false);
							}
						}
						if ($this->form_fields[$subkey]->extended_type == "Selection")
						{
							$this->tpl[0]->set_regexp_var("/SectSet_" . $subkey . "_.+/", "");
							$this->tpl[0]->parse("SectSet_" . $subkey . "_" . $this->form_fields[$subkey]->value->getValue($this->form_fields[$subkey]->base_type, FF_SYSTEM_LOCALE), false);
						}

						$this->tpl[0]->parse("Sect_$subkey", false);

						// "display" hidden original values
/*						if (!isset($this->key_fields[$subkey])) // displayed key fields are threated previously
						{
							if (is_array($this->form_fields[$subkey]->multi_fields) && count($this->form_fields[$subkey]->multi_fields))
							{
								foreach ($this->form_fields[$subkey]->multi_fields as $mul_subkey => $mul_subvalue)
								{
									$this->tpl[0]->set_var("id", $subkey . "[" . $mul_subkey . "]");
									$this->tpl[0]->set_var("value", ffCommon_specialchars($this->form_fields[$subkey]->value[$mul_subkey]->getValue($mul_subvalue["type"], FF_SYSTEM_LOCALE)));
									$this->tpl[0]->parse("SectHiddenField", true);

                                    if($this->record_exist) {
									    $this->tpl[0]->set_var("id", $subkey . "_ori[" . $mul_subkey . "]");
									    $this->tpl[0]->set_var("value", ffCommon_specialchars($this->form_fields[$subkey]->value_ori[$mul_subkey]->getValue($mul_subvalue["type"], FF_SYSTEM_LOCALE)));
									    $this->tpl[0]->parse("SectHiddenField", true);
                                    }
								}
								reset($this->form_fields[$subkey]->multi_fields);
							}
							else
							{
                                if($this->record_exist) {
								    $this->tpl[0]->set_var("id", $subkey . "_ori");
								    $this->tpl[0]->set_var("value", ffCommon_specialchars($this->form_fields[$subkey]->value_ori->getValue($this->form_fields[$subkey]->base_type, FF_SYSTEM_LOCALE)));
								    $this->tpl[0]->parse("SectHiddenField", true);
                                }
							}
						}
*/					}
				}
				reset($this->groups[$key]["contents"]);

				if ($i > 0)
				{
					$this->tpl[0]->set_var("colspan", $cols - ($col - $span));

					$rc = $this->tpl[0]->parse("SectGroupCol" . $suffix, true);
					if (!$rc) $this->tpl[0]->parse("SectGroupCol", true);
					$rc = $this->tpl[0]->parse("SectGroupRow" . $suffix, true);
					if (!$rc) $this->tpl[0]->parse("SectGroupRow", true);

					$res = $this->doEvent("on_before_parse_group", array(&$this, $key));

					$rc = $this->tpl[0]->parse("SectGroup" . $suffix, true);
					if (!$rc)
					{
						if (count($this->tabs))
						{
							if ($this->groups[$key]["tab"])
							{
								$tab = $this->groups[$key]["tab"];
								$this->tabs_data["contents"][$tab]["title"] = $this->tabs[$tab]["title"];
								if ($this->groups[$key]["hide_title"] === true || ($this->groups[$key]["hide_title"] === null && $groups_tabs[$tab] == 1))
									$this->tpl[0]->set_var("SectGroupTitle", "");
								else
									$this->tpl[0]->parse("SectGroupTitle", false);
								$this->tabs_data["contents"][$tab]["data"] .= $this->tpl[0]->rpparse("SectGroup", false);
							}
							else
							{
								$this->tabs_data["contents"]["_main_"]["data"] .= $this->tpl[0]->rpparse("SectGroup", true);
							}
							$this->tpl[0]->set_var("SectGroup", "");
						}
						else
							$this->tpl[0]->parse("SectGroup", true);
					}
				}
			}
		}
		reset($this->contents);

		if (isset($this->tabs_data["contents"]["_main_"]) && $this->tabs_data["contents"]["_main_"]["data"] === null)
		{
			unset($this->tabs_data["contents"]["_main_"]);
		}
	}

	/**
	 * Visualizza i pulsanti del record
	 */
	protected function tplDisplayControls()
	{
		if ($this->hide_all_controls || !$this->tplSection["buttons"]["display"])
		{
			$this->tpl[0]->set_var("SectControls", "");
			return;
		}

		// EVENT HANDLER
		$res = $this->doEvent("on_before_process_buttons", array(&$this));

        $tmp_action_buttons = $this->action_buttons;
        $rc = usort($tmp_action_buttons, "ffCommon_IndexOrder");
        if (!$rc)
            ffErrorHandler::raise("UNABLE TO ORDER ACTION BUTTONS", E_USER_ERROR, $this, get_defined_vars());

		// PROCESS ALL BUTTONS
		$buffer = "";
		foreach ($tmp_action_buttons as $key => $value)
		{
			if ($this->tpl[0]->isset_var($tmp_action_buttons[$key]["obj"]->id))
				$this->tpl[0]->set_var($tmp_action_buttons[$key]["obj"]->id, $tmp_action_buttons[$key]["obj"]->process());
			else
				$buffer = $tmp_action_buttons[$key]["obj"]->process() . $buffer;
		}
		reset($tmp_action_buttons);
		
		$this->tpl[0]->set_var("ActionButtons", $buffer);
		$this->tpl[0]->parse("SectControls", "");
	}

	/**
	 * Rielabora la sezione di visualizzazione dell'errore
	 * dev'essere richiamata esplicitamente ogniqualvolta viene alterato l'errore
	 * @param String $sError la descrizione dell'errore
	 */
	public function tplDisplayError($sError = null)
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
	}

    /**
	 * Inizializza i controlli di default del record
	 * Al momento consistono nei pulsanti insert, update, delete e cancel
	 */
	function initControls()
    {
		if ($this->hide_all_controls)
			return;

		// PREPARE DEFAULT BUTTONS
		if ($this->buttons_options["cancel"]["display"])
		{
			if ($this->buttons_options["cancel"]["obj"] === null)
			{
				$tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->getTheme());
				$tmp->id 			= "ActionButtonCancel";
				$tmp->label 		= $this->buttons_options["cancel"]["label"];
				if ($this->buttons_options["cancel"]["jsaction"])
				{
					$tmp->aspect		= "button";
					$tmp->action_type 	= "submit";
					$tmp->frmAction		= "cancel";
					$tmp->jsaction		= $this->buttons_options["cancel"]["jsaction"];
				}
				else
				{
					$tmp->aspect 		= "link";
					$tmp->action_type 	= "gotourl";
					$tmp->url			= "[RET_URL]";
				}
				if (isset($this->buttons_options["cancel"]["class"]))
					$tmp->class			= $this->buttons_options["cancel"]["class"];
				$this->buttons_options["cancel"]["obj"] =& $tmp;
			}
		}

		parent::initControls();

		if (isset($this->action_buttons["ActionButtonCancel"]))
		{
			$tmp = $this->getActionButton("ActionButtonCancel");
			$tmp->class = $tmp->get_class() . " cancel";
		}
		
		if (isset($this->action_buttons["ActionButtonDelete"]))
		{
			$tmp = $this->getActionButton("ActionButtonDelete");
			$tmp->class = $tmp->get_class() . " delete";
		}

		if (isset($this->action_buttons["ActionButtonUpdate"]))
		{
			$tmp = $this->getActionButton("ActionButtonUpdate");
			$tmp->class = $tmp->get_class() . " update";
		}

		if (isset($this->action_buttons["ActionButtonInsert"]))
		{
			$tmp = $this->getActionButton("ActionButtonInsert");
			$tmp->class = $tmp->get_class() . " insert";
		}
	}

	/**
	 * Esegue un redirect in base alle caratteristiche del record e della richiesta, se XHR o meno
	 * @param String $url l'indirizzo di destinazione
	 * @return String
	 */
	/*function redirect($url)
	{
		if ($this->parent[0]->isXHR())
		{
			$this->json_result["url"] = $url;
			$this->json_result["close"] = false;
			die(ffCommon_jsonenc($this->json_result, true));
		}
		else
		{
			return parent::redirect($url);
		}
	}*/
	public function structProcess($tpl)
	{
	}
}
