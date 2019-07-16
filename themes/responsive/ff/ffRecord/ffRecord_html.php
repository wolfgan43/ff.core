<?php
frameworkCSS::extend(array(
            "records" => array(
                "default" => array(
                    "field" => array(
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
                    )
                    , "required" => "*"
                )
                , "inline" => array(
                    "field" => array(
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
                    )
                    , "required" => "*"
                )
            )
            , "outer_wrap" => null
			, "component" => array(
                "inner_wrap" => array(
                    "card" => "container"
                )
                , "header_wrap" => array(
                    "card" => "header"
                )
                , "body_wrap" => array(
                    "card" => "body"
                )
                , "footer_wrap" => array(
                    "class" => null
                    , "card" => "footer"
                )
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

			, "group" => array(
				"container" => array(
				    "class" => null
                )
				, "title" => array(
					"class" => "null"
				)
                , "description" => array(
                    "class" => null
                    //, "callout" => "info"
                )
			)
            , "footer" => array(
                "def" => array(
                    "class" => "d-flex"
                )
                , "require-note" => array(
                    "class" => "align-self-center"
                )
                , "actions" => array(
                    "class" => "ml-auto actions"
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

			, "error" => array(
				"class" => "error"
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
	), "ffRecord");


class ffRecord_html extends ffRecord_base
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
										);
	var $id_if					= null;
	var $type					= "inline";

	var $cursor_dialog 			= false;

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

	/**
	 * Variabile ad uso interno che conteggia il numero di campi visualizzati
	 * @var Int
	 */

	var $disable_mod_notifier_on_error = false;

	var $js_deps = array(
		"ff.ffRecord" => null
	);

	var $properties = null;


	function __construct($suffix = null)
    {
        parent::__construct();

        $this->framework_css = ffPage::getInstance()->frameworkCSS->findComponent("ffRecord" . $suffix);

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
				$content->framework_css = array_replace($this->framework_css["field"], $content->framework_css);
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
        if(is_array($key) ) {
            $params                     = $key;
            $key                        = ($params["title"]
                ? $params["title"]
                : $this->randID("group")
            );
        }

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
		//$this->tplDisplayControls();

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
		//$res = ffRecord::doEvent("on_tplParse", array(&$this, $this->tpl[0]));
		$this->doEvent("on_tpl_parse", array(&$this, $this->tpl[0]));

		$actions = $this->tplDisplayControls();
        if ($this->display_required && $this->display_required_note) {
            $require_note = '<small class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["footer"]["require-note"]) . '">' . $this->required_symbol . ' ' . ffTranslator::get_word_by_code("is required") . '</small>';
        }

        if($actions || $require_note) {
            $footer = '<div class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["footer"]["def"]) . '">' . $require_note . $actions . '</div>';
        }

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

        if($this->framework_css["component"]["footer_wrap"] && $footer) {
            $this->tpl[0]->set_var("footer_content", $footer);
            unset($footer);

            $this->tpl[0]->set_var("footer_wrap_start", '<div class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["component"]["footer_wrap"]) . '">');
            $this->tpl[0]->set_var("footer_wrap_end", '</div>');
        }

		if ($this->tab && count($this->tabs))
		{
			$this->tpl[0]->set_var("form_tabs", $this->parent[0]->widgets["tabs"]->process($this->getIDIF(), $this->tabs, $this->parent[0], $this->id));
		}

        $this->tpl[0]->set_var("fixed_pre_content", $this->fixed_pre_content);
        $this->tpl[0]->set_var("fixed_post_content", $this->fixed_post_content . $footer);

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
                            frameworkCSS::colsWrapper("row-" . $this->id . "-" . $tab_key
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
			/*if($this->title) {
                $this->tpl[0]->parse("SectTitle", false);
            }*/

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
                            $arrTitleProperties["class"] = $this->parent[0]->frameworkCSS->getClass($group["framework_css"]["title"], array("ffCheckDep"));
                            $arrTitleProperties["onclick"] = "javascript:ff.ffRecord.displayFieldSetElem(this, '" . $this->getIDIF() . "_" . $group["primary_field"] . "');";
                            $this->tpl[0]->set_var("title_properties", $this->getProperties($arrTitleProperties));
                            $this->tpl[0]->parse("SectGroupTitle", false);
                        } else {
                            if ($group["title"] && !$hide_title) {
                                $title_class = $this->parent[0]->frameworkCSS->getClass($group["framework_css"]["title"]);

                                $this->tpl[0]->set_var("title_properties", ($title_class
                                    ? ' class="' . $title_class . '"'
                                    : ''
                                ));

                                $this->tpl[0]->set_var("GroupTitle", $group["title"]);
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
		$this->tpl[0]->set_var("content", "");

		$i = 0;
        $buffer = array();
        if(is_array($contents) && count($contents))
        {
		    foreach ($contents as $key => $subvalue)
		    {
                $content = null;
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
                    if (!$this->display_required) {
                        $subvalue["data"]->framework_css["required"] = false;
                    }

                    // EVENT HANDLER
				    $this->doEvent("on_process_field", array(&$this, $key));

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

				    }

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

                    $content = ($this->display_values
                        ? $this->form_fields[$key]->getDisplayValue()
                        : $this->form_fields[$key]->process()
                    );
                    $outer_wrap = $this->form_fields[$key]->framework_css["outer_wrap"]["col"]["lg"];
			    }

                frameworkCSS::colsWrapper("content-" . $this->id . "-" . $group_key, $buffer, $outer_wrap, $content, count($contents));
		    }
		    reset($contents);

            $this->tpl[0]->set_var("content", implode("", $buffer));
        }
        
		return $i;
	}

	/**
	 * Visualizza i pulsanti del record
	 */
	protected function tplDisplayControls()
	{
		if ($this->hide_all_controls || !count($this->action_buttons))
		{
			//$this->tpl[0]->set_var("SectControls", "");
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

		/*
		$this->tpl[0]->set_var("actions_class", $this->parent[0]->frameworkCSS->getClass($this->framework_css["actions"]));
		$this->tpl[0]->set_var("ActionButtons", $buffer);
		$this->tpl[0]->parse("SectControls", false);
		*/
		return '<div class="' . $this->parent[0]->frameworkCSS->getClass($this->framework_css["footer"]["actions"]) . '">' . $buffer . '</div>';
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
        if ($this->hide_all_controls) {
            return;
        }

      /* if(!$this->parent[0]->ret_url || $this->parent[0]->ret_url == $_SERVER["REQUEST_URI"]) {
            $this->buttons_options["cancel"]["display"] = false;
        }*/

		parent::initControls();
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
        if(is_array($resolution_large_to_small) || is_numeric($resolution_large_to_small)) {
            $this->framework_css["outer_wrap"]["col"] = frameworkCSS::setResolution($resolution_large_to_small);
        } elseif(strlen($resolution_large_to_small)) {
            $this->framework_css["outer_wrap"]["row"] = $resolution_large_to_small;
        }
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
		$this->framework_css["title"]["class"] = $class;
		$this->title = $title;
	}
}
