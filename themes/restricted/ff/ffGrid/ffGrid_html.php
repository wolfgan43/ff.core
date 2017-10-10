<?php
class ffGrid_html extends ffGrid_base 
{   
	var $id_if					= null;
	
    var $dialog_delete_url = null;
    var $dialog_action_button = false;
	
	var $cursor_dialog = false;
    
    /**
     * La label del tasto "turnon"
     * usato in presenza di checkbox
     * @var String
     */
    var $turn_on_label    = "ON";
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
    var $full_ajax        = false;
    /**
     * Se dev'essere usata la funzionalità di aggiunta ajax con dialog
     * @var Boolean
     */
    var $ajax_addnew    = false;
    /**
     * Se dev'essere usata la funzionalità di editing ajax con dialog
     * @var Boolean
     */
    var $ajax_edit        = false;
    /**
     * Se dev'essere usata la funzionalità di cancellazione ajax con dialog
     * @var Boolean
     */
    var $ajax_delete    = false;
    /**
     * Se dev'essere usata la funzionalità di ricerca ajax con dialog
     * @var Boolean
     */
    var $ajax_search    = false;
    
    var $ajax_update_all = false;
    
    var $dialog_options = array(
        "add" => array(
            "width" => ""
            , "height" => ""
        )
        , "edit" => array(
            "width" => ""
            , "height" => ""
        )
    );
    
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
    var $column_class_last            = "";
    /**
     * La classe da assegnare alle label di ordinamento
     * @var String
     */
    var $label_class                = "";
    /**
     * La classe da assegnare alle label di ordinamento quando selezionate
     * @var String
     */
    var $label_selected_class        = "";
    /**
     * La classe da assegnare alla prima label di ordinamento quando selezionata
     * @var String
     */
    var $label_selected_class_first    = "";
    /**
     * La classe da assegnare all'ultima label di ordinamento quando selezionata
     * @var String
     */
    var $label_selected_class_last    = "";
    /**
     * Se visualizzare l'url di editing sulla griglia
     * @var Boolean
     */
    var $display_edit_url        = true;            // display edit record url (on fields)
    var $display_edit_url_alt   = "";
    /**
     * Se visualizzare il pulsante di editing sulla griglia
     * @var Boolean
     */
    var $display_edit_bt        = false;        // display edit record button
    /**
     * Se visualizzare il pulsante di cancellazione sulla griglia
     * @var Boolean
     */
    var $display_delete_bt        = true;            // display delete record button. This cause use of dialog.
    /**
     * Se il pulsante di editing dev'essere visibile sulla riga processata attualmente
     * @var Boolean
     */
    var $visible_edit_bt        = true;            // display edit record button record per record
    /**
     * Se il pulsante di eliminazione dev'essere visibile sulla riga processata attualmente
     * @var Boolean
     */
    var $visible_delete_bt        = true;            // display delete record button record per record
    /**
     * Il simbolo usato per visualizzare i campi valuta
     * @var String
     */
    var $symbol_valuta          = "&euro; ";
    /**
     * Se visualizzare il campo di ricerca semplice
     * @var Boolean
     */
    var $display_search_simple     = true;
    /**
     * Le opzioni del campo di ricerca semplice
     * @var Array
     */
    var $search_simple_field_options    = array(
                                                "id" => "searchall"
                                                , "label"     => ""
                                                , "src_operation" => " [NAME] LIKE([VALUE]) "
                                                , "src_prefix"     => "%"
                                                , "src_postfix"     => "%"
                                                , "obj"     => null
                                                , "index"     => 0
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
    
    private $parsed_hidden_fields     = 0;
    /**
     * Variabile ad uso interno
     */
    private $parsed_fields         = 0;
    /**
     * Variabile ad uso interno
     */
    private $parsed_filters        = 0;

	var $js_deps = array(
		"ff.ffGrid" => null
	);
	
	/**
     * Il costruttore dell'oggetto
     * da non richiamare direttamente
     * @param ffPage_base $page
     * @param String $disk_path
     * @param String $theme 
     */
    function __construct(ffPage_base $page, $disk_path, $theme)
    {
        parent::__construct($page, $disk_path, $theme);

		if (FF_THEME_RESTRICTED_RANDOMIZE_COMP_ID)
			$this->id_if = uniqid();
		
        if ($this->display_search_simple)
        {
            if ($this->search_simple_field_options["obj"] !== null)
            {
                $this->addSearchField($this->search_simple_field_options["obj"]);
            }
            else
            {
                $tmp = ffField::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->theme);
                $tmp->id			= $this->search_simple_field_options["id"];
                $tmp->label			= $this->search_simple_field_options["label"];
                $tmp->src_operation = $this->search_simple_field_options["src_operation"];
                $tmp->src_prefix	= $this->search_simple_field_options["src_prefix"];
                $tmp->src_postfix	= $this->search_simple_field_options["src_postfix"];
                $tmp->data_type		= "";
                $tmp->display		= false;
                $this->addSearchField($tmp);
            }
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
        $this->tpl[0]->set_var("fixed_body_content", $this->fixed_body_content);
        
        $this->tpl[0]->set_var("SectHiddenField", "");
    
        $this->tpl[0]->set_var("component", $this->getPrefix());
        $this->tpl[0]->set_var("component_id", $this->getIDIF());
    
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
            $tmp_sqlHaving = "";
            foreach ($this->grid_fields AS $key => $value) 
            {
                if ($this->grid_fields[$key]->data_type == "" || $this->grid_fields[$key]->skip_search)
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
					if ($this->grid_fields[$key]->crypt)
					{
						$tmp_where_value = $this->search_fields[$this->search_simple_field_options["id"]]->value->getValue();
						
						if ($this->grid_fields[$key]->crypt_modsec)
						{
							$tmp_where_value = mod_sec_crypt_string($tmp_where_value);
						}
						
						if (is_array($this->grid_fields[$key]->src_fields) && count($this->grid_fields[$key]->src_fields))
							$tmp_sql .= " ( ";

						$tmp_sql = " " . $tblprefix . "`" . $this->grid_fields[$key]->get_data_source() . "` = UNHEX(" . $this->db[0]->toSql(bin2hex($tmp_where_value)) . ") ";
						
						if (is_array($this->grid_fields[$key]->src_fields) && count($this->grid_fields[$key]->src_fields))                        
						{
							foreach ($this->grid_fields[$key]->src_fields AS $addfld_key => $addfld_value)
							{                                                                                                                                                                                                                               
								$tmp_where_part = " " . $tblprefix . "`" . $this->grid_fields[$addfld_key]->get_data_source() . "` = UNHEX(" . $this->db[0]->toSql(bin2hex($tmp_where_value)) . ") ";
								$tmp_sql .= " OR " . $tmp_where_part . " ";                                                                                                                                                                                                                      
							}
							reset($this->grid_fields[$key]->src_fields);                                                                                                                                                                                                                                                                                                                                                                                        
							$tmp_sql .= " ) ";
						}
					}
					else
					{
						if($this->search_fields[$this->search_simple_field_options["id"]]->src_prefix || $this->search_fields[$this->search_simple_field_options["id"]]->src_postfix)
						{
							$tmp_where_value = $this->db[0]->toSql($this->search_fields[$this->search_simple_field_options["id"]]->value, $this->search_fields[$this->search_simple_field_options["id"]]->base_type, false);
							if ($this->grid_fields[$key]->crypt)
							{
								if ($this->grid_fields[$key]->crypt_modsec)
								{
									$tmp_where_value = mod_sec_crypt_string($tmp_where_value);
								}
							}
							$tmp_where_value = "'" . $this->search_fields[$this->search_simple_field_options["id"]]->src_prefix . $tmp_where_value . $this->search_fields[$this->search_simple_field_options["id"]]->src_postfix . "'";
						}
						else
						{
							$tmp_where_value = $this->db[0]->toSql($this->search_fields[$this->search_simple_field_options["id"]]->value);
						}
					
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
	 * alpha template processing function
	 * called by process_interface()
	 */
	function process_alpha()
	{
		if (!$this->use_alpha)
		{
			$this->tpl[0]->set_var("SectAlpha", "");
			return;
		}

		$this->tpl[0]->set_var("actual_alpha", ffCommon_specialchars($this->alpha));
		$this->tpl[0]->set_var("field", ffCommon_specialchars($this->search_fields[$this->alpha_field]->label));
		$this->tpl[0]->set_var("alpha_selected_" . $this->alpha, "selected");
		
		$this->tpl[0]->parse("SectAlpha", false);
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
					document.getElementById('frmAction').value = '" . $this->getIDIF() . "_search';
					jQuery('#" . $this->getPrefix() . $this->navigator[0]->page_parname . "').val('1');  
					" . ($this->open_adv_search === true || $this->open_adv_search == "always" 
						? "" 
						: "ff.ffGrid.advsearchHide('" . $this->getPrefix() . "', '" . $this->getIDIF() . "'); "
					) . "
					" .  ($_REQUEST["XHR_CTX_TYPE"] === "dialog" ? 
								"ff.ajax.ctxDoRequest('" . $_REQUEST["XHR_CTX_ID"] . "', {'action'    : '" . $this->getIDIF() . "_search','component' : '" . $this->getIDIF() . "','section'    : 'GridData'});"
							: 
								(!$this->full_ajax && !$this->ajax_search ? "jQuery('#frmMain').submit();" : "ff.ajax.doRequest({'component' : '" . $this->getIDIF() . "','section' : 'GridData'});")
						) . "
					return false;
				}";

                $this->tpl[0]->set_var("SearchAll", $this->search_fields[$this->search_simple_field_options["id"]]->process($this->search_simple_field_options["id"] . "_src"));
                $this->tpl[0]->parse("SectSearchSimple", false);
            }
    
            if($this->open_adv_search === "never" || (!$this->searched && $this->open_adv_search === false))                                                                                                                                                    
                $this->tpl[0]->set_var("advsearch_visibility", " hidden");
             
            $this->tpl[0]->parse("SezAdvSearch", false);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        
        }
        
		if (!$this->use_search)
		{
			$this->tpl[0]->set_var("SectSearch", "");
			return;
		}

		$this->tpl[0]->set_var("maxspan", ($this->search_cols * 2));
		$this->tpl[0]->set_var("search_method", $this->search_method);
		$this->tpl[0]->set_var("search_url", $this->search_url);

		$col = 1; $last_span = 0;

		$this->parsed_fields = 0;
		$this->parsed_filters = 0;
		
		foreach ($this->search_fields as $key => $value)
		{
			if (!$this->search_fields[$key]->display)
				continue;

			$class = $this->search_fields[$key]->container_class;
			if(!strlen($class))
				$class = $this->column_class;
			
			if(strlen($class)) {
				$this->tpl[0]->set_var("container_class", " " . $class);
			} else {
				$this->tpl[0]->set_var("container_class", "");
			}
			if ($this->search_fields[$key]->multi_disp_as_filter)
			{
				$this->parsed_filters++;
				
				$this->tpl[0]->set_var("id", $this->search_fields[$key]->id . "_src");
				$this->tpl[0]->set_var("value", ffCommon_specialchars($this->search_fields[$key]->value->getValue(null, FF_SYSTEM_LOCALE)));
				$this->parse_hidden_field();
				
				$this->tpl[0]->set_var("SectFilterRow", "");
				$this->tpl[0]->set_var("SectFilterElement", "");
				$this->tpl[0]->set_var("SectSelFilterElement", "");
				$this->tpl[0]->set_var("SectNoElements", "");
				
				$this->tpl[0]->set_var("FilterElement_id", $this->search_fields[$key]->id . "_src");
				$this->tpl[0]->set_var("Filter_title", $this->search_fields[$key]->label);
				
				$this->search_fields[$key]->pre_process();
				
				$tmp_field_value = $this->search_fields[$key]->value->getValue($this->search_fields[$key]->get_app_type(), $this->search_fields[$key]->get_locale());
				
				if ($this->search_fields[$key]->multi_select_one && !$this->search_fields[$key]->multi_limit_select)
				{
					$this->tpl[0]->set_var("FilterElement_label", $this->search_fields[$key]->multi_select_one_label);
					
					if ($this->search_fields[$key]->multi_select_one_val !== null)
					{
						$tmp_value = $this->search_fields[$key]->multi_select_one_val->getValue($this->search_fields[$key]->get_app_type(), $this->search_fields[$key]->get_locale());
					}
					else
					{
						$tmp_value = "";
					}
					
					$this->tpl[0]->set_var("FilterElement_value", $tmp_value);
					
					if ($tmp_value == $tmp_field_value)
					{
						$this->tpl[0]->set_var("SectFilterElement", "");
						$this->tpl[0]->parse("SectSelFilterElement", false);
					}
					else
					{
						$this->tpl[0]->parse("SectFilterElement", false);
						$this->tpl[0]->set_var("SectSelFilterElement", "");
					}
					
					$this->tpl[0]->parse("SectFilterRow", true);
				}
	
				if ($this->search_fields[$key]->multi_select_noone &&
						(!$this->search_fields[$key]->multi_limit_select ||
							($this->search_fields[$key]->multi_limit_select && $this->search_fields[$key]->multi_select_noone_val->getValue($this->search_fields[$key]->get_app_type(), $this->search_fields[$key]->get_locale()) == $tmp_field_value)
						)
					)
				{
					$this->tpl[0]->set_var("FilterElement_label", $this->search_fields[$key]->multi_select_noone_label);
					
					if ($this->search_fields[$key]->multi_select_noone_val !== null)
						$tmp_value = $this->search_fields[$key]->multi_select_noone_val->getValue($this->search_fields[$key]->get_app_type(), $this->search_fields[$key]->get_locale());
					else
						$tmp_value = "";
					
					$this->tpl[0]->set_var("FilterElement_value", $tmp_value);
					
					if ($tmp_value == $tmp_field_value)
					{
						$this->tpl[0]->set_var("SectFilterElement", "");
						$this->tpl[0]->parse("SectSelFilterElement", false);
					}
					else
					{
						$this->tpl[0]->parse("SectFilterElement", false);
						$this->tpl[0]->set_var("SectSelFilterElement", "");
					}
					
					$this->tpl[0]->parse("SectFilterElement", false);
					$this->tpl[0]->parse("SectFilterRow", true);
				}
	
				
				if (is_array($this->search_fields[$key]->recordset) && count($this->search_fields[$key]->recordset))
				{
					foreach ($this->search_fields[$key]->recordset as $sub_key => $sub_item)
					{
						list($tmp, $item_key) = each($sub_item);
						list($tmp, $item_value) = each($sub_item);
						
						$this->tpl[0]->set_var("FilterElement_value", $item_key->getValue($this->search_fields[$key]->get_app_type(), $this->search_fields[$key]->get_locale()));
						$this->tpl[0]->set_var("FilterElement_label", $item_value->getValue($this->search_fields[$key]->get_multi_app_type(), $this->search_fields[$key]->get_locale()));
						
						if ($item_key->getValue($this->search_fields[$key]->get_app_type(), $this->search_fields[$key]->get_locale()) == $tmp_field_value)
						{
							$this->tpl[0]->set_var("SectFilterElement", "");
							$this->tpl[0]->parse("SectSelFilterElement", false);
						}
						else
						{
							$this->tpl[0]->parse("SectFilterElement", false);
							$this->tpl[0]->set_var("SectSelFilterElement", "");
						}
						
						$this->tpl[0]->parse("SectFilterRow", true);
					}
					reset($this->search_fields[$key]->recordset);
				}
				else
				{
					$this->tpl[0]->parse("SectNoElements", "");
				}
				
				$this->tpl[0]->parse("SectFilter", true);
			}
			elseif($this->display_search)
			{
				if ($this->search_fields[$key]->src_interval)
				{
					$this->parsed_fields++;

					// --------------------------
					//     Process From Field
					$col += $last_span;
					$last_span = 1;
					if ($col > $this->search_cols)
					{
						$this->tpl[0]->parse("SectSearchRow", true);
						$this->tpl[0]->set_var("SectSearchCol", "");
						$col = 1;
					}
					$this->tpl[0]->set_var("colspan", 1);

					// display label
					$this->tpl[0]->set_var("Label", ffCommon_specialchars($this->search_fields[$key]->interval_from_label));
					// display control
					$this->tpl[0]->set_var("Control", $this->search_fields[$key]->process($this->search_fields[$key]->id . "_from_src", $this->search_fields[$key]->interval_from_value));
					$this->tpl[0]->parse("SectSearchCol", true);

					// --------------------------
					//     Process To Field
					$col += $last_span;
					$last_span = 1;
					if ($col > $this->search_cols)
					{
						$this->tpl[0]->parse("SectSearchRow", true);
						$this->tpl[0]->set_var("SectSearchCol", "");
						$col = 1;
					}
					// display label
					$this->tpl[0]->set_var("Label", ffCommon_specialchars($this->search_fields[$key]->interval_to_label));
					// display control
					$this->tpl[0]->set_var("Control", $this->search_fields[$key]->process($this->search_fields[$key]->id . "_to_src", $this->search_fields[$key]->interval_to_value));
					$this->tpl[0]->parse("SectSearchCol", true);
				}
				else
				{
					$this->parsed_fields++;
					
					$col += $last_span;
					$last_span = $this->search_fields[$key]->span;
					if ($col > $this->search_cols)
					{
						$this->tpl[0]->parse("SectSearchRow", true);
						$this->tpl[0]->set_var("SectSearchCol", "");
						$col = 1;
					}

					$this->tpl[0]->set_var("colspan", (1 + ($this->search_fields[$key]->span - 1) * 2));

					// display label
					$this->tpl[0]->set_var("Label", ffCommon_specialchars($this->search_fields[$key]->label));

					// display control
					$this->tpl[0]->set_var("Control", $this->search_fields[$key]->process($this->search_fields[$key]->id . "_src"));
					$this->tpl[0]->parse("SectSearchCol", true);
				}
			}
		}
		reset($this->search_fields);
		
		if($this->display_search) 
		{
			$this->tpl[0]->parse("SectSearchRow", true);
			
			$show_section = false;

			//$this->initControls();

			// PROCESS ALL BUTTONS
			$buffer = "";
			if (is_array($this->search_buttons) && count($this->search_buttons))
			{
				$show_section = true;
				foreach ($this->search_buttons as $key => $value)
				{
					if (strlen($buffer))
						$buffer = "&nbsp;" . $buffer;
					$buffer = $this->search_buttons[$key]->process() . $buffer;
				}
				reset($this->search_buttons);
			}

			if ($show_section)
			{
				$this->tpl[0]->set_var("SearchButtons", $buffer);
				$this->tpl[0]->parse("SectSearchButtons", false);
			}
			else
				$this->tpl[0]->set_var("SectSearchButtons", "");

			if ($this->parsed_fields)
				$this->tpl[0]->parse("SectFields", false);
				
			if ($this->search_container === null)
				$this->tpl[0]->parse("SectSearch", false);
			else
			{
				$this->search_container_buffer = $this->tpl[0]->rpparse("SectSearch", false);
				$this->tpl[0]->set_var("SectSearch", "");
			}
		}

		if ($this->parsed_filters)
			$this->tpl[0]->parse("SectFilters", false);
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
        $this->process_labels();

        parent::process_grid();

        if ($this->display_new) // done at this time due to maxspan
        {
            if (strlen($this->bt_insert_url))
            {
                $temp_url = ffProcessTags($this->bt_insert_url, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), $_SERVER['REQUEST_URI'], $this->parent[0]->get_globals(), null, $this->db[0]);
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

/*            $res = $this->doEvent("onUrlInsert", array(&$this, $temp_url));
            $rc = end($res);
                if ($rc !== null)
                    $temp_url = $rc;
*/
            $res = ffGrid::doEvent("onProcessBtAddNew", array(&$this, &$temp_url));
            $rc = end($res);
			if (!$rc)
			{
				$res = $this->doEvent("onProcessBtAddNew", array(&$this, &$temp_url));
				$rc = end($res);
			}
			
			if (!$rc)
			{
				if ($this->full_ajax || $this->ajax_addnew)
				{
					$this->parent[0]->widgetLoad("dialog");
					$this->tpl[0]->set_var("addnew_bt", $this->parent[0]->widgets["dialog"]->process(
							$this->getIDIF()
							, array(
									"title"            => ffCommon_specialchars(($this->buttons_options["addnew"]["label"] ? $this->buttons_options["addnew"]["label"] : ffTemplate::_get_word_by_code("ffGrid_addnew")))
									, "url"            => $temp_url
									, "class"        => $this->buttons_options["addnew"]["class"]
									, "name"        => ($this->buttons_options["addnew"]["label"] ? $this->buttons_options["addnew"]["label"] : ffTemplate::_get_word_by_code("ffGrid_addnew"))
									, "tpl_id"        => $this->getIDIF()
									, "width"        => $this->dialog_options["add"]["width"]
									, "height"        => $this->dialog_options["add"]["height"]
									, "params"        => $this->dialog_options["add"]["params"]
								)
							, $this->parent[0]
						));
					$this->tpl[0]->parse("SectAddNewBt", false);
						$this->tpl[0]->set_var("SectAddNewUrl", "");
				}
				else
				{
					$this->tpl[0]->set_var("addnew_url", ffCommon_specialchars($temp_url));
					$this->tpl[0]->set_var("addnew_label", ($this->buttons_options["addnew"]["label"] ? $this->buttons_options["addnew"]["label"] : ffTemplate::_get_word_by_code("ffGrid_addnew")));
					$this->tpl[0]->parse("SectAddNewUrl", false);
					$this->tpl[0]->set_var("SectAddNewBt", "");
				}
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
			$rows = $this->db[0]->numRows();
			$rrow = 0;
			
            if (!$this->pagination_save_memory_in_use)
			{
				if ($this->rrow !== false)
				{
					$this->db[0]->seek($this->rrow);
				}
				elseif ($this->use_paging)
				{
					$this->db[0]->jumpToPage($this->page, $this->records_per_page);
				}
			}
			
            if ($this->rrow === false)
				$rrow = $this->rrow;
            else if ($this->use_paging)
				$rrow = $this->records_per_page * ($this->page - 1);
			
            $i = 0;
            $break_while = false;
            $actual_row_class = "";
            do
            {
                $this->tpl[0]->set_var("row", $i);
                $this->tpl[0]->set_var("rrow", $i + 1);

                $res = $this->doEvent("on_load_row", array(&$this, $this->db[0], $i));
                
                /* Step 1: retrieve values (done in 2 steps due to events needs) */

                $keys = "";
				
				$dlg_edit = false;
				$dlg_delete = false;
				
                //$unicKey = $i;
                if (count($this->key_fields))
                {
                    // find global recordset corrispondency (if one)
                    $aKeys = array();
                    foreach($this->key_fields as $key => $FormField)
                    {
                        $this->key_fields[$key]->value = $this->db[0]->getField($this->key_fields[$key]->get_data_source(), $this->key_fields[$key]->base_type);
                        $aKeys[$key] = $this->key_fields[$key]->value->getValue(null, FF_SYSTEM_LOCALE);
                        $keys .= "keys[" . $this->key_fields[$key]->id . "]=" . $this->key_fields[$key]->value->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE) . "&";
                        //$unicKey .= $this->key_fields[$key]->value->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE);
                        
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

                    $recordset_key = array_search($aKeys, $this->recordset_keys, true);
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
				
				parent::processRow();

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
									($this->cursor_dialog ? 
										"cursor[id]=" . rawurlencode($this->getIDIF()) . "&" .
										"cursor[rrow]=" . rawurlencode($rrow + $i) . "&" .
										"cursor[rows]=" . rawurlencode($rows) . "&"
										: "") .
                                    "ret_url=" . rawurlencode($this->parent[0]->getRequestUri());
//										"cursor[url]=" . rawurlencode($_SERVER["REQUEST_URI"]) . "&" .
                    }
                    $modify_url = ffProcessTags($modify_url, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), $_SERVER['REQUEST_URI'], $this->parent[0]->get_globals(), null, $this->db[0]);

                    $cancelurl = $this->parent[0]->getRequestUri();
                    if (strlen($this->dialog_delete_url))
                    {
                        $delete_url = ffProcessTags($this->dialog_delete_url, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), $_SERVER['REQUEST_URI'], $this->parent[0]->get_globals(), null, $this->db[0]);
                    }
                    else if (strlen($this->record_delete_url))
                    {
                        $confirmurl = $this->record_delete_url . "?" . $keys .
                                        $this->parent[0]->get_globals() .
                                        $this->addit_record_param .
                                        $this->record_id . "_frmAction=confirmdelete&" .
                                        "ret_url=" . rawurlencode($this->parent[0]->getRequestUri());
                        $confirmurl = ffProcessTags($confirmurl, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), $_SERVER['REQUEST_URI'], $this->parent[0]->get_globals(), null, $this->db[0]);

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
                            $confirmurl = ($this->record_url_delete ? $this->record_url_delete : $this->record_url) . "?" . $keys .
                                        $this->parent[0]->get_globals() .
                                        $this->addit_record_param .
                                        $this->record_id . "_frmAction=confirmdelete&" .
                                        "ret_url=" . rawurlencode($this->parent[0]->getRequestUri());
                        }
                        $confirmurl = ffProcessTags($confirmurl, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), $_SERVER['REQUEST_URI'], $this->parent[0]->get_globals(), null, $this->db[0]);

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

                        if(strlen($this->symbol_valuta) && $this->grid_fields[$key]->app_type == "Currency")
                        {
                            $this->tpl[0]->set_var("euro", $this->symbol_valuta);
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
                                $field_url = ffProcessTags($this->grid_fields[$key]->url, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), $_SERVER['REQUEST_URI'], $this->parent[0]->get_globals(), null, $this->db[0]);
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

									$res = ffGrid::doEvent("onProcessBtEdit", array(&$this, &$tmp_url, &$buffer));
									$rc = end($res);
									if (!$rc)
									{
										$res = $this->doEvent("onProcessBtEdit", array(&$this, &$tmp_url, &$buffer));
										$rc = end($res);
									}

									if (!$rc)
									{
										if (strpos($tmp_url, "javascript:") === 0)
										{
											$this->tpl[0]->set_var("Value", $buffer);
											$this->tpl[0]->set_var("field_url", $tmp_url);
											$this->tpl[0]->parse("SectUrlBefore", false);
											$this->tpl[0]->parse("SectUrlAfter", false);
										}
										else
										{
											if (!$dlg_edit)
											{
												$this->parent[0]->widgetLoad("dialog");
												$this->parent[0]->widgets["dialog"]->process(
														$this->record_id . "_edit" 
														, array(
																"title"            => ffCommon_specialchars("Modifica " . $this->title)
																, "tpl_id"        => $this->getIDIF()
																, "width"        => $this->dialog_options["edit"]["width"]
																, "height"        => $this->dialog_options["edit"]["height"]
																, "params"        => $this->dialog_options["edit"]["params"]
															)
														, $this->parent[0]
													);
												$dlg_edit = true;
											}
											$this->tpl[0]->set_var("Value", "<a " . (strlen($this->buttons_options["edit"]["class"] && 0) ? "class=\"" . $this->buttons_options["edit"]["class"] . "\" " : "") . "href=\"javascript:ff.ffPage.dialog.doOpen('" . $this->record_id . "_edit', '" . rawurlencode($tmp_url) . "');\">" . $buffer . "</a>");
										}
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
                            
                            $this->tpl[0]->set_var(      "GridButton"
                                                    , $this->grid_buttons[$key]->process(
                                                                    null
                                                                    , false
                                                                    , $this->grid_buttons[$key]->id . "_" . $i
                                                                /*ffProcessTags($FormButton->url, $this->key_fields, $this->grid_fields, "normal", $this->parent[0]->get_params(), urlencode($_SERVER['REQUEST_URI']), $this->parent[0]->get_globals(), null, $this->db[0])*/
                                                                )
                                                   );
                            $this->tpl[0]->parse("SectGridButton", true);
                        }
                        reset($this->grid_buttons);
                    }
                    else
                        $this->tpl[0]->set_var("SectGridButton", "");

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
												"title"            => ffCommon_specialchars("Modifica " . $this->title)
												, "tpl_id"        => $this->getIDIF()
												, "width"        => $this->dialog_options["edit"]["width"]
												, "height"        => $this->dialog_options["edit"]["height"]
												, "params"        => $this->dialog_options["edit"]["params"]
											)
										, $this->parent[0]
									);
								$dlg_edit = true;
							}
							$this->tpl[0]->set_var("SectEditButtonVisible", "<a " . (strlen($this->buttons_options["edit"]["class"]) ? "class=\"" . $this->buttons_options["edit"]["class"] . "\" " : "") . "href=\"javascript:ff.ffPage.dialog.doOpen('" . $this->record_id . "_edit', '" . rawurlencode($modify_url) . "');\">" . $this->buttons_options["edit"]["label"] . "</a>");
                        }
                        else
                            $this->tpl[0]->parse("SectEditButtonVisible", false);
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
                        $this->tpl[0]->set_var("delete_url", ffCommon_specialchars($delete_url));
                        if (/*!strlen($this->record_delete_url) && */($this->full_ajax || $this->ajax_delete))
                        {
							if (!$dlg_delete)
							{
								$this->parent[0]->widgetLoad("dialog");
								$this->parent[0]->widgets["dialog"]->process(
										$this->record_id . "_delete" 
										, array(
												"title"            => ffCommon_specialchars("Elimina " . $this->title)
												, "tpl_id"        => $this->getIDIF()
												, "width"        => $this->dialog_options["delete"]["width"]
												, "height"        => $this->dialog_options["delete"]["height"]
												, "params"        => $this->dialog_options["delete"]["params"]
											)
										, $this->parent[0]
									);
								$dlg_delete = true;
							}
							$this->tpl[0]->set_var("SectDeleteButtonVisible", "<a " . (strlen($this->buttons_options["delete"]["class"]) ? "class=\"" . $this->buttons_options["delete"]["class"] . "\" " : "") . "href=\"javascript:ff.ffPage.dialog.doOpen('" . $this->record_id . "_delete', '" . rawurlencode($delete_url) . "');\">" . $this->buttons_options["delete"]["label"] . "</a>");
                        }
                        else
                            $this->tpl[0]->parse("SectDeleteButtonVisible", false);
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
                        if(trim($actual_row_class, " ") == $this->switch_row_class["second"] || $actual_row_class == "")
                            $actual_row_class = " " . $this->switch_row_class["first"];
                        else 
                            $actual_row_class = " " . $this->switch_row_class["second"];
                    } else 
                        $actual_row_class = "";
                        
                    $res = $this->doEvent("on_before_parse_record", array(&$this));

                    if ($this->row_class || strlen($actual_row_class))
                    {
                        $this->tpl[0]->set_var("row_class", trim($this->row_class . $actual_row_class, " "));
                        $this->tpl[0]->parse("SectRowClass", false);
                    }
                    else
                    {
                        $this->tpl[0]->set_var("SectRowClass", "");
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

                $displayed = array_search($aKeys, $this->displayed_keys, true);
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

        if ($this->db[0]->query_id && $this->db[0]->numRows())
        {
            $this->tpl[0]->parse("SectGridData", false);
            $this->tpl[0]->parse("SectGrid", false);
            $this->tpl[0]->set_var("SectNoRecords", "");
        } 
        else 
        {
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

        if (isset($_REQUEST["XHR_CTX_ID"]))
            $this->tpl[0]->set_var("dialogid", "'" . $_REQUEST["XHR_CTX_ID"] . "'");
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
            if ($this->order_method != "none" && $this->grid_fields[$key]->allow_order)
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
                $this->tpl[0]->set_var("col_label", ($this->grid_buttons[$key]->column_label_encode_ent ? $this->grid_buttons[$key]->column_label : ffCommon_specialchars($this->grid_buttons[$key]->column_label)));

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
            elseif ($col == $tot_labels && $this->column_class_last)
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
            elseif ($col == $tot_labels && $this->column_class_last)
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
            if(!strlen($this->buttons_options["search"]["label"]))
                $this->buttons_options["search"]["label"] = ffTemplate::_get_word_by_code("ffGrid_search");

            if ($this->buttons_options["search"]["obj"] !== null)
            {
                $this->addSearchButton(      $this->buttons_options["search"]["obj"]
                                        , $this->buttons_options["search"]["index"]);
            }
            else
            {
                $tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->theme);
                $tmp->id             = "searched";
                $tmp->label         = $this->buttons_options["search"]["label"];
                $tmp->class         = $this->buttons_options["search"]["class"];
                $tmp->aspect         = "link";
                $tmp->action_type     = "submit";
                $tmp->frmAction        = "search";
                if (isset($_REQUEST["XHR_CTX_ID"]))
                {
                    if  (strlen($tmp->class)) $tmp->class .= " ";
                    $tmp->class .= "noactivebuttons";
                    $tmp->jsaction = ($this->reset_page_on_search ? " jQuery('#" . $this->getPrefix() . $this->navigator[0]->page_parname . "').val('1'); " : "") . ($this->open_adv_search === true || $this->open_adv_search == "always" ? "" : " ff.ffGrid.advsearchHide('" . $this->getIDIF() . "_', '" . $this->getIDIF() . "'); ") . " ff.ajax.ctxDoRequest('" . $_REQUEST["XHR_CTX_ID"] . "',{'action'    : '" . $this->getIDIF() . "_search','component' : '" . $this->getIDIF() . "','section'    : 'GridData'});";
                } 
                elseif ($this->full_ajax || $this->ajax_search)
                {
					$this->parent[0]->tplAddJs("ff.ajax");

                    if  (strlen($tmp->class)) $tmp->class .= " ";
                    $tmp->class .= "noactivebuttons";
                    $tmp->jsaction = ($this->reset_page_on_search ? " jQuery('#" . $this->getPrefix() . $this->navigator[0]->page_parname . "').val('1'); " : "")  . ($this->open_adv_search === true || $this->open_adv_search == "always" ? "" : " ff.ffGrid.advsearchHide('" . $this->getIDIF() . "_', '" . $this->getIDIF() . "'); ") . " ff.ajax.doRequest({'component' : '" . $this->getIDIF() . "','section' : 'GridData'});";
                }
                $this->addSearchButton(      $tmp
                                        , $this->buttons_options["search"]["index"]);
            }
        }

        if ($this->buttons_options["export"]["display"])
        {
            if(!strlen($this->buttons_options["export"]["label"]))
                $this->buttons_options["export"]["label"] = ffTemplate::_get_word_by_code("ffGrid_export");

            if ($this->buttons_options["export"]["obj"] !== null)
            {
                $this->addActionButtonHeader($this->buttons_options["export"]["obj"]);
            }
            else
            {
                $tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->theme);
                $tmp->id             = "export";
                $tmp->label         = $this->buttons_options["export"]["label"];
                $tmp->class         = $this->buttons_options["export"]["class"];
                $tmp->aspect         = "link";
                $tmp->action_type     = "submit";
                $tmp->frmAction        = "export";
                //$tmp->aspect         = "link";
                //$tmp->action_type     = "gotourl";
                if  (strlen($tmp->class)) $tmp->class .= " ";
                $tmp->class .= "noactivebuttons";
                //$tmp->form_action_url = $this->parent[0]->getRequestUri() . "&" . $this->getIDIF() . "_t=xls";
                //$tmp->jsaction = "ff.ajax.doRequest({'component' : '" . $this->getIDIF() . "', 'addFields' : '" . $this->getIDIF() . "t=xls'});";
                //$tmp->class         .= "noactivebuttons";
                //$tmp->url = $this->parent[0]->getRequestUri() . "&" . $this->getIDIF() . "_t=xls";
                $this->addActionButtonHeader($tmp);
            }
        }
    }
    
    public function structProcess($tpl)
    {
		$rc = 0;
		
        if ($this->ajax_update_all)
        {
			$rc++;
            $tpl->set_var("prop_name",    "update_all");
            $tpl->set_var("prop_value",    "true");
            $tpl->parse("SectFFObjProperty",    true);
        }
		
		if ($this->id_if !== null)
		{
			$rc++;
            $tpl->set_var("prop_name",    "factory_id");
            $tpl->set_var("prop_value",   '"' . $this->id . '"');
            $tpl->parse("SectFFObjProperty",    true);
		}
		
		return $rc;
    }
}
