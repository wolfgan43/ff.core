<?php
class ffGrid_div extends ffGrid_base 
{
	var $dialog_action_button = false;

	/**
	 * La label del tasto "turnon"
	 * usato in presenza di checkbox
	 * @var String
	 */
	var $turn_on_label	= "ON";
	/**
	 * La label del tasto "turnoff"
	 * usato in presenza di checkbox
	 * @var String
	 */
	var $turn_off_label = "OFF";

	/**
	 * Se la grid dev'essere full ajax
	 * @var Boolean
	 */
	var $full_ajax		= false;
	/**
	 * Se dev'essere usata la funzionalità di aggiunta ajax con dialog
	 * @var Boolean
	 */
	var $ajax_addnew	= false;
	/**
	 * Se dev'essere usata la funzionalità di editing ajax con dialog
	 * @var Boolean
	 */
	var $ajax_edit		= false;
	/**
	 * Se dev'essere usata la funzionalità di cancellazione ajax con dialog
	 * @var Boolean
	 */
	var $ajax_delete	= false;
	/**
	 * Se dev'essere usata la funzionalità di ricerca ajax con dialog
	 * @var Boolean
	 */
	var $ajax_search	= false;

	var $dialog_options = array(
		"add" => array(
			"width" => ""
			, "height" => ""
            , "title" => ""
		)
		, "edit" => array(
			"width" => ""
			, "height" => ""
            , "title" => ""
		)
        , "delete" => array(
            "width" => ""
            , "height" => ""
            , "title" => ""
        )
	);
	/**
	 * una classe da associare alla riga
	 * @var String
	 */
	var $row_class		= "";
    var $row_properties   = array();

	/**
	 * Un elenco di classi da associare alle righe della grid, ciclate in sequenza
	 * @var Array
	 */
	var $switch_row_class =  array(
								"display" => true
								, "first" => "odd"
								, "second" => "even"
							);

	/**
	 * La classe da assegnare alle colonne intermedie
	 * @var String
	 */
	var $column_class				= "";
	/**
	 * La classe da assegnare alla prima colonna
	 * @var String
	 */
	var $column_class_first 		= "";
	/**
	 * La classe da assegnare all'ultima colonna
	 * @var String
	 */
	var $column_class_last			= "";

	/**
	 * La classe da assegnare alle label di ordinamento
	 * @var String
	 */
	var $label_class				= "";
	/**
	 * La classe da assegnare alle label di ordinamento quando selezionate
	 * @var String
	 */
	var $label_selected_class		= "";
	/**
	 * La classe da assegnare alla prima label di ordinamento quando selezionata
	 * @var String
	 */
	var $label_selected_class_first	= "";
	/**
	 * La classe da assegnare all'ultima label di ordinamento quando selezionata
	 * @var String
	 */
	var $label_selected_class_last	= "";

	/**
	 * Se visualizzare l'url di editing sulla griglia
	 * @var Boolean
	 */
	var $display_edit_url		= true;			// display edit record url (on fields)
    var $display_edit_url_alt   = "";
	/**
	 * Se visualizzare il pulsante di editing sulla griglia
	 * @var Boolean
	 */
	var $display_edit_bt		= false;		// display edit record button
	/**
	 * Se visualizzare il pulsante di cancellazione sulla griglia
	 * @var Boolean
	 */
	var $display_delete_bt		= true;			// display delete record button. This cause use of dialog.
	/**
	 * Se il pulsante di editing dev'essere visibile sulla riga processata attualmente
	 * @var Boolean
	 */
	var $visible_edit_bt		= true;			// display edit record button record per record
	/**
	 * Se il pulsante di eliminazione dev'essere visibile sulla riga processata attualmente
	 * @var Boolean
	 */
	var $visible_delete_bt		= true;			// display delete record button record per record

    /**
	 * Il simbolo usato per visualizzare i campi valuta
	 * @var String
	 */
	var $symbol_valuta          = "&euro; ";
    
	/**
	 * Se visualizzare il campo di ricerca semplice
	 * @var Boolean
	 */
	var $display_search_simple 	= true;

	/**
	 * Le opzioni del campo di ricerca semplice
	 * @var Array
	 */
	var $search_simple_field_options	= array(
												"id" => "searchall"
												, "label" 	=> ""
												, "src_operation" => " [NAME] LIKE([VALUE]) "
												, "src_prefix" 	=> "%"
												, "src_postfix" 	=> "%"
												, "obj" 	=> null
												, "index" 	=> 0
											);
	var $reset_page_on_search = false;
	/**
	 * Le dipendenze in widgets del componente
	 * @var Array
	 */
	var $widget_deps = array(
			array("name" => "fullclick")
		);

	/**
	 * Se la ricerca avanzata dev'essere aperta di default
	 * @var Boolean
	 */
	var $open_adv_search = false;

	/**
	 * Una descrizione aggiuntiva da assegnare al componente
	 * @var String
	 */
	public $description = null;

    public $no_record_label = null;
    
	/**
	 * Se la widget "disclosure panels" dev'essere abilitata di default
	 * @var Boolean
	 */
	var $widget_discl_enable = false;

	/**
	 * Se la widget "disclosure panels" dev'essere aperta di default
	 * @var Boolean
	 */
	var $widget_def_open = true;
	
	var $widget_activebt_enable = false;
	
	var $navigator_doAjax = true;
	var $navigator_display_selector = true;
	
	private $parsed_hidden_fields 	= 0;
	/**
	 * Variabile ad uso interno
	 */
	private $parsed_fields 		= 0;
	/**
	 * Variabile ad uso interno
	 */
	private $parsed_filters		= 0;
	
	/**
	 * Il costruttore dell'oggetto
	 * da non richiamare direttamente
	 * @param ffPage_base $page
	 * @param String $disk_path
	 * @param String $theme 
	 */
	 
	var $template_file         = "ffGrid_div.html";
	
	function __construct(ffPage_base $page, $disk_path, $theme)
	{
		parent::__construct($page, $disk_path, $theme);
		
		if ($this->display_search_simple)
		{
			if ($this->search_simple_field_options["obj"] !== null)
			{
				$this->addSearchField($this->search_simple_field_options["obj"]);
			}
			else
			{
				$tmp = ffField::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->theme);
				$tmp->id				= $this->search_simple_field_options["id"];
				$tmp->label				= $this->search_simple_field_options["label"];
				$tmp->src_operation		= $this->search_simple_field_options["src_operation"];
				$tmp->src_prefix		= $this->search_simple_field_options["src_prefix"];
				$tmp->src_postfix		= $this->search_simple_field_options["src_postfix"];
				$tmp->data_type			= "";
				$tmp->display	= false;
				$this->addSearchField($tmp);
			}
		}
	}

	/**
	 * Carica il template della griglia
	 */
	protected function tplLoad()
	{

		$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
		$this->tpl[0]->load_file($this->template_file, "main");

		$this->tpl[0]->set_var("site_path", $this->site_path);
		$this->tpl[0]->set_var("page_path", $this->page_path);
		$this->tpl[0]->set_var("theme", $this->getTheme());
		$this->tpl[0]->set_var("class", $this->class);
		$this->tpl[0]->set_var("this_url", rawurlencode($this->parent[0]->getRequestUri()));

		$this->tpl[0]->set_var("fixed_pre_content", $this->fixed_pre_content);
		$this->tpl[0]->set_var("fixed_post_content", $this->fixed_post_content);
		
		$this->tpl[0]->set_var("fixed_title_content", $this->fixed_title_content);
		$this->tpl[0]->set_var("fixed_heading_content", $this->fixed_heading_content);
		
		$this->tpl[0]->set_var("SectHiddenField", "");

		if (strlen($this->id))
			$this->prefix = $this->id . "_";
		$this->tpl[0]->set_var("component", $this->prefix);
		$this->tpl[0]->set_var("component_id", $this->id);

		if(strlen($this->title) || $this->widget_discl_enable) {
			$this->tpl[0]->set_var("title", $this->title);
			$this->tpl[0]->parse("SectTitle", false);
		} else {
			$this->tpl[0]->set_var("SectTitle", "");
		}

    	if($this->dialog_action_button)
    		$this->tpl[0]->set_var("dialog_class", " force");

		if ($this->description !== null) {
			$this->tpl[0]->set_var("description", $this->description);
			$this->tpl[0]->parse("SectDescription", false);
		} else {
			$this->tpl[0]->set_var("SectDescription", "");
		}

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

	/**
	 * Elabora il template
	 * @param Boolean $output_result se dev'essere visualizzata immediatamente l'elaborazione
	 * @return Mixed il risultato dell'operazione
	 */
	public function tplParse($output_result)
	{
		$res = ffGrid::doEvent("on_tplParse", array($this, $this->tpl[0], $output_result));
		$res = $this->doEvent("on_tplParse", array($this, $this->tpl[0], $output_result));

		if ($this->parent[0]->getXHRComponent() == $this->id)
		{
			if($this->db[0]->query_id)
                $this->json_result["rows"] = $this->db[0]->numRows();
            else
                $this->json_result["rows"] = 0;
                
            $this->json_result["page"] = $this->page;

			if (isset($_REQUEST["XHR_SECTION"])) 
			{
				if ($this->use_paging || $this->use_search || $this->use_alpha || $this->use_order || $this->parsed_hidden_fields || $this->use_fields_params)
					$this->json_result["hidden"] = $this->tpl[0]->rpparse("SectHidden", false);
				else
					$this->json_result["hidden"] = "";
				return $this->tpl[0]->rpparse("Sect" . $_REQUEST["XHR_SECTION"], false);
			}
		}
		
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
		$this->parent[0]->tplAddJs("ff.ffGrid", "ffGrid.js", FF_THEME_DIR . "/library/ff");

		if (!isset($this->tpl[0]))
			return;

        if (!$this->display_navigator || !$this->use_paging || ($this->navigator[0]->num_rows <= $this->navigator[0]->records_per_page && !($this->ajax_search || $this->full_ajax)))
			return $this->tpl[0]->rpparse("SectHeaders", false);
		else
			return $this->tpl[0]->rpparse("SectHeaders", false) . $this->navigator[0]->process_headers();
	}

	function process_footers()
	{
		if (!isset($this->tpl[0]))
			return;
			
        if (!$this->display_navigator || !$this->use_paging || ($this->navigator[0]->num_rows <= $this->navigator[0]->records_per_page && !($this->ajax_search || $this->full_ajax)))
			return $this->tpl[0]->rpparse("SectFooters", false);
		else
			return $this->tpl[0]->rpparse("SectFooters", false) . $this->navigator[0]->process_footers();
	}
	/**
	 * error template processing function
	 * called by process_interface()
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
	}	
	/**
	 * Elabora i parametri di ricerca
	 */
	function process_search_params()
	{
		parent::process_search_params();

		if($this->display_search_simple && strlen($this->search_fields[$this->search_simple_field_options["id"]]->getValue())) 
		{
			foreach ($this->grid_fields AS $key => $value) 
			{
				if ($this->grid_fields[$key]->data_type == "")
					continue;

				$tmp_sql = "";

				$tmp_where_value = "";
				if(strlen($this->grid_fields[$key]->src_table))
					$tblprefix = "`" . $this->grid_fields[$key]->src_table . "`.";
				else
					$tblprefix = "";
					
				if($this->grid_fields[$key]->extended_type == "Selection" && $this->grid_fields[$key]->source_SQL) 
				{
					$this->grid_fields[$key]->pre_process();
					
					if(is_array($this->grid_fields[$key]->recordset) && count($this->grid_fields[$key]->recordset))
					{
						foreach ($this->grid_fields[$key]->recordset AS $source_SQL_key => $source_SQL_value)
						{
							if(strpos($source_SQL_value[1]->getValue(), $this->search_fields[$this->search_simple_field_options["id"]]->getValue()) !== false) 
							{
								if(strlen($tmp_where_value))
									$tmp_where_value .= ", ";
									
								$tmp_where_value .= $this->db[0]->toSql($source_SQL_value[0]);
							}
						}
					}
					if(strlen($tmp_where_value))
						$tmp_sql = $tblprefix . "`" . $this->grid_fields[$key]->get_data_source() . "` IN (" . $tmp_where_value . ")";
				} 
				elseif($this->grid_fields[$key]->extended_type == "Selection" && $this->grid_fields[$key]->multi_pairs) 
				{
					if(is_array($this->grid_fields[$key]->multi_pairs) && count($this->grid_fields[$key]->multi_pairs))
					{
						foreach ($this->grid_fields[$key]->multi_pairs AS $multi_pairs_key => $multi_pairs_value)
						{
							if(stripos($multi_pairs_value[1]->getValue(null, FF_LOCALE), $this->search_fields[$this->search_simple_field_options["id"]]->getValue()) !== false)
							{
								if(strlen($tmp_where_value))
									$tmp_where_value .= ", ";
									
								$tmp_where_value .= $this->db[0]->toSql($multi_pairs_value[0]);
							}
						}
					}
					if(strlen($tmp_where_value))
						$tmp_sql = $tblprefix . "`" . $this->grid_fields[$key]->get_data_source() . "` IN (" . $tmp_where_value . ")";
				} 
				else 
				{
					$tmp_where_value = new ffData($this->search_fields[$this->search_simple_field_options["id"]]->getValue(), $this->grid_fields[$key]->base_type, FF_LOCALE);
					$tmp_where_value = $tmp_where_value->getValue($this->grid_fields[$key]->base_type, FF_SYSTEM_LOCALE);
					
					if($this->search_fields[$this->search_simple_field_options["id"]]->src_prefix || $this->search_fields[$this->search_simple_field_options["id"]]->src_postfix)
					{
						$tmp_where_value = $this->db[0]->toSql($this->search_fields[$this->search_simple_field_options["id"]]->value, $this->search_fields[$this->search_simple_field_options["id"]]->base_type, false);
						$tmp_where_value = "'" . $this->search_fields[$this->search_simple_field_options["id"]]->src_prefix . $tmp_where_value . $this->search_fields[$this->search_simple_field_options["id"]]->src_postfix . "'";
					}
					else
						$tmp_where_value = $this->db[0]->toSql($this->search_fields[$this->search_simple_field_options["id"]]->value);
				
					if (is_array($this->grid_fields[$key]->src_fields) && count($this->grid_fields[$key]->src_fields))
						$tmp_sql .= " ( ";

					$tmp_where_part = $this->search_fields[$this->search_simple_field_options["id"]]->src_operation;
					$tmp_where_part = str_replace("[NAME]", $tblprefix . "`" . $this->grid_fields[$key]->get_data_source() . "`", $tmp_where_part);
					$tmp_where_part = str_replace("[VALUE]", $tmp_where_value, $tmp_where_part);
					$tmp_sql .= " " . $tmp_where_part . " ";

					if (is_array($this->grid_fields[$key]->src_fields) && count($this->grid_fields[$key]->src_fields))
					{
						foreach ($this->grid_fields[$key]->src_fields AS $addfld_key => $addfld_value)
						{
							$tmp_where_part = $this->search_fields[$this->search_simple_field_options["id"]]->src_operation;
							$tmp_where_part = str_replace("[NAME]", $this->grid_fields[$addfld_key], $tmp_where_part);
							$tmp_where_part = str_replace("[VALUE]", $tmp_where_value, $tmp_where_part);
							$tmp_sql .= " OR " . $tmp_where_part . " ";
						}
						reset($this->grid_fields[$key]->src_fields);
						$tmp_sql .= " ) ";
					}
				}				
				
				//if($this->grid_fields[$key]->src_having)
				//{
					if (strlen($tmp_sqlHaving) && strlen($tmp_sql))
						$tmp_sqlHaving .= " OR ";

					$tmp_sqlHaving .=  $tmp_sql;
				/*} else {
					if (strlen($tmp_sqlWhere) && strlen($tmp_sql))
						$tmp_sqlWhere .= " OR ";

					$tmp_sqlWhere .=  $tmp_sql;
				}*/
			}
			reset ($this->grid_fields);
			if(strlen($tmp_sqlHaving)) 
				$this->sqlHaving = " ( " . $tmp_sqlHaving . " ) ";
				
			/*if(strlen($tmp_sqlWhere))
				$this->sqlWhere = " ( " . $tmp_sqlWhere . " ) ";*/
		}
	}
	
	/**
	 * Elabora la sezione di ricerca
	 */
	function process_search()
	{
		if ($this->parent[0]->getXHRComponent() == $this->id && $this->parent[0]->getXHRSection() == "GridData")
			return;

		if($this->display_search)
		{
			if ($this->display_search_simple)
			{
				foreach ($this->search_fields AS $key => $value)
				{
					if($this->open_adv_search != "always" && $this->search_fields[$key]->display && !$this->search_fields[$key]->multi_disp_as_filter)
					{
						$this->tpl[0]->parse("SectAdvSearch", false);
						break;
					}
				}
				reset($this->search_fields);
				
    			$this->search_fields[$this->search_simple_field_options["id"]]->properties["onkeydown"] = "
                if (null == event)
                    event = window.event;
                if (event.keyCode == 13)  {
                    document.getElementById('frmAction').value = 'layout_search';
                    jQuery('#" . $this->prefix . $this->navigator[0]->page_parname . "').val('1');  
                    ff.ffGrid.advsearchHide('" . $this->id . "_', '" . $this->id . "');  
                    ff.ajax.doRequest({'component' : '" . $this->id . "','section' : 'GridData'});
                    return false;
                }";
				
				$this->tpl[0]->set_var("SearchAll", $this->search_fields[$this->search_simple_field_options["id"]]->process($this->search_simple_field_options["id"] . "_src"));
				$this->tpl[0]->parse("SectSearchSimple", false);
			}
			
			
			if($this->open_adv_search === "never" || (!$this->searched && $this->open_adv_search === false))
				$this->tpl[0]->set_var("advsearch_visibility", " hidden");
				
			$this->tpl[0]->parse("SezAdvSearch", false);
		}
		
		parent::process_search();
	}
	
	/**
	 * Esegue il processing vero e proprio dei contenuti della grid
	 */
	public function process_grid()
	{
		if ($this->display_grid == "never" || ($this->display_grid == "search" && !strlen($this->searched)))
		{
			$this->tpl[0]->set_var("SectGrid", "");
			return;
		}
		
		parent::process_grid();

		if ($this->display_new) // done at this time due to maxspan
		{
			if (strlen($this->bt_insert_url))
			{
				$temp_url = ffProcessTags($this->bt_insert_url, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), $_SERVER['REQUEST_URI'], $this->parent[0]->get_globals());
			}
			else
			{
				if (strlen($this->record_insert_url))
					$temp_url = $this->record_insert_url;
				else
					$temp_url = $this->record_url;
				$temp_url .= "?" . $this->parent[0]->get_keys($this->key_fields) .
							$this->parent[0]->get_globals() . $this->addit_insert_record_param .
							"ret_url=" . rawurlencode($this->parent[0]->getRequestUri());
			}

/*			$res = $this->doEvent("onUrlInsert", array(&$this, $temp_url));
			$rc = end($res);
				if ($rc !== null)
					$temp_url = $rc;
*/
            if($this->buttons_options["addnew"]["label"] === null)
                $this->buttons_options["addnew"]["label"] = ffTemplate::_get_word_by_code("ffGrid_addnew");
            
            if($this->buttons_options["addnew"]["icon"] === null)
                $this->buttons_options["addnew"]["icon"] = cm_getClassByFrameworkCss("addnew", "icon-" . $this->buttons_options["addnew"]["aspect"] . "-tag");

            if($this->buttons_options["addnew"]["class"] === null)
            $this->buttons_options["addnew"]["class"] = cm_getClassByFrameworkCss("addnew", $this->buttons_options["addnew"]["aspect"]);

			if ($this->full_ajax || $this->ajax_addnew)
			{
				$this->parent[0]->widgetLoad("dialog");
                
                $this->tpl[0]->set_var("addnew_bt", $this->parent[0]->widgets["dialog"]->process(
                        $this->id
                        , array(
                                "title"                => (strlen($this->dialog_options["add"]["title"])
                                                            ? $this->dialog_options["add"]["title"]
                                                            : ffTemplate::_get_word_by_code("ffGrid_addnew_title")
                                                        )
                                , "url"            	=> $temp_url
                                , "class"           => $this->buttons_options["addnew"]["class"]
                                , "name"            => $this->buttons_options["addnew"]["icon"] . $this->buttons_options["addnew"]["label"]
                                , "tpl_id"       	=> $this->id
                                , "width"        	=> $this->dialog_options["add"]["width"]
                                , "height"        	=> $this->dialog_options["add"]["height"]
                            )
                        , $this->parent[0]
                    ));
				$this->tpl[0]->parse("SectAddNewBt", false);
				$this->tpl[0]->set_var("SectAddNewUrl", "");
			}
			else
			{
				$this->tpl[0]->set_var("addnew_url", ffCommon_specialchars($temp_url));
				$this->tpl[0]->set_var("addnew_label", $this->buttons_options["addnew"]["label"]);
                if(strlen($this->buttons_options["addnew"]["class"]))
                    $this->tpl[0]->set_var("addnew_class", ' class="' . $this->buttons_options["addnew"]["class"] . '"');
                $this->tpl[0]->set_var("addnew_icon", $this->buttons_options["addnew"]["icon"]);				
                $this->tpl[0]->parse("SectAddNewUrl", false);
				$this->tpl[0]->set_var("SectAddNewBt", "");
			}
			$this->tpl[0]->parse("SectAddNew", false);
			$this->tpl[0]->set_var("SectNotAddNew", "");
		}
		else
		{
			$this->tpl[0]->set_var("SectAddNew", "");
			if ($this->display_not_add_new)
				$this->tpl[0]->parse("SectNotAddNew", false);
			else
				$this->tpl[0]->set_var("SectNotAddNew", "");
		}
		
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

		if ($this->db[0]->query_id && $this->db[0]->nextRecord())
		{
			//$this->tpl[0]->set_var("SectNoRecords", "");

			if ($this->use_paging)
				$this->db[0]->jumpToPage($this->page, $this->records_per_page);
			$i = 0;
			$break_while = false;
			do
			{
				$this->tpl[0]->set_var("row", $i);
				$this->tpl[0]->set_var("rrow", $i + 1);

				$res = $this->doEvent("on_load_row", array(&$this, $this->db[0], $i));
				
				/* Step 1: retrieve values (done in 2 steps due to events needs) */

				$keys = "";
				$unicKey = "";
				if (count($this->key_fields))
				{
					// find global recordset corrispondency (if one)
					$aKeys = array();
					foreach($this->key_fields as $key => $FormField)
					{
						$this->key_fields[$key]->value = $this->db[0]->getField($this->key_fields[$key]->get_data_source(), $this->key_fields[$key]->base_type);
						$aKeys[$key] = $this->key_fields[$key]->value->getValue(null, FF_SYSTEM_LOCALE);
						$keys .= "keys[" . $this->key_fields[$key]->id . "]=" . $this->key_fields[$key]->value->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE) . "&";
						$unicKey .= $this->key_fields[$key]->value->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE);
						
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
							$this->parse_hidden_field();
						}
						reset($this->key_fields);
					}
				}

				$keys .= $this->parent[0]->get_keys($this->key_fields);

				foreach ($this->grid_fields as $key => $FormField)
				{
					if ($this->use_fields_params && $this->grid_fields[$key]->control_type != "" && isset($this->recordset_values[$recordset_key][$key]))
					{
						$this->grid_fields[$key]->value = new ffData($this->recordset_values[$recordset_key][$key], $this->grid_fields[$key]->base_type, FF_SYSTEM_LOCALE);
						if (isset($this->recordset_ori_values[$recordset_key][$key]))
						{
							$this->tpl[0]->set_var("id", "recordset_ori_values[" . $recordset_key . "][" . $this->grid_fields[$key]->id . "]");
							$this->tpl[0]->set_var("value",   ffCommon_specialchars($this->recordset_ori_values[$recordset_key][$key]));
							$this->parse_hidden_field();
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
						if ($this->use_fields_params && $this->grid_fields[$key]->control_type != "")
						{
							$this->tpl[0]->set_var("id", "recordset_ori_values[" . $recordset_key . "][" . $this->grid_fields[$key]->id . "]");
							$this->tpl[0]->set_var("value", ffCommon_specialchars($this->grid_fields[$key]->value->getValue($this->grid_fields[$key]->base_type, FF_SYSTEM_LOCALE)));
							$this->parse_hidden_field();
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
									$this->addit_record_param .
									"ret_url=" . rawurlencode($this->parent[0]->getRequestUri());
					}
					$modify_url = ffProcessTags($modify_url, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), $_SERVER['REQUEST_URI'], $this->parent[0]->get_globals());

					$cancelurl = $this->parent[0]->getRequestUri();
					if (strlen($this->record_delete_url))
					{
                        $confirmurl = $this->record_delete_url . "?" . $keys .
                                        $this->parent[0]->get_globals() .
                                        $this->addit_record_param .
                                        $this->record_id . "_frmAction=confirmdelete&" .
                                        "ret_url=" . rawurlencode($this->parent[0]->getRequestUri());
                        $confirmurl = ffProcessTags($confirmurl, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), $_SERVER['REQUEST_URI'], $this->parent[0]->get_globals());

                        $delete_url = $this->dialog(
                                          true
                                        , "yesno"
                                        , $this->parent[0]->title
                                        , $this->label_delete
                                        , ($this->full_ajax || $this->ajax_delete ? "[CLOSEDIALOG]" : $cancelurl)
                                        , $confirmurl
                                        );
					}
					else
					{
						if (strlen($this->bt_delete_url))
						{
							$confirmurl = $this->bt_delete_url;
						}
						else
						{
							$confirmurl = $this->record_url . "?" . $keys .
										$this->parent[0]->get_globals() .
										$this->addit_record_param .
										$this->record_id . "_frmAction=confirmdelete&" .
										"ret_url=" . rawurlencode($this->parent[0]->getRequestUri());
						}
						$confirmurl = ffProcessTags($confirmurl, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), $_SERVER['REQUEST_URI'], $this->parent[0]->get_globals());

						$delete_url = $this->dialog(
													  true
													, "yesno"
													, $this->parent[0]->title
													, $this->label_delete
													, ($this->full_ajax || $this->ajax_delete ? "[CLOSEDIALOG]" : $cancelurl)
													, $confirmurl
													);
					}

					foreach ($this->grid_fields as $key => $FormField)
					{
						$buffer = "";
						if (
								$this->grid_fields[$key]->display === false
								/*|| (
									$this->use_fixed_fields == true
									&& $this->grid_fields[$key]->skip_if_empty
									&& $this->grid_fields[$key]->value->ori_value == ""
								)*/
							)
							continue;

            			if (!is_subclass_of($this->grid_fields[$key], "ffField_base"))
							ffErrorHandler::raise("Wrong Grid Field: must be a ffField", E_USER_ERROR, $this, get_defined_vars());

						//---------------------------------------------------------------------
						//---------------------------------------------------------------------
						$this->tpl[0]->set_var("col", $col); // Useful for Fixed Fields Templates
						$col++;
						$class = "";
						
						if (strlen($this->grid_fields[$key]->container_class))
							$class = $this->grid_fields[$key]->container_class;
						else
							$class = $this->column_class;
						
						if ($col == 1 && $this->column_class_first)
							$class .= " " . $this->column_class_first;
						elseif ($col == $totfields && $this->column_class_last)
							$class .= " " . $this->column_class_last;
						
						$class .= " ffField";

						if($this->grid_fields[$key]->get_app_type() == "Text" && $this->grid_fields[$key]->extended_type != "String")
							$class .= " " . strtolower($this->grid_fields[$key]->extended_type);
						else 
							$class .= " " . strtolower($this->grid_fields[$key]->get_app_type());

						$class = str_replace("[COL]", $col, $class);
						$class = str_replace("[ID]", $this->grid_fields[$key]->id, $class);
						$class = trim($class);
						
						if (strlen($class))
							$class = "class=\"" . $class . "\"";
						
						$this->tpl[0]->set_var("col_class", $class);
						$this->tpl[0]->set_var("col_properties", $this->grid_fields[$key]->getProperties($this->grid_fields[$key]->container_properties));
						//---------------------------------------------------------------------
						//---------------------------------------------------------------------
						
						$this->tpl[0]->set_var("data_class", $this->grid_fields[$key]->data_class);
						$this->tpl[0]->set_var("data_properties", $this->grid_fields[$key]->getProperties($this->grid_fields[$key]->data_properties));

						if(strlen($symbol_valuta) && $this->grid_fields[$key]->app_type == "Currency")
						{
							$this->tpl[0]->set_var("euro", $symbol_valuta);
							$this->tpl[0]->parse("SectEuro", false);
						}
						else
						{
							$this->tpl[0]->set_var("SectEuro", "");
						}

						if ($this->grid_fields[$key]->control_type === "")
						{
							$this->grid_fields[$key]->pre_process(true);
							$buffer = $this->grid_fields[$key]->fixed_pre_content . $this->grid_fields[$key]->getDisplayValue() . $this->grid_fields[$key]->fixed_post_content;
						}
						else
						{
							$buffer = $this->grid_fields[$key]->process(
										"recordset_values[$recordset_key][" . $this->grid_fields[$key]->id . "]");
						}

						$this->tpl[0]->set_var($key . "_display_value", $buffer);
						$this->tpl[0]->set_var($key . "_value", $this->grid_fields[$key]->value->getValue());
						$this->tpl[0]->set_var("url_" . $key, ffCommon_url_rewrite($this->grid_fields[$key]->value->getValue()));
						$this->tpl[0]->set_var("url_" . $key . "_display_value", ffCommon_url_rewrite($buffer));
						$this->tpl[0]->set_var("ent_" . $key . "_display_value", ffCommon_specialchars($buffer));
						$this->tpl[0]->set_var("ent_" . $key . "_value", ffCommon_specialchars($this->grid_fields[$key]->value->getValue()));
						
						if (!$this->full_ajax && !$this->ajax_edit)
						{
							if ($this->grid_fields[$key]->url === null)
							{
								$this->tpl[0]->set_var("field_url", $modify_url);
							}
							else
							{
								$field_url = ffProcessTags($this->grid_fields[$key]->url, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), $_SERVER['REQUEST_URI'], $this->parent[0]->get_globals());
								$this->tpl[0]->set_var("field_url", $field_url);
							}

							if ($this->grid_fields[$key]->url === "" || $this->grid_fields[$key]->control_type != "" || !$this->display_edit_url)
							{
								$this->tpl[0]->set_var("SectUrlBefore", "");
								$this->tpl[0]->set_var("SectUrlAfter", "");
							}
							else
							{
								if($this->display_edit_url_alt) {
									$tmp_url = $this->display_edit_url_alt . "?" . $keys .
	                                            $this->parent[0]->get_globals() .
	                                            $this->addit_record_param .
	                                            "ret_url=" . rawurlencode($this->parent[0]->getRequestUri());
									$this->tpl[0]->set_var("field_url", $tmp_url);
								}
								$this->tpl[0]->parse("SectUrlBefore", false);
								$this->tpl[0]->parse("SectUrlAfter", false);
							}
						}
						else
						{
							$this->tpl[0]->set_var("SectUrlBefore", "");
							$this->tpl[0]->set_var("SectUrlAfter", "");
						}
						
						//ffErrorHandler::raise("DEBUG", E_USER_ERROR, $this, get_defined_vars());
						if (
								!isset($this->tpl[0]->DVars["Field_" . $key])
								&& !isset($this->tpl[0]->DVars["EncodedField_" . $key])
							)
						{
                            if (!$this->use_fixed_fields)
                            {
                                if (
                                        ($this->full_ajax || $this->ajax_edit)
                                        && !($this->grid_fields[$key]->url === "" || $this->grid_fields[$key]->control_type != "" || !$this->display_edit_url)
                                    )
                                {
                                    $this->parent[0]->widgetLoad("dialog");

                                    if ($this->grid_fields[$key]->url === null)
                                    {
                                        if($this->display_edit_url_alt) {
                                            $tmp_url = $this->display_edit_url_alt . "?" . $keys .
                                            $this->parent[0]->get_globals() .
                                            $this->addit_record_param .
                                            "ret_url=" . rawurlencode($this->parent[0]->getRequestUri());
                                        } else {
                                            $tmp_url = $modify_url;
                                        }
                                    }
                                    else
                                    {
                                        $tmp_url = ffProcessTags($this->grid_fields[$key]->url, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), $_SERVER['REQUEST_URI'], $this->parent[0]->get_globals(), null, $this->db[0]);
                                    }

                                    if (strpos($tmp_url, "javascript:") === 0)
                                    {
                                        $this->tpl[0]->set_var("Value", $buffer);
                                        $this->tpl[0]->set_var("field_url", $tmp_url);
                                        $this->tpl[0]->parse("SectUrlBefore", false);
                                        $this->tpl[0]->parse("SectUrlAfter", false);
                                    }
                                    else
                                    {
                                        //if($this->buttons_options["edit"]["label"] === null)
                                        //    $this->buttons_options["edit"]["label"] = ffTemplate::_get_word_by_code("ffGrid_modify_row");
                                        
                                        //if($this->buttons_options["edit"]["icon"] === null)
                                        //    $this->buttons_options["edit"]["icon"] = cm_getClassByFrameworkCss("editrow", "icon-" . $this->buttons_options["edit"]["aspect"] . "-tag");
                                         
                                        if($this->buttons_options["edit"]["class"] === null) 
                                            $this->buttons_options["edit"]["class"] = cm_getClassByFrameworkCss("editrow", $this->buttons_options["edit"]["aspect"]);        
                                               
                                        if (!$dlg_edit)
                                        {
                                            $this->parent[0]->widgets["dialog"]->process(
                                                    $this->record_id . "_edit" 
                                                    , array(
                                                            "title"          => (strlen($this->dialog_options["edit"]["title"])
                                                                                    ? $this->dialog_options["edit"]["title"]
                                                                                    : ffTemplate::_get_word_by_code("ffGrid_modify_title")  
                                                                                )
                                                            , "tpl_id"        => $this->id
                                                            , "width"        => $this->dialog_options["edit"]["width"]
                                                            , "height"        => $this->dialog_options["edit"]["height"]
                                                        )
                                                    , $this->parent[0]
                                                );
                                            $dlg_edit = true;
                                        }
                                        $this->tpl[0]->set_var("Value", "<a href=\"javascript:ff.ffPage.dialog.doOpen('" . $this->record_id . "_edit', '" . rawurlencode($tmp_url) . "');\">" 
                                                                                . $buffer 
                                                                            . "</a>");
                                    }
                                }
                                else
                                    $this->tpl[0]->set_var("Value", $buffer);
                                
                                if($this->grid_fields[$key]->data_info["field"] !== null)
                                {
                                    $data_info = $this->db[0]->getField($this->grid_fields[$key]->data_info["field"], $this->grid_fields[$key]->data_info["base_type"], true);
                                    if(strlen($data_info))
                                    {
                                        if($this->grid_fields[$key]->data_info["multilang"])
                                            $this->tpl[0]->set_var("data_info", ffTemplate::_get_word_by_code(strip_tags($this->db[0]->getField($this->grid_fields[$key]->data_info["field"], $this->grid_fields[$key]->data_info["base_type"], true))));
                                        else
                                            $this->tpl[0]->set_var("data_info", strip_tags($this->db[0]->getField($this->grid_fields[$key]->data_info["field"], $this->grid_fields[$key]->data_info["base_type"], true)));
                                        
                                        $this->tpl[0]->parse("SectDataInfo", false);
                                    }
                                    else 
                                    {
                                        $this->tpl[0]->set_var("SectDataInfo", "");
                                    }
                                }
                                else 
                                {
                                    $this->tpl[0]->set_var("SectDataInfo", "");
                                }    
                                $this->tpl[0]->parse("SectField", true);

                                $this->tpl[0]->set_var("Fvalue_" . $key, $buffer);
                                $this->tpl[0]->set_var("EncodedFvalue_" . $key, rawurlencode(ffCommon_url_normalize($buffer)));
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

					if (is_array($this->grid_buttons) && count($this->grid_buttons))
					{
						foreach($this->grid_buttons as $key => $FormButton)
						{
							//---------------------------------------------------------------------
							//---------------------------------------------------------------------
							$this->tpl[0]->set_var("col", $col); // Useful for Fixed Fields Templates
							$col++;
							$class = "";

							if (strlen($this->grid_buttons[$key]->container_class))
								$class = $this->grid_buttons[$key]->container_class;
							else
								$class = $this->column_class;

							if ($col == 1 && $this->column_class_first)
								$class .= " " . $this->column_class_first;
							elseif ($col == $totfields && $this->column_class_last)
								$class .= " " . $this->column_class_last;

							$class .= " ffButton";

							$class = str_replace("[COL]", $col, $class);
							$class = str_replace("[ID]", "delete_bt", $class);
							$class = trim($class);

							if (strlen($class))
								$class = "class=\"" . $class . "\"";

							$this->tpl[0]->set_var("col_class", $class);
							$this->tpl[0]->set_var("col_properties", $this->grid_buttons[$key]->getProperties($this->grid_buttons[$key]->container_properties));
							//---------------------------------------------------------------------
							//---------------------------------------------------------------------

							//$this->tpl[0]->set_var("data_class", $this->grid_buttons[$key]->data_class);
							//$this->tpl[0]->set_var("data_properties", $this->grid_buttons[$key]->getProperties($this->grid_buttons[$key]->data_properties));
							
							$this->tpl[0]->set_var(	  "GridButton"
													, $this->grid_buttons[$key]->process(
																	null
																	, false
																	, $this->grid_buttons[$key]->id . "_" . $i
																/*ffProcessTags($FormButton->url, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), urlencode($_SERVER['REQUEST_URI']), $this->parent[0]->get_globals())*/
																)
												   );
							$this->tpl[0]->parse("SectGridButton", true);
						}
						reset($this->grid_buttons);
					}
					else
						$this->tpl[0]->set_var("SectGridButton", "");

                                              //if($this->buttons_options["edit"]["label"] === null)
                    //    $this->buttons_options["edit"]["label"] = ffTemplate::_get_word_by_code("ffGrid_modify_row");
                    
                    if($this->buttons_options["edit"]["icon"] === null)
                        $this->buttons_options["edit"]["icon"] = cm_getClassByFrameworkCss("editrow", "icon-" . $this->buttons_options["edit"]["aspect"] . "-tag");
                     
                    if($this->buttons_options["edit"]["class"] === null) 
                        $this->buttons_options["edit"]["class"] = cm_getClassByFrameworkCss("editrow", $this->buttons_options["edit"]["aspect"]);        
                                
                    if ($this->visible_edit_bt && $this->display_edit_bt)
                    {
                        $this->tpl[0]->set_var("modify_url", ffCommon_specialchars($modify_url));
                        if ($this->full_ajax || $this->ajax_edit)
                        {
                            if (!$dlg_edit)
                            {
                                $this->parent[0]->widgetLoad("dialog");
                                $this->parent[0]->widgets["dialog"]->process(
                                        $this->record_id . "_edit" 
                                        , array(
                                                "title"          => (strlen($this->dialog_options["edit"]["title"])
                                                                        ? $this->dialog_options["edit"]["title"]
                                                                        : ffTemplate::_get_word_by_code("ffGrid_modify_title")  
                                                                    )
                                                , "tpl_id"       => $this->id
                                                , "width"        => $this->dialog_options["edit"]["width"]
                                                , "height"       => $this->dialog_options["edit"]["height"]
                                            )
                                        , $this->parent[0]
                                    );
                                $dlg_edit = true;
                            }
                            $this->tpl[0]->set_var("SectEditButtonVisible", "<a " . (strlen($this->buttons_options["edit"]["class"]) 
                                                                                        ? "class=\"" . $this->buttons_options["edit"]["class"] . "\" " 
                                                                                        : ""
                                                                                    ) 
                                                                                    . "href=\"javascript:ff.ffPage.dialog.doOpen('" . $this->record_id . "_edit', '" . rawurlencode($modify_url) . "');\">" 
                                                                                    . $this->buttons_options["edit"]["icon"] . $this->buttons_options["edit"]["label"] 
                                                                                . "</a>");
                        }
                        else
                        {
                            $this->tpl[0]->set_var("modify_label", $this->buttons_options["edit"]["label"]);
                            if(strlen($this->buttons_options["edit"]["class"]))
                                $this->tpl[0]->set_var("modify_class", ' class="' . $this->buttons_options["edit"]["class"] . '"');
                            $this->tpl[0]->set_var("modify_icon", $this->buttons_options["edit"]["icon"]);
                            $this->tpl[0]->parse("SectEditButtonVisible", false);
                        }
                    }
                    else
                        $this->tpl[0]->set_var("SectEditButtonVisible", "");

					if ($this->display_edit_bt)
					{
						//---------------------------------------------------------------------
						//---------------------------------------------------------------------
						$this->tpl[0]->set_var("col", $col); // Useful for Fixed Fields Templates
						$col++;
						$class = "";
						
						/*if (strlen($this->grid_fields[$key]->container_class))
							$class = $this->grid_fields[$key]->container_class;
						else*/
							$class = $this->column_class;
						
						if ($col == 1 && $this->column_class_first)
							$class .= " " . $this->column_class_first;
						elseif ($col == $totfields && $this->column_class_last)
							$class .= " " . $this->column_class_last;

						$class .= " ffButton";

						$class = str_replace("[COL]", $col, $class);
						$class = str_replace("[ID]", "edit_bt", $class);
						$class = trim($class);
						
						if (strlen($class))
							$class = "class=\"" . $class . "\"";
						
						$this->tpl[0]->set_var("col_class", $class);
						//$this->tpl[0]->set_var("col_properties", $this->grid_buttons[$key]->getProperties($this->grid_buttons[$key]->container_properties));
						//---------------------------------------------------------------------
						//---------------------------------------------------------------------
						
						//$this->tpl[0]->set_var("data_class", $this->grid_buttons[$key]->data_class);
						//$this->tpl[0]->set_var("data_properties", $this->grid_buttons[$key]->getProperties($this->grid_buttons[$key]->data_properties));

						$this->tpl[0]->parse("SectEditButton", false);
					}
					else
						$this->tpl[0]->set_var("SectEditButton", "");

					if ($this->visible_delete_bt)
					{
                        //if($this->buttons_options["delete"]["label"] === null)
                        //    $this->buttons_options["delete"]["label"] = ffTemplate::_get_word_by_code("ffGrid_delete_row");
                        
                        if($this->buttons_options["delete"]["icon"] === null)
                            $this->buttons_options["delete"]["icon"] = cm_getClassByFrameworkCss("deleterow", "icon-" . $this->buttons_options["delete"]["aspect"] . "-tag");
                         
                         if($this->buttons_options["delete"]["class"] === null) 
                            $this->buttons_options["delete"]["class"] = cm_getClassByFrameworkCss("deleterow", $this->buttons_options["delete"]["aspect"]);        
                        
                        if (/*!strlen($this->record_delete_url) && */($this->full_ajax || $this->ajax_delete))
                        {
                            if (!$dlg_delete)
                            {
                                $this->parent[0]->widgetLoad("dialog");
                                $this->parent[0]->widgets["dialog"]->process(
                                    $this->record_id . "_delete" 
                                    , array(
                                            "title"             => (strlen($this->dialog_options["delete"]["title"])
                                                                        ? $this->dialog_options["delete"]["title"]
                                                                        : ffTemplate::_get_word_by_code("ffGrid_delete_title") . " " . $this->title  
                                                                    )
                                            , "tpl_id"          => $this->id
                                            , "width"           => $this->dialog_options["delete"]["width"]
                                            , "height"          => $this->dialog_options["delete"]["height"]
                                        )
                                    , $this->parent[0]
                                );
                                $dlg_delete = true;
                            }
                            $this->tpl[0]->set_var("SectDeleteButtonVisible", "<a " . (strlen($this->buttons_options["delete"]["class"]) 
                                                                                        ? "class=\"" . $this->buttons_options["delete"]["class"] . "\" " 
                                                                                        : "") 
                                                                                    . "href=\"javascript:ff.ffPage.dialog.doOpen('" . $this->record_id . "_delete', '" . rawurlencode($delete_url) . "');\">" 
                                                                                        . $this->buttons_options["delete"]["icon"] . $this->buttons_options["delete"]["label"] 
                                                                            . "</a>");                        }
                        else  
                        {
                            $this->tpl[0]->set_var("delete_label", $this->buttons_options["delete"]["label"]);
                            if(strlen($this->buttons_options["delete"]["class"]))
                                $this->tpl[0]->set_var("delete_class", ' class="' . $this->buttons_options["delete"]["class"] . '"');
                            $this->tpl[0]->set_var("delete_icon", $this->buttons_options["delete"]["icon"]);
                                                        
                            $this->tpl[0]->parse("SectDeleteButtonVisible", false);
                        }
                    }
                    else
                        $this->tpl[0]->set_var("SectDeleteButtonVisible", "");

					if ($this->display_delete_bt)
					{
						//---------------------------------------------------------------------
						//---------------------------------------------------------------------
						$this->tpl[0]->set_var("col", $col); // Useful for Fixed Fields Templates
						$col++;
						$class = "";
						
						/*if (strlen($this->grid_fields[$key]->container_class))
							$class = $this->grid_fields[$key]->container_class;
						else*/
							$class = $this->column_class;
						
						if ($col == 1 && $this->column_class_first)
							$class .= " " . $this->column_class_first;
						elseif ($col == $totfields && $this->column_class_last)
							$class .= " " . $this->column_class_last;

						$class .= " ffButton";

						$class = str_replace("[COL]", $col, $class);
						$class = str_replace("[ID]", "delete_bt", $class);
						$class = trim($class);
						
						if (strlen($class))
							$class = "class=\"" . $class . "\"";
						
						$this->tpl[0]->set_var("col_class", $class);
						//$this->tpl[0]->set_var("col_properties", $this->grid_buttons[$key]->getProperties($this->grid_buttons[$key]->container_properties));
						//---------------------------------------------------------------------
						//---------------------------------------------------------------------

						//$this->tpl[0]->set_var("data_class", $this->grid_buttons[$key]->data_class);
						//$this->tpl[0]->set_var("data_properties", $this->grid_buttons[$key]->getProperties($this->grid_buttons[$key]->data_properties));
						
						$this->tpl[0]->parse("SectDeleteButton", false);
					}
					else
						$this->tpl[0]->set_var("SectDeleteButton", "");

					$i++;
					if ($this->use_paging && $i >= $this->records_per_page)
						$break_while = true;
					$this->tpl[0]->set_var("RowNumber", $i);
					$this->tpl[0]->set_var("RecordNumber", ($i + ($this->page - 1 ) * $this->records_per_page));

					if($this->switch_row_class["display"])
                    {
                        if($row_class["oddeven"] == $this->switch_row_class["first"])
                            $row_class["oddeven"] = $this->switch_row_class["second"];
                        else 
                            $row_class["oddeven"] = $this->switch_row_class["first"];
                    }
                         
                    $res = $this->doEvent("on_before_parse_record", array(&$this));

                    if (count($this->row_properties))
                    {
                    	$row_class["custom"] = $this->row_class;
                    	
                    	$row_properties = $this->row_properties;
                    	$row_properties["class"] = implode(" " , array_filter($row_class));
                    	
                        $this->tpl[0]->set_var("row_properties", $this->getProperties($row_properties));
                    }
                    
                    $rc = end($res);
					if ($rc === null)
						$this->tpl[0]->parse("SectRecord", true);

					$this->tpl[0]->set_var("SectField", "");
					$this->tpl[0]->set_var("SectGridButton", "");
				}

                // EVENT HANDLER
                $res = $this->doEvent("on_after_parse_row", array(&$this, $i));
                $rc = end($res);
                if($rc === true)
                	break;
				
			} while ($this->db[0]->nextRecord() && !$break_while);
		}
		else
		{
			//$this->tpl[0]->set_var("SectActionButtonsHeader", "");
			$this->tpl[0]->set_var("SectRecord", "");
		}

		// store value of fields in hidden section, but only for not displayed records

		if ($this->use_fields_params && is_array($this->recordset_keys) && count($this->recordset_keys))
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
					$this->parse_hidden_field();
					$aKeys[$key] = $this->recordset_keys[$rst_key][$key];
				}
				reset($this->key_fields);

				$displayed = array_search($aKeys, $this->displayed_keys);
				// if not displayed, store hidden
				if ($displayed === false)
				{
					// cycle fields
					foreach($this->grid_fields as $key => $value)
					{
						if ($this->grid_fields[$key]->control_type !== "")
						{
							$this->tpl[0]->set_var("id", "recordset_ori_values[" . $rst_key . "][" . $key . "]");
							$this->tpl[0]->set_var("value", ffCommon_specialchars($this->recordset_ori_values[$rst_key][$key]));
							$this->parse_hidden_field();

							$this->tpl[0]->set_var("id", "recordset_values[" . $rst_key . "][" . $key . "]");
							$this->tpl[0]->set_var("value", ffCommon_specialchars($this->recordset_values[$rst_key][$key]));
							$this->parse_hidden_field();
						}
					}
					reset($this->grid_fields);
				}
			}
			reset($this->recordset_keys);
		}

		$res = $this->doEvent("on_after_process_grid", array(&$this, $this->tpl[0]));

        $component_class["default"] = $this->class;
		$component_class["grid"] = cm_getClassByFrameworkCss($this->framework_css["component"]["col"], "col");
        if ($this->db[0]->query_id && $this->db[0]->numRows())
        {
            $this->tpl[0]->parse("SectGridData", false);
            $this->tpl[0]->parse("SectGrid", false);
            $this->tpl[0]->set_var("SectNoRecords", "");
        } 
        else 
        {
        	$component_class["empty"] = "norecord";
            $this->tpl[0]->set_var("SectGridData", "");
            $this->tpl[0]->set_var("SectGrid", "");
            //$this->tpl[0]->set_var("SectAlpha", "");
            //$this->tpl[0]->set_var("SectFilter", "");
            //$this->tpl[0]->set_var("SectSearch", "");
            if ($this->no_record_label !== null)
            {
                $this->tpl[0]->set_var("grid_no_record", $this->no_record_label);
            } 
            else 
            {
                $this->tpl[0]->set_var("grid_no_record", ffTemplate::_get_word_by_code("grid_no_record"));
            }
            
            $this->tpl[0]->parse("SectNoRecords", false);
        }

		$this->tpl[0]->set_var("component_class", implode(" ", array_filter($component_class)));

		if(is_array($this->framework_css["component"]["col"]) && $this->framework_css["component"]["inner_wrap"] === null)
			$this->framework_css["component"]["inner_wrap"] = "row";

	    if($this->framework_css["component"]["inner_wrap"])
		{
			$this->tpl[0]->set_var("inner_wrap_class", cm_getClassByFrameworkCss("", $this->framework_css["component"]["inner_wrap"]));
			$this->tpl[0]->parse("SectGridInnerWrapStart", false);
			$this->tpl[0]->parse("SectGridInnerWrapEnd", false);
		} 
		else
		{
			$this->tpl[0]->set_var("SectGridInnerWrapStart", "");
			$this->tpl[0]->set_var("SectGridInnerWrapEnd", "");
		}

        if($this->use_paging || $this->use_search || $this->use_alpha || $this->use_order || $this->parsed_hidden_fields || $this->use_fields_params)
            $this->tpl[0]->parse("SectHidden", false);
        else
            $this->tpl[0]->set_var("SectHidden", "");
		
	}

	/**
	 *  Elabora le label della griglia
	 */
	function process_labels()
	{
		if (!$this->display_labels)
		{
			$this->tpl[0]->set_var("SectLabels", "");
			return;
		}

		if (isset($_REQUEST["XHR_DIALOG_ID"]))
			$this->tpl[0]->set_var("dialogid", "'" . $_REQUEST["XHR_DIALOG_ID"] . "'");
		else
			$this->tpl[0]->set_var("dialogid", "undefined");

		$this->tpl[0]->set_var("turn_on_label", ffCommon_specialchars($this->turn_on_label));
		$this->tpl[0]->set_var("turn_off_label", ffCommon_specialchars($this->turn_off_label));

		$tot_labels = 0;
		$tot_labels += (is_array($this->grid_buttons) ? count($this->grid_buttons) : 0);
		$tot_labels += (is_array($this->grid_fields) ? count($this->grid_fields) : 0);

		$selected_class = $this->label_selected_class;
		$selected_class_f = ($this->label_selected_class_first ? $this->label_selected_class_first : $selected_class);
		$selected_class_l = ($this->label_selected_class_last ? $this->label_selected_class_last : $selected_class);

		$col = 0;
		$count_order = 0;
		
		foreach ($this->grid_fields as $key => $FormField)
		{
			if ($this->grid_fields[$key]->display === false)
				continue;

			$this->tpl[0]->set_var("Name", $this->grid_fields[$key]->id);

			if($this->grid_fields[$key]->display_label) {
				if ($this->grid_fields[$key]->label_encode_entities)
					$this->tpl[0]->set_var("Label", ffCommon_specialchars($this->grid_fields[$key]->label));
				else
					$this->tpl[0]->set_var("Label", $this->grid_fields[$key]->label);
			}			

			$this->tpl[0]->set_var("col", $col); // Useful for Fixed Fields Templates
			$col++;
			$class = "";
			
			if ($this->grid_fields[$key]->container_class)
				$class = $this->grid_fields[$key]->container_class;
			else
				$class = $this->column_class;
			
			if ($col == 1 && $this->column_class_first)
				$class .= " " . $this->column_class_first;
			elseif ($col == $tot_labels && $this->column_class_last)
				$class .= " " . $this->column_class_last;

			$this->tpl[0]->set_var("order", $this->grid_fields[$key]->id);
			if ($this->use_order && $this->order_method != "none" && $this->grid_fields[$key]->allow_order)
			{
				if ($this->label_class)
					$class .= " " . $this->label_class;
					
				$this->tpl[0]->set_var("width", ffCommon_specialchars($this->grid_fields[$key]->width));
				if ($this->order == $this->grid_fields[$key]->id)
				{
					if ($this->label_selected_class)
						$class .= " " . $this->label_selected_class;
					
					if ($col == 1 && $this->label_selected_class_first)
						$class .= " " . $this->label_selected_class_first;
					elseif ($col == $tot_labels && $this->label_selected_class_last)
						$class .= " " . $this->label_selected_class_last;

					if ($this->direction == "ASC")
						$this->tpl[0]->set_var("direction", "DESC");
					else
						$this->tpl[0]->set_var("direction", "ASC");
				}
				else
				{
					$this->tpl[0]->set_var("direction", $this->grid_fields[$key]->order_dir);
				}
				
				if ($this->grid_fields[$key]->display_label && ($this->order_method == "labels" || $this->order_method == "both"))
				{          
                    $this->tpl[0]->set_var("sort_class", "sort" /*cm_getClassByFrameworkCss("sort", "link")*/);

					$this->tpl[0]->parse("SectOrderLabel", false);
					$this->tpl[0]->set_var("SectNoOrder", "");
				}
				else
				{
					$this->tpl[0]->set_var("SectOrderLabel", "");
					$this->tpl[0]->parse("SectNoOrder", false);
				}

				if ($this->order_method == "icons" || $this->order_method == "both")
                { 
                    $this->tpl[0]->set_var("sort_asc_class", "sorc-asc" /*cm_getClassByFrameworkCss("sort-asc", "link")*/);
                    $this->tpl[0]->set_var("sort_desc_class", "sorc-desc" /*cm_getClassByFrameworkCss("sort-desc", "link")*/);
                    $this->tpl[0]->set_var("sort_asc_icon", cm_getClassByFrameworkCss("sort-asc", "icon-link-tag"));
                    $this->tpl[0]->set_var("sort_desc_icon", cm_getClassByFrameworkCss("sort-desc", "icon-link-tag"));
					$this->tpl[0]->parse("SectOrderIcons", false);
				} else
					$this->tpl[0]->set_var("SectOrderIcons", "");

				$this->tpl[0]->parse("SectOrder", false);
				
				$count_order++;
			}
			else
			{
				$this->tpl[0]->set_var("SectOrder", "");
				$this->tpl[0]->parse("SectNoOrder", false);
			}

			if($this->grid_fields[$key]->get_app_type() == "Text" && $this->grid_fields[$key]->extended_type != "String")
				$class .= " " . strtolower($this->grid_fields[$key]->extended_type);
			else 
				$class .= " " . strtolower($this->grid_fields[$key]->get_app_type());

			$class .= " ffField";

			$class = str_replace("[COL]", $col, $class);
			$class = str_replace("[ID]", $this->grid_fields[$key]->id, $class);
			$class = trim($class);
			
			if (strlen($class))
				$class = "class=\"" . $class . "\"";

			$this->tpl[0]->set_var("col_class", $class);
			$this->tpl[0]->set_var("col_properties", $this->grid_fields[$key]->getProperties($this->grid_fields[$key]->container_properties));

			if ($this->grid_fields[$key]->get_control_type() == "checkbox" && $this->grid_fields[$key]->display_label)
				$this->tpl[0]->parse("SectSelectAll", false);
			else
				$this->tpl[0]->set_var("SectSelectAll", "");
			
			if (!$this->use_fixed_fields)
				$this->tpl[0]->parse("SectLabel", true);
			else
				$this->tpl[0]->parse("SectLabel" . $FormField->id, false);
		}
		reset($this->grid_fields);
		
		if (is_array($this->grid_buttons) && count($this->grid_buttons))
		{
			foreach($this->grid_buttons as $key => $FormButton)
			{
				$this->tpl[0]->set_var("col", $col); // Useful for Fixed Fields Templates
				$col++;
				$class = "";
				
				if ($this->grid_buttons[$key]->container_class)
					$class = $this->grid_buttons[$key]->container_class;
				else
					$class = $this->column_class;
				
				if ($col == 1 && $this->column_class_first)
					$class .= " " . $this->column_class_first;
				elseif ($col == $tot_labels && $this->column_class_last)
					$class .= " " . $this->column_class_last;
				
				$class .= " ffButton";

				$class = str_replace("[COL]", $col, $class);
				$class = str_replace("[ID]", $this->grid_buttons[$key]->id, $class);
				$class = trim($class);
				
				if (strlen($class))
					$class = "class=\"" . $class . "\"";

				$this->tpl[0]->set_var("col_class", $class);
				$this->tpl[0]->set_var("col_properties", $this->grid_buttons[$key]->getProperties($this->grid_buttons[$key]->container_properties));

				$this->tpl[0]->parse("SectButtonLabel", true);
			}
			reset($this->grid_buttons);
		}
		else
			$this->tpl[0]->set_var("SectButtonLabel", "");

		if ($this->display_edit_bt)
		{
			$this->tpl[0]->set_var("col", $col); // Useful for Fixed Fields Templates
			$col++;
			$class = "";

			/*if (strlen($this->grid_buttons[$key]->container_class))
				$class = $this->grid_buttons[$key]->container_class;
			else*/
				$class = $this->column_class;

			if ($col == 1 && $this->column_class_first)
				$class .= " " . $this->column_class_first;
			elseif ($col == $totfields && $this->column_class_last)
				$class .= " " . $this->column_class_last;

			$class .= " ffButton";

			$class = str_replace("[COL]", $col, $class);
			$class = str_replace("[ID]", "edit_bt", $class);
			$class = trim($class);

			if (strlen($class))
				$class = "class=\"" . $class . "\"";

			$this->tpl[0]->set_var("col_class", $class);
			//$this->tpl[0]->set_var("col_properties", $this->grid_buttons[$key]->getProperties($this->grid_buttons[$key]->container_properties));
			
			$this->tpl[0]->parse("SectEditLabel", false);
		}
		else
			$this->tpl[0]->set_var("SectEditLabel", "");

		if ($this->display_delete_bt)
		{
			$this->tpl[0]->set_var("col", $col); // Useful for Fixed Fields Templates
			$col++;
			$class = "";

			/*if (strlen($this->grid_buttons[$key]->container_class))
				$class = $this->grid_buttons[$key]->container_class;
			else*/
				$class = $this->column_class;

			if ($col == 1 && $this->column_class_first)
				$class .= " " . $this->column_class_first;
			elseif ($col == $totfields && $this->column_class_last)
				$class .= " " . $this->column_class_last;

			$class .= " ffButton";

			$class = str_replace("[COL]", $col, $class);
			$class = str_replace("[ID]", "delete_bt", $class);
			$class = trim($class);

			if (strlen($class))
				$class = "class=\"" . $class . "\"";

			$this->tpl[0]->set_var("col_class", $class);
			//$this->tpl[0]->set_var("col_properties", $this->grid_buttons[$key]->getProperties($this->grid_buttons[$key]->container_properties));
			
			$this->tpl[0]->parse("SectDeleteLabel", false);
		}
		else
			$this->tpl[0]->set_var("SectDeleteLabel", "");

			
		if($this->use_order && $count_order) 
		{
			$this->tpl[0]->parse("SectSort", false);
			$this->tpl[0]->parse("SectSortJS", false);
		}

		$this->tpl[0]->parse("SectLabels", false);
	}

	/**
	 * Elabora il page navigator
	 */
	function process_navigator()
	{
        $this->navigator[0]->parent[0] = $this;

        $this->navigator[0]->page = $this->page;
        $this->navigator[0]->records_per_page = $this->records_per_page;
        $this->navigator[0]->nav_selector_elements = $this->nav_selector_elements;
        if ($this->db[0]->query_id)
            $this->navigator[0]->num_rows = $this->db[0]->numRows();

        if (!$this->display_navigator || !$this->use_paging || ($this->navigator[0]->num_rows <= $this->navigator[0]->records_per_page && !($this->ajax_search || $this->full_ajax)))
        {
        	$this->tpl[0]->set_var("SectHiddenPageNavigator", "");
			$this->tpl[0]->set_var("SectPageNavigator", "");
			$this->tpl[0]->set_var("SectPaginatorTop", "");
			$this->tpl[0]->set_var("SectPaginatorBottom", "");
			return;
		}
		$this->navigator[0]->doAjax = $this->navigator_doAjax;
		$this->navigator[0]->nav_display_selector = $this->navigator_display_selector;
        $this->tpl[0]->set_var("page_parname", $this->navigator[0]->page_parname);
        $this->tpl[0]->set_var("records_per_page_parname", $this->navigator[0]->records_per_page_parname);
        $this->tpl[0]->set_var("page_per_frame_parname", $this->navigator[0]->page_per_frame_parname);

        $this->tpl[0]->set_var("page_per_frame", $this->navigator[0]->PagePerFrame);

		/*if($this->navigator[0]->num_rows >  $this->navigator[0]->records_per_page)
		{*/
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
		//}
		$this->tpl[0]->parse("SectHiddenPageNavigator", false);
	}

    /**
	 * Inizializza i controlli di default della griglia
	 */
	function initControls()
    {
		// PREPARE DEFAULT BUTTONS
		if ($this->buttons_options["search"]["display"])
		{
			if(0 && $this->buttons_options["search"]["label"] === null) //RICHIESTA DEL GIOVINE
				$this->buttons_options["search"]["label"] = ffTemplate::_get_word_by_code("ffGrid_search");

			if ($this->buttons_options["search"]["obj"] !== null)
			{
				$this->addSearchButton(	  $this->buttons_options["search"]["obj"]
										, $this->buttons_options["search"]["index"]);
			}
			else
			{
				$tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->theme);
				$tmp->id 			= "search";
                $tmp->label         = $this->buttons_options["search"]["label"];
                $tmp->class         = $this->buttons_options["search"]["class"];
                $tmp->icon          = $this->buttons_options["search"]["icon"];
				$tmp->aspect 		= $this->buttons_options["search"]["aspect"];
				$tmp->action_type 	= "submit";
				$tmp->frmAction		= "search";
				if (isset($_REQUEST["XHR_DIALOG_ID"]))
				{
					$tmp->jsaction = ($this->reset_page_on_search ? " jQuery('#" . $this->prefix . $this->navigator[0]->page_parname . "').val('1'); " : "") . ($this->open_adv_search === true || $this->open_adv_search == "always" ? "" : " ff.ffGrid.advsearchHide('" . $this->id . "_', '" . $this->id . "'); ") . " ff.ffPage.dialog.doRequest('" . $_REQUEST["XHR_DIALOG_ID"] . "',{'action'	: '" . $this->id . "_search','component' : '" . $this->id . "','section'	: 'GridData'});";
				} 
				elseif ($this->full_ajax || $this->ajax_search)
				{
					if ($this->parent !== NULL) 
                    {//code for ff.js
						//$this->parent[0]->tplAddJs("jquery.blockui", "jquery.blockui.js", FF_THEME_DIR . "/library/plugins/jquery.blockui");
						$this->parent[0]->tplAddJs("ff.ajax", "ajax.js", FF_THEME_DIR . "/library/ff");
					}

					$tmp->jsaction = ($this->reset_page_on_search ? " jQuery('#" . $this->prefix . $this->navigator[0]->page_parname . "').val('1'); " : "")  . ($this->open_adv_search === true || $this->open_adv_search == "always" ? "" : " ff.ffGrid.advsearchHide('" . $this->id . "_', '" . $this->id . "'); ") . " ff.ajax.doRequest({'component' : '" . $this->id . "','section' : 'GridData'});";
				}
				$this->addSearchButton(	  $tmp
										, $this->buttons_options["search"]["index"]);
			}
		}
        
		if ($this->buttons_options["export"]["display"])
		{
			if($this->buttons_options["export"]["label"] === null)
				$this->buttons_options["export"]["label"] = ffTemplate::_get_word_by_code("ffGrid_export");

			if ($this->buttons_options["export"]["obj"] !== null)
			{
				$this->addActionButtonHeader($this->buttons_options["export"]["obj"]);
			}
			else
			{
				$tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->theme);
				$tmp->id 			= "export";
                $tmp->label         = $this->buttons_options["export"]["label"];
                $tmp->class         = $this->buttons_options["export"]["class"];
                $tmp->icon          = $this->buttons_options["export"]["icon"];
				$tmp->aspect 		= $this->buttons_options["export"]["aspect"];
				$tmp->action_type 	= "submit";
				$tmp->frmAction		= "export";
				//$tmp->aspect 		= "link";
				//$tmp->action_type 	= "gotourl";
				//$tmp->form_action_url = $this->parent[0]->getRequestUri() . "&" . $this->id . "_t=xls";
				//$tmp->jsaction = "ff.ajax.doRequest({'component' : '" . $this->id . "', 'addFields' : '" . $this->id . "t=xls'});";
				//$tmp->class 		.= "noactivebuttons";
				//$tmp->url = $this->parent[0]->getRequestUri() . "&" . $this->id . "_t=xls";
				$this->addActionButtonHeader($tmp);
			}
		}
	}
	
	public function structProcess($tpl)
	{
	}
}
