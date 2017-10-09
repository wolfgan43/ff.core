<?php
/**
* @package Forms PHP Framework
* @category Field Class
* @desc ffField_html.php - Forms Framework Interface Field, html version
* @author Samuele Diella <samuele.diella@gmail.com>
* @copyright Copyright &copy; 2004-2017, Samuele Diella
* @license https://opensource.org/licenses/LGPL-3.0
* @link http://www.formsphpframework.com
* @version beta 2
* @since beta 2
*/

class ffField_html extends ffField_base
{
	var $url = null;
	var $label_encode_entities = true;

	var $buttons_options = array(
		"file" => array(
			"edit" => array(
				"class" => "edit-file"
				, "label" => ""
			)
			, "delete" => array(
				"class" => "del-file"
				, "label" => ""
			)
		)
	);

	//----------------------
	//  Widget Settings

	// Active Combo EX Stuffs
	/**
	 * Il padre se presente
	 * @var String
	 */
	var $actex_father					= null;
	/**
	 * Il od I figli
	 * @var Mixed può essere stringa o array
	 */
	var $actex_child					= null;
	
	var $actex_reset_childs				= false;
	/**
	 * Se il contenuto dev'essere recuperato con richieste asincrone
	 * @var Boolean
	 */
	var $actex_update_from_db			= false;
	/**
	 * Il percorso di un servizio alternativo a quello di default
	 * @var String
	 */
	var $actex_service					= null;
	/**
	 * Il campo di relazione con il padre da usare per la [WHERE]
	 * @var Mixed
	 */
	var $actex_related_field			= "";

	/**
	 * Il tipo di operazione usabile nella [WHERE]
	 * Possibili valori '=', 'IN', 'LIKE', '<>'
	 * @var Mixed
	 */
	var $actex_operation_field			= "=";

	/**
	 * Abilita o disabilita la visualizzazione dei Dialog collegati
	 * @var Boolean
	 */
	var $actex_dialog					= true; // disable everything
	/**
	 * Visualizza il pulsante di aggiunta al combo che apre il relativo dialog
	 * @var Boolean
	 */
	var $actex_dialog_show_add			= true; // without actex_dialog_url this is ignored
	/**
	 * l'url di apertura del Dialog
	 * @var String
	 */
	var $actex_dialog_url				= "";
	/**
	 * il titolo del dialog
	 * @var String
	 */
	var $actex_dialog_title				= "";
	/**
	 * Visualizza il pulsante di editing dell'elemento selezionato
	 * @var Boolean
	 */
	var $actex_dialog_show_edit			= true;
	/**
	 * l'url di editing per il dialgo. Se omesso è uguale a actex_dialog_url
	 * @var String
	 */
	var $actex_dialog_edit_url			= "";
	/**
	 * Il titolo del dialog in editing. Se omesso è uguale a actex_dialog_title
	 * @var String
	 */
	var $actex_dialog_edit_title		= "";
	/**
	 * Un elenco di parametri nel formato query_string da passare all'url di editing
	 * E' possibile usare i tag speciali [[ID_DOM]] per recuperare valori dal dom,
	 * così come i normali tag [ID] per recuperare valori dai fields del framework
	 * @var String
	 */
	var $actex_dialog_edit_params		= "";
	/**
	 * Visualizza il pulsante di eliminazione dell'elemento correntemente selezionato
	 * @var Boolean
	 */
	var $actex_dialog_show_delete		= true; // without actex_dialog_delete_url this is ignored
	/**
	 * L'url del dialog di eliminazione, se omesso è uguale a $actex_dialog_edit_url
	 * @var String
	 */
	var $actex_dialog_delete_url		= "";
	/**
	 * Il titolo del dialog di eliminazione, se omesso è uguale a $actex_dialog_edit_title
	 * @var String
	 */
	var $actex_dialog_delete_title		= "";
	/**
	 * Un elenco di parametri nel formato query_string da passare all'url di eliminazione
	 * E' possibile usare i tag speciali [[ID_DOM]] per recuperare valori dal dom,
	 * così come i normali tag [ID] per recuperare valori dai fields del framework
	 * Se omesso è uguale ad $actex_dialog_edit_params
	 * @var String
	 */
	var $actex_dialog_delete_params		= null;
	/**
	 * Il messaggio da visualizzare per l'eliminazione nel dialog
	 * @var String
	 */
	var $actex_dialog_delete_message	= "Confermi l'eliminazione del dato?<span>Il dato verr&agrave; eliminato definitivamente, non potr&agrave; essere recuperato.</span>";
	/**
	 * L'ID del componente collegato per l'eliminazione
	 * @var String
	 */
	var $actex_dialog_delete_idcomp = null;
			
	/**
	 * Se l'oggetto DB dell'activecombo deve collegarsi direttamente al database principale (usando mod_security)
	 * @var Boolean default false
	 */
	var $actex_skip_empty = false;
	var $actex_use_main_db	= false;
    var $actex_add_plus     = false;
    var $actex_on_change	= null;
    var $actex_on_update_bt = null;
    var $actex_on_refill 	= null;
    var $actex_hide_empty = false;
    var $actex_group = null;
    var $actex_attr = null;
    var $actex_plugin = null;
	var $actex_use_own_session = false;
	var $actex_dialog_icon_add			= "add.png";
	var $actex_dialog_icon_edit			= "edit.png";
	var $actex_dialog_icon_delete		= "delete.png";
	var $actex_dialog_title_add			= "";
	var $actex_dialog_title_edit		= "";
	var $actex_dialog_title_delete		= "";
	var $actex_hide_result_on_query_empty = false;
	var $actex_preserve_field			= null;
	var $actex_cache					= true;
	
	var $actex_autocomp					= false;
	var $actex_autocomp_ajax			= false;
	
	var $autocomplete_service			= null;
    var $autocomplete_disabled          = false;
    var $autocomplete_minLength         = 3;
    var $autocomplete_delay             = 500;
    var $autocomplete_multi             = false;
    var $autocomplete_cache             = true;
    var $autocomplete_readonly          = true;
    var $autocomplete_combo             = false;
    var $autocomplete_compare           = "";
    var $autocomplete_compare_having    = "";
    var $autocomplete_operation         = "LIKE [%[VALUE]%]";
    var $autocomplete_strip_char        = "";
    var $autocomplete_label 			= "";
	var $autocomplete_use_own_session	= false;
	var $autocomplete_use_main_db	= false;
	var $autocomplete_hide_result_on_query_empty = false;
	
    var $autocompletetoken_minLength 		= 3;
    var $autocompletetoken_delay 			= 300;
    var $autocompletetoken_compare 			= "";
    var $autocompletetoken_compare_having   = "";
    var $autocompletetoken_operation 		= "LIKE [%[VALUE]%]";
    var $autocompletetoken_theme 			= "";
    var $autocompletetoken_not_found_label 	= "Not Found";
    var $autocompletetoken_init_label 		= "What you want to search?";
    var $autocompletetoken_searching_label 	= "Searching...";
    var $autocompletetoken_limit 			= "null";
    var $autocompletetoken_delimiter 		= ",";
    var $autocompletetoken_label 			= "";
    var $autocompletetoken_service 			= null;
    var $autocompletetoken_combo            = false;
    var $autocompletetoken_concat_field		= array();
    var $autocompletetoken_concat_separator = " - ";
    
    
    var $datepicker_lang					= NULL; // default to first two chars of FF_LOCALE
    var $datepicker_force_datetime			= false;
    var $datepicker_showbutton				= true;
	var $datepicker_weekselector			= false;
	// Slider
	/**
	 * Quante posizioni sono a disposizione dello slider
	 * @var Int
	 */
	var $step = "1";
	/**
	 * L'elenco di label per ogni step dello slider
	 * @var Array
	 */
	var $desc_label = array();
    /**
	 * Se devono essere visualizzate le label relative ad ogni posizione
	 * @var Boolean
	 */
    var $display_label = true;
    var $encode_label = true;
    
    var $data_info = array("field" => null
    						, "base_type", "Text"
    						, "multilang", false
    					);	

	// gmap stuffs
	/**
	 * la chiave di google maps
	 * @var String
	 */
	var $gmap_key = null;
	/**
	 * Se il pinpoint dev'essere Spostabile
	 * @var Boolean
	 */
	var $gmap_draggable = true;
	/**
	 * Il livello di zoom iniziale
	 * @var Int
	 */
	var $gmap_start_zoom = 6;
	/**
	 * La latitudine di default
	 * @var Int
	 */
	var $gmap_start_lat = 45;
	/**
	 * La longitudine di default
	 * @var Int
	 */
	var $gmap_start_lng = 9;
	
	var $gmap_force_search = false;
	var $gmap_update_class = "";
	var $gmap_update_class_prefix = "";
	
	var $gmap_region									= "";
	var $gmap3_marker_limit								= 1;
	var $gmap3_max_zoom									= 1;
	var $gmap3_min_zoom									= 18;
	var $gmap3_zoom_control								= true;
	var $gmap3_zoom_control_position					= "RIGHT_BOTTOM";
	var $gmap3_marker_icon								= "";// esempio "/themes/site/images/beachflag_with_shadow.png";
	var $gmap3_map_type_control							= false;
	var $gmap3_map_type_control_options					= "DROPDOWN_MENU";
	var $gmap3_map_type_control_enable_your_style		= true;
	var $gmap3_map_type_control_your_style_name			= "my_style";
	var $gmap3_zoom_control_style						= "SMALL";
	var $gmap3_pan_control								= false;
	var $gmap3_pan_control_position						= "BOTTOM_CENTER";
	var $gmap3_scale_control							= false;
	var $gmap3_scale_control_position					= "LEFT_CENTER";
	var $gmap3_streetview_control						= false;
	var $gmap3_streetview_control_position				= "RIGHT_CENTER";
	/**
	 * permette di customizzare graficamente le mappe. 
	 * Bisogna generare il JSON seguendo questo link:
	 * http://gmaps-samples-v3.googlecode.com/svn/trunk/styledmaps/wizard/index.html
	 * @var JSON
	 */
    var $gmap3_personal_style							= false;
	var $gmap3_personal_style_text                 		= "";
	
	//tiny_mce    
    var $tiny_mce_group_by_auth = false;
    var $tinymce_group = "default";

	//ckeditor
	var $ckeditor_custom_config = array();		//Altera la configurazione di base
	var $ckeditor_theme = "default";	//Altera la struttura di ckeditor
	var $ckeditor_br_mode = false;		//Usa i br o le p per costruire la paragrafazione dei testi
	var $ckeditor_skin = "kama";		//Altera l'aspetto grafico  di ckeditor
	var $ckeditor_group_by_auth = false;//Abilita l'assegnazione del gruppo per le toolbar di ckeditor basandosi 
										//sul nome del gruppo di appartenenza dell'utente definito in sessione.
	var $ckeditor_group = "default";	//Altera le toolbar all'interno di ckeditor.
										//I possibili valori sono:
										/*	default =
											[
												['Source','NewPage','Preview','-','Bold','Italic','Underline','-','Find','Replace','-','Cut','Copy','Paste','PasteFromWord'],
											    ['Maximize', 'ShowBlocks'],['Link','Unlink','Anchor'],['Format', 'TextColor'],['NumberedList','BulletedList']
											]; 
											administrators =
											[
											    ['Source','-','Save','NewPage','Preview','-','Templates'],
											    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
											    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
											    ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
											    '/',
											    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
											    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
											    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
											    ['Link','Unlink','Anchor'],
											    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
											    '/',
											    ['Styles','Format','Font','FontSize'],
											    ['TextColor','BGColor'],
											    ['Maximize', 'ShowBlocks','-','About']
											]; 
											dataentry =
											[
											    ['Source'],
											    ['Cut','Copy','Paste','PasteText','PasteFromWord'],
											    ['Undo','Redo'],
											    '/',
											    ['Bold','Italic','Underline','Strike'],
											    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
											    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
											    ['Link','Unlink','Anchor'],
											    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
											    '/',
											    ['Styles','Format','Font','FontSize'],
											    ['TextColor','BGColor'],
											    ['Maximize', 'ShowBlocks']
											]; 
											user =
											[
											    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
											    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
											    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
											    ['Link','Unlink','Anchor'],
											    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
											    '/',
											    ['Styles','Format','Font','FontSize'],
											    ['TextColor','BGColor'],
											]; 
										*/
	
	// ckfinder
    var $file_widget_preview = true;		//Abilita la preview dell'immagine
    									// (se ckfinder_show_file non e valorizzato la preview sara disabilitata)
    var $ckfinder_show_file = "";		// percorso assoluto della chiamata a show_files per la preview del thumb
    									// (omettendo la porzione descritta nel db)
    var $ckfinder_base_path = null;		// percorso assoluto della cartella degli uploads es: /var/www/miosito/uploads
										// se omesso verra generato come segue: FF_DISK_PATH . "/uploads"
    var $ckfinder_storing_path = "";	// percorso assoluto di dove verra salvato il file

    
    var $editarea_syntax 		= "";
    var $editarea_writable 		= true;
    
    
	//uploadify
	var $uploadify_use_own_session = false;
    var $uploadify_model = "default";		//Abilita la preview dell'immagine
    var $uploadify_model_thumb = "";
    									// (se ckfinder_show_file non e valorizzato la preview sara disabilitata)

	//uploadifive
    var $uploadifive_model = "default";		//deprecata
    var $uploadifive_showfile_path = null;
    var $uploadifive_showfile_plugin = "fancybox";
    var $uploadifive_model_thumb = "";

    									// (se ckfinder_show_file non e valorizzato la preview sara disabilitata)
    
    var $placeholder = false;
    
	//slug
	/**
	 * Il nome del campo slug associato 
	 * @var String
	 */
	var $slug_title_field = null;

	// List Splitter Stuffs
	var $size = 7;
	var $grouping_separator	= ",";							// the string used to separate groups of strings

	/**
	 * Una descrizione aggiuntiva da associare al field
	 * @var String
	 */
	var $description = null;

	/**
	 * Se il campo dev'essere visualizzato dal programmatore invece che dal componente
	 * @var Boolean
	 */
	var $manual_display = false;

	/**
	 * proprietà da associare all'elemento che fa il wrap dei dati
	 * @var Array
	 */
	var $data_properties		= array();
	var $data_class = "";

	var $fixed_vars = array();
	var $container_vars = array();
    
    var $widget_path = "";
    
	/**
	 * recupera il file del template
	 * @param String $control_type il tipo di controllo di cui recuperare il template
	 * @return String
	 */
	public function getTemplateFile($control_type)
	{
		if (strlen($this->template_file))
			return $this->template_file;
		else
			return "ffControl_" . $control_type . ".html";
	}
	
	function get_control_class($control_type = null)
	{
		if (strlen($this->class))
			return $this->class;
		else if(strlen($this->widget))
			return $this->widget;
		
		if ($control_type === null)
			$control_type = $this->get_control_type();
		
		switch($control_type)
		{
			case "picture":
			case "picture_no_link":
				return "picture";

			default:
				return $control_type;
		}
	}	
	/**
	 * Esegue il parsing del template
	 * @param Boolean $output_result se true visualizza a video il risultato del processing, se false restituisce il contenuto del processing
	 * @return Mixed può essere string o true, a seconda di output_result
	 */
	public function tplParse($output_result)
	{
		$buffer = $this->fixed_pre_content . $this->tpl[0]->rpparse("main", false) . $this->fixed_post_content;

		if ($this->parent_page !== NULL) //code for ff.js
			$this->parent_page[0]->tplAddJs("ff.ffField");

		if ($output_result)
		{
			echo $buffer;
			return true;
		}
		else
		{
			return $buffer;
		}
	}

	/**
	 * carica l'oggetto template dentro $tpl
	 */
	public function tplLoad($control_type)
	{
		$this->tpl[0] = ffTemplate::factory($this->getTemplateDir($control_type));
		$this->tpl[0]->load_file($this->getTemplateFile($control_type), "main");
                              
		if ($this->parent !== null && strlen($this->parent[0]->getIDIF()))
		{
			if (!$this->omit_parent_id)
				$this->tpl[0]->set_var("container", $this->parent[0]->getPrefix());
		}

		if($this->parent_page !== NULL) {
			$this->tpl[0]->set_var("keys", $this->parent_page[0]->get_params("", "", false));
			$this->tpl[0]->set_var("query_string", $this->parent_page[0]->get_script_params());
		}
		
		$this->tpl[0]->set_var("site_path", ffCommon_specialchars($this->site_path));
		$this->tpl[0]->set_var("page_path", ffCommon_specialchars($this->page_path));

		if (!strlen($this->widget))
			$this->tpl[0]->set_var("class", ffCommon_specialchars($this->get_control_class($control_type)));

        if (is_array($this->fixed_vars) && count($this->fixed_vars))
        {
            foreach ($this->fixed_vars as $key => $value)
            {
                $this->tpl[0]->set_var($key, $value);
            }
            reset($this->fixed_vars);
        }
    
		$this->tpl[0]->set_var("properties", $this->getProperties());

		$res = $this->doEvent("on_tpl_load", array(&$this));
	}
	
	function getProperties($property_set = null)
	{
		if ($property_set === null)
		{
			$property_set = $this->properties;

			if ($this->placeholder === true)
				$property_set["placeholder"] = ffCommon_specialchars($this->label);
			else if(strlen($this->placeholder))
				$property_set["placeholder"] = $this->placeholder;
		}
		
		return parent::getProperties($property_set);
	}
	
	function process_file($id, &$value)
	{
		$this->tpl[0]->set_var("butt_del_class", $this->buttons_options["file"]["delete"]["class"]);
		$this->tpl[0]->set_var("butt_del_label", $this->buttons_options["file"]["delete"]["label"]);

		$this->tpl[0]->set_var("butt_edit_class", $this->buttons_options["file"]["edit"]["class"]);
		$this->tpl[0]->set_var("butt_edit_label", $this->buttons_options["file"]["edit"]["label"]);
		
		parent::process_file($id, $value);
	}
}
