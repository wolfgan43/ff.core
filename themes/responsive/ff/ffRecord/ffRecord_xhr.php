<?php
frameworkCSS::extend(array(
            "records" => array(
                "default" => array(
                    "container" => array(
                        "class" => null
                        , "form" => "row"
                    )
                    , "label-wrap" => false
                    , "label" => array(
                        "class" => null
                        , "form" => "label"
                    )
                    , "control-wrap" => false
                    , "control" => array(
                        "form" => "control"
                    )
                    , "required" => "*"
                )
                , "inline" => array(
                    "container" => array(
                        "class" => null
                        , "form" => "row-inline"
                    )
                    , "label-wrap" => array(
                        "class" => null
                        , "col" => array(
                            "xs" => 12
                            , "sm" => 12
                            , "md" => 4
                            , "lg" => 3
                        )
                    )
                    , "label" => array(
                        "class" => null
                        , "form" => "label-inline"
                    )
                    , "control-wrap" => array(
                        "class" => null
                        , "col" => array(
                            "xs" => 12
                            , "sm" => 12
                            , "md" => 8
                            , "lg" => 9
                        )
                    )
                    , "control" => array(
                        "form" => "control"
                    )
                    , "required" => "*"
                )
            )
            , "outer_wrap" => null
			, "component" => array(
                "inner_wrap" => null
                , "header_wrap" => null
                , "body_wrap" => null
                , "footer_wrap" => null
                , "grid" => false        //false OR array(xs, sm, md, lg) OR 'row' OR 'row-fluid'
			)
			, "title" => array(
				"class" => null
				, "card" => "title"
			)
            , "description" => array(
                "class" => null
                , "card" => "sub-title"
            )
            , "title-alt" => array(
				"class" => null
				, "card" => "title"
			)
            , "description-alt" => array(
                "class" => "mb-2"
                , "card" => "sub-title"
            )
			, "actions" => array(
				"class" => "actions dialogActionsPanel"
                , "col" => false
			)
			, "group" => array(
                "container" => array(
                    "class" => null
                )
				, "title" => array(
					"class" => null
				)
                , "description" => array(
                    "class" => null
                    , "callout" => "info"
                )
			)
			/*, "record" => array(
				"row" => true
			)*/
			/*, "field" => array(
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
                    , "form" => "control"
				)
			)*/

            , "field" => null

			, "info" => array(
				"class" => "info"
				, "callout" => "info"
			)
			, "error" => array(
				"class" => "error dialogSubTitle"
				, "callout" => "danger"
			)
			, "widget" => array(
				"tab" => array(
					"menu" => array(
						"class" => null
						//, "tab" => null //menu OR menu-vertical OR menu-vertical-right
						, "wrap_menu" => null	// null OR array(xs, sm, md, lg)
						, "wrap_pane" => null	// null OR array(xs, sm, md, lg)
					)
					, "menu-item" => array(
						"class" => null
						, "tab" => "menu-item"
					)
					, "pane" => array(
						"class" => null
						, "tab" => "pane"
					)
					, "pane-item" => array(
						"class" => null
						, "tab" => "pane-item" // pane-item-effect OR pane-item
					)
				)
			)
	), "ffRecord_xhr");


class ffRecord_xhr extends ffRecord_base
{
    var $framework_css = null;

	var $buttons_options		= array(
											"insert" => array(
														  "display" => true
														, "index" 	=> 1
														, "obj" 	=> null
														, "label" 	=> null
                                                        , "aspect"  => "link"
                                                        , "icon"	=> null
                                                        , "activebuttons" => true
											  				)
										  , "update" => array(
														  "display" => true
														, "index" 	=> 2
														, "obj" 	=> null
														, "label" 	=> null
                                                        , "aspect"  => "link"
                                                        , "icon"	=> null
                                                        , "activebuttons" => true
											  				)

										  , "delete" => array(
														  "display" => true
														, "index" 	=> 1
														, "obj" 	=> null
														, "label" 	=> null
                                                        , "aspect"  => "link"
                                                        , "icon"	=> null
                                                        , "activebuttons" => true
											  				)

										  , "cancel" => array(
														  "display" => true
														, "index" 	=> 0
														, "obj" 	=> null
														, "label" 	=> null
                                                        , "aspect"  => "link"
                                                        , "icon"	=> null
											  				)
										  , "cursor_first" => array(
														  "display" => true
														, "index" 	=> 4
														, "obj" 	=> null
														, "label" 	=> ""
								                        , "aspect"  => "button"
														, "newicon" => "NewIcon_firstl"
															)
										  , "cursor_prev" => array(
														  "display" => true
														, "index" 	=> 3
														, "obj" 	=> null
														, "label" 	=> ""
								                        , "aspect"  => "button"
														, "newicon" => "NewIcon_left"
															)
										  , "cursor_next" => array(
														  "display" => true
														, "index" 	=> 0
														, "obj" 	=> null
														, "label" 	=> ""
								                        , "aspect"  => "button"
														, "newicon" => "NewIcon_right"
															)
										  , "cursor_last" => array(
														  "display" => true
														, "index" 	=> -1
														, "obj" 	=> null
														, "label" 	=> ""
								                        , "aspect"  => "button"
														, "newicon" => "NewIcon_lastr"
															)
										);
	var $id_if					= null;
	var $type					= null;

	var $cursor_dialog 			= false;

	/**
	 * Il template da utilizzare per il rendering
	 * @var String
	 */
	public $template_file		= "ffRecord_xhr.html";

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

    private $tab_available          = array("default", "pills", "pills-justified", "bordered", "bordered-justified", "left", "right", "top");
    /**
	 * Abilita i tab
	 * @var true | false | pills | pills-justified | bordered | bordered-justified | left | right | top | default
	 */
	public $tab				= false;

    /**
	 * Una collezione dei tabs presenti nella pagina
	 * @var Array
	 */
	protected $tabs				= array();

	/**
	 * Un testo descrittivo da associare all'oggetto
	 * @var String
	 */
	public $description			= null;

	var $disable_mod_notifier_on_error = false;
	var $auto_wrap 				= true;
	var $js_deps = array(
		"ff.ffRecord" => null
	);

	var $properties = null;


	function __construct(ffPage_html $page, $disk_path, $theme)
    {
        parent::__construct($page, $disk_path, $theme);

        $this->framework_css = $page->frameworkCSS->findComponent("ffRecord_xhr");

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
	 * aggiunge un contenuto all'oggetto record
	 * supporta ffField_base, ffDetails_base, ffGrid_base
	 * può essere utilizzata anche per definire gruppi
	 * @param mixed $content
	 * @param mixed $group può essere o il nome di un gruppo o true nel caso si voglia definire un gruppo
	 * @param string $id l'id del contenuto, anche nel caso di un gruppo
	 */
	/*public function addContent($content, $group = null, $id = null)
	{
		if ($content !== null)
		{
			if (is_object($content)	&& is_subclass_of($content, "ffField_base"))
			{
				$content->framework_css = array_replace($content->framework_css, $this->framework_css["field" . ($this->framework_css["component"]["type"] ? "-" . $this->framework_css["component"]["type"] : "")]);
			}
		}

		parent::addContent($content, $group, $id);
	}*/

    /**
     * @param $key
     * @param array|null $params
     * 	    hide_title
            fixed_pre_content
            fixed_post_content
            class
            title
            primary_field
            description
            tab
     */
	public function addGroup($key, Array $params = null) {
	    if(is_array($params["width"])) {
            $params["framework_css"]["container"]["col"] = frameworkCSS::setResolution($params["width"]);
        } elseif(strlen($params["width"])) {
            $params["framework_css"]["container"]["class"] = $params["width"];
        }

        $params["framework_css"] = array_replace_recursive(($this->groups[$key]["framework_css"]
            ? $this->groups[$key]["framework_css"]
            : $this->framework_css["group"]
        ), (array) $params["framework_css"]);

        parent::addGroup($key, $params);
    }

	/**
	 * Aggiunge un nuovo Tab con un tag specifico
	 * @param String $name
	 */
	public function addTab($name)
	{
		if(!$this->tab)
			$this->tab = true;

		if (!isset($this->tabs["contents"][$name]))
			$this->tabs["contents"][$name] = array();
	}

	/**
	 * Imposta il titolo di un tab
	 * @param String $name il tag del tab
	 * @param String $title
	 */
	public function setTabTitle($name, $title)
	{
		$this->tabs["contents"][$name]["title"] = $title;
	}

	/**
	 * Funzione di rendering principale
	 */
	public function tplDisplay()
	{
        if($this->type) {
            $this->framework_css["field"] = ($this->framework_css["records"][$this->type]
                ? $this->framework_css["records"][$this->type]
                : $this->framework_css["records"]["default"]
            );
        } elseif(!$this->framework_css["field"]) {
            $this->framework_css["field"] = $this->framework_css["records"]["default"];
        }

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
            $this->tpl[0] = $this->parent[0]->loadTemplate(pathinfo($this->template_file, PATHINFO_FILENAME));
            //$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
			//$this->tpl[0]->load_file($this->template_file, "main");
		}

		$this->tpl[0]->set_var("component", $this->getPrefix());
		$this->tpl[0]->set_var("component_id", $this->getIDIF());

		$this->tpl[0]->set_var("site_path", $this->site_path);
		$this->tpl[0]->set_var("page_path", $this->page_path);
		$this->tpl[0]->set_var("theme", $this->getTheme());

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

        if(strlen($this->title)) {
            if($this->framework_css["component"]["header_wrap"]) {
                $this->tpl[0]->set_var("title", '<h2 class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["title"]) . '">' . $this->title . '</h2>');
            } else {
                $this->tpl[0]->set_var("title", '<h4 class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["title-alt"]) . '">' . $this->title . '</h4>');
            }
        }

        if(strlen($this->description)) {
            if($this->framework_css["component"]["header_wrap"]) {
                $this->tpl[0]->set_var("subtitle", '<div class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["description"]) . '">' . $this->description . '</div>');
            } else {
                $this->tpl[0]->set_var("subtitle", '<p class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["description-alt"]) . '">' . $this->description . '</p>');
            }
        }

		if(!$this->parent[0]->use_own_form && !$this->ajax) {
			$this->tpl[0]->set_var("record_tag", "form");
			$this->tpl[0]->set_var("component", "");
			$this->tpl[0]->set_var("id", "frmAction");
			$this->tpl[0]->set_var("value", "");
			$this->tpl[0]->parse("SectHiddenField", true);
			$this->properties["method"] = "post";
		} else
			$this->tpl[0]->set_var("record_tag", "div");

		$component_class["default"] = $this->class;
		if($this->framework_css["component"]["grid"]) {
            if(is_array($this->framework_css["component"]["grid"]))
                $component_class["grid"] = $this->parent[0]->frameworkCSS->get($this->framework_css["component"]["grid"], "col");
            else {
                $component_class["grid"] = $this->parent[0]->frameworkCSS->get("", $this->framework_css["component"]["grid"]);
            }
        }
		$component_class["form"] = $this->parent[0]->frameworkCSS->get("component" . ($this->framework_css["component"]["type"] ? "-" : "") . $this->framework_css["component"]["type"], "form");

		$this->properties["id"] = $this->getIDIF();
		$this->properties["class"] = implode(" ", array_filter($component_class));

		$this->tpl[0]->set_var("component_properties", $this->getProperties());

        if($this->framework_css["outer_wrap"]) {
            $this->tpl[0]->set_var("outer_wrap_start", '<div class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["outer_wrap"]) . '">');
            $this->tpl[0]->set_var("outer_wrap_end", '</div>');
        }

        if(is_array($this->framework_css["component"]["col"]) && $this->framework_css["component"]["inner_wrap"] === null)
            $this->framework_css["component"]["inner_wrap"] = array("row" => true);

        if($this->framework_css["component"]["inner_wrap"]) {
            $this->tpl[0]->set_var("inner_wrap_start", '<div class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["component"]["inner_wrap"]) . '">');
            $this->tpl[0]->set_var("inner_wrap_end", '</div>');
        }

        if($this->framework_css["component"]["header_wrap"]
            && ($this->title || $this->description || $this->fixed_pre_content)
        ) {
            $this->tpl[0]->set_var("header_wrap_start", '<div class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["component"]["header_wrap"]) . '">');
            $this->tpl[0]->set_var("header_wrap_end", '</div>');
        }

        if($this->framework_css["component"]["body_wrap"]) {
            if(!$this->framework_css["component"]["header_wrap"] && ($this->title || $this->description || $this->fixed_pre_content)) {
                $this->tpl[0]->set_var("header_wrap_start", '<div class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["component"]["body_wrap"]) . '">');
            } else {
                $this->tpl[0]->set_var("body_wrap_start", '<div class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["component"]["body_wrap"]) . '">');
            }

            $this->tpl[0]->set_var("body_wrap_end", '</div>');
        }

        if($this->framework_css["component"]["footer_wrap"]
            && ($this->tpl[0]->isset_block("SectControls"))
        ) {
            $this->tpl[0]->set_var("footer_wrap_start", '<div class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["component"]["footer_wrap"]) . '">');
            $this->tpl[0]->set_var("footer_wrap_end", '</div>');
        }

		if ($this->tab && count($this->tabs))
		{
			$this->tpl[0]->set_var("form_tabs", $this->parent[0]->widgets["tabs"]->process($this->getIDIF(), $this->tabs, $this->parent[0], $this->id));
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

                /**
                 * roba per fare il template custom
                 */
				/*$this->tpl[0]->set_var("Key_" . $key, $this->key_fields[$key]->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE));
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
				}*/
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

		if ($this->display_required && $this->display_required_note)
		{
			$this->tpl[0]->set_var("required_symbol", $this->required_symbol);
			$this->tpl[0]->parse("SectRequiredNote", false);
		}
		//else
		//	$this->tpl[0]->set_var("SectRequiredNote", "");

		if ($this->tab)
		{
			$this->parent[0]->widgetLoad("tabs", null, $this->parent[0]);

            $this->tabs["tab_mode"] = ($this->tab !== true && in_array($this->tab, $this->tab_available)
                ? $this->tab
                : $this->tab_available[0]
            );
			$this->tabs["framework_css"] = $this->framework_css["widget"]["tab"];
			if(is_array($this->tabs["contents"]) && count($this->tabs["contents"])) {

			    foreach($this->tabs["contents"] AS $tab_key => $tab) {
			        if(is_array($tab["groups"]) && count($tab["groups"])) {
			            foreach($tab["groups"] AS $group_key) {
                            frameworkCSS::colsWrapper("row-" . $this->id . "-". $tab_key
                                , $this->tabs["contents"][$tab_key]["data"]
                                , $this->groups[$group_key]["framework_css"]["container"]["col"]["lg"]
                                , $this->tplDisplayGroup($group_key, ($tab_key == $group_key))
                                , count($tab["groups"])
                            );

                            //$this->tabs["contents"][$tab_key]["data"][$group_key] = $this->tplDisplayGroup($group_key, ($tab_key == $group_key));

                            if($this->groups[$group_key]["current"]) {
                                $this->tabs["current"] = $tab_key;
                            }
                        }
                    }
                }
            }

            if (isset($this->groups["_main_"])) {
                $this->tpl[0]->set_var("form_default", $this->tplDisplayGroup($this->groups["_main_"]));
            }
		}
		else
		{
			if($this->title) {
                $this->tpl[0]->parse("SectTitle", false);
            }

            if(is_array($this->groups) && count($this->groups)) {
                $buffer = array();
                if (isset($this->groups["_main_"])) {
                    $main_group = $this->groups["_main_"];
                    unset($this->groups["_main_"]);
                    //$this->groups = array_merge(array("_main_" => $main_group), $this->groups);
                }

                $count_group = count($this->groups);
                foreach($this->groups AS $group_key => $group_value) {
                    //$buffer[] = $this->tplDisplayGroup($group_key);

                    frameworkCSS::colsWrapper("row-" . $this->id . "-groups"
                        , $buffer
                        , $this->groups[$group_key]["framework_css"]["container"]["col"]["lg"]
                        , $this->tplDisplayGroup($group_key)
                        , $count_group
                    );
                }

                $this->tpl[0]->set_var("form_default", $this->tplDisplayGroup($main_group));
                $this->tpl[0]->set_var("form_tabs", implode("", $buffer));
            }
		}

		//$groups_tabs = array(); // conteggio gruppi per tab
		/*if(!is_array($this->groups))
			$this->groups = array();*/

		/*$this->groups = array_merge(array("_main_" => array(
											"class" => "default"
											, "title" => ""
											, "contents" => array()
											, "data" => null
										)
									), $this->groups);*/



		/*foreach ($this->contents as $key => $content)
		{
			if (!$content["group"]) {
				$this->groups["_main_"]["contents"][$key] = $content;
				continue;
			}
			//if ($this->groups[$key]["tab"])
			//	$groups_tabs[$this->groups[$key]["tab"]]++;
		}
		reset($this->contents);

		if(!count($this->groups["_main_"]["contents"])) {
            unset($this->groups["_main_"]);
        }*/
/*
		if (0 && is_array($this->groups) && count($this->groups))
		{
            $last_tab_key = "";
			foreach($this->groups AS $group_key => $group_value)
			{
				if ($this->tplDisplayFields($this->groups[$group_key]["contents"], $group_key))
				{
					$res = $this->doEvent("on_before_parse_group", array(&$this, $group_key));
                    $rc = end($res);

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
							//$group_class = array();
							//$group_class["default"] = $this->parent[0]->frameworkCSS->getClass($this->framework_css["group"]["def"]);
							//$group_class["custom"] = (strlen($this->groups[$group_key]["class"]) ? $this->groups[$group_key]["class"] : $group_key);

							if(is_array($group_value["tab"])) {
                                $tab_key = ($group_value["tab"]["title"]
                                    ? $group_value["tab"]["title"]
                                    : $group_key
                                );
                            } elseif(is_string($group_value["tab"])) {
                                $tab_key = (strlen($group_value["tab"])
                                    ? $group_value["tab"]
                                    : $group_key
                                );
                            } elseif(is_bool($group_value["tab"])) {
                                $tab_key = ($last_tab_key
                                    ? $last_tab_key
                                    : $group_key
                                );

                            } else {
                                $tab_key = $group_key;
                            }

                            $last_tab_key = $tab_key;

							if($this->groups[$group_key]["hide_title"]
								|| (!strlen($this->groups[$group_key]["title"])
									&& !strlen($this->groups[$group_key]["primary_field"])
								)
							) {
								$this->tpl[0]->set_var("SectGroupTitle", "");
							} else {
								$arrTitleProperties = array();
								$arrTitleProperties["class"]["default"] = $this->parent[0]->frameworkCSS->getClass($this->groups[$group_key]["framework_css"]["title"]);
								$this->tpl[0]->set_var("title_properties", "");

								if($group_value["primary_field"]) {
									$arrTitleProperties["class"]["primary_field"] = "ffCheckDep";
									$arrTitleProperties["onclick"] = "javascript:ff.ffRecord.displayFieldSetElem(this, '" . $this->getIDIF() . "_" . $group_value["primary_field"] . "');";
								}

                                if(!$group_value["primary_field"]) {
                                    if($this->groups[$group_key]["title"] && $tab_key != $group_key) {
                                        $this->tpl[0]->set_var("GroupTitle", $this->groups[$group_key]["title"]);
                                    } else {
                                        $this->tpl[0]->set_var("GroupTitle", "");
                                    }
                                }
								//}


								if(is_array($arrTitleProperties) && count($arrTitleProperties)) {
									$str_title_properties = "";
									foreach($arrTitleProperties AS $arrTitleProperties_key => $arrTitleProperties_value) {
										$str_title_properties .= " " . $arrTitleProperties_key . '="' . (is_array($arrTitleProperties_value) ? implode(" ", array_filter($arrTitleProperties_value)) : $arrTitleProperties_value) . '"';
									}
									$this->tpl[0]->set_var("title_properties", $str_title_properties);
								}
								if($this->tpl[0]->getBlockContent("GroupTitle", false))  //(!$this->tab || $this->tab === "left" || $this->tab === "right") &&
									$this->tpl[0]->parse("SectGroupTitle", false);
								else
									$this->tpl[0]->set_var("SectGroupTitle", "");
							}

							$this->tpl[0]->set_var("group_class", $this->parent[0]->frameworkCSS->getClass($this->groups[$group_key]["framework_css"]["container"]));
							//$this->tpl[0]->set_var("group_class", implode(" ", array_filter($group_class)));

							if(isset($this->groups[$group_key]["description"]) && strlen($this->groups[$group_key]["description"])) {
							    $description_class = $this->parent[0]->frameworkCSS->getClass($this->groups[$group_key]["framework_css"]["description"]);

                                $this->tpl[0]->set_var("description_properties", ($description_class
                                    ? ' class="' . $description_class . '"'
                                    : ''
                                ));
                                $this->tpl[0]->set_var("GroupDescription", $this->groups[$group_key]["description"]);
								$this->tpl[0]->parse("SectGroupDescription", false);
							} else {
								$this->tpl[0]->set_var("SectGroupDescription", "");
							}

							$this->tpl[0]->parse("SectGroupStart", false);
							$this->tpl[0]->parse("SectGroupEnd", false);
						}

						if ($this->tab)
						{
							if ($group_key == "_main_")
								$this->groups["_main_"]["data"] .= $this->tpl[0]->rpparse("SectGroup", false);
							else
							{
								if(!$this->tabs["contents"][$tab_key]["title"])
									$this->tabs["contents"][$tab_key]["title"] = (is_array($group_value["tab"]) && $group_value["tab"]["title"]
                                        ? $group_value["tab"]["title"]
                                        : ($group_value["title"]
                                            ? $group_value["title"]
                                            : $tab_key
                                        )
                                    );

								if($group_value["current"])
									$this->tabs["current"] = $tab_key;

                                //$this->row_wrapper("tab-" . $tab_key, $this->tabs["contents"][$tab_key]["data"], "6", $this->tpl[0]->rpparse("SectGroup", false), count($this->tabs["contents"][$tab_key][""]));


                                $this->tabs["contents"][$tab_key]["data"][$group_key] = $this->tpl[0]->rpparse("SectGroup", false);
                                if(is_array($group_value["tab"]) && is_array($group_value["tab"]["menu"]))
                                    $this->tabs["contents"][$tab_key]["menu"][$group_value["tab"]["menu"]["mode"]][$group_key] = $group_value["tab"]["menu"]["title"];

							}
							$this->tpl[0]->set_var("SectGroup", "");
						}
						else
						{
							if (!$this->groups[$group_key]["hide_title"]) {
                                $this->tpl[0]->parse("SectGroupTitle", false);
                            }
							$this->tpl[0]->parse("SectGroup", true);
						}
					}
				}
			}
		}
*/
//print_r($this->tabs["contents"]);
	//	die();
		//if (isset($this->groups["_main_"]) && $this->groups["_main_"]["data"] !== null)
		//	$this->tpl[0]->set_var("form_default", $this->groups["_main_"]["data"]);
	}
    protected function tplDisplayGroup($group_key, $hide_title = false) {
        $group = (is_array($group_key)
            ? $group_key
            : $this->groups[$group_key]
        );

        if ($this->tplDisplayFields($group["contents"],(string) $group_key/*, $group_value["primary_field"]*/)) {
            $res = $this->doEvent("on_before_parse_group", array(&$this, $group_key));
            $rc = end($res);

            if (!$rc) {
                $this->tpl[0]->set_var("GroupTitle", "");
                $this->tpl[0]->set_var("GroupDescription", "");
                $this->tpl[0]->set_var("SectGroupTitle", "");
                $this->tpl[0]->set_var("SectGroupDescription", "");

                $this->tpl[0]->set_var("SectGroupStart", "");
                $this->tpl[0]->set_var("SectGroupEnd", "");

                $this->tpl[0]->set_var("group_pre_content", $group["fixed_pre_content"]);
                $this->tpl[0]->set_var("group_post_content", $group["fixed_post_content"]);

                if (is_array($group_key)) {
                    $rc = $this->tpl[0]->rpparse("SectGroup", false);
                } else {
                    /**
                     * Group title
                     */
                    if(!$group["hide_title"]) {
                        $arrTitleProperties = array();

                        if ($group["primary_field"]) {
                            $arrTitleProperties["class"]["primary_field"] = "ffCheckDep";
                            $arrTitleProperties["onclick"] = "javascript:ff.ffRecord.displayFieldSetElem(this, '" . $this->getIDIF() . "_" . $group["primary_field"] . "');";
                            $this->tpl[0]->set_var("title_properties", $this->getProperties($arrTitleProperties));
                            $this->tpl[0]->parse("SectGroupTitle", false);
                        } else {
                            if ($group["title"] && !$hide_title) {
                                $this->tpl[0]->set_var("title_properties", "");
                                $arrTitleProperties["class"]["default"] = $this->parent[0]->frameworkCSS->getClass($group["framework_css"]["title"]);

                                $this->tpl[0]->set_var("GroupTitle", $group["title"]);
                                $this->tpl[0]->set_var("title_properties", $this->getProperties($arrTitleProperties));
                                $this->tpl[0]->parse("SectGroupTitle", false);
                            }
                        }
                    }

                    /**
                     * Group Description
                     */
                    if (isset($group["description"]) && strlen($group["description"])) {
                        $description_class = $this->parent[0]->frameworkCSS->getClass($group["framework_css"]["description"]);

                        $this->tpl[0]->set_var("description_properties", ($description_class
                            ? ' class="' . $description_class . '"'
                            : ''
                        ));
                        $this->tpl[0]->set_var("GroupDescription", $group["description"]);
                        $this->tpl[0]->parse("SectGroupDescription", false);
                    }

                    /**
                     * Container Class
                     */

                    $this->tpl[0]->set_var("group_class", $this->parent[0]->frameworkCSS->getClass($group["framework_css"]["container"], null, true));
                    $this->tpl[0]->parse("SectGroupStart", false);
                    $this->tpl[0]->parse("SectGroupEnd", false);

                    $rc = $this->tpl[0]->rpparse("SectGroup", false);
                }

                $this->tpl[0]->set_var("SectGroup", "");
            }
        }

        return $rc;
    }

    /**
     * Visualizza il campo del record
     */
    protected function tplDisplayFields($contents, $group_key/*, $primary_field = null*/)
    {
        //$this->tpl[0]->set_var("SectGroupRow", "");
        //$this->tpl[0]->set_var("SectGroupRow_" . $group_key, "");

        $this->tpl[0]->set_var("content", "");

        $vars_to_reset = array();
        $i = 0;
        $buffer = array();
        //$wrap_count = null;
        //$is_wrapped = false;
        // $wrapping = 0;
        //$count_contents = 0;

        $framework_css_field = $this->framework_css["field" . ($this->framework_css["component"]["type"] ? "-" . $this->framework_css["component"]["type"] : "")];
        if(is_array($contents) && count($contents))
        {
            foreach ($contents as $key => $subvalue)
            {
                //$is_wrapped = false;

                //$this->tpl[0]->set_var("SectGroupLabel", "");
                //$this->tpl[0]->set_var("field_container_start", "");
                //$this->tpl[0]->set_var("field_container_end", "");
                //$this->tpl[0]->set_var("content_pre_label", "");
                //$this->tpl[0]->set_var("content_in_label", "");


                //$container_class = array();
                //$container_properties = array();
                $content = null;
                //$count_contents++;
                if(is_string($subvalue["data"])) {
                    $i++;
                    $content = $subvalue["data"];
                    $outer_wrap = false;
                } elseif(get_class($subvalue["data"]) == "ffTemplate") {
                    $i++;
                    $content = $subvalue["data"]->rpparse("main", false);
                    $outer_wrap = false;
                } elseif(is_subclass_of($subvalue["data"], "ffDetails_base")) {
                    $i++;
                    $content = "{{" . $subvalue["data"]->id . "}}";
                    $outer_wrap = $subvalue["data"]->framework_css["outer_wrap"]["col"]["lg"];
                } elseif(is_subclass_of($subvalue["data"], "ffGrid_base")) {
                    $i++;
                    $content = "{{" . $subvalue["data"]->id . "}}";
                    $outer_wrap = $subvalue["data"]->framework_css["outer_wrap"]["col"]["lg"];
                } elseif(is_subclass_of($subvalue["data"], "ffRecord_base")) {
                    $i++;
                    $content = "{{" . $subvalue["data"]->id . "}}";
                    $outer_wrap = $subvalue["data"]->framework_css["outer_wrap"]["col"]["lg"];
                } elseif (is_subclass_of($subvalue["data"], "ffField_base")) {
                    if (
                    (/*!$this->use_fixed_fields && $this->display_values && */$this->form_fields[$key]->skip_if_empty && !strlen($this->form_fields[$key]->value->ori_value))
                    )
                        continue;

                    if (!isset($this->key_fields[$subvalue["data"]->id])) // displayed key fields are threated previously
                    {
                        if (is_array($this->form_fields[$subvalue["data"]->id]->multi_fields) && count($this->form_fields[$subvalue["data"]->id]->multi_fields))
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

                    $subvalue["data"]->framework_css = array_replace($subvalue["data"]->framework_css, $this->framework_css["field"]);

                    // EVENT HANDLER
                    $res = $this->doEvent("on_process_field", array(&$this, $key));

                    // container
                    foreach ($vars_to_reset as $field_key => $field_values)
                    {
                        $this->tpl[0]->set_var($field_key, "");
                    }
                    reset($vars_to_reset);

                    $this->tpl[0]->set_var("container_properties", $this->form_fields[$key]->getProperties($this->form_fields[$key]->container_properties));

                    if (is_array($this->form_fields[$key]->container_vars) && count($this->form_fields[$key]->container_vars))
                    {
                        foreach ($this->form_fields[$key]->container_vars as $field_key => $field_value)
                        {
                            $this->tpl[0]->set_var($field_key, $field_value);
                            $vars_to_reset[$field_key] = true;
                        }
                        reset($this->form_fields[$key]->container_vars);
                    }


                    /* if(strlen($this->form_fields[$key]->container_class))
                         $container_class[] = $this->form_fields[$key]->container_class;
                     else
                         $container_class[] = $this->form_fields[$key]->class;
                         */
                    if($this->form_fields[$key]->placeholder === true) {
                        $this->form_fields[$key]->placeholder = ffCommon_specialchars($this->form_fields[$key]->label);
                    }

                    if (!$this->display_required) {
                        $this->form_fields[$key]->framework_css["required"] = false;
                    }

                    //$control_var = "";
                    //$control_prefix = "";
                    //$control_postfix = "";
                    //$is_combine_field = false;


                    // LABEL
                    //$label_set = false;
                    if($this->form_fields[$key]->display_label)
                    {
                        /**
                         * parte nuova
                         */
                        /*if ($this->display_required && $this->form_fields[$key]->required) {
                            //$container_class["require"] = "required";
                            $required_symbol = "*";
                        }

                        $this->form_fields[$key]->label_properties["for"] = $this->getIDIF() . "_" . $key;
                        $html_block["label"] = '<label class="' . $this->parent[0]->frameworkCSS->getClass($framework_css_field["label"]) . '" ' . $this->form_fields[$key]->getProperties($this->form_fields[$key]->label_properties) . '>'
                            . ($this->form_fields[$key]->encode_label
                                ? ffCommon_specialchars($this->form_fields[$key]->label) . $required_symbol
                                : $this->form_fields[$key]->label . $required_symbol
                            )
                            . "</label>";
                        if($framework_css_field["label-wrap"]) {
                            $html_block["label"] = '<div class="' . $this->parent[0]->frameworkCSS->getClass($framework_css_field["label-wrap"]) . '">'
                                . $html_block["label"]
                                . '</div>';
                        }*/

                        /**
                         * parte nuova
                         */

                        /*
                                                $this->tpl[0]->set_var("label_prefix", "");
                                                $this->tpl[0]->set_var("label_postfix", "");
                                                $required_symbol = "";

                                                if(($this->form_fields[$key]->get_control_type() == "checkbox" || $this->form_fields[$key]->get_control_type() == "radio") && $this->form_fields[$key]->widget == "") {
                                                    $control_var = $this->parent[0]->frameworkCSS->get("control-check-position", "form");
                                                    $is_combine_field = true;
                                                }

                                                if ($this->form_fields[$key]->description !== null)
                                                {
                                                    $this->tpl[0]->set_var("fielDescription", $this->form_fields[$key]->description);
                                                    $this->tpl[0]->parse("SectDescriptionLabel", false);

                                                    $label_set = true;
                                                }
                                                else
                                                {
                                                    $this->tpl[0]->set_var("fielDescription", "");
                                                    $this->tpl[0]->set_var("SectDescriptionLabel", "");
                                                }

                                                if($this->form_fields[$key]->label_properties)
                                                {
                                                    $this->tpl[0]->set_var("label_properties", " " . $this->form_fields[$key]->getProperties($this->form_fields[$key]->label_properties));
                                                    $label_set = true;
                                                }

                                                if ($this->display_required && $this->form_fields[$key]->required) {
                                                    $container_class["require"] = "required";
                                                    $required_symbol = "*";
                                                }

                                                 if(strlen($this->form_fields[$key]->label))
                                                {
                                                    if($primary_field == $key && !$control_var) {
                                                        $prefix_label = "content_pre_";
                                                        $label_set = false;
                                                    }
                                                    else
                                                    {
                                                        $prefix_label = "";
                                                        $label_set = true;
                                                    }
                                                    if($this->form_fields[$key]->encode_label)
                                                        $this->tpl[0]->set_var($prefix_label . "label", ffCommon_specialchars($this->form_fields[$key]->label) . $required_symbol);
                                                    else
                                                        $this->tpl[0]->set_var($prefix_label . "label",$this->form_fields[$key]->label . $required_symbol);
                                                }

                                                if($label_set) {

                                                    //Label Class







                                                    $this->tpl[0]->set_var("label_for", $this->getIDIF() . "_" . $key);

                                                    $arrColumnLabel = $this->form_fields[$key]->framework_css["label"]["col"];
                                                    $arrColumnControl = $this->form_fields[$key]->framework_css["control"]["col"];
                                                    $type_label = "";
                                                    //if($primary_field == $key)
                                                    //	$type_label = "inline";

                                                    if(!strlen($control_var))
                                                    {
                                                        if(is_array($arrColumnLabel) && count($arrColumnLabel)
                                                            && is_array($arrColumnControl) && count($arrColumnControl)
                                                        ) {
                                                            $this->tpl[0]->set_var("label_prefix", '<div class="' . $this->parent[0]->frameworkCSS->getClass($this->form_fields[$key]->framework_css["label"]) . '">');
                                                            //$this->tpl[0]->set_var("label_prefix", '<div class="' . $this->parent[0]->frameworkCSS->get($arrColumnLabel, "col") . " " . $this->parent[0]->frameworkCSS->get("align-right", "util") . '">');
                                                            $this->tpl[0]->set_var("label_postfix", '</div>');

                                                            $control_prefix = '<div class="' . $this->parent[0]->frameworkCSS->getClass($this->form_fields[$key]->framework_css["control"]) . '">';
                                                            //$control_prefix = '<div class="' . $this->parent[0]->frameworkCSS->get($arrColumnControl, "col") . '">';
                                                            $control_postfix = '</div>';
                                                           // $type_label = "inline";
                                                        }
                                                    }

                                                    //if($this->framework_css["component"]["type"] === null && $type_label)
                                                    //	$this->framework_css["component"]["type"] = $type_label;

                                                    $label_class = $this->parent[0]->frameworkCSS->get("label" . $this->framework_css["component"]["type"], "form");
                                                    if($this->framework_css["component"]["type"] && $is_combine_field) {
                                                        if($control_var == "_in_label")
                                                            $label_class .= ($label_class ? " " : "") . $this->parent[0]->frameworkCSS->get($arrColumnLabel, "push") . " " . $this->parent[0]->frameworkCSS->get($arrColumnControl, "col");
                                                        else
                                                            $container_class["align"] = $this->parent[0]->frameworkCSS->get("align-right", "util");
                                                    }

                                                    if($label_class)
                                                        $this->tpl[0]->set_var("label_class", ' class="' . $label_class . '"');
                                                    else
                                                        $this->tpl[0]->set_var("label_class", "");
                                                } else {
                                                    $control_var = "";
                                                }*/
                    }

                    /**
                     * Row Class
                     */
                    /*$container_class["default"] = $this->form_fields[$key]->get_control_class(null, array("framework_css" => false, "control_type" => false));

                    if($primary_field != $key) {
                        if(is_array($this->form_fields[$key]->framework_css["container"]["col"])
                            && count($this->form_fields[$key]->framework_css["container"]["col"])
                        ) {
                            $container_class["grid"] = $this->parent[0]->frameworkCSS->get($this->form_fields[$key]->framework_css["container"]["col"], "col");
                            if($this->auto_wrap && !$is_wrapped) {
                                $wrap_class = array("form-wrap");
                                if($this->form_fields[$key]->framework_css["container"]["row"]) {
                                    $wrap_class[] = $this->parent[0]->frameworkCSS->get("row", "form");
                                }
                                $this->tpl[0]->set_var("wrap_class", implode(" ", array_filter($wrap_class)));
                                $is_wrapped = $this->tpl[0]->parse("SectWrapStart", false);
                            }

                            $wrap_count = $wrap_count + $this->form_fields[$key]->framework_css["container"]["col"]["lg"];
                        } elseif($this->form_fields[$key]->framework_css["container"]["row"]) {
                            $container_class["row"] = $this->parent[0]->frameworkCSS->get("row-padding", "form");

                        }
                    }*/


                    // CONTROL/VALUE SECTION
                    /**
                     * Control Class
                     */
                    $rc = false;
                    if($this->tpl[0]->isset_var($key . ":value"))
                        $rc |= $this->tpl[0]->set_var($key . ":value", $this->form_fields[$key]->getValue());
                    if($this->tpl[0]->isset_var($key . ":displayvalue"))
                        $rc |= $this->tpl[0]->set_var($key . ":displayvalue", $this->form_fields[$key]->getDisplayValue());

                    if ($this->tpl[0]->isset_var($key)) {
                        if ($this->display_values)
                            $buffer_field = $this->form_fields[$key]->getDisplayValue();
                        else
                            $buffer_field = $this->form_fields[$key]->process();

                        if(is_array($this->form_fields[$key]->multi_fields) && count($this->form_fields[$key]->multi_fields))
                        {
                            foreach ($this->form_fields[$key]->multi_fields as $mul_subkey => $mul_subvalue)
                            {
                                $buffer_field .= '<input type="hidden" id="' . $this->id . "_". $key . "[" . $mul_subkey . "]" . '" name="' . $this->id . "_". $key . "[" . $mul_subkey . "]" . '" value="' . ffCommon_specialchars($this->form_fields[$key]->value[$mul_subkey]->getValue($mul_subvalue["type"], FF_SYSTEM_LOCALE)) . '" />';
                                if($this->record_exist)
                                    $buffer_field .= '<input type="hidden" id="' . $this->id . "_". $key . "_ori" . "[" . $mul_subkey . "]" . '" name="' . $this->id . "_". $key . "_ori" . "[" . $mul_subkey . "]" . '" value="' . ffCommon_specialchars($this->form_fields[$key]->value_ori[$mul_subkey]->getValue($mul_subvalue["type"], FF_SYSTEM_LOCALE)) . '" />';
                            }
                        } else {
                            $buffer_field .= '<input type="hidden" id="' . $this->id . "_". $key . "_ori" . '" name="' . $this->id . "_". $key . "_ori" . '" value="' . ffCommon_specialchars($this->form_fields[$key]->value_ori->getValue($this->form_fields[$key]->base_type, FF_SYSTEM_LOCALE)) . '" />';
                        }

                        $rc |= $this->tpl[0]->set_var($key, $buffer_field);
                    }

                    if (!$rc)
                    {
                        /**
                         * parte nuova
                         */
                        /*$html_block["control"] = ($this->display_values
                            ? $this->form_fields[$key]->getDisplayValue()
                            : $this->form_fields[$key]->process()
                        );
                        if($framework_css_field["control-wrap"]) {
                            $html_block["control"] = '<div class="' . $this->parent[0]->frameworkCSS->getClass($framework_css_field["control-wrap"]) . '">'
                                . $html_block["control"]
                                . '</div>';
                        }*/
                        /**
                         * parte nuova
                         */


                        /*if (!$this->display_values || strlen($this->form_fields[$key]->control_type))
                        {
                            if ($this->tpl[0]->isset_var("content" . $control_var)) {
                                $processed_field = $this->form_fields[$key]->process();

                                if($control_prefix && ($this->form_fields[$key]->framework_css["fixed_pre_content"] || $this->form_fields[$key]->framework_css["fixed_post_content"])) {
                                    $control_prefix = $control_prefix . '<div class="' . $this->parent[0]->frameworkCSS->get("group", "form") . '">';
                                    $control_postfix = '</div>' . $control_postfix;
                                }
                                $this->tpl[0]->set_var("content" . $control_var, $control_prefix . $processed_field . $control_postfix);
                            }
                        }
                        else
                            $this->tpl[0]->set_var("content" . $control_var, $this->form_fields[$key]->getDisplayValue());*/
                    }

                    /*if($label_set)
                        $this->tpl[0]->parse("SectGroupLabel", false);*/


                    // Manage Fixed Sections
                    if(!is_array($this->form_fields[$key]->value->ori_value))
                    {
                        if (strlen($this->form_fields[$key]->value->ori_value))
                        {
                            $this->tpl[0]->parse("SectSet_$key", false);
                            $this->tpl[0]->set_var("SectNotSet_$key", "");
                        }
                        else
                        {
                            $this->tpl[0]->set_var("SectSet_$key", "");
                            $this->tpl[0]->parse("SectNotSet_$key", false);
                        }
                    }
                    if ($this->form_fields[$key]->extended_type == "Selection")
                    {
                        $this->tpl[0]->set_regexp_var("/SectSet_" . $key . "_.+/", "");
                        $this->tpl[0]->parse("SectSet_" . $key . "_" . $this->form_fields[$key]->value->getValue($this->form_fields[$key]->base_type, FF_SYSTEM_LOCALE), false);
                    }

                    $this->tpl[0]->parse("Sect_$key", false);

                    /**
                     * parte nuova
                     */
                    /*
                                        if($this->form_fields[$key]->get_control_type() == "checkbox" || $this->form_fields[$key]->get_control_type() == "radio") {

                                        }

                                        $this->tpl[0]->set_var("content", '<div class="' . $this->parent[0]->frameworkCSS->getClass($framework_css_field["def"]) . '">'
                                            . $html_block["label"]
                                            . $html_block["control"]
                                            . "</div>"
                                        );*/

                    /* $this->tpl[0]->set_var("content", ($this->display_values
                          ? $this->form_fields[$key]->getDisplayValue()
                          : $this->form_fields[$key]->process()
                      ));*/



                    $content = ($this->display_values
                        ? $this->form_fields[$key]->getDisplayValue()
                        : $this->form_fields[$key]->process()
                    );
                    $outer_wrap = $this->form_fields[$key]->framework_css["outer_wrap"]["col"]["lg"];

                    /**
                     * parte nuova
                     */

                }

                frameworkCSS::colsWrapper("content-" . $this->id . "-" . $group_key, $buffer, $outer_wrap, $content, count($contents));

                /*
                if($content) {
                    if ($outer_wrap) {
                        $is_wrapped = false;
                        $wrap_count = $wrap_count + $outer_wrap;
                        if ($wrap_count > 12) {
                            $buffer[] = '</div>';
                            $buffer[] = '<div class="' . $this->parent[0]->frameworkCSS->get("wrap", "form") . '">';
                            $is_wrapped = true;
                            $wrap_count = 0;
                        } elseif ($wrap_count == $outer_wrap) { //first
                            $buffer[] = '<div class="' . $this->parent[0]->frameworkCSS->get("wrap", "form") . '">';
                            $is_wrapped = true;
                        } elseif($wrap_count) {
                            $is_wrapped = true;
                        }

                        $buffer[] = $content;
                        if($is_wrapped && $count_contents == count($contents)) {
                            $buffer[] = '</div>';
                        }
                    } else {
                        if ($wrap_count > 0 || $is_wrapped) {
                            $buffer[] = '</div>';
                            $wrap_count = 0;
                        }
                        $buffer[] = $content;
                    }
                }*/
                /* if($wrapping) {
                     $buffer[] = '</div>';
                 }
                 $this->tpl[0]->set_var("content", implode("", $buffer));*/


                /**
                 * container field
                 */
                /*$container_inner_start = '';
                $container_inner_end = '';
                if(count($container_class)) {
                    if(!$control_prefix && ($this->form_fields[$key]->framework_css["fixed_pre_content"] || $this->form_fields[$key]->framework_css["fixed_post_content"])) {
                        $wrap_addon = $this->parent[0]->frameworkCSS->get("wrap-addon", "form");
                        if($wrap_addon) {
                            if($container_class["grid"]) {
                                $container_inner_start = '<div class="' . $this->parent[0]->frameworkCSS->get("group-padding", "form") . '">';
                                $container_inner_end = '</div>';
                            } else {
                                $container_class["row"] = $this->parent[0]->frameworkCSS->get("group-padding", "form");
                            }
                        }
                    }

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

                    $this->tpl[0]->set_var("field_container_start", '<div' . $str_container_properties . '>' . $container_inner_start);
                    $this->tpl[0]->set_var("field_container_end", $container_inner_end . '</div>');
                }*/
                /*
                                if($primary_field == $key && is_subclass_of($subvalue["data"], "ffField_base")) {
                                    $this->tpl[0]->set_var("GroupTitle", $this->tpl[0]->ProceedTpl("SectGroupRow"));
                                } else {



                                    if(($wrap_count >= 12 || $count_contents == count($contents)) && $is_wrapped) {
                                        $wrap_class = array("form-wrap");
                                        if($this->form_fields[$key]->framework_css["container"]["row"]) {
                                            $wrap_class[] = $this->parent[0]->frameworkCSS->get("row", "form");
                                        }
                                        $this->tpl[0]->set_var("wrap_class", implode(" ", array_filter($wrap_class)));
                                        $is_wrapped = $this->tpl[0]->parse("SectWrapStart", false);
                                        $this->tpl[0]->parse("SectWrapEnd", false);
                                        $wrap_count = 0;
                                        $is_wrapped = false;
                                    }

                                    $rc = $this->tpl[0]->parse("SectGroupRow_" . $group_key, true);
                                    if (!$rc) $this->tpl[0]->parse("SectGroupRow", true);

                                    $this->tpl[0]->set_var("SectWrapStart", "");
                                    $this->tpl[0]->set_var("SectWrapEnd", "");
                                }*/

            }
            reset($contents);

            $this->tpl[0]->set_var("content", implode("", $buffer));
        }

        return $i;
    }

    private function row_wrapper(&$buffer, $outer_wrap, $content, $count) {
        static $wrap_count = null;
        static $is_wrapped = false;
        static $count_contents = 0;

        $count_contents++;
        if($content) {
            if ($outer_wrap) {
                $is_wrapped = false;
                $wrap_count = $wrap_count + $outer_wrap;
                if ($wrap_count > 12) {
                    $buffer[] = '</div>';
                    $buffer[] = '<div class="' . $this->parent[0]->frameworkCSS->get("wrap", "form") . '">';
                    $is_wrapped = true;
                    $wrap_count = 0;
                } elseif ($wrap_count == $outer_wrap) { //first
                    $buffer[] = '<div class="' . $this->parent[0]->frameworkCSS->get("wrap", "form") . '">';
                    $is_wrapped = true;
                } elseif ($wrap_count) {
                    $is_wrapped = true;
                }

                $buffer[] = $content;
                if ($is_wrapped && $count_contents == $count) {
                    $buffer[] = '</div>';
                    $is_wrapped = false;
                    $wrap_count = 0;
                }
            } else {
                if ($wrap_count > 0 || $is_wrapped) {
                    $buffer[] = '</div>';
                    $wrap_count = 0;
                }
                $buffer[] = $content;
            }
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

		$this->tpl[0]->set_var("actions_class", $this->parent[0]->frameworkCSS->getClass($this->framework_css["actions"]));
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
			if ($this->tpl[0]->isset_var("error"))
			{
				$this->tpl[0]->set_var("error", $this->strError);
			}
			/*elseif (function_exists("mod_notifier_add_message_to_queue") && !MOD_NOTIFIER_DISABLE_AJAX && !$this->disable_mod_notifier_on_error)
			{
				mod_notifier_add_message_to_queue($this->strError, MOD_NOTIFIER_ERROR);
			}*/
			else
			{
				$this->tpl[0]->set_var("error_class", $this->parent[0]->frameworkCSS->getClass($this->framework_css["error"]));
				$this->tpl[0]->set_var("strError", $this->strError);
				$this->tpl[0]->parse("SectError", false);
			}
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

		if ($this->cursor_dialog && $this->record_exist)
		{
			// -------------
			//  FIRST

			$tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->getTheme());
			$tmp->id 			= "ActionButtonFirst";
			$tmp->label 		= $this->buttons_options["cursor_first"]["label"];
			$tmp->aspect 		= $this->buttons_options["cursor_first"]["aspect"];
			$tmp->action_type 	= "submit";
			$tmp->frmAction		= ($this->buttons_options["cursor_first"]["frmAction"] ? $this->buttons_options["cursor_first"]["frmAction"] : "first");

			if ($this->buttons_options["cursor_first"]["jsaction"])
				$tmp->jsaction = $this->buttons_options["cursor_first"]["jsaction"];
			else
				$tmp->jsaction = "javascript:ff.ffRecord.cursor.first('[[XHR_CTX_ID]]');";

			if (isset($this->buttons_options["cursor_first"]["class"]))
				$tmp->class			= $this->buttons_options["cursor_first"]["class"];
			else
				$tmp->class			= "noactivebuttons";

			if ($this->buttons_options["cursor_first"]["image"])
				$tmp->image = $this->buttons_options["cursor_first"]["image"];

			if ($this->buttons_options["cursor_first"]["newicon"])
				$tmp->newicon = $this->buttons_options["cursor_first"]["newicon"];

			$this->addActionButton(	  $tmp
									, $this->buttons_options["cursor_first"]["index"]);

			// -------------
			//  PREV

			$tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->getTheme());
			$tmp->id 			= "ActionButtonPrev";
			$tmp->label 		= $this->buttons_options["cursor_prev"]["label"];
			$tmp->aspect 		= $this->buttons_options["cursor_prev"]["aspect"];
			$tmp->action_type 	= "submit";
			$tmp->frmAction		= ($this->buttons_options["cursor_prev"]["frmAction"] ? $this->buttons_options["cursor_prev"]["frmAction"] : "prev");

			if ($this->buttons_options["cursor_prev"]["jsaction"])
				$tmp->jsaction = $this->buttons_options["cursor_prev"]["jsaction"];
			else
				$tmp->jsaction = "javascript:ff.ffRecord.cursor.prev('[[XHR_CTX_ID]]');";

			if (isset($this->buttons_options["cursor_prev"]["class"]))
				$tmp->class			= $this->buttons_options["cursor_prev"]["class"];
			else
				$tmp->class			= "noactivebuttons";

			if ($this->buttons_options["cursor_prev"]["image"])
				$tmp->image = $this->buttons_options["cursor_prev"]["image"];

			if ($this->buttons_options["cursor_prev"]["newicon"])
				$tmp->newicon = $this->buttons_options["cursor_prev"]["newicon"];

			$this->addActionButton(	  $tmp
									, $this->buttons_options["cursor_prev"]["index"]);
		}

		// PREPARE DEFAULT BUTTONS
		if ($this->buttons_options["cancel"]["display"])
		{
			if ($this->buttons_options["cancel"]["obj"] === null)
			{
				if($this->buttons_options["cancel"]["label"] === null)
					$this->buttons_options["cancel"]["label"] = ffTemplate::_get_word_by_code("ffRecord_close");

				$tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->getTheme());
				$tmp->id 			= "ActionButtonCancel";
				$tmp->label 		= $this->buttons_options["cancel"]["label"];
				$tmp->aspect 		= $this->buttons_options["cancel"]["aspect"];
				$tmp->action_type 	= "submit";
				$tmp->frmAction		= "close";

                if (isset($this->buttons_options["cancel"]["class"]))
					$tmp->class	= $this->buttons_options["cancel"]["class"];
                $tmp->icon  = $this->buttons_options["cancel"]["icon"];


/*				$tmp->class	= $this->parent[0]->frameworkCSS->get("cancel", $this->buttons_options["cancel"]["aspect"]);
				if ($this->buttons_options["cancel"]["class"])
					$tmp->class	.= " " . $this->buttons_options["cancel"]["class"];
*/
/*                if (isset($this->buttons_options["cancel"]["class"]))
                    $tmp->class     = $this->buttons_options["cancel"]["class"];
*/
				$this->buttons_options["cancel"]["obj"] =& $tmp;
			}
		}

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

		if ($this->cursor_dialog && $this->record_exist)
		{
			// -------------
			//  NEXT

			$tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->getTheme());
			$tmp->id 			= "ActionButtonNext";
			$tmp->label 		= $this->buttons_options["cursor_next"]["label"];
			$tmp->aspect 		= $this->buttons_options["cursor_next"]["aspect"];
			$tmp->action_type 	= "submit";
			$tmp->frmAction		= ($this->buttons_options["cursor_next"]["frmAction"] ? $this->buttons_options["cursor_next"]["frmAction"] : "next");

			if ($this->buttons_options["cursor_next"]["jsaction"])
				$tmp->jsaction = $this->buttons_options["cursor_next"]["jsaction"];
			else
				$tmp->jsaction = "javascript:ff.ffRecord.cursor.next('[[XHR_CTX_ID]]');";

			if (isset($this->buttons_options["cursor_next"]["class"]))
				$tmp->class			= $this->buttons_options["cursor_next"]["class"];
			else
				$tmp->class			= "noactivebuttons";

			if ($this->buttons_options["cursor_next"]["image"])
				$tmp->image = $this->buttons_options["cursor_next"]["image"];

			if ($this->buttons_options["cursor_next"]["newicon"])
				$tmp->newicon = $this->buttons_options["cursor_next"]["newicon"];

			$this->addActionButton(	  $tmp
									, $this->buttons_options["cursor_next"]["index"]);

			// -------------
			//  LAST

			$tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->getTheme());
			$tmp->id 			= "ActionButtonLast";
			$tmp->label 		= $this->buttons_options["cursor_last"]["label"];
			$tmp->aspect 		= $this->buttons_options["cursor_last"]["aspect"];
			$tmp->action_type 	= "submit";
			$tmp->frmAction		= ($this->buttons_options["cursor_last"]["frmAction"] ? $this->buttons_options["cursor_last"]["frmAction"] : "last");

			if ($this->buttons_options["cursor_last"]["jsaction"])
				$tmp->jsaction = $this->buttons_options["cursor_last"]["jsaction"];
			else
				$tmp->jsaction = "javascript:ff.ffRecord.cursor.last('[[XHR_CTX_ID]]');";

			if (isset($this->buttons_options["cursor_last"]["class"]))
				$tmp->class			= $this->buttons_options["cursor_last"]["class"];
			else
				$tmp->class			= "noactivebuttons";

			if ($this->buttons_options["cursor_last"]["image"])
				$tmp->image = $this->buttons_options["cursor_last"]["image"];

			if ($this->buttons_options["cursor_last"]["newicon"])
				$tmp->newicon = $this->buttons_options["cursor_last"]["newicon"];

			$this->addActionButton(	  $tmp
									, $this->buttons_options["cursor_last"]["index"]);
		}
	}

	/**
	 * Esegue un redirect in base alle caratteristiche del record e della richiesta, se XHR o meno
	 * @param String $url l'indirizzo di destinazione
	 * @return String
	 */
	/*function redirect($url)
	{
		if(!$url)
			$url = $_SERVER["REQUEST_URI"];

		return parent::redirect($url);
	}*/

	/**
	 * Esegue un reload dello stesso componente in base alle caratteristiche del record e della richiesta, se XHR o meno
	 * NB: se la richiesta non è XHR, viene effettuato un redirect completo
	 * @param String $url l'indirizzo di destinazione
	 * @return String
	 */
	function doReload($refresh_keys = true)
	{
		if ($this->parent[0]->isXHR())
		{
			$this->json_result["url"] = $this->parent[0]->getRequestUri($refresh_keys);
			$this->json_result["close"] = false;
			$this->json_result["component"] = $this->getIDIF();
			if ($this->parent[0]->getXHRSection())
				$this->json_result["section"] = $this->parent[0]->getXHRSection();
			$this->json_result["ctx"] = $this->parent[0]->getXHRCtx();
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
		if ($this->id_if !== null)
		{
            $tpl->set_var("prop_name",    "factory_id");
            $tpl->set_var("prop_value",   '"' . $this->id . '"');
            $tpl->parse("SectFFObjProperty",    true);
		}
	}

	function getProperties($property_set = null)
	{
		if ($property_set === null)
			$property_set = $this->properties;

		$buffer = "";
		if (is_array($property_set) && count($property_set))
		{
			foreach ($property_set as $key => $value)
			{
				if ($key == "style")
				{
					if (strlen($buffer))
						$buffer .= " ";
					$buffer .= $key . "=\"";
					foreach ($property_set[$key] as $subkey => $subvalue)
					{
						$buffer .= $subkey . ": " . $subvalue . ";";
					}
					reset($property_set[$key]);
					$buffer .= "\"";
				}
				elseif(strlen($value))
				{
					if (strlen($buffer))
						$buffer .= " ";
					$buffer .= $key . "=\"" . $value . "\"";
				}
			}
			reset($property_set);
		}
		return $buffer;
	}

	function setWidthComponent($resolution_large_to_small)
	{
		/*if(is_array($resolution_large_to_small) || is_numeric($resolution_large_to_small))
			$this->framework_css["component"]["grid"] = frameworkCSS::setResolution($resolution_large_to_small);
		elseif(strlen($resolution_large_to_small))
			$this->framework_css["component"]["grid"] = $resolution_large_to_small;
		else
			$this->framework_css["component"]["grid"] = false;*/
	}

	function addDefaultButton($type, $obj)
	{
		if($obj->label === null)
			$obj->label = ffTemplate::_get_word_by_code("ffRecord_" . $type);

		$obj->label = $this->buttons_options[$type]["icon"] . $obj->label;

		$obj->activebuttons	= $this->buttons_options[$type]["activebuttons"];
		$obj->icon  = $this->buttons_options[$type]["icon"];

		if(strlen($obj->url) && strpos($obj->url, "javascript:") === 0)
		{
			$obj->url = "javascript:void(0);";
			$obj->properties["onclick"] = substr($obj->url, strlen("javascript:"));
		}

		parent::addDefaultButton($type, $obj);
	}

	function setTitle($title, $class = null)
	{
		$this->framework_css["title"]["class"] = "dialogTitle " . $class;
		$this->title = $title;
	}
}
