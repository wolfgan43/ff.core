<?php
class ffRecord_html extends ffRecord_base
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
	 * Per compatibilitÃ  con il tema dialog
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

	var $disable_mod_notifier_on_error = false;
	
	var $use_form = false;
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

		if(strlen($this->title)) {
			$this->tpl[0]->set_var("title", $this->title);
			$this->tpl[0]->parse("SectTitle", false);
		} else
			$this->tpl[0]->set_var("SectTitle", "");

		if ($this->description !== null) {
			$this->tpl[0]->set_var("info_class", cm_getClassByDef($this->framework_css["info"]));
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
				if (is_object($value) && get_class($value) == "ffData")
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
		
		if($this->use_form)
			$this->tpl[0]->set_var("record_tag", "form");
		else
			$this->tpl[0]->set_var("record_tag", "div");

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
		
		if (count($this->tabs))
		{
			$this->tpl[0]->set_var("form_tabs", $this->parent[0]->widgets["tabs"]->process($this->id, $this->tabs_data, $this->parent[0], $this->id));
		}
		elseif ($this->displayed_fields)
		{
			$this->tpl[0]->parse("SectGroup", false);
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
		$this->parent[0]->tplAddJs("ff.ffRecord", "ffRecord.js", FF_THEME_DIR . "/library/ff");

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

		if (is_array($this->hidden_fields) && count($this->hidden_fields))
		{
			foreach($this->hidden_fields as $key => $value)
			{
				$this->tpl[0]->set_var("id", $key);
				$this->tpl[0]->set_var("value", ffCommon_specialchars($value->getValue(null, FF_SYSTEM_LOCALE)));
				$this->tpl[0]->parse("SectHiddenField", true);
			}
			reset($this->hidden_fields);
		}

		$this->tpl[0]->set_var("maxspan", ($this->cols * 2));

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
			//$this->tabs_data["contents"]["_main_"]["title"] = ffTemplate::_get_word_by_code(preg_replace('/[^a-zA-Z0-9]/', "", $this->id) . "_main"); //Dati Generici
			//$this->tabs_data["contents"]["_main_"]["data"] = null;
		}
		else
		{
			if(strlen($this->title))
				$this->tpl[0]->parse("SectTitle", false);
			else
				$this->tpl[0]->set_var("SectTitle", "");
		}

		$this->displayed_fields = 0;

		$groups_tabs = array(); // conteggio gruppi per tab
		if(!is_array($this->groups))
			$this->groups = array();

		$this->groups = array_merge(array("_main_" => array(
											"class" => "default"
											, "title" => ""
											, "tab" => false
											, "contents" => array()
											, "data" => null
										)
									), $this->groups);

		foreach ($this->contents as $key => $content)
		{
			if (!$content["group"]) {
				$this->groups["_main_"]["contents"][$key] = $content;
				continue;
			}	
			if ($this->groups[$key]["tab"])
				$groups_tabs[$this->groups[$key]["tab"]]++;
		}
		reset($this->contents);
		
		if(!count($this->groups["_main_"]["contents"]))
			unset($this->groups["_main_"]);

		if (is_array($this->groups) && count($this->groups))
		{
			foreach($this->groups AS $group_key => $group_value) 
			{
				$i = 0;
				$suffix = "_" . $group_key;

				$this->tpl[0]->set_var("SectGroupRow", "");
				$this->tpl[0]->set_var("SectGroupRow" . $suffix, "");
			
				$i = $this->tplDisplayFields($this->groups[$group_key]["contents"], $group_value["primary_field"]);
				if ($i > 0)
				{
					//$rc = $this->tpl[0]->parse("SectGroupRow" . $suffix, true);
					//if (!$rc) $this->tpl[0]->parse("SectGroupRow", true);

					$res = $this->doEvent("on_before_parse_group", array(&$this, $group_key));

					//$rc = $this->tpl[0]->parse("SectGroup" . $suffix, true);
					if (!$rc)
					{
				        $this->tpl[0]->set_var("group_pre_content", $this->groups[$group_key]["fixed_pre_content"]);
				        $this->tpl[0]->set_var("group_post_content", $this->groups[$group_key]["fixed_post_content"]);

						if($group_key == "_main_") 
						{
							$this->tpl[0]->set_var("SectGroupStart", "");
							$this->tpl[0]->set_var("SectGroupEnd", "");
						} 
						else 
						{
							$group_class = array();
							$group_class["default"] = (strlen($this->groups[$group_key]["class"]) ? $this->groups[$group_key]["class"] : $group_key);
														
							if($this->groups[$group_key]["hide_title"] 
								|| (!strlen($this->groups[$group_key]["title"]) 
									&& !strlen($this->groups[$group_key]["tab_dialog"])
									&& !strlen($this->groups[$group_key]["primary_field"])
								)
							) {
								$this->tpl[0]->set_var("SectGroupTitle", "");
							} else {
								$arrTitleProperties = array();
								$str_title_properties = "";
								$this->tpl[0]->set_var("title_properties", "");

								if($group_value["primary_field"]) {
									$arrTitleProperties["class"]["primary_field"] = "ffCheckDep";
									$arrTitleProperties["onclick"] = "javascript:ff.ffRecord.displayFieldSetElem(this, '" . $this->id . "_" . $group_value["primary_field"] . "');";
								}
								$group_class["tab_dialog"] = "dlg-tab dlg-" . ffCommon_url_rewrite($group_key);
								if($this->groups[$group_key]["tab_dialog"] === true && $group_value["primary_field"]) {
									$group_title = '<span class="dialogSubTitleTab ' . ($this->groups[$group_key]["tab_dialog_selected"] ? "selected " : "") . 'dep-' . ffCommon_url_rewrite($group_key) . '">' . ($this->groups[$group_key]["title"] ? $this->groups[$group_key]["title"] : ffTemplate::_get_word_by_code($group_key)) . '</span>';
										
									$this->tpl[0]->set_var("GroupTitle", $group_title . $this->tpl[0]->getBlockContent("GroupTitle", false));
								} else {
									if($this->groups[$group_key]["tab_dialog"] === true)
										$arrTitleProperties["class"]["tab_dialog"] = "dialogSubTitleTab " . ($this->groups[$group_key]["tab_dialog_selected"] ? "selected " : "") . "dep-" . ffCommon_url_rewrite($group_key);
									elseif(strlen($this->groups[$group_key]["tab_dialog"])) 
										$group_class["tab_dialog"] = "dlg-tab dlg-" . ffCommon_url_rewrite($this->groups[$group_key]["tab_dialog"]);

									if(!$group_value["primary_field"]) {
										if($this->groups[$group_key]["title"])
											$this->tpl[0]->set_var("GroupTitle", $this->groups[$group_key]["title"]);
										else 
											$this->tpl[0]->set_var("GroupTitle", "");
									}
								}										
								if(is_array($arrTitleProperties) && count($arrTitleProperties)) {
									foreach($arrTitleProperties AS $arrTitleProperties_key => $arrTitleProperties_value) {
										
										$str_title_properties .= " " . $arrTitleProperties_key . '="' . (is_array($arrTitleProperties_value) ? implode(" ", array_filter($arrTitleProperties_value)) : $arrTitleProperties_value) . '"';
									}
									$this->tpl[0]->set_var("title_properties", $str_title_properties);
								}
								if($this->tpl[0]->getBlockContent("GroupTitle", false))
									$this->tpl[0]->parse("SectGroupTitle", false);
								else
									$this->tpl[0]->set_var("SectGroupTitle", "");
							}

							$this->tpl[0]->set_var("group_class", implode(" ", array_filter($group_class)));
							
							if(isset($this->groups[$group_key]["description"]) && strlen($this->groups[$group_key]["description"])) {
								$this->tpl[0]->set_var("GroupDescription", $this->groups[$group_key]["description"]);
								$this->tpl[0]->parse("SectGroupDescription", false);
							} else {
								$this->tpl[0]->set_var("SectGroupDescription", "");
							}
						
							$this->tpl[0]->parse("SectGroupStart", false);
							$this->tpl[0]->parse("SectGroupEnd", false);
						}
						
						if (count($this->tabs))
						{
							if ($this->groups[$group_key]["tab"])
							{
								$tab = $this->groups[$group_key]["tab"];
								$this->tabs_data["contents"][$tab]["title"] = $this->tabs[$tab]["title"];
								if ($this->groups[$group_key]["hide_title"] !== false && $groups_tabs[$tab] == 1)
									$this->tpl[0]->set_var("SectGroupTitle", "");
								else
									$this->tpl[0]->parse("SectGroupTitle", false);
								
								$this->tabs_data["contents"][$tab]["data"] .= $this->tpl[0]->rpparse("SectGroup", false);
							}
							else
								$this->groups["_main_"]["data"] .= $this->tpl[0]->rpparse("SectGroup", false);

							$this->tpl[0]->set_var("SectGroup", "");
						}
						else
							$this->tpl[0]->parse("SectGroup", true);
					}
				}				
			}
		}

		if (isset($this->groups["_main_"]) && $this->groups["_main_"]["data"] !== null)
			$this->tpl[0]->set_var("form_default", $this->groups["_main_"]["data"]);
	}
	
	/**
	 * Visualizza il campo del record
	 */
	protected function tplDisplayFields($contents, $primary_field = null) 
	{
		$vars_to_reset = array();
		$i = 0;
		$wrap_count = 0;
		$count_contents = 0;

        if(is_array($contents) && count($contents)) 
        {
		    foreach ($contents as $subkey => $subvalue)
		    {
		    	$count_contents++;
			    if (is_string($subvalue["data"]) || get_class($subvalue["data"]) == "ffTemplate" || is_subclass_of($subvalue["data"], "ffDetails_base") || is_subclass_of($subvalue["data"], "ffGrid_base") || is_subclass_of($subvalue["data"], "ffRecord_base"))
			    {
				    $i++;

				    $this->tpl[0]->set_var("SectGroupLabel", "");
				    $this->tpl[0]->set_var("content_pre_label", "");

				    if (is_string($subvalue["data"]))
					    $this->tpl[0]->set_var("content", $subvalue["data"]);
				    else if (get_class($subvalue["data"]) == "ffTemplate")
					    $this->tpl[0]->set_var("content", $subvalue["data"]->rpparse("main", false));
				    else
					    $this->tpl[0]->set_var("content", "{{" . $subvalue["data"]->id . "}}");
				    
				    $container_class = array("form-wrap");
				    if($this->framework_css["record"]["row"] && (!$wrap_count || $wrap_count >= 12)) //con le grid e troppo piccola la visualizzazione
				    	$container_class[] = cm_getClassByFrameworkCss("row", "form");

				    $this->tpl[0]->set_var("container_class", implode(" ", array_filter($container_class)));
				    //$this->displayed_fields++;
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
				    
				    $container_class = array();
				    $i++;
					
				    // EVENT HANDLER
				    $res = $this->doEvent("on_process_field", array(&$this, $subkey));

				    // container
				    foreach ($vars_to_reset as $field_key => $field_values)
				    {
					    $this->tpl[0]->set_var($field_key, "");
				    }
				    reset($vars_to_reset);
				    
				    $this->tpl[0]->set_var("container_properties", $this->form_fields[$subkey]->getProperties($this->form_fields[$subkey]->container_properties));

				    if (is_array($this->form_fields[$subkey]->container_vars) && count($this->form_fields[$subkey]->container_vars))
				    {
					    foreach ($this->form_fields[$subkey]->container_vars as $field_key => $field_value)
					    {
						    $this->tpl[0]->set_var($field_key, $field_value);
						    $vars_to_reset[$field_key] = true;
					    }
					    reset($this->form_fields[$subkey]->container_vars);
				    }
			    
				    
				    if(strlen($this->form_fields[$subkey]->container_class))
					    $container_class[] = $this->form_fields[$subkey]->container_class;
					else
						$container_class[] = $this->form_fields[$subkey]->class;
						
				    if($this->form_fields[$subkey]->placeholder === true) {
					    $this->form_fields[$subkey]->placeholder = ffCommon_specialchars($this->form_fields[$subkey]->label);
				    }				    	

				    $control_var = "";
					$control_prefix = "";
					$control_postfix = "";
					$is_combine_field = false;
				    $this->tpl[0]->set_var("content_pre_label", "");
				    $this->tpl[0]->set_var("content_in_label", "");
				    $this->tpl[0]->set_var("content", "");
				    $this->tpl[0]->set_var("field_container_start", "");
				    $this->tpl[0]->set_var("field_container_end", "");

				    // LABEL 
				    $label_set = false;
				    if($this->form_fields[$subkey]->display_label)
				    {
					    $this->tpl[0]->set_var("label_prefix", "");
					    $this->tpl[0]->set_var("label_postfix", "");
				    	$required_symbol = "";

						if(($this->form_fields[$subkey]->get_control_type() == "checkbox" || $this->form_fields[$subkey]->get_control_type() == "radio") && $this->form_fields[$subkey]->widget == "") {
							$control_var = (0
												? cm_getClassByFrameworkCss("control-check-position", "form")
												: "_pre_label"
											);
							$is_combine_field = true;
						}

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

	                    if($this->form_fields[$subkey]->label_properties)
	                    {
						    $this->tpl[0]->set_var("label_properties", " " . $this->form_fields[$subkey]->getProperties($this->form_fields[$subkey]->label_properties));
	                        $label_set = true;
	                    }

					    if ($this->display_required && $this->form_fields[$subkey]->required) {
							$container_class["require"] = "required";
							$required_symbol = "*";
					    }

 						if(strlen($this->form_fields[$subkey]->label)) 
	                    {
						    if($primary_field == $subkey && !$control_var) {
						    	$prefix_label = "content_pre_";
						    	$label_set = false; 
						    } 
						    else
						    {
						    	$prefix_label = "";
						    	$label_set = true; 
							}
						    if($this->form_fields[$subkey]->encode_label) 	
							    $this->tpl[0]->set_var($prefix_label . "label", ffCommon_specialchars($this->form_fields[$subkey]->label) . $required_symbol);
						    else
							    $this->tpl[0]->set_var($prefix_label . "label",$this->form_fields[$subkey]->label . $required_symbol);
	                    }

	                    if($label_set) {
						    /**
						    * Label Class
						    */
						    $this->tpl[0]->set_var("label_for", $this->id . "_" . $subkey);
						    
							if(0)
							{
								$arrColumnLabel = $this->form_fields[$subkey]->framework_css["label"]["col"];
								$arrColumnControl = $this->form_fields[$subkey]->framework_css["control"]["col"];
								$type_label = "";
								if($primary_field == $subkey)
									$type_label = "-inline";
								
								if(!strlen($control_var))
								{
									if(is_array($arrColumnLabel) && count($arrColumnLabel)
										&& is_array($arrColumnControl) && count($arrColumnControl)
									) {
									    $this->tpl[0]->set_var("label_prefix", '<div class="' . cm_getClassByFrameworkCss($arrColumnLabel, "col") . " " . cm_getClassByFrameworkCss("align-right", "util") . '">');
									    $this->tpl[0]->set_var("label_postfix", '</div>');
									
									    $control_prefix = '<div class="' . cm_getClassByFrameworkCss($arrColumnControl, "col") . '">';
									    $control_postfix = '</div>';
									    $type_label = "-inline";
								    }
								}
								
								if($this->framework_css["component"]["type"] === null && $type_label)
									$this->framework_css["component"]["type"] = $type_label;
								
								$label_class = cm_getClassByFrameworkCss("label" . $type_label, "form");
								if($this->framework_css["component"]["type"] && $is_combine_field) {
									if($control_var == "_in_label")
										$label_class .= ($label_class ? " " : "") . cm_getClassByFrameworkCss($arrColumnLabel, "push") . " " . cm_getClassByFrameworkCss($arrColumnControl, "col");
									else
										$container_class["align"] = cm_getClassByFrameworkCss("align-right", "util");
								}

							    if($label_class)
								    $this->tpl[0]->set_var("label_class", ' class="' . $label_class . '"');
								else
									$this->tpl[0]->set_var("label_class", "");								
							}							    
					    } else {
					    	$control_var = "";
					    }
				    } 

				    /**
				    * Row Class
				    */
				    $container_properties = array();
				    $container_class["default"] = $this->form_fields[$subkey]->get_control_class(null, null, array("framework_css" => false, "control_type" => false)); 
				    
					if($primary_field != $subkey) {
					    if(is_array($this->form_fields[$subkey]->framework_css["container"]["col"]) 
				    		&& count($this->form_fields[$subkey]->framework_css["container"]["col"])
					    ) {
							$container_class["grid"] = cm_getClassByFrameworkCss($this->form_fields[$subkey]->framework_css["container"]["col"], "col");
							if(!$is_wrapped) {
								$wrap_class = array("form-wrap");
								if($this->form_fields[$subkey]->framework_css["container"]["row"]) {
									$wrap_class[] = cm_getClassByFrameworkCss("row", "form");
								}
								$this->tpl[0]->set_var("wrap_class", implode(" ", array_filter($wrap_class)));
								$is_wrapped = $this->tpl[0]->parse("SectWrapStart", false);
							}

							$wrap_count = $wrap_count + $this->form_fields[$subkey]->framework_css["container"]["col"]["lg"];
						} elseif($this->form_fields[$subkey]->framework_css["container"]["row"]) {
							if($is_wrapped) {
								$wrap_count = 12;
								$container_class["grid"] = cm_getClassByFrameworkCss(array($wrap_count), "col");
							} elseif($label_set) { 
								$container_class["grid"] = cm_getClassByFrameworkCss("row", "form");
							}
						} else {
							if($is_wrapped) {
								$wrap_count = 12;
								$container_class["grid"] = cm_getClassByFrameworkCss(array($wrap_count), "col");
							}
						}
					}

					if(count($container_class)) {
				    	$str_container_class = implode(" ", array_filter($container_class));
				    	if($str_container_class)
				    		$container_properties["class"] = $str_container_class;
					}
					if(is_array($container_properties) && count($container_properties)) {
						$str_container_properties = "";
						foreach($container_properties AS $container_properties_key => $container_properties_value) {
							
							$str_container_properties .= " " . $container_properties_key . '="' . (is_array($container_properties_value) ? implode(" ", array_filter($container_properties_value)) : $container_properties_value) . '"';
						}
						$this->tpl[0]->set_var("container_properties", $str_container_properties);
						
						$this->tpl[0]->set_var("field_container_start", '<div' . $str_container_properties . '>');
						$this->tpl[0]->set_var("field_container_end", '</div>');
					}				    
					
					// CONTROL/VALUE SECTION
				    /**
				    * Control Class
				    */
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
						    if ($this->tpl[0]->isset_var("content" . $control_var))
							    $this->tpl[0]->set_var("content" . $control_var, $control_prefix . $this->form_fields[$subkey]->process() . $control_postfix);
					    }
					    else
						    $this->tpl[0]->set_var("content" . $control_var, $this->form_fields[$subkey]->getDisplayValue());
				    }

					if($label_set)
						$this->tpl[0]->parse("SectGroupLabel", false);
					else
	                	$this->tpl[0]->set_var("SectGroupLabel", "");

				    /*
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
				    }*/

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
	    */					
			    }
				if($primary_field == $subkey && is_subclass_of($subvalue["data"], "ffField_base")) {
					$this->tpl[0]->set_var("GroupTitle", $this->tpl[0]->ProceedTpl($this->tpl[0]->DBlocks["SectGroupRow"]));
				} else {
					if(($wrap_count >= 12 || $count_contents == count($contents)) && $is_wrapped) {
						$this->tpl[0]->parse("SectWrapEnd", false);
						$wrap_count = 0;
						$is_wrapped = false;
					}			    

				    $rc = $this->tpl[0]->parse("SectGroupRow" . $suffix, true);
				    if (!$rc) $this->tpl[0]->parse("SectGroupRow", true);
				    
				    $this->tpl[0]->set_var("SectWrapStart", "");
				    $this->tpl[0]->set_var("SectWrapEnd", "");
				}

		    }
		    reset($contents);
        }
        
		return $i;
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

		$action_class["default"] = $this->framework_css["actions"]["class"];
		if($this->framework_css["actions"]["col"]) {
            $action_class["grid"] = cm_getClassByFrameworkCss($this->framework_css["actions"]["col"], "col");
        }
        
		$this->tpl[0]->set_var("actions_class", implode(" ", array_filter($action_class)));
		$this->tpl[0]->set_var("ActionButtons", $buffer);
		$this->tpl[0]->parse("SectControls", false);
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
		
		$this->doEvent("on_error", array($this));

		$this->tpl[0]->set_var("SectError", "");
		if (strlen($this->strError))
		{
			/*if (function_exists("mod_notifier_add_message_to_queue") && $this->parent[0]->isXHR())
			{
				mod_notifier_add_message_to_queue($this->strError, MOD_NOTIFIER_ERROR);
			}
			else
			{*/
				$this->tpl[0]->set_var("error_class", cm_getClassByDef($this->framework_css["error"]));
				$this->tpl[0]->set_var("strError", $this->strError);
				$this->tpl[0]->parse("SectError", false);
			//}
		}
	}

    /**
	 * Inizializza i controlli di default del record
	 * Al momento consistono nei pulsanti insert, update, delete e cancel
	 */
	function initControls()
    {
		if ($this->hide_all_controls)
			return;

		parent::initControls();

		/*if (isset($this->action_buttons["ActionButtonCancel"]))
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
		}*/
	}

	/**
	 * Esegue un redirect in base alle caratteristiche del record e della richiesta, se XHR o meno
	 * @param String $url l'indirizzo di destinazione
	 * @return String
	 */
/*	function redirect($url)
	{
		if ($this->parent[0]->isXHR())
		{
			$this->json_result["url"] = $url;
			$this->json_result["close"] = false;
			cm::jsonParse($this->json_result);
			exit;
		}
		else
		{
			return parent::redirect($url);
		}
	}
*/
	/**
	 * Esegue un reload dello stesso componente in base alle caratteristiche del record e della richiesta, se XHR o meno
	 * NB: se la richiesta non Ã¨ XHR, viene effettuato un redirect completo
	 * @param String $url l'indirizzo di destinazione
	 * @return String
	 */
	function doReload($refresh_keys = true)
	{
		if ($this->parent[0]->isXHR())
		{
			$this->json_result["url"] = $this->parent[0]->getRequestUri($refresh_keys);
			$this->json_result["close"] = false;
			$this->json_result["component"] = $this->id;
			if ($this->parent[0]->getXHRSection())
				$this->json_result["section"] = $this->parent[0]->getXHRSection();

			cm::jsonParse($this->json_result, true);
			exit;
		}
		else
		{
			return parent::redirect($this->parent[0]->getRequestUri($refresh_keys));
		}
	}
	
	public function structProcess($tpl)
	{
	}
}
