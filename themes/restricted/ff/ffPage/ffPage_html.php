<?php
class ffPage_html extends ffPage_base 
{
	/**
	 * Il suffisso della dir del layer
	 * il file HTML si chiamerà layer_$layer.html
	 * posizionato sotto la dir del tema, sottodir "layers"
	 * @var String
	 */
	var $layer 					= "empty";

	/**
	 * Permette di alterare la directory di default da dove
	 * caricare il layer
	 * @var String
	 */
	var $layer_dir				= null;

	/**
	 * Contiene tutte le sezioni a disposizione del layout
	 * @var Array
	 */
	var $sections 				= array();

	/**
	 * Se deve essere utilizzato il framework Javascript
	 * @var Boolean
	 */
	var $use_own_js				= true;

	/**
	 * Javascript di default del framework
	 * @var Boolean
	 */
	var $default_own_js				= array("ff" => array(
														"path" => "/themes/library/ff"
														, "file" => "ff.js"
														, "async" => FALSE
														, "embed" => NULL
														, "exclude_compact" => FALSE
											)
											, "ff.ffevent" => array(
														"path" => "/themes/library/ff"
														, "file" => "ffEvent.js"
														, "async" => FALSE
														, "embed" => NULL
														, "exclude_compact" => FALSE
											)
											, "ff.ffevents" => array(
														"path" => "/themes/library/ff"
														, "file" => "ffEvents.js"
														, "async" => FALSE
														, "embed" => NULL
														, "exclude_compact" => FALSE
											)	
											, "ff.ffpage" => array(
														"path" => "/themes/library/ff"
														, "file" => "ffPage.js"
														, "async" => FALSE
														, "embed" => NULL
														, "exclude_compact" => FALSE
											)
									);
    var $cdn_version                = array(
                                        "jquery" => array(
                                                            "major" => "1"  
                                                            , "minor" => "11"
                                                            , "build" => "1"
                                                        )
                                        , "jquery.ui" => array(
                                                            "major" => "1"  
                                                            , "minor" => "10"
                                                            , "build" => "4"
                                                        )
                                        , "swfobject" => array(
                                                            "major" => "2"  
                                                            , "minor" => "2"
                                                        )
                                    );
	var $framework_css_setting		= array(
										"base" => array(
											"params" => array(
												"css" => ""
												, "js" => ""
												, "js_init" => ""
											)
											, "class" => array(
												"container" => "container"
												, "wrap" => "row"
												, "skip-full" => false
												, "row-prefix" => "row"
												, "col-append" => "col-"
												, "col-hidden" => "hidden-"
												, "col-hidden-smallest" => ""
												, "col-hidden-largest" => ""
												, "push-append" => "push-"
												, "push-prepend" => ""
												, "pull-append" => "pull-"
												, "pull-prepend" => ""
												, "skip-resolution" => true
												, "skip-prepost" => false
											)
											, "class-fluid" => array(
												"container" => "container-fluid"
												, "wrap" => "row-fluid clearfix"
												, "skip-full" => false
												, "row-prefix" => ""
												, "col-append" => "col-"
												, "col-hidden" => "hidden-"
												, "col-hidden-smallest" => ""
												, "col-hidden-largest" => ""
												, "push-append" => "push-"
												, "push-prepend" => ""
												, "pull-append" => "pull-"
												, "pull-prepend" => ""
												, "skip-resolution" => true
												, "skip-prepost" => false
											)
											, "resolution" => array()
                                            , "button" => array(
                                                "base"              => "btn"
                                                , "skip-default"    => false
                                                , "width"       => array(
                                                    "full"          => "expand"
                                                )
                                                , "size"        => array(
                                                    "large"         => "large"
                                                    , "small"       => "small"
                                                    , "tiny"        => "tiny"
                                                )
                                                , "state"       => array(
                                                    "current"       => "current"
                                                    , "disabled"    => "disabled"
                                                )
                                                , "corner"      => array(
                                                    "round"         => "round"
                                                    , "radius"      => "radius"
                                                )
                                                , "color"       => array(
                                                    "default"       => ""
                                                    , "primary"     => "primary"
                                                    , "success"     => "success"
                                                    , "info"        => "info"
                                                    , "warning"     => "warning"
                                                    , "danger"      => "danger"
                                                    , "link"        => "link"
                                                )                                                
                                            )                                            
											, "form" => array(
												"component" => ""
												, "component-inline" => ""
												, "row" => "row"
												, "row-full" => "row"
												, "group" => "row-smart"
												, "label" => ""
												, "label-inline" => "inline"
												, "control" => ""
                                                , "control-exclude" => array()
												, "control-check-position" => "_pre_label"
												, "control-prefix" => "prefix"
												, "control-postfix" => "postfix"
												, "control-feedback" => "postfix-feedback"
												, "wrap-addon" => false
											)
                                            , "bar" => array(
                                                "topbar" => "topbar"
                                                , "navbar" => "navbar"
                                            )
											, "callout" => array(
												"default"       => "callout"
												, "primary"     => "callout callout-primary"
												, "success"     => "callout callout-success"
												, "info"        => "callout callout-info"
												, "warning"     => "callout callout-warning"
												, "danger"      => "callout callout-danger"
											)
											
											/*da trovare e gestire:
											show		bs
											radius 		fd
											round		fd
											active		bs
											disabled		bs
											img-rounded		bs
											img-circle		bs
											img-thumbnail		bs
											
											text-muted		bs
											text-primary		bs
											text-success		bs
											text-info		bs
											text-warning		bs
											text-danger		bs
											
											bg-primary		bs
											bg-success		bs
											bg-info			bs
											bg-warning		bs
											bg-danger		bs
											
											center-block	bs
											
											clearfix		bs
											invisible		bs
											text-hide		bs
											
												
											*/
											, "pagination" => array(
												"align-left" => "text-left"
												, "align-center" => "text-center"
												, "align-right" => "text-right"
												, "pages" => "pagination"
												, "arrows" => "arrow"
												, "current" => "current"
											)
											, "util" => array(
												"left" => "left"
												, "right" => "right"
												, "hide" => "hidden"
												, "align-left" => "align-left"
												, "align-center" => "align-center"
												, "align-right" => "align-right"
												, "align-justify" => "align-justify"
												, "text-nowrap" => "text-nowrap"
												, "text-overflow" => "text-overflow"
												, "text-lowercase" => "text-lowercase"
												, "text-uppercase" => "text-uppercase"
												, "text-capitalize" => "text-capitalize"
												, "current" => "current"
												, "equalizer-row" => "data-equalizer"
												, "equalizer-col" => "data-equalizer-watch"
											)
										)
										, "bootstrap" => array(
											"params" => array(
												"css" => "//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"
												, "js" => "//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js" 
												, "js_init" => ""
											)
											, "class" => array(
												"container" => "container"
												, "wrap" => "container"
												, "skip-full" => false 
												, "row-prefix" => "container"
												, "col-prefix" => ""
												, "col-append" => "col-"
												, "col-hidden" => "hidden-"
												, "col-hidden-smallest" => ""
												, "col-hidden-largest" => ""
												, "push-append" => "col-"
												, "push-prepend" => "push-"
												, "pull-append" => "col-"
												, "pull-prepend" => "pull-"
											)
											, "class-fluid" => array(
												"container" => "container-fluid"
												, "wrap" => "row"
												, "skip-full" => false
												, "row-prefix" => "row"
												, "col-prefix" => ""
												, "col-append" => "col-"
												, "col-hidden" => "hidden-"
												, "col-hidden-smallest" => ""
												, "col-hidden-largest" => ""
												, "push-append" => "col-"
												, "push-prepend" => "push-"
												, "pull-append" => "col-"
												, "pull-prepend" => "pull-"
											)
											, "resolution" => array(
												"xs"
												, "sm"
												, "md"
												, "lg"
											)
                                            , "button" => array(
                                                "base"              => "btn"
                                                , "skip-default"    => true
                                                , "width"       => array(
                                                    "full"          => "btn-block"
                                                )
                                                , "size"        => array(
                                                    "large"         => "btn-lg"
                                                    , "small"       => "btn-sm"
                                                    , "tiny"        => "btn-xs"
                                                )
                                                , "state"       => array(
                                                    "current"       => "active"
                                                    , "disabled"    => "disabled"
                                                )
                                                , "corner"      => array(
                                                    "round"         => false
                                                    , "radius"      => false
                                                )
                                                , "color"       => array(
                                                    "default"       => "btn-default"
                                                    , "primary"     => "btn-primary"
                                                    , "success"     => "btn-success"
                                                    , "info"        => "btn-info"
                                                    , "warning"     => "btn-warning"
                                                    , "danger"      => "btn-danger"
                                                    , "link"        => "btn-link"
                                                )                                                
                                            )
											, "form" => array(
												"component" => ""
												, "component-inline" => "form-horizontal"
												, "row" => "form-group clearfix"
												, "row-full" => "form-group clearfix"
												, "group" => "input-group"
												, "label" => ""
												, "label-inline" => "control-label"
												, "control" => "form-control"
                                                , "control-exclude" => array("checkbox", "radio")
												, "control-check-position" => "_in_label"
												, "control-prefix" => "input-group-addon"
												, "control-postfix" => "input-group-addon"
												, "control-feedback" => "form-control-feedback"
												, "wrap-addon" => false
											)
                                            , "bar" => array(
                                                "topbar" => "nav navbar-nav"
                                                , "navbar" => "nav nav-pills"
                                            )
											, "callout" => array(
												"default"       => "bs-callout"
												, "primary"     => "bs-callout bs-callout-primary"
												, "success"     => "bs-callout bs-callout-success"
												, "info"        => "bs-callout bs-callout-info"
												, "warning"     => "bs-callout bs-callout-warning"
												, "danger"      => "bs-callout bs-callout-danger"
											)
											, "pagination" => array(
												"align-left" => "text-left" 
												, "align-center" => "text-center" 
												, "align-right" => "text-right"
												, "pages" => "pagination"
												, "arrows" => ""
												, "current" => "active"
											)											
											, "util" => array(
												"left" => "pull-left"
												, "right" => "pull-right"
												, "hide" => "hidden"
												, "align-left" => "text-left"
												, "align-center" => "text-center"
												, "align-right" => "text-right"
												, "align-justify" => "text-justify"
												, "text-nowrap" => "text-nowrap"
												, "text-overflow" => "text-overflow"
												, "text-lowercase" => "text-lowercase"
												, "text-uppercase" => "text-uppercase"
												, "text-capitalize" => "text-capitalize"
												, "current" => "active"
												, "equalizer-row" => "data-equalizer"
												, "equalizer-col" => "data-equalizer-watch"
											)
											, "theme" => array(
												"amelia" => "//netdna.bootstrapcdn.com/bootswatch/2.3.2/amelia/bootstrap.min.css"
												, "cerulean" => "//netdna.bootstrapcdn.com/bootswatch/2.3.2/cerulean/bootstrap.min.css"
												, "cosmo" => "//netdna.bootstrapcdn.com/bootswatch/2.3.2/cosmo/bootstrap.min.css"
												, "cyborg" => "//netdna.bootstrapcdn.com/bootswatch/2.3.2/cyborg/bootstrap.min.css"
												, "flatly" => "//netdna.bootstrapcdn.com/bootswatch/2.3.2/flatly/bootstrap.min.css"
												, "journal" => "//netdna.bootstrapcdn.com/bootswatch/2.3.2/journal/bootstrap.min.css"
												, "readable" => "//netdna.bootstrapcdn.com/bootswatch/2.3.2/readable/bootstrap.min.css"
												, "simplex" => "//netdna.bootstrapcdn.com/bootswatch/2.3.2/simplex/bootstrap.min.css"
												, "slate" => "//netdna.bootstrapcdn.com/bootswatch/2.3.2/slate/bootstrap.min.css"
												, "spacelab" => "//netdna.bootstrapcdn.com/bootswatch/2.3.2/spacelab/bootstrap.min.css"
												, "united" => "//netdna.bootstrapcdn.com/bootswatch/2.3.2/united/bootstrap.min.css"
											)
										)
										, "foundation" => array(
											"params" => array(
												"css" => "//cdnjs.cloudflare.com/ajax/libs/foundation/5.5.0/css/foundation.min.css" 
												, "js" => "//cdnjs.cloudflare.com/ajax/libs/foundation/5.5.0/js/foundation.min.js"
												, "js_init" => 'jQuery(function() { jQuery(document).foundation(); });'//non funziona con la cache
											)
											, "class" => array(
												"container" => "container"
												, "wrap" => "row"
												, "skip-full" => true
												, "row-prefix" => "row"
												, "col-prefix" => "columns"
												, "col-hidden" => "hide-for-"
												, "col-hidden-smallest" => ""
												, "col-hidden-largest" => "-up"
												, "push-append" => ""
												, "push-prepend" => "push-"
												, "pull-append" => ""
												, "pull-prepend" => "pull-"
												, "skip-resolution" => false
												, "skip-prepost" => false
											)
											, "class-fluid" => array(
												"container" => "container-fluid"
												, "wrap" => "row-fluid clearfix"
												, "skip-full" => true
												, "row-prefix" => ""
												, "col-prefix" => "columns"
												, "col-hidden" => "hide-for-"
												, "col-hidden-smallest" => ""
												, "col-hidden-largest" => "-up"
												, "push-append" => ""
												, "push-prepend" => "push-"
												, "pull-append" => ""
												, "pull-prepend" => "pull-"
												, "skip-resolution" => false
												, "skip-prepost" => false 
											)
											, "resolution" => array(
												"small"
												, "medium"
												, "large"
											)
                                            , "button" => array(
                                                "base"   => "button"
                                                , "skip-default"    => true
                                                , "width"     => array(
                                                    "full"          => "expand"
                                                )
                                                , "size"    => array(
                                                    "large"         => "large"
                                                    , "small"       => "small"
                                                    , "tiny"        => "tiny"
                                                )
                                                , "state"   => array(
                                                    "current"       => "current"
                                                    , "disabled"    => "disabled"
                                                )
                                                , "corner"  => array(
                                                    "round"         => "round"
                                                    , "radius"      => "radius"
                                                )
                                                , "color"   => array(
                                                    "default"     => "secondary"
                                                    , "primary"     => ""
                                                    , "success"     => "success"
                                                    , "info"        => "secondary"
                                                    , "warning"     => "alert"
                                                    , "danger"      => "alert"
                                                    , "link"        => "secondary"
                                                )                                                
                                            )                                            
											, "form" => array(
												"component" => ""
												, "component-inline" => ""
												, "row" => "row"
												, "row-full" => "columns"
												, "group" => "row collapse"
												, "label" => ""
												, "label-inline" => "inline right"
												, "control" => ""
                                                , "control-exclude" => array()
												, "control-check-position" => "_pre_label"
												, "control-prefix" => "prefix"
												, "control-postfix" => "postfix"
												, "control-feedback" => "postfix-feedback"
												, "wrap-addon" => true
											)
                                            , "bar" => array(
                                                "topbar" => "top-bar top-bar-section"
                                                , "navbar" => "sub-nav"
                                            )                                            
											, "callout" => array(
												"default"       => "panel"
												, "primary"     => "alert-box"
												, "success"     => "alert-box success"
												, "info"        => "panel callout"
												, "warning"     => "alert-box warning"
												, "danger"      => "alert-box alert"
											)
											, "pagination" => array(
												"align-left" => "text-left" 
												, "align-center" => "pagination-centered" //"text-center"
												, "align-right" => "text-right"
												, "pages" => "pagination"
												, "arrows" => "arrow"
												, "current" => "current"
											)											
											, "util" => array(
												"left" => "left"
												, "right" => "right"
												, "hide" => "hide"
												, "align-left" => "text-left"
												, "align-center" => "text-center"
												, "align-right" => "text-right"
												, "align-justify" => "text-justify"
												, "text-nowrap" => "text-nowrap"	
												, "text-overflow" => "text-overflow" 		//custom
												, "text-lowercase" => "text-lowercase" 		//custom
												, "text-uppercase" => "text-uppercase" 		//custom
												, "text-capitalize" => "text-capitalize"	//custom
												, "current" => "active"
												, "equalizer-row" => "data-equalizer"
												, "equalizer-col" => "data-equalizer-watch"
											)
										)
									);                                    
	var $font_icon_setting			= array(
										"base" => array(
											 "css" => ""
											, "prefix" => "icon"
											, "postfix" => ""
											, "prepend" => "ico-"
											, "append" => ""
										)
										, "glyphicons" => array(
											 "css" => "//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css"
											, "prefix" => "glyphicons"
											, "postfix" => ""
											, "prepend" => ""
											, "append" => ""
										)
										, "fontawesome" => array(
											 "css" => "//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.css"
											, "prefix" => "fa"
											, "postfix" => ""
											, "prepend" => "fa-"
											, "append" => ""
										)
									);                                    

    var $framework_css              = null;
    var $font_icon					= null;
	/**
	 * Abilita o disabilita l'utilizzo del form
	 * il default (null) lo considera abilitato
	 * @var Boolean
	 */
	var $use_own_form			= null;

    /**
	 * L'id del form di default
	 * @var String
	 */
	var $form_id                = "frmMain";
	/**
	 * il nome del form di default
	 * @var String
	 */
    var $form_name              = "frmMain";

	/**
	 * il metodo del form di default
	 * blank sarà autoselezionato sulla base dei componenti presenti nella pagina
	 * altrinenti può essere impostato a GET o POST
	 * @var String
	 */
	var $form_method			= "";

	/**
	 * L'azione da impostare con il form. Corrisponde all'url di destinazione
	 * @var String
	 */
	var $form_action			= "";

	/**
	 * L'enctype del form, se blank viene selezionato automaticamente
	 * @var String
	 */
	var $form_enctype			= "";
	
	var $form_workaround		= true;
	/**
	 * Il tema di jquery.ui
	 * @var String
	 */
	var $jquery_ui_theme 		= "smoothness";

	/**
	 * Il tema alternativo di jquery.ui
	 * @var String
	 */
	var $jquery_ui_force_theme 	= null;
	
	/**
	 * Il nome del template da caricare per l'utilizzo con ffPage
	 * @var String
	 */
	public $template_file 			= "ffPage.html";

	/**
	 * Un Array di variabili fisse da utilizzare per essere
	 * inserite all'interno del template
	 * nella forma coppia chiave/valore
	 * @var Array
	 */
	public $fixed_vars 				= array();

	/**
	 * Se i css devono essere compattati in un unico file
	 * @var Boolean
	 */
	public $compact_css 		   = false;
	public $css_buffer 			   = array();
    public $override_css           = array();
	/**
	 * I css caricati nella pagina, identificati da un TAG
	 * @var Array
	 */
	public $page_css				= array();
	public $compact_js 			   = false;
	public $js_buffer 			   = array();
    public $override_js            = array(); 
	/**
	 * I Javascript caricati nella pagina, identificati da un TAG
	 * @var Array
	 */
    public $page_js                	= array();
	public $compress			   	= false;
	public $minify					= "strip"; // can be: false, strip, strong_strip, minify
    public $page_defer				= array(); // array keys js and css of all compress resources (js and css)
	
	/**
	 * I Meta caricati nella pagina, identificati da un TAG
	 * @var Array
	 */
    public $page_meta              = array();

	/**
	 * I Meta caricati nella pagina, identificati da un TAG
	 * @var Array
	 */
    public $page_html_attr         = array();
    
	/**
	 * Una classe da impostare sul tag BODY
	 * @var String
	 */
    public $class_body             = null;

	/**
	 * Il risultato JSON della pagina
	 * @var Array
	 */
	var $json_result = array();
	
	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)

	/**
	 * l'oggetto ffTemplate usato per il layer
	 * @var Array
	 */
	var $tpl_layer				= null;
	
	
	var $browser = null;
	
	var $canonical = null;
	
	/**
	 * Il costruttore, istanzia un nuovo oggetto ffPage
	 * @param String $site_path
	 * @param String $disk_path
	 * @param String $page_path
	 * @param String $theme
	 */
	public function __construct($site_path, $disk_path, $page_path, $theme)
	{
		parent::__construct($site_path, $disk_path, $page_path, $theme);

		$registry = ffGlobals::getInstance("_registry_");

		if (isset($registry->themes[$this->theme]))
		{
			ffTheme_html_construct($this, $this->theme);
		}

		if ($this->theme !== cm_getMainTheme() && (!isset($registry->themes[$this->theme]) || !isset($registry->themes[$this->theme]->exclude_main_theme_defaults)))
		{
			ffTheme_html_construct($this, cm_getMainTheme());
		}

		$this->tplAddJs("ff.ffPage", "ffPage.js", FF_THEME_DIR . "/library/ff"); 
	}

	/**
	 * Recupera la directory in cui è contenuto il layer
	 * @param String $layer_file Permette di specificare un nome file aggiuntivo da passare all'evento getLayerDir
	 * @return String
	 */
	function getLayerDir($layer_file = null)
	{
		if ($this->layer_dir !== null)
			return $this->layer_dir;
		
		$res = $this->doEvent("getLayerDir", array(&$this, $layer_file));
		$last_res = end($res);
		if ($last_res !== null)
			return $last_res;
		else
			return $this->disk_path . "/themes/" . $this->getTheme() . "/layouts";
	}

	/**
	 * Recupera la directory da cui caricare tutti gli elementi di layout
	 * @param String $layout_file Permette di specificare un nome file aggiuntivo da passare all'evento getLayoutDir
	 * @return String
	 */
	function getLayoutDir($layout_file = null)
	{
		$res = $this->doEvent("getLayoutDir", array(&$this, $layout_file));
		$last_res = end($res);
		if ($last_res !== null)
			return $last_res;
		else
			return $this->disk_path . "/themes/" . $this->getTheme() . "/layouts";
	}

	/**
	 * Aggiunge un CSS alla pagina
	 * @param String $tag
	 * @param String $file
	 * @param String $path
	 * @param String $css_rel di default è "stylesheet"
	 * @param String $css_type di default è "text/css"
	 * @param Boolean $overwrite
	 * @param Boolean $async
	 * @param String $css_media
	 * @param Boolean $exclude_compact
	 * @param String $priority la coda di priorità di caricamento del CSS, di default è "top"
	 * @return Boolean se l'aggiunta ha avuto successo o meno
	 */
	public function tplAddCss($tag, $file = null, $path = null, $css_rel = "stylesheet", $css_type = "text/css", $overwrite = false, $async = false, $css_media = null, $exclude_compact = false, $priority = "top", $embed = null)
	{
		static $last_top = 0;
		static $bottom_exist = false;
        $found = false;

        if ($file !== null && is_array($file))
        {
            $params = $file;
            $file = null;
            if (ffIsset($params, "file"))				$file = $params["file"];
            if (ffIsset($params, "path"))				$path = $params["path"];
            if (ffIsset($params, "overwrite"))			$overwrite = $params["overwrite"];
            if (ffIsset($params, "rel"))				$css_rel = $params["rel"];
            if (ffIsset($params, "css_rel"))			$css_rel = $params["css_rel"];
            if (ffIsset($params, "type"))				$css_type = $params["type"];
            if (ffIsset($params, "css_type"))			$css_type = $params["css_type"];
            if (ffIsset($params, "overwrite"))			$overwrite = $params["overwrite"];
            if (ffIsset($params, "async"))				$async = $params["async"];
            if (ffIsset($params, "css_media"))			$css_media = $params["css_media"];
            if (ffIsset($params, "exclude_compact"))	$exclude_compact = $params["exclude_compact"];
            if (ffIsset($params, "priority"))			$priority = $params["priority"];
            if (ffIsset($params, "embed"))				$embed = $params["embed"];
            if (ffIsset($params, "index"))				$index = $params["index"];
            if (ffIsset($params, "version"))			$version = $params["version"];
        }

        foreach ($this->page_css AS $css_key => $css_value)
        {
            if(
                    $css_value["path"] == $path
                    && $css_value["file"] == $file
                    && $css_value["path"] !== null
                    && $css_value["file"] !== null
                )
            {
                $found = $css_key;
                break;
            }
        }
        reset($this->page_css);

		if (!$found && isset($this->page_css[strtolower($tag)]))
			$found = strtolower($tag);

        if(!$found)
        {
        	if($priority == "first") {
        		
        		$this->page_css = array(strtolower($tag) => array(
															  "path" => $path
															, "file" => $file
	                                                        , "rel"  => $css_rel
	                                                        , "type" => $css_type
	                                                        , "async" => $async
	                                                        , "media" => $css_media
	                                                        , "exclude_compact" => $exclude_compact
	                                                        , "embed" => $embed
														)) + $this->page_css;
	            $last_top++;
        	} elseif($priority == "top" && $bottom_exist) {
				$tmp_css_top = array_slice($this->page_css, 0, $last_top, true);
				$tmp_css_bottom = array_slice($this->page_css, $last_top, count($this->page_css) - $last_top, true);
				$tmp_css_top[strtolower($tag)] = array(
															  "path" => $path
															, "file" => $file
	                                                        , "rel"  => $css_rel
	                                                        , "type" => $css_type
	                                                        , "async" => $async
	                                                        , "media" => $css_media
	                                                        , "exclude_compact" => $exclude_compact
	                                                        , "embed" => $embed
														);
	            $this->page_css = array_merge($tmp_css_top, $tmp_css_bottom);

	            $last_top++;
			} else {
			    $this->page_css[strtolower($tag)] = array(
															  "path" => $path
															, "file" => $file
	                                                        , "rel"  => $css_rel
	                                                        , "type" => $css_type
	                                                        , "async" => $async
	                                                        , "media" => $css_media
	                                                        , "exclude_compact" => $exclude_compact
	                                                        , "embed" => $embed
														);
	            if(!$bottom_exist && count($this->page_css) > 1)
	            	$last_top++;
	            
	            $bottom_exist = true;
			}			
        }
		elseif ($overwrite)
		{
		    $this->page_css[$found] = array(
														  "path" => $path
														, "file" => $file
                                                        , "rel"  => $css_rel
                                                        , "type" => $css_type
                                                        , "async" => $async
                                                        , "media" => $css_media
                                                        , "exclude_compact" => $exclude_compact
                                                        , "embed" => $embed
													);
		}
		else
			return false;

		return true;
	}
	
    /**
	 * Aggiunge un Javascript alla pagina
	 * @param String $tag un nome univoco
	 * @param String $file il nome del file
	 * @param String $path il percorso del file
	 * @param Boolean $overwrite se deve sovrascrivere se già presente
	 * @param Boolean $async se deve essere caricato in modo asincrono, tramite il framework JS
	 * @param Boolean $embed specifica l'attributo EMBED
	 * @return Boolean l'esito dell'operazione
	 */
    public function tplAddJs($tag, $file = null, $path = null, $overwrite = false, $async = false, $embed = null, $exclude_compact = false, $priority = "top")
    {
		static $last_top = 0;
		static $bottom_exist = true;
        $found = false;

        if ($file !== null && is_array($file))
        {
            $params = $file;
            $file = null;
            if (ffIsset($params, "file"))				$file = $params["file"];
            if (ffIsset($params, "path"))				$path = $params["path"];
            if (ffIsset($params, "overwrite"))			$overwrite = $params["overwrite"];
            if (ffIsset($params, "async"))				$async = $params["async"];
            if (ffIsset($params, "embed"))				$embed = $params["embed"];
            if (ffIsset($params, "exclude_compact"))	$exclude_compact = $params["exclude_compact"];
            if (ffIsset($params, "priority"))			$priority = $params["priority"];
            if (ffIsset($params, "index"))				$index = $params["index"];
            if (ffIsset($params, "version"))			$version = $params["version"];
        }

        foreach ($this->page_js AS $js_key => $js_value)
        {
            if(
            		   $js_value["path"] == $path 
            		&& $js_value["file"] == $file
            		&& $js_value["path"] !== null 
            		&& $js_value["file"] !== null
            	) 
            {
                $found = $js_key;
                break;
            }
        }
        reset($this->page_js);
        
		if (!$found && isset($this->page_js[strtolower($tag)]))
			$found = strtolower($tag);

        if (!$found)
        {
        	if($priority == "first") {
        		$this->page_js = array(strtolower($tag) => array(
	                                                          "path" => $path
	                                                        , "file" => $file
	                                                        , "async" => $async
	                                                        , "embed" => $embed
	                                                        , "exclude_compact" => $exclude_compact
	                                                    )) + $this->page_js;
	            $last_top++;
        	} elseif($priority == "top" && $bottom_exist) {
				$tmp_js_top = array_slice($this->page_js, 0, $last_top, true);
				$tmp_js_bottom = array_slice($this->page_js, $last_top, count($this->page_js) - $last_top, true);
				$tmp_js_top[strtolower($tag)] = array(
	                                                          "path" => $path
	                                                        , "file" => $file
	                                                        , "async" => $async
	                                                        , "embed" => $embed
	                                                        , "exclude_compact" => $exclude_compact
	                                                    );
	            $this->page_js = array_merge($tmp_js_top, $tmp_js_bottom);

	            $last_top++;
			} else {
	            $this->page_js[strtolower($tag)] = array(
	                                                          "path" => $path
	                                                        , "file" => $file
	                                                        , "async" => $async
	                                                        , "embed" => $embed
	                                                        , "exclude_compact" => $exclude_compact
	                                                    );
	            if(!$bottom_exist)
	            	$last_top++;
	            
	            $bottom_exist = true;
			}
        }
        elseif ($overwrite)
        {
            $this->page_js[$found] = array(
                                                          "path" => $path
                                                        , "file" => $file
                                                        , "async" => $async
                                                        , "embed" => $embed
                                                        , "exclude_compact" => $exclude_compact
                                                    );
        }
		else
			return false;

		return true;
    }

    /**
	 * Aggiunge un META-TAG alla pagina
	 * @param String $name il nome del meta
	 * @param String $content il contenuto
	 * @param Boolean $overwrite se deve sovrascrivere se già presente, di default false
	 * @param String $type l'attribugo type del meta, di default "name"
	 * @return Boolean l'esito dell'azione
	 */
	public function tplAddMeta($name, $content, $overwrite = false, $type = "name")
    {
		if (!isset($this->page_meta[$name]) || $overwrite)
		{
			$this->page_meta[$name] = array(
										"name" => $name
										, "content" => $content
										, "type" => $type
									);
			return true;
		}
		else
			return false;
    }
    /**
	 * Aggiunge un Attributo al TAG html
	 * @param String $name il nome dell'attributo
	 * @param String $content il contenuto
	 * @param Boolean $overwrite se deve sovrascrivere se già presente, di default false
	 * @param String $type l'attribugo type del meta, di default "name"
	 * @return Boolean l'esito dell'azione
	 */
	public function tplAddHtmlAttr($content, $overwrite = false, $type = "xmlns")
    {
		if (!isset($this->page_html_attr[$content]) || $overwrite)
		{
			$this->page_html_attr[$content] = array(
										"content" => $content
										, "type" => $type
									);
			return true;
		}
		else
			return false;
    }
    
	public function tplLoad($tpl = null)
	{
		if ($this->template_loaded)
		    return;

		if ($tpl === null)
		{
			if ($this->getXHRDialog() && $this->template_file === "ffPage.html")
			{
				$tmp = $this->getTemplateDir("ffPage_dialog.html");
				if ($tmp !== null)
				{
					$this->tpl[0] = ffTemplate::factory($tmp);
					$this->tpl[0]->load_file("ffPage_dialog.html", "main");
				}
				else
				{
					$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
					$this->tpl[0]->load_file($this->template_file, "main");
				}
			}
			else 
			{
				$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
				$this->tpl[0]->load_file($this->template_file, "main");
			}
		}
		else
			$this->tpl[0] = $tpl;
		
        $this->tpl[0]->compress = $this->compress;
        $this->tpl[0]->minify = $this->minify;

		// ff.js
        $this->doEvent("on_tpl_load", array($this, $this->tpl));		
		
        $this->tplProcessVars($this->tpl);
		
		$this->tplParseHidden();
		
		if (is_array($this->globals) && count($this->globals))
		{
			foreach ($this->globals as $key => $value)
			{
				if (!$value["display_hidden"])
					continue;
				
				$this->tpl[0]->set_var("varname", $key);
				$this->tpl[0]->set_var("varvalue", $this->retrieve_global($key));
				$this->tpl[0]->parse("SectFormHidden", true);
				$this->tpl[0]->parse("SectHiddenFields", false);
			}
			reset($this->globals);
		}
			
		$this->doEvent("on_tpl_loaded", array(&$this, $this->tpl));

		// LAYER SECTION
		if (strlen($this->layer) && !$this->isXHR())
		{                                   
			$this->tpl_layer[0] = ffTemplate::factory($this->getLayerDir("layer_" . $this->layer  . ".html"));
			$this->tpl_layer[0]->load_file("layer_" . $this->layer  . ".html", "main");
			
			//$this->tpl_layer[0]->strip_extra_newlines = $this->strip_extra_newlines;
			
			$res = $this->doEvent("on_tpl_layer_load", array(&$this, $this->tpl_layer[0]));

			$this->tplProcessVars($this->tpl_layer);
			$this->tplSetGlobals($this->tpl_layer);

			$res = $this->doEvent("on_tpl_layer_loaded", array(&$this, $this->tpl_layer[0]));

			// SECTIONS
			if (property_exists("ffPage_html", "navbar") && strlen($this->navbar))
			{
				ffErrorHandler::raise("Obsolete use of ->navbar, use ->sections[\"navbar\"] instead", E_USER_ERROR, $this, get_defined_vars());
			}

			if (property_exists("ffPage_html", "topbar") && strlen($this->topbar))
			{
				ffErrorHandler::raise("Obsolete use of ->topbar, use ->sections[\"topbar\"] instead", E_USER_ERROR, $this, get_defined_vars());
			}

			if (is_array($this->sections) && count($this->sections))
			{
				foreach ($this->sections as $key => $value)
				{
					if (strlen($value["name"]))
					{
						if ($value["is_php"])
						{
							ob_start();
							require($value["name"]);
							$this->tpl[0]->set_var($key, ob_get_contents());
							$this->tpl_layer[0]->set_var($key, ob_get_contents());
							ob_end_clean();
						}
						else
						{
							if ($value["dir"] === null)
								$this->sections[$key]["tpl"] = ffTemplate::factory($this->getLayoutDir($key . "_" . $value["name"] . ".html"));
							else
								$this->sections[$key]["tpl"] = ffTemplate::factory($value["dir"]);
							
							$this->sections[$key]["tpl"]->load_file($key . "_" . $value["name"] . ".html", "main");

							//$this->sections[$key]["tpl"]->strip_extra_newlines = $this->strip_extra_newlines;
							
							$this->tplProcessVars(array(&$this->sections[$key]["tpl"]));
							$this->tplSetGlobals(array(&$this->sections[$key]["tpl"]));

							if ($this->sections[$key]["events"] === null)
								$this->sections[$key]["events"] = new ffEvents();
							else
								$this->sections[$key]["events"]->doEvent("on_load_template", array(&$this, &$this->sections[$key]["tpl"]));
						}
					}
				}
				reset($this->sections);
			}
		}
		// END OF LOADING
		$this->template_loaded = true;
	}
	
	public function tplParseHidden()
	{
		$this->tpl[0]->set_var("SectFormHidden", "");
		if (is_array($this->hidden_fields) && count($this->hidden_fields))
		{
			foreach ($this->hidden_fields as $key => $value)
			{
				$this->tpl[0]->set_var("varname", $key);
				if (is_object($value["field"]))
				{
					switch (get_class($value["field"]))
					{
						case "ffField":
						case "ffData":
							$this->tpl[0]->set_var("varvalue", $value["field"]->getValue($value["type"], $value["locale"]));
							break;
							
						default:
							$this->tpl[0]->set_var("varvalue", $value["field"]);
					}
				}
				else
					$this->tpl[0]->set_var("varvalue", $value["field"]);
				$this->tpl[0]->parse("SectFormHidden", true);
				$this->tpl[0]->parse("SectHiddenFields", false);
			}
			reset($this->hidden_fields);
		}
	}

	/**
	 * Elabora i componenti di transizione, cioè quei componenti segnalati ma non presenti nella pagina
	 */
	protected function tplProcessBounceComponents()
	{
		$varcount = FALSE;
		if (is_array($this->bounce_components) && count($this->bounce_components))
		{
			foreach ($this->bounce_components as $key => $value)
			{
				if (isset($this->params[$key]) && is_array($this->params[$key]) && count($this->params[$key]))
				{
					$varcount = true;
					foreach ($this->params[$key] as $subkey => $subvalue)
					{
						if (is_array($subvalue))
						{
							$this->tplProcessBounceArray($subvalue, $key, $subkey);
						}
						elseif ($subkey !== "frmAction")
						{
							$this->tpl[0]->set_var("varname", $key . "_" . $subkey);
							$this->tpl[0]->set_var("varvalue", $subvalue);
							$this->tpl[0]->parse("SectFormHidden", true);
						}
					}
					reset($this->params[$key]);
				}
			}
			reset($this->bounce_components);
		}
		return;
	}
		
	/**
	 * Elabora gli array di transizione per i bounce componente, funzione ricorsiva di supporto
	 */
	protected function tplProcessBounceArray($array, $prefix, $arrayname, $arraykeys = "")
	{
		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				$this->tplProcessBounceArray($value, $prefix, $arrayname, $arraykeys . "[" . $key . "]");
			}
			else
			{
				$this->tpl[0]->set_var("varname", $prefix . "_" . $arrayname . $arraykeys . "[" . $key . "]");
				$this->tpl[0]->set_var("varvalue", $value);
				$this->tpl[0]->parse("SectFormHidden", true);
			}
		}
		reset($array);
	}
		
	/**
	 * Processa le variabili standard in un template associato alla pagina
	 * @param Array $tpl l'oggetto template
	 */
	protected function tplProcessVars($tpl)
	{
		$tpl[0]->set_var("site_path", $this->site_path);
		$tpl[0]->set_var("language", FF_LOCALE);
		$tpl[0]->set_var("locale", strtolower(substr(FF_LOCALE, 0, 2)));

		$tpl[0]->set_var("theme", $this->theme);
		if(strlen($this->jquery_ui_force_theme)) {
			$tpl[0]->set_var("theme_ui", $this->jquery_ui_force_theme);
		} elseif(strlen($this->jquery_ui_theme)) {
			$tpl[0]->set_var("theme_ui", $this->jquery_ui_theme);
		} else {
			$tpl[0]->set_var("theme_ui", "");
		}
		$tpl[0]->set_var("layer", $this->layer);
        
        if(MOD_SEC_GROUPS) 
		{
            $user_permission = get_session("user_permission");    
            if(strlen($user_permission["primary_gid_name"]))
            {
                $tpl[0]->set_var("group", $user_permission["primary_gid_name"]);
                $tpl[0]->parse("SectGroup", false);
            }
        }    
        
		$tpl[0]->set_var("encoded_this_url", rawurlencode($_SERVER['REQUEST_URI']));

		foreach ($this->global_params as $key => $value)
		{
			if (is_array($value))
			{
				$tmp = "";
				foreach ($value as $subkey => $subvalue)
				{
					if (!is_array($subvalue))
				    {
				        $tpl[0]->set_var("[VAR_$key\[$subkey]]", $subvalue);
				        $tpl[0]->set_var("[VAR_URL_$key\[$subkey]]", urlencode($subvalue));
				        $tmp .= $key . "[" . $subkey . "]=" . urlencode($subvalue) . "&";
					}
				}
				reset($value);
				$tpl[0]->set_var("[VAR_URL_$key]", $tmp);
			}
			else
			{
				$tpl[0]->set_var("[VAR_$key]", $value);
				$tpl[0]->set_var("[VAR_URL_$key]", urlencode($value));
			}
		}
		reset($this->global_params);

		$tpl[0]->set_var("query_string", $this->get_script_params());
		
		if (is_array($this->keys) && count($this->keys))
		{
			foreach ($this->keys as $key => $value)
			{
				$tpl[0]->set_var("_key_name", $key);
				$tpl[0]->set_var("_key_value", $value);
				$tpl[0]->parse("SectFormKeys", true);
			}
			reset($this->keys);
		}
		else
			$tpl[0]->set_var("SectFormKeys", "");

		if (is_array($this->fixed_vars) && count($this->fixed_vars))
		{
			foreach ($this->fixed_vars as $key => $value)
			{
				$tpl[0]->set_var($key, $value);
				if (strlen($value))
					$tpl[0]->parse("SectFixed_" . $key, false);
			}
			reset($this->fixed_vars);
		}
	}

	/**
	 * Elabora i template e restituisce il risultato
	 * il risultato dipende dal formato (XHR, normale, etc)
	 * @param Boolean $output_result Se dev'essere eseguito l'output immediatamente
	 * @return Mixed il risultato dell'operazione
	 */
	protected function tplParse($output_result)
	{
		$this->doEvent("on_tpl_parse", array(&$this, $this->tpl[0]));

		$this->tpl[0]->set_var("title", $this->title);
        if($this->class_body)
            $this->tpl[0]->set_var("class_body", " class=\"" . $this->class_body . "\"");

        if($this->use_own_js) {
        	if(!array_key_exists("ff", $this->page_js)) {
				$this->page_js = array_merge($this->default_own_js, $this->page_js); 
        	}
			$this->tpl[0]->parse("SectFFJS", false);
		} else
			$this->tpl[0]->set_var("SectFFJS", "");

		$this->parse_css();
		if ($this->canonical)
		{
			$this->tpl[0]->set_var("css_rel", "canonical");
			$this->tpl[0]->set_var("css_path", "");
			$this->tpl[0]->set_var("link_properties", "");
			$this->tpl[0]->set_var("css_file", $this->canonical);
			$this->tpl[0]->parse("SectCss", true);
		}
        $this->parse_js();
        $this->parse_meta();
        $this->parse_html_attr();
			
        $this->doEvent("on_tpl_parsed_header", array($this, $this->tpl[0]));

		if ($this->isXHR())
		{
			$this->output_buffer["headers"] = $this->tpl[0]->rpparse("SectHeaders", false) . $this->output_buffer["headers"];
			$this->output_buffer["footers"] .= $this->tpl[0]->rpparse("SectFooters", false);
			
			if (!$this->getXHRComponent())
			{
				$this->tpl[0]->set_var("SectHeaders", "");
				$this->tpl[0]->set_var("SectFooters", "");
				$this->tpl[0]->set_var("content", $this->output_buffer["html"]);
				$this->output_buffer["html"] = $this->tpl[0]->rpparse("main", false);
			}

			$this->doEvent("on_tpl_parsed", array(&$this, $this->tpl[0]));
			
			cm::jsonParse(array_merge($this->json_result, $this->output_buffer), $output_result);
		}
		else
		{
			$this->tpl[0]->set_var("content", $this->output_buffer["html"]);
			
			if(strlen($this->output_buffer["headers"])) 
			{
				$this->tpl[0]->set_var("WidgetsContent", $this->output_buffer["headers"]);
				$this->tpl[0]->parse("SectWidgetsHeaders", true);
			}
			
			if(strlen($this->output_buffer["footers"])) 
			{
				$this->tpl[0]->set_var("WidgetsContent", $this->output_buffer["footers"]);
				$this->tpl[0]->parse("SectWidgetsFooters", true);
			}

			$this->doEvent("on_tpl_parsed", array(&$this, $this->tpl[0]));

			if ($output_result)
			{
				$this->tpl[0]->pparse("main", false);
			} 
			else 
			{
				return $this->tpl[0]->rpparse("main", false);
			}
		}
		return true;
	}

	/**
	 * Imposta le variabili globali all'interno di un template
	 * @param Array $tpl l'oggetto template implicato
	 */
	function tplSetGlobals($tpl)
	{
		if (is_array($this->globals) && count($this->globals))
		{
			foreach ($this->globals as $key => $value)
			{
				$keyval = $this->retrieve_global($key);
				$tpl[0]->set_var($key, $keyval);
			}
			reset($this->globals);
			$tpl[0]->set_var("globals", $this->get_globals());
		}
	}

	/**
	 * Elabora il template valorizzandone tutte le parti
	 */
	protected function tplProcess()
	{
		if ($this->getXHRDialog())
			$this->tpl[0]->set_var("dialog_id", $this->getXHRDialog());

		$this->struct_process();

		$this->doEvent("on_tpl_process", array(&$this, $this->tpl[0]));
		if ($this->use_own_form !== false)
		{
            $this->tpl[0]->set_var("form_id", $this->form_id);
            $this->tpl[0]->set_var("form_name", $this->form_name);
			$this->tpl[0]->set_var("form_method", (strlen($this->form_method) ? strtolower($this->form_method) : "get"));
			if($this->form_enctype)
				$this->tpl[0]->set_var("form_enctype", ' enctype="' . $this->form_enctype . '"');
			if($this->form_action)
				$this->tpl[0]->set_var("form_action", ' action="' . $this->form_action . '"');

			$this->tpl[0]->set_var("script_name", $this->get_script_name() . "?" . $this->get_script_params());
			
			if($this->form_workaround) {
				$this->tpl[0]->parse("SectFormWorkaround", false);
			} else {
				$this->tpl[0]->set_var("SectFormWorkaround", "");
			}
			$this->tpl[0]->parse("SectFormHeader", false);
			$this->tpl[0]->parse("SectFormFooter", false);
		}
		else
		{
			$this->tpl[0]->set_var("SectFormHeader", "");
			$this->tpl[0]->set_var("SectFormFooter", "");
		}

		if ($this->getXHRComponent())
		{
			if (!isset($this->components_buffer[$this->getXHRComponent()])) {
				ffErrorHandler::raise("Component Not Found", E_USER_ERROR, $this, get_defined_vars());
			} else {
				$this->output_buffer = $this->components_buffer[$this->getXHRComponent()];
				foreach ($this->components[$this->getXHRComponent()]->json_result as $key => $value)
				{
					if ($key == "refresh")
						$this->json_result["refresh"]	|=	$value;
					elseif ($key == "close")
						$this->json_result["close"]		|=	$value;
					elseif ($key == "insert_id")
						$this->json_result["insert_id"]	=	$value;
					else
						$this->json_result[$key]		=	$value;
				}
			}
		}
		else
		{
			if(!is_array($this->output_buffer)) //TODO: Fixare veramente :)
			{
				$this->output_buffer = array("html" => $this->output_buffer, "headers" => "", "footers" => "");
			}

			foreach ($this->contents as $key => $content)
			{
				if ($content["group"] === true)
				{
					if (!count($this->groups[$key]["contents"]))
						continue;
						
					$this->output_buffer["html"] .= $this->widgets["tabs"]->process($key, $this->groups[$key], $this);
				}
				else
				{
					$tmp = $this->getContentData($content["data"]);

					if (is_array($tmp))
					{
						//if ($this->isXHR()) {
						$this->output_buffer["html"]	.= $tmp["html"];
						$this->output_buffer["headers"] .= $tmp["headers"];
						$this->output_buffer["footers"] .= $tmp["footers"];
						/*}
						else
							$this->output_buffer["html"] .= $tmp["headers"] . $tmp["html"] . $tmp["footers"];*/
					}
					else
						$this->output_buffer["html"] .= $tmp;
				}
			}
			reset($this->contents);

			$rc = $this->doEvent("on_fixed_process_before", array(&$this));
			$this->output_buffer["html"] = $this->fixed_pre_content . $this->output_buffer["html"] . $this->fixed_post_content;

			if (strlen($this->layer) && !$this->isXHR())
			{
				$this->tpl_layer[0]->set_var("content", $this->output_buffer["html"]);
				$this->output_buffer["html"] = $this->tpl_layer[0]->rpparse("main", false);
			}
		}
	}

	/**
	 * In base al contenuto, recupera di dati d'elaborazione ad esso associati
	 * @param Mixed $content
	 * @return Mixed 
	 */
	public function getContentData($content)
	{
		if (
				is_object($content)
				&& (
					   is_subclass_of($content, "ffGrid_base")
					|| is_subclass_of($content, "ffRecord_base")
					|| is_subclass_of($content, "ffDetails_base")
				)
			)
		{
			if ($content->display !== false)
				return $this->components_buffer[$content->id];
			else
				return "";
		}
		elseif (
					is_object($content)
					&& (
						is_subclass_of($content, "ffField_base")
						|| is_subclass_of($content, "ffButton_base")
					)
			)
		{
			return $content->process();
		}
		elseif (
					is_object($content)
					&& get_class($content) == "ffData"
			)
		{
			return $content->getValue(null, FF_LOCALE);
		}
		elseif (
					is_object($content)
					&& get_class($content) == "ffTemplate"
			)
		{
			foreach($this->components as $key => $item)
			{
				$rc = false;

				if ($this->components[$key]->display !== false)
				{
					if ($this->components[$key]->location_name === null)
						$rc = $content->set_var($key, $this->components_buffer[$key]["html"]);
					else
						$rc = $content->set_var($this->components[$key]->location_name, $this->components_buffer[$key]["html"]);
				}

				if ($rc)
					$this->components_buffer[$key]["html"] = "";
			}
			reset($this->components);
			
			return $content->rpparse("main", false);
		}
		elseif (is_string($content))
		{
			return $content;
		}
		else
			ffErrorHandler::raise("Unhandled Content", E_USER_ERROR, $this, get_defined_vars());
	}
	
	/**
	 * Aggiunge una sezione all'array sections
	 * @param String $sName Il nome della sezione
	 */
	function addSection($sName)
	{
		if (!isset($this->sections[$sName]))
		{
			$this->sections[$sName] = array(
											  "dir" => null
											, "name" => ""
											, "tpl" => null
											, "is_php" => false
											, "events" => new ffEvents()
										);
			$this->sections[$sName]["events"]->addEvent("on_load_template", "cm::oPage_on_process_parts", ffEvent::PRIORITY_HIGH);
		}
	}

	/**
	 * Elabora il template del layout e delle sezioni
	 */
	protected function tplProcessLayout()
	{
		$this->doEvent("on_tpl_layer_process", array(&$this, $this->tpl_layer[0]));

		// process components buffer
		foreach($this->components as $key => $item)
		{
			$rc = false;
			
			if (/*$this->components[$key]->use_own_location &&*/ $this->components[$key]->display !== false)
			{
				if ($this->components[$key]->location_name === null)
					$rc = $this->tpl_layer[0]->set_var($key, $this->components_buffer[$key]["html"]);
				else
					$rc = $this->tpl_layer[0]->set_var($this->components[$key]->location_name, $this->components_buffer[$key]["html"]);
			}
			
			if ($rc)
				$this->components_buffer[$key]["html"] = "";
				
			if (get_class($this->components[$key]) == "ffGrid_html" && $this->components[$key]->search_container !== null)
			{
				$this->sections[$this->components[$key]->search_container]["content"] .= $this->components[$key]->search_container_buffer;
			}
		}
		reset($this->components);
		
		if (is_array($this->sections) && count($this->sections))
		{
			foreach ($this->sections as $key => $value)
			{
				if ($value["tpl"] === null)
					continue;
		
				$this->sections[$key]["events"]->doEvent("on_process", array(&$this, $this->sections[$key]["tpl"]));
				
				// process components buffer
				foreach ($this->components as $subkey => $item)
				{
					$rc = false;

					if (/*$this->components[$subkey]->use_own_location && */$this->components[$subkey]->display !== false)
					{
						if ($this->components[$subkey]->location_name === null)
							$rc = $value["tpl"]->set_var($subkey, $this->components_buffer[$subkey]["html"]);
						else
							$rc = $value["tpl"]->set_var($this->components[$subkey]->location_name, $this->components_buffer[$subkey]["html"]);
					}

					if ($rc)
						$this->components_buffer[$subkey]["html"] = "";

				}
				reset($this->components);
				$value["tpl"]->set_var("content", $value["content"]);

				$this->tpl_layer[0]->set_var($key, $value["tpl"]->rpparse("main", false));
			}
			reset($this->sections);
		}
	}

    /**
	 * Elabora i CSS
	 * Da richiamare ad ogni aggiunta di CSS se si aggiungono CSS dinamicamente post-elaborazione
	 */
	public function parse_css()
    {
        $this->tpl[0]->set_var("SectCssEmbed", "");
        $this->tpl[0]->set_var("SectCss", "");
        $this->tpl[0]->set_var("SectAsyncCssPlugin", "");

		if (is_array($this->page_css) && count($this->page_css))
        {            
        	if($this->browser === null)
        		$this->browser = $this->getBrowser();

            foreach ($this->page_css as $key => $value)
            {
            	$tmp_path = "";
            	$tmp_path_add = "";
            	$tmp_file = "";

                if($value["embed"])
                {
                    if(!$this->isXHR() && $this->compact_css && !$value["exclude_compact"]) {
						$this->css_buffer["default"][]["content"] = $value["embed"];
					} else {
	                    $this->tpl[0]->set_var("css_embed", $value["embed"]);
	                    $this->tpl[0]->set_var("css_type", $value["type"]);
	                    $this->tpl[0]->parse("SectCssEmbed", true);
					}
                } 
                else 
                {
                    $this->tpl[0]->set_var("css_embed", "");
            	
	                if(isset($this->override_css[$key]) && strlen($this->override_css[$key]))
					{
	                    $tmp_path = ffcommon_dirname($this->override_css[$key]);
	                    $tmp_file = basename($this->override_css[$key]);
	                } 
					else 
					{
            		    $res = $this->doEvent("on_css_parse", array($this, $key, $value["path"], $value["file"]));
            		    $rc = end($res);

            		    if ($rc === null)
            		    {
						    if ($value["path"] === null) 
							{
                    		    if(!$this->isXHR() && $this->compact_css)
                        		    $tmp_path = "/themes/" . $this->theme . "/css";
							    else
								    $tmp_path = $this->site_path . "/themes/" . $this->theme . "/css";
						    } 
							elseif (strlen($value["path"])) 
							{
	                            if (
	                                substr(strtolower($value["path"]), 0, 7) == "http://"
	                                || substr(strtolower($value["path"]), 0, 8) == "https://"
                                    || substr($value["path"], 0, 2) == "//"
	                            )
	                                $tmp_path = $value["path"];
                    		    elseif(!$this->isXHR() && $this->compact_css)
                    			    $tmp_path = $value["path"];
							    else
								    $tmp_path = $this->site_path . $value["path"];
						    }
						    if ($value["file"] === null)
							    $tmp_file = $key . ".css";
							elseif (substr($value["path"], -4) === ".css")
								$tmp_file = "";
						    else
							    $tmp_file = $value["file"];
					    }
					    else
					    {
                    		if(!$this->isXHR() && $this->compact_css)
                    			$tmp_path = $rc["path"];
							else
								$tmp_path = $this->site_path . $rc["path"];

						    $tmp_file = $rc["file"];
					    }
						
						if (substr($value["path"], -4) !== ".css")
						{
							$tmp_path = rtrim($tmp_path, "/");
							$tmp_file = ltrim($tmp_file, "/");

							if($value["path"] === null) {
								if(strlen($this->browser["name"]) . strlen($this->browser["majorver"]) && is_file($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . ffCommon_url_rewrite($this->browser["name"]) . $this->browser["majorver"] . "/replace_" . $tmp_file)) {
									$tmp_path = FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . ffCommon_url_rewrite($this->browser["name"]) . $this->browser["majorver"];
									$tmp_file = "replace_" . $tmp_file;
								} elseif(strlen($this->browser["name"]) . strlen($this->browser["majorver"]) && is_file($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . ffCommon_url_rewrite($this->browser["name"]) . $this->browser["majorver"] . "/" . $tmp_file)) {
									$tmp_path_add = FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . ffCommon_url_rewrite($this->browser["name"]) . $this->browser["majorver"];
								} else {
									if(strlen($this->browser["name"]) && is_file($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . ffCommon_url_rewrite($this->browser["name"]) . "/replace_" . $tmp_file)) {
										$tmp_path = FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . ffCommon_url_rewrite($this->browser["name"]);
										$tmp_file = "replace_" . $tmp_file;
									} elseif(strlen($this->browser["name"]) && is_file($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . ffCommon_url_rewrite($this->browser["name"]) . "/" . $tmp_file)) {
										$tmp_path_add = FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . ffCommon_url_rewrite($this->browser["name"]);
									}
								}
								if(strlen($this->browser["platform"]) && is_file($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . ffCommon_url_rewrite($this->browser["platform"]) . "/" . $tmp_file)) {
									$tmp_path = FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . ffCommon_url_rewrite($this->browser["platform"]);
									$tmp_file = "replace_" . $tmp_file;
								} elseif(strlen($this->browser["platform"]) && is_file($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . ffCommon_url_rewrite($this->browser["platform"]) . "/" . $tmp_file)) {
									$tmp_path_add = FF_THEME_DIR . "/" . $this->getTheme() . "/css/" . ffCommon_url_rewrite($this->browser["platform"]);
								}
							}
						}
	                }

					if($value["async"]) 
					{
		                $this->tpl[0]->set_var("css_path", $tmp_path . (strlen($tmp_file) ? "/" : ""));
		                $this->tpl[0]->set_var("css_file", $tmp_file);
		                //$this->tpl[0]->set_var("css_rel", $value["rel"]);
		                //$this->tpl[0]->set_var("css_type", $value["type"]);
		                $this->tpl[0]->parse("SectAsyncCssPlugin", true);
					} 
					else 
					{
		                if(!$this->isXHR() 
		                    && $this->compact_css 
		                    && !$value["exclude_compact"]
		                    && $value["rel"] == "stylesheet"
		                    && $value["type"] == "text/css"
						) {
							if($value["media"] === null)
								$tmp_media = "default";
							else
								$tmp_media = $value["media"];

	                        if (
	                            substr(strtolower($tmp_path), 0, 7) == "http://"
	                            || substr(strtolower($tmp_path), 0, 8) == "https://"
                                || substr($tmp_path, 0, 2) == "//"
	                        )
	                            $this->css_buffer[$tmp_media][]["path"] = $tmp_path . (strlen($tmp_file) ? "/" : "") . $tmp_file;
							elseif (strpos($tmp_path, cm_getModulesExternalPath()) === 0)
								$this->css_buffer[$tmp_media][]["path"] = preg_replace("/^" . preg_quote(cm_getModulesExternalPath(), "/") . "(\/[^\/]+)/", CM_MODULES_ROOT . "\$1/themes", $tmp_path);
	                        else
	                            $this->css_buffer[$tmp_media][]["path"] = FF_DISK_PATH . $tmp_path . (strlen($tmp_file) ? "/" : "") . $tmp_file;

						} else {
			                if (
	                            substr(strtolower($tmp_path), 0, 7) == "http://"
	                            || substr(strtolower($tmp_path), 0, 8) == "https://"
                                || substr($tmp_path, 0, 2) == "//"
	                            || (strlen(FF_SITE_PATH) && strpos($tmp_path, FF_SITE_PATH) === 0) 
	                        ) {
	                            $this->tpl[0]->set_var("css_path", $tmp_path);
	                        } else {
	                            $this->tpl[0]->set_var("css_path", FF_SITE_PATH . $tmp_path);
	                        }

			                $this->tpl[0]->set_var("css_file", (strlen($tmp_file) ? "/" : "") . $tmp_file);
			                $this->tpl[0]->set_var("css_rel", $value["rel"]);

			                $link_properties = "";
							if($value["type"])
								$link_properties = 'type="' . $value["type"] . '"';
							if($value["media"] && $value["media"] !== "default")
								$link_properties .= ' media="' . $value["media"] .'"';

							$this->tpl[0]->set_var("link_properties", $link_properties);			                
							/*
			                $this->tpl[0]->set_var("css_type", $value["type"]);
			                if($value["media"] !== null) {
	                			$this->tpl[0]->set_var("css_media", $value["media"]);
	                			$this->tpl[0]->parse("SectCssMedia", false);
							} else {
								$this->tpl[0]->set_var("SectCssMedia", "");
							}*/
			                $this->tpl[0]->parse("SectCss", true);
						}
					}
					if(!$this->isXHR() && $this->use_own_js) {
						$preload_data = $preload_data . " ff.preloadCSS('" . $key . "'); ";
					}
					if(strlen($tmp_path_add)) {
						if($value["async"]) 
						{
			                $this->tpl[0]->set_var("css_path", $tmp_path_add);
			                $this->tpl[0]->set_var("css_file", $tmp_file);
			                //$this->tpl[0]->set_var("css_rel", $value["rel"]);
			                //$this->tpl[0]->set_var("css_type", $value["type"]);
			                $this->tpl[0]->parse("SectAsyncCssPlugin", true);
						} 
						else 
						{ 
			                if(!$this->isXHR() 
			                    && $this->compact_css 
			                    && !$value["exclude_compact"]
			                    && $value["rel"] == "stylesheet"
			                    && $value["type"] == "text/css"
							) {
								if($value["media"] === null)
									$tmp_media = "default";
								else
									$tmp_media = $value["media"];

		                        if (
		                            substr(strtolower($tmp_path_add), 0, 7) == "http://"
		                            || substr(strtolower($tmp_path_add), 0, 8) == "https://"
                                    || substr($tmp_path_add, 0, 2) == "//"
		                        )
		                            $this->css_buffer[$tmp_media][]["path"] = $tmp_path_add . (strlen($tmp_file) ? "/" : "") . $tmp_file;
								elseif (strpos($tmp_path_add, cm_getModulesExternalPath()) === 0)
									$this->css_buffer[$tmp_media][]["path"] = preg_replace("/^" . preg_quote(cm_getModulesExternalPath(), "/") . "(\/[^\/]+)/", CM_MODULES_ROOT . "\$1/themes", $tmp_path_add);
		                        else
		                            $this->css_buffer[$tmp_media][]["path"] = FF_DISK_PATH . $tmp_path_add . (strlen($tmp_file) ? "/" : "") . $tmp_file;

							} else {
				                if (
		                            substr(strtolower($tmp_path_add), 0, 7) == "http://"
		                            || substr(strtolower($tmp_path_add), 0, 8) == "https://"
                                    || substr($tmp_path_add, 0, 2) == "//"
		                            || (strlen(FF_SITE_PATH) && strpos($tmp_path_add, FF_SITE_PATH) === 0) 
		                        ) {
		                            $this->tpl[0]->set_var("css_path", $tmp_path_add);
		                        } else {
		                            $this->tpl[0]->set_var("css_path", FF_SITE_PATH . $tmp_path_add);
		                        }

				                $this->tpl[0]->set_var("css_file", (strlen($tmp_file) ? "/" : "") . $tmp_file);
				                $this->tpl[0]->set_var("css_rel", $value["rel"]);
				                
				                $link_properties = "";
								if($value["type"])
									$link_properties = 'type="' . $value["type"] . '"';
								if($value["media"] && $value["media"] !== "default")
									$link_properties .= ' media="' . $value["media"] .'"';

								$this->tpl[0]->set_var("link_properties", $link_properties);				                
								/*
				                $this->tpl[0]->set_var("css_type", $value["type"]);
				                if($value["media"] !== null) {
	                				$this->tpl[0]->set_var("css_media", $value["media"]);
	                				$this->tpl[0]->parse("SectCssMedia", false);
								} else {
									$this->tpl[0]->set_var("SectCssMedia", "");
								}*/
				                $this->tpl[0]->parse("SectCss", true);
							}
						}
					}
				}				
                /*$this->tpl[0]->set_var("key", $key);
                $this->tpl[0]->parse("SectLoadedCSS", true);*/
            }
            reset($this->page_css);
            if(strlen($preload_data)) {
            	$this->tplAddJs("ff.loaded_css", null, null, false, false, $preload_data); 
			}
        }
        else
        { 
            $this->tpl[0]->set_var("SectCss", "");
            $this->tpl[0]->set_var("SectAsyncCssPlugin", "");
		}
    }
    
   /**
	 * Elabora i Javascript
	 * Da richiamare ad ogni aggiunta di Javascript se si aggiungono Javascript dinamicamente post-elaborazione
	 */
    public function parse_js() 
    {
        $this->tpl[0]->set_var("SectJs", ""); 
        $this->tpl[0]->set_var("SectAsyncJsPlugin", "");

		$this->parse_js_fix();
        
        if (is_array($this->page_js) && count($this->page_js))
        {
        	if($this->browser === null)
        		$this->browser = $this->getBrowser();

            foreach ($this->page_js as $key => $value)
            {
            	$tmp_path = "";
            	$tmp_path_add = "";
            	$tmp_file = "";
				
                if($value["embed"])
                {
                    if(!$this->isXHR() && $this->compact_js) {
						$this->js_buffer[]["content"] = $value["embed"];
					} else {
						
	                    $this->tpl[0]->set_var("js_embed", $value["embed"]);
	                    $this->tpl[0]->set_var("SectJsSrc", "");
	                    $this->tpl[0]->parse("SectJs", true);
					}
                } 
                else 
                {
                    $this->tpl[0]->set_var("js_embed", "");
                    
                    if(isset($this->override_js[$key]) && strlen($this->override_js[$key])) 
					{                                                                
                        $tmp_path = ffcommon_dirname($this->override_js[$key]);
                        $tmp_file = basename($this->override_js[$key]);
                    } 
					else 
					{
                        $res = $this->doEvent("on_js_parse", array($this, $key, $value["path"], $value["file"]));
                        $rc = end($res);
                        
                        if ($rc === null)
                        {
                            if ($value["path"] === null)
                                $tmp_path = $this->site_path . "/themes/" . $this->theme . "/javascript";
                            elseif (strlen($value["path"])) 
							{
							    if (
								    substr(strtolower($value["path"]), 0, 7) == "http://"
								    || substr(strtolower($value["path"]), 0, 8) == "https://"
                                    || substr($value["path"], 0, 2) == "//"
							    )
                            	    $tmp_path = $value["path"];
                                elseif(!$this->isXHR() && $this->compact_js)
                            	    $tmp_path = $value["path"];
                                elseif(substr($value["path"], -3) === ".js")
									$tmp_path = $value["path"];
								else
								    $tmp_path = $this->site_path . $value["path"];
						    }
						    
                            if ($value["file"] === null)
                                $tmp_file = $key . ".js";
                            elseif(substr($value["path"], -3) === ".js")
								$tmp_file = "";
                            else
                                $tmp_file = $value["file"];
                        }
                        else
                        {
                    		if(!$this->isXHR() && $this->compact_js)
                    			$tmp_path = $rc["path"];
							else
								$tmp_path = $this->site_path . $rc["path"];

                            $tmp_file = $rc["file"];
                        }

						if (substr($value["path"], -3) !== ".js")
						{
							$tmp_path = rtrim($tmp_path, "/");
							$tmp_file = ltrim($tmp_file, "/");

							if($value["path"] === null) 
							{
								if(strlen($this->browser["name"]) && strlen($this->browser["majorver"]) && is_file($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . ffCommon_url_rewrite($this->browser["name"]) . $this->browser["majorver"] . "/replace_" . $tmp_file))
								{
									$tmp_path = FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . ffCommon_url_rewrite($this->browser["name"]) . $this->browser["majorver"];
									$tmp_file = "replace_" . $tmp_file;
								} 
								elseif(strlen($this->browser["name"]) && strlen($this->browser["majorver"]) && is_file($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . ffCommon_url_rewrite($this->browser["name"]) . $this->browser["majorver"] . "/" . $tmp_file))
								{
									$tmp_path_add = FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . ffCommon_url_rewrite($this->browser["name"]) . $this->browser["majorver"];
								} 
								else 
								{
									if(strlen($this->browser["name"]) && is_file($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . ffCommon_url_rewrite($this->browser["name"]) . "/replace_" . $tmp_file)) 
									{
										$tmp_path = FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . ffCommon_url_rewrite($this->browser["name"]);
										$tmp_file = "replace_" . $tmp_file;
									} 
									elseif(strlen($this->browser["name"]) && is_file($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . ffCommon_url_rewrite($this->browser["name"]) . "/" . $tmp_file))
									{
										$tmp_path_add = FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . ffCommon_url_rewrite($this->browser["name"]);
									}
								}
								if(strlen($this->browser["platform"]) && is_file($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . ffCommon_url_rewrite($this->browser["platform"]) . "/" . $tmp_file))
								{
									$tmp_path = FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . ffCommon_url_rewrite($this->browser["platform"]);
									$tmp_file = "replace_" . $tmp_file;
								} 
								elseif(strlen($this->browser["platform"]) && is_file($this->disk_path . FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . ffCommon_url_rewrite($this->browser["platform"]) . "/" . $tmp_file))
								{
									$tmp_path_add = FF_THEME_DIR . "/" . $this->getTheme() . "/javascript/" . ffCommon_url_rewrite($this->browser["platform"]);
								}
							}
						}
                    }

                    if($value["async"])
                    {
                        $this->tpl[0]->set_var("js_tag", $key);
                        $this->tpl[0]->set_var("js_path", $tmp_path . (strlen($tmp_file) ? "/" : ""));
                        $this->tpl[0]->set_var("js_file", $tmp_file);
                        $this->tpl[0]->parse("SectAsyncJsPlugin", true);
                    }
                    else 
                    {
	                    if(!$this->isXHR() 
	                    	&& $this->compact_js 
	                    	&& !$value["exclude_compact"]
						) {
							if (
	                            substr(strtolower($tmp_path), 0, 7) == "http://"
	                            || substr(strtolower($tmp_path), 0, 8) == "https://"
                                || substr($tmp_path, 0, 2) == "//"
	                        )
	                            $this->js_buffer[]["path"] = $tmp_path . (strlen($tmp_file) ? "/" : "") . $tmp_file;
							elseif (strpos($tmp_path, cm_getModulesExternalPath()) === 0)
								$this->js_buffer[]["path"] = preg_replace("/^" . preg_quote(cm_getModulesExternalPath(), "/") . "(\/[^\/]+)/", CM_MODULES_ROOT . "\$1/themes", $tmp_path);
							else
	                            $this->js_buffer[]["path"] = FF_DISK_PATH . $tmp_path . (strlen($tmp_file) ? "/" : "") . $tmp_file;

							//$this->js_buffer[]["path"] = FF_DISK_PATH . $tmp_path . (strlen($tmp_file) ? "/" : "") . $tmp_file;
						} else {
                            if (
                                substr(strtolower($tmp_path), 0, 7) == "http://"
                                || substr(strtolower($tmp_path), 0, 8) == "https://"
                                || substr($tmp_path, 0, 2) == "//"
                                || (strlen(FF_SITE_PATH) && strpos($tmp_path, FF_SITE_PATH) === 0) 
                            ) {
                                $this->tpl[0]->set_var("js_path", $tmp_path);
                            } else {
                                $this->tpl[0]->set_var("js_path", FF_SITE_PATH . $tmp_path);
                            }
	                        $this->tpl[0]->set_var("js_file", (strlen($tmp_file) ? "/" : "") . $tmp_file);
	                        $this->tpl[0]->parse("SectJsSrc", false);
	                        $this->tpl[0]->parse("SectJs", true);
						}
                    }
					if(strlen($tmp_path_add)) {
	                    if($value["async"])
	                    {
	                        $this->tpl[0]->set_var("js_tag", $key);
	                        $this->tpl[0]->set_var("js_path", $tmp_path_add . (strlen($tmp_file) ? "/" : ""));
	                        $this->tpl[0]->set_var("js_file", $tmp_file);
	                        $this->tpl[0]->parse("SectAsyncJsPlugin", true);
	                    }
	                    else 
	                    {
		                    if(!$this->isXHR() 
	                    		&& $this->compact_js 
	                    		&& !$value["exclude_compact"]
	                            && !(substr(strtolower($tmp_path_add), 0, 7) == "http://"
	                                || substr(strtolower($tmp_path_add), 0, 8) == "https://"
                                    || substr($tmp_path_add, 0, 2) == "//")
							) {
								if (strpos($tmp_path_add, cm_getModulesExternalPath()) === 0)
									$this->js_buffer[]["path"] = preg_replace("/^" . preg_quote(cm_getModulesExternalPath(), "/") . "(\/[^\/]+)/", CM_MODULES_ROOT . "\$1/themes", $tmp_path_add);
								else
									$this->js_buffer[]["path"] = FF_DISK_PATH . $tmp_path_add . (strlen($tmp_file) ? "/" : "") . $tmp_file;
							} else {
	                            if (
	                                substr(strtolower($tmp_path_add), 0, 7) == "http://"
	                                || substr(strtolower($tmp_path_add), 0, 8) == "https://"
                                    || substr($tmp_path_add, 0, 2) == "//"
	                                || (strlen(FF_SITE_PATH) && strpos($tmp_path_add, FF_SITE_PATH) === 0) 
	                            ) {
	                                $this->tpl[0]->set_var("js_path", $tmp_path_add);
	                            } else {
	                                $this->tpl[0]->set_var("js_path", FF_SITE_PATH . $tmp_path_add);
	                            }
		                        $this->tpl[0]->set_var("js_file", (strlen($tmp_file) ? "/" : "") . $tmp_file);
		                        $this->tpl[0]->parse("SectJsSrc", false);
		                        $this->tpl[0]->parse("SectJs", true);
							}
	                    }
					}                    
                }
                /*$this->tpl[0]->set_var("key", $key);
                $this->tpl[0]->parse("SectLoadedJS", true);*/
            }
            reset($this->page_js);
        }
        else
        {
            $this->tpl[0]->set_var("SectJs", "");
            $this->tpl[0]->set_var("SectAsyncJsPlugin", "");
		}
	}
	function parse_js_fix() 
	{
		if(is_array($this->page_js) && count($this->page_js)) 
		{
			if(is_array($this->cdn_version) && count($this->cdn_version)) 
			{
				foreach($this->cdn_version AS $version_key => $version_value) 
				{
					if(array_key_exists($version_key, $this->page_js)) 
					{
						if(is_file(FF_DISK_PATH . FF_THEME_DIR . "/library/" . $version_key . "/" . $version_key . ".fix." . $version_value["major"] . "." . $version_value["minor"] . ".js")) {
							$tmp_js_top = array_slice($this->page_js, 0, array_search($version_key, array_keys($this->page_js)) + 1, true);
							$tmp_js_bottom = array_slice($this->page_js, array_search($version_key, array_keys($this->page_js)) + 1, null, true);

							$tmp_js_top[$version_key . ".fix." . $version_value["major"] . "." . $version_value["minor"]] = array(
					            "path" => $this->page_js[$version_key]["path"]
					            , "file" => $version_key . ".fix." . $version_value["major"] . "." . $version_value["minor"] . ".js"
					            , "async" => $this->page_js[$version_key]["async"]
					            , "embed" => $this->page_js[$version_key]["embed"]
					            , "exclude_compact" => $this->page_js[$version_key]["exclude_compact"]
					        );

							$this->page_js = array_merge($tmp_js_top, $tmp_js_bottom);

						}
					}
				}
			}
		}
	}
   /**
	 * Elabora i Meta Tag
	 * Da richiamare ad ogni aggiunta di Meta se si aggiungono Meta dinamicamente post-elaborazione
	 */
    public function parse_meta() 
    {   
        $this->tpl[0]->set_var("SectMeta", "");
        if (is_array($this->page_meta) && count($this->page_meta))
        {
            foreach ($this->page_meta as $key => $value)
            {
            	$this->tpl[0]->set_var("meta_type", $value["type"]);
                $this->tpl[0]->set_var("meta_content", $value["content"]);
                $this->tpl[0]->set_var("meta_name", $value["name"]);
                $this->tpl[0]->parse("SectMeta", true);
            }
            reset($this->page_meta);
        }
        else
            $this->tpl[0]->set_var("SectMeta", "");
    }

   /**
	 * Elabora gli attributi del tag html
	 * Da richiamare ad ogni aggiunta di Meta se si aggiungono Meta dinamicamente post-elaborazione
	 */
    public function parse_html_attr() 
    {   
        $this->tpl[0]->set_var("SectHtmlAttr", "");
        if (is_array($this->page_html_attr) && count($this->page_html_attr))
        {
            foreach ($this->page_html_attr as $key => $value)
            {
            	$this->tpl[0]->set_var("attr_type", $value["type"]);
                $this->tpl[0]->set_var("attr_content", $value["content"]);
                $this->tpl[0]->parse("SectHtmlAttr", true);
            }
            reset($this->page_html_attr);
        }
        else
            $this->tpl[0]->set_var("SectHtmlAttr", "");
    }
	/**
	 * Carica una widget
	 * @param String $name il nome della widget
	 * @param String $path il percorso della widget. Se omesso viene determinato in base all'oggetto di riferimento
	 * @param Object $ref l'oggetto di riferimento, di default la pagina stessa
	 */
	function widgetLoad($name, $path = null, &$ref = null)
	{
		parent::widgetLoad($name, $path, $ref);

		if(is_array($this->widgets[$name]->js_deps) && count($this->widgets[$name]->js_deps))
		{
			foreach ($this->widgets[$name]->js_deps AS $js_key => $js_value)
			{
				if (is_array($js_value))
				{
					$this->tplAddJs($js_key, $js_value["file"], $js_value["path"]);
				}
				elseif (is_null($js_value))
				{
					if(file_exists(FF_DISK_PATH . $this->getThemePath(false) . "/javascript/" . $js_key . ".js"))
						$this->tplAddJs($js_key, $js_key . ".js", $this->getThemePath() . "/javascript");
					elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library/" . $js_key . "/" . $js_key . ".js"))
						$this->tplAddJs($js_key, $js_key . ".js", FF_THEME_DIR . "/library/" . $js_key);
				}
				else
				{
					if(file_exists(FF_DISK_PATH . $this->getThemePath(false) . "/javascript" . $js_value))
						$this->tplAddJs($js_key, null, $this->getThemePath() . "/javascript" . $js_value);
					elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library" . $js_value))
						$this->tplAddJs($js_key, null, FF_THEME_DIR . "/library" . $js_value);
					//$this->tplAddJs($js_key, $js_value, null);
				}
			}
			reset($this->widgets[$name]);
		}

		if(is_array($this->widgets[$name]->css_deps) && count($this->widgets[$name]->css_deps))
		{
			foreach ($this->widgets[$name]->css_deps AS $css_key => $css_value)
			{
				$rc = $this->widgetResolveCss($css_key, $css_value, $this); 

				$this->tplAddCss(preg_replace('/[^0-9a-zA-Z]+/', "", $css_key), $rc["file"], $rc["path"], "stylesheet", "text/css", false, false, null, false, "bottom");
			}
			reset($this->widgets[$name]);
		}
	}

	function widgetResolveCss($css_key, $css_value, &$oPage)
	{
		if(is_array($css_value))
		{
			if(!is_null($css_value["path"]))
			{
				$rc = $css_value;
			}
			else 
			{
	            if(isset($css_value["rel"]) && strlen($css_value["file"]))
	                $sub_path_css = $css_value["rel"];
	            else
	                $sub_path_css = $css_key;

                if($oPage->jquery_ui_force_theme !== null && strpos($oPage->jquery_ui_force_theme, "/") === 0 && file_exists(FF_DISK_PATH . $oPage->jquery_ui_force_theme . "/" . $css_value["file"]))
                    $rc = array("file" => $css_value["file"], "path" => $oPage->jquery_ui_force_theme);
                elseif($oPage->jquery_ui_force_theme !== null && array_key_exists("jquery.ui", $oPage->cdn_version) && file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library/" . $sub_path_css . "/themes/" . $oPage->jquery_ui_force_theme . "/" . $oPage->cdn_version["jquery.ui"]["major"] . "." . $oPage->cdn_version["jquery.ui"]["minor"] . ".x" . "/" . $css_value["file"]))
                    $rc = array("file" => $css_value["file"], "path" => FF_THEME_DIR . "/library/" . $sub_path_css . "/themes/" . $oPage->jquery_ui_force_theme . "/" . $oPage->cdn_version["jquery.ui"]["major"] . "." . $oPage->cdn_version["jquery.ui"]["minor"] . ".x");
				elseif($oPage->jquery_ui_force_theme !== null && file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library/" . $sub_path_css . "/themes/" . $oPage->jquery_ui_force_theme . "/" . $css_value["file"]))
					$rc = array("file" => $css_value["file"], "path" => FF_THEME_DIR . "/library/" . $sub_path_css . "/themes/" . $oPage->jquery_ui_force_theme);
				elseif(file_exists(FF_DISK_PATH . $oPage->getThemePath(false) . "/css/" . $sub_path_css . "/" . $css_value["file"]))
					$rc = array("file" => $css_value["file"], "path" => $oPage->getThemePath(false) . "/css/" . $sub_path_css);
				elseif(file_exists(FF_DISK_PATH . $oPage->getThemePath(false) . "/css/" . $css_value["file"]))
					$rc = array("file" => $css_value["file"], "path" => $oPage->getThemePath(false) . "/css");
				elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library/" . $sub_path_css . "/" . $css_value["file"]))
					$rc = array("file" => $css_value["file"], "path" => FF_THEME_DIR . "/library/" . $sub_path_css);
                elseif(array_key_exists("jquery.ui", $oPage->cdn_version) && file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library/" . $sub_path_css . "/themes/" . $oPage->jquery_ui_theme . "/" . $oPage->cdn_version["jquery.ui"]["major"] . "." . $oPage->cdn_version["jquery.ui"]["minor"] . ".x" . "/" . $css_value["file"]))
                    $rc = array("file" => $css_value["file"], "path" => FF_THEME_DIR . "/library/" . $sub_path_css . "/themes/" . $oPage->jquery_ui_theme . "/" . $oPage->cdn_version["jquery.ui"]["major"] . "." . $oPage->cdn_version["jquery.ui"]["minor"] . ".x");
				elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library/" . $sub_path_css . "/themes/" . $oPage->jquery_ui_theme . "/" . $css_value["file"]))
					$rc = array("file" => $css_value["file"], "path" => FF_THEME_DIR . "/library/" . $sub_path_css . "/themes/" . $oPage->jquery_ui_theme);
			}
		}
		elseif (is_null($css_value))
		{
			if(file_exists(FF_DISK_PATH . $oPage->getThemePath(false) . "/css/" . $css_key . ".css"))
				$rc = array("file" => null, "path" => $oPage->getThemePath(false) . "/css");
			elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library/" . $css_key . "/" . $css_key . ".css"))
				$rc = array("file" => null, "path" => FF_SITE_PATH . FF_THEME_DIR . "/library/" . $css_key);
		} 
		else
		{
			if(file_exists(FF_DISK_PATH . $oPage->getThemePath(false) . "/css" . $css_value))
				$rc = array("file" => $oPage->getThemePath(false) . "/css" . $css_value, "path" => "");
			elseif(file_exists(FF_DISK_PATH . FF_THEME_DIR ."/library" . $css_value))
				$rc = array("file" => FF_THEME_DIR . "/library" . $css_value, "path" => "");
		}					
		
		return $rc;
		/*if (!is_null($css_value))
			$this->tplAddCss($css_key, $this->getThemePath() . "/css" . $css_value, "");
		else
		{
			$this->tplAddCss($css_key, null, $this->getThemePath() . "/css");
		}*/
	}
	
	/**
	 *
	 * @param Mixed $content il contenuto da aggiungere. Se null si sta aggiungendo un gruppo
	 * @param Mixed $group può essere una stringa se si aggiunge un contenuto ad un gruppo o "true" nel caso si aggiunga un gruppo
	 * @param String $id L'id del contenuto
	 * @param Array $options opzioni aggiuntive da passare relativamente ad un aggiunta
	 */
	public function addContent($content, $group = null, $id = null, $options = array())
	{
		if ($content === null && ($group === true))
			$this->widgetLoad("tabs");
			
		parent::addContent($content, $group, $id, $options);
	}

	/**
	 * Esegue il processing dell'oggetto.
	 * Difficilmente verrà chiamata due volte.
	 * @param Boolean $output_result se dev'essere emesso l'output immediatamente
	 * @return Mixed il risultato dell'elaborazione
	 */
	function process($output_result = true)
	{
		$this->output_buffer = array();

		if($this->use_own_form !== false)
			$this->addHiddenField("frmAction", ffCommon_specialchars($_REQUEST["frmAction"]));

		if (!$this->params_processed)
			$this->process_params();

		$this->tplLoad();

		$this->doEvent("on_page_process", array(&$this));

		if (is_array($this->components) && count($this->components))
		{
			// First of all, do a preprocess to retrieve params
			foreach ($this->components as $key => $item)
			{
				$this->components[$key]->pre_process();

				if ($this->form_method == "" && is_subclass_of($this->components[$key], "ffRecord_base"))
					$this->form_method = "post";
			}
			reset($this->components);

			if ($this->form_method == "")
				$this->form_method = "get";

			if ($this->form_enctype == "")
			{
				if (strtolower($this->form_method) == "get")
					$this->form_enctype = "application/x-www-form-urlencoded";
				else if (strtolower($this->form_method) == "post")
					$this->form_enctype = "multipart/form-data";
			}

			// After params, process page contents (without parsing templates)
			$components_keys = array_keys($this->components);
			foreach ($components_keys as $key => $item)
			{
				if (is_array($this->components[$item]->widget_deps) && count($this->components[$item]->widget_deps))
				{
					foreach ($this->components[$item]->widget_deps as $subkey => $subvalue)
					{
						if ($subvalue["options"])
							call_user_func_array(array($this->widgets[$subvalue["name"]], "process"), $subvalue["options"]);
						else
							$this->widgets[$subvalue["name"]]->process($this->components[$item]);
					}
					reset($this->components[$item]->widget_deps);
				}
				$success = false;
				if (FF_ENABLE_MEM_PAGE_CACHING && isset($this->components[$item]->cache_get_resources) && count($this->components[$item]->cache_get_resources))
					$res = $this->cache->get($this->request_key . "_" . $item, $success);
				if ($success)
				{
					$this->components_buffer[$item] = $res;
					//$ret = $this->componentWidgetsProcess($item);
				}
				else
					$this->components[$item]->process();
			}
			reset($components_keys);
		}
		
		// After components, process fields
		$fields_keys = array_keys($this->fields);
		foreach ($fields_keys as $key => $item)
		{
			if (is_array($this->fields[$item]->widget_deps) && count($this->fields[$item]->widget_deps))
			{
				foreach ($this->fields[$item]->widget_deps as $subkey => $subvalue)
				{
					if ($subvalue["options"])
						call_user_func_array(array($this->widgets[$subvalue["name"]], "process"), $subvalue["options"]);
					else
						$this->widgets[$subvalue["name"]]->process($this->fields[$item]);
				}
				reset($this->fields[$item]->widget_deps);
			}
			//TOCHECK $this->fields[$item]->process();
		}
		reset($fields_keys);

		// process buttons
		$butt_keys = array_keys($this->buttons);
		foreach ($butt_keys as $key => $item)
		{
			if (is_array($this->buttons[$item]->widget_deps) && count($this->buttons[$item]->widget_deps))
			{
				foreach ($this->buttons[$item]->widget_deps as $subkey => $subvalue)
				{
					if ($subvalue["options"])
						call_user_func_array(array($this->widgets[$subvalue["name"]], "process"), $subvalue["options"]);
					else
						$this->widgets[$subvalue["name"]]->process($this->buttons[$item]);
				}
				reset($this->buttons[$item]->widget_deps);
			}
		}
		reset($butt_keys);

		$obj_keys = array_keys($this->objects);
		foreach ($obj_keys as $key => $item)
		{
			if ($this->objects[$item]->parent[0] === $this)
			{
				$this->objects_buffer[$item]["headers"] = $this->objects[$item]->process_headers();
				$this->objects_buffer[$item]["footers"] = $this->objects[$item]->process_footers();
			}
		}
		reset($obj_keys);

		if (is_array($this->components) && count($this->components))
		{
			// After processing, retrieve output
			foreach ($components_keys as $key => $item)
			{
				if ($this->components[$item]->display !== false)
				{
					if ($this->getXHRComponent() && $this->getXHRComponent() != $item)
						continue;

					if ($this->components_buffer[$item] === null) // ignora nel caso in cui sia già stato preso da cache
					{
						$this->components_buffer[$item]["html"] = $this->components[$item]->process_interface();
						$this->components_buffer[$item]["headers"] = $this->components[$item]->process_headers();
						$this->components_buffer[$item]["footers"] = $this->components[$item]->process_footers();
						
						if(property_exists($this->components[$item], "widget_activebt_enable") && $this->components[$item]->widget_activebt_enable && !isset($this->widgets["activebuttons"]))
							$this->widgetLoad("activebuttons");
						
						if(property_exists($this->components[$item], "widget_discl_enable") && $this->components[$item]->widget_discl_enable && !isset($this->widgets["disclosures"]))
							$this->widgetLoad("disclosures");

						$ret = $this->componentWidgetsProcess($item);
						$this->components_buffer[$item]["headers"] .= $ret["headers"];
						$this->components_buffer[$item]["footers"] .= $ret["footers"];

						if (FF_ENABLE_MEM_PAGE_CACHING && isset($this->components[$item]->cache_get_resources) && count($this->components[$item]->cache_get_resources))
						{
							call_user_func_array(array($this->cache, "set"),
									array_merge(
										array(
												$this->request_key . "_" . $item
												, null
												, $this->components_buffer[$item]
										),
										$this->components[$item]->cache_get_resources
									)
								);
						}
					}
				}
			}
			reset($components_keys);

			// After processing buffers, set inner components
			$components_keys_copy = array_keys($this->components);
			do
			{
				$replaces = 0;
				foreach ($components_keys_copy as $key => $item)
				{
					foreach ($components_keys as $subkey => $subitem)
					{
						if ($this->getXHRComponent() == $subitem)
							continue;
						
						$this->components_buffer[$item]["html"] = str_replace("{{" . $subitem . "}}", $this->components_buffer[$subitem]["html"], $this->components_buffer[$item]["html"], $count);
						$replaces += $count;
						if ($count)
						{
							$this->components_buffer[$item]["headers"] .= $this->components_buffer[$subitem]["headers"];
							$this->components_buffer[$item]["footers"] .= $this->components_buffer[$subitem]["footers"];
							$this->components_buffer[$subitem] = array();
						}
					}
					reset($components_keys);
				}
				reset($components_keys_copy);
			} while ($replaces > 0);
		}

		// process buttons with different location
		$butt_keys = array_keys($this->buttons);
		foreach ($butt_keys as $key => $item)
		{
			if ($this->buttons[$item]->use_own_location)
			{
				if ($this->buttons[$item]->location_context !== null)
				{
					if (
						is_object($this->buttons[$item]->location_context)
						&& get_class($this->buttons[$item]->location_context) == "ffTemplate"
					)
						$this->buttons[$item]->location_context->set_var(($this->buttons[$item]->location_name !== null ? $this->buttons[$item]->location_name : $this->buttons[$item]->id), $this->buttons[$item]->process());
				}
			}
		}
		reset($butt_keys);
			
		$this->tplProcessBounceComponents();

		$rc = $this->doEvent("on_after_process_components", array(&$this));

		if (strlen($this->layer) && !$this->isXHR())
			$this->tplProcessLayout();

		$this->tplProcess();

		if (!($this->isXHR() && $this->getXHRComponent()))
			$this->widgetsProcess();

		return $this->tplParse($output_result);
	}
	
	function getBrowser()
	{
	    $u_agent = $_SERVER['HTTP_USER_AGENT'];
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version= "";

	    //First get the platform?
	    if (preg_match('/linux/i', $u_agent)) {
	        $platform = 'linux';
	    }
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        $platform = 'mac';
	    }
	    elseif (preg_match('/windows|win32/i', $u_agent)) {
	        $platform = 'windows';
	    }
	   
	    // Next get the name of the useragent yes seperately and for good reason
		if(preg_match('/iPad/i',$u_agent))
	    {
	        $bname = 'Ipad';
	        $ub = "Ipad";
	    }
		elseif(preg_match('/iPhone/i',$u_agent))
	    {
	        $bname = 'iPhone';
	        $ub = "iPhone";
	    }
		elseif(preg_match('/iPod/i',$u_agent))
	    {
	        $bname = 'Ipod';
	        $ub = "Ipod";
	    }
	    elseif(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
	    {
	        $bname = 'Internet Explorer';
	        $ub = "MSIE";
	    }
	    elseif(preg_match('/Firefox/i',$u_agent))
	    {
	        $bname = 'Mozilla Firefox';
	        $ub = "Firefox";
	    }
	    elseif(preg_match('/Chrome/i',$u_agent))
	    {
	        $bname = 'Google Chrome';
	        $ub = "Chrome";
	    }
	    elseif(preg_match('/Safari/i',$u_agent))
	    {
	        $bname = 'Apple Safari';
	        $ub = "Safari";
	    }
	    elseif(preg_match('/Opera/i',$u_agent))
	    {
	        $bname = 'Opera';
	        $ub = "Opera";
	    }
	    elseif(preg_match('/Netscape/i',$u_agent))
	    {
	        $bname = 'Netscape';
	        $ub = "Netscape";
	    }

	    // finally get the correct version number
	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?P<browser>' . join('|', $known) .
	    ')[/ ]+(?P<version>[0-9.|a-zA-Z.]*)#';
	    $rc = @preg_match_all($pattern, $u_agent, $matches);
	   	if($rc === false) {
			$pattern = '#(?<browser>' . join('|', $known) .
		    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		    $rc = @preg_match_all($pattern, $u_agent, $matches);
		    
		   	if($rc === false) {
			   	ffErrorHandler::raise("unable to check browser version", E_USER_ERROR, null, get_defined_vars());
		   	}
	   	}
	    // see how many we have
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        //we will have two since we are not using 'other' argument yet
	        //see if version is before or after the name
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
	            $version= $matches['version'][0];
	        }
	        else {
	            $version= $matches['version'][1];
	        }
	    }
	    else {
	        $version= $matches['version'][0];
	    }
	   
	    // check if we have a number
	    if ($version==null || $version=="") {$version="?";}
	   
	    return array(
	        'userAgent' 	=> $u_agent
	        , 'extendname'  => $bname
	        , 'name'		=> $ub
	        , 'majorver'   	=> (strpos($version, ".") === false ? $version : substr($version, 0, strpos($version, ".")))
	        , 'lowerver'   	=> (strpos($version, ".") === false ? $version : substr($version, strpos($version, ".") + 1))
	        , 'platform'  	=> $platform
	        , 'pattern'    	=> $pattern
	    );
	}
	
	/**
	 * rileva se la pagina è stata richiesta con una chiamata Ajax
	 * @return boolean
	 */
	function isXHR()
	{
		if (!$this->force_no_xhr && $_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest")
			return true;
		else
			return false;
	}

	/**
	 * rileva se è stata richiesta l'elaborazione di un singolo componente, se si restituisce il nome del componente
	 * @return mixed
	 */
	function getXHRComponent()
	{
		if (!isset($_REQUEST["XHR_COMPONENT"]))
			return false;
		else
			return $_REQUEST["XHR_COMPONENT"];
	}

	/**
	 * rileva se è stata richiesta l'elaborazione di una sezione di un singolo componente, se si restituisce il nome della sezione, se no false
	 * @return mixed
	 */
	function getXHRSection()
	{
		if (!isset($_REQUEST["XHR_SECTION"]))
			return false;
		else
			return $_REQUEST["XHR_SECTION"];
	}

	/**
	 * rileva se è stata richiesta l'elaborazione dell'intera pagina
	 * @return mixed
	 */
	function getXHRFull()
	{
		if (!isset($_REQUEST["XHR_GET_FULL"]))
			return false;
		else
			return true;
	}

	/**
	 * rileva se è stata richiesta l'elaborazione di un dialog
	 * @return mixed
	 */
	function getXHRDialog()
	{
		if (!isset($_REQUEST["XHR_DIALOG_ID"]))
			return false;
		else
			return $_REQUEST["XHR_DIALOG_ID"];
	}
	
	
}
