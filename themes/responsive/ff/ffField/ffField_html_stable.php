<?php
/**
* @package Forms PHP Framework
* @category Field Class
* @desc ffField_html.php - Forms Framework Interface Field, html version
* @author Samuele Diella <samuele.diella@gmail.com>
* @copyright Copyright &copy; 2004-2017, Samuele Diella
* @license http://opensource.org/licenses/gpl-3.0.html
* @link http://www.formsphpframework.com
* @version beta 2
* @since beta 2
*/
frameworkCSS::extend(array(
        "types" => array(
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
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "select-custom" => array(
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
                    "class" => "custom-select"
                    , "form" => "control"
                )
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "checkbox" => array(
                "container" => array(
                    "class" => null
                    , "form" => "row-check"
                )
                , "label-wrap" => false
                , "label" => array(
                    "class" => null
                    , "form" => "label-check"
                )
                , "control-wrap" => false
                , "control" => array(
                    "form" => "control-check"
                )
                , "prototype" => "[CONTROL][LABEL]"
            )
            , "radio" => array(
                "container" => array(
                    "class" => null
                    , "form" => "row-check"
                )
                , "label-wrap" => false
                , "label" => array(
                    "class" => null
                    , "form" => "label-check"
                )
                , "control-wrap" => false
                , "control" => array(
                    "form" => "control-check"
                )
                , "prototype" => "[CONTROL][LABEL]"
            )
            , "label" => array(
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
                    "form" => "control-plaintext"
                )
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "file" => array(
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
                    "form" => "control-file"
                )
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "file-custom" => array(
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
                    "class" => "custom-file-input"
                    , "form" => false
                )
                , "prototype" => '[LABEL] <div class="custom-file">[CONTROL] <label class="custom-file-label">[PLACEHOLDER]</label></div>[DESCRIPTION]'
            )
            , "file-thumb" => array( //todo: da finire
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
                    "class" => null
                    , "form" => false
                )
                , "prototype" => '[LABEL] <div class="input-file-container">[CONTROL] <label class="input-file-trigger">[PLACEHOLDER]</label></div>[DESCRIPTION]'
            )
            , "picture" => array(
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
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "currency" => array(
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
                    , "util" => "right"
                )
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "number" => array(
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
                    , "util" => "center"
                )
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "range" => array(
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
                , "prototype" => "[LABEL][CONTROL]"
            )

        )
        , "outer_wrap" => false
        , "field" => null
        , "label-for" => true
        , "label-tag" => "label"
        , "description-tag" => "small"
        , "required" => "*"
        , "input-group" => array(
            "class" => null
            , "form" => array("group")
        )
        , "exception" => array(
            "radio-multi" => array(
                "field" => array(
                    "container" => array(
                        "class" => "mt-3 mb-2"
                        , "form" => false
                    )
                    , "label" => false
                    , "prototype" => '<h5>[LABEL] [DESCRIPTION]</h5> [CONTROL]'
                )
                , "label-for" => false
                , "label-tag" => false
                , "description-tag" => "small"
            )
        )
    ), "ffField");


class ffField_html extends ffField_base
{
	var $framework_css					= null;
    var $type					        = null;
    var $size					        = null; //small, normal, large

    var $url = null;
	var $url_ajax = false;
	var $url_parsed = null;
	var $label_encode_entities = true;

	var $buttons_options = array(
		"file" => array(
			"edit" => array(
				"class" => "crop"
				, "label" => ""
			)
			, "delete" => array(
				"class" => "cancel"
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
	var $actex_update_from_db			= true;
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
	var $actex_compare_field			= "";
	var $actex_having_field				= "";

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
	 * Un elenco di parametri nel formato query_string da passare all'url di editing
	 * E' possibile usare i tag speciali [[ID_DOM]] per recuperare valori dal dom,
	 * così come i normali tag [ID] per recuperare valori dai fields del framework
	 * @var String
	 */
	var $actex_dialog_add_params		= "";

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
	var $actex_dialog_delete_message	= "Confermi l'eliminazione del dato?<br /><span>Il dato verr&agrave; eliminato definitivamente, non potr&agrave; essere recuperato.</span>";
	/**
	 * L'ID del componente collegato per l'eliminazione
	 * @var String
	 */
	var $actex_dialog_delete_idcomp = null;

	/**
	 * Se l'oggetto DB dell'activecombo deve collegarsi direttamente al database principale (usando mod_security)
	 * @var Boolean default false
	 */
	var $actex_skip_empty 				= false;
	var $actex_use_main_db				= false;
    var $actex_add_plus     			= false;
    var $actex_on_change				= null;
    var $actex_on_update_bt 			= null;
    var $actex_on_refill 				= null;
    var $actex_hide_empty				= false;
    var $actex_group 					= null;
    var $actex_attr 					= null;
    var $actex_plugin 					= null;
    var $actex_multi             		= false;
    var $actex_multi_sort             	= true;
	var $actex_use_own_session 			= false;
	var $actex_dialog_icon_add			= "add.png";
	var $actex_dialog_icon_edit			= "edit.png";
	var $actex_dialog_icon_delete		= "delete.png";
	var $actex_dialog_title_add			= "";
	var $actex_dialog_title_edit		= "";
	var $actex_dialog_title_delete		= "";
	var $actex_hide_result_on_query_empty = false;
	var $actex_preserve_field			= null;
	var $actex_cache					= true;
	var $actex_limit					= null;

	var $actex_autocomp					= false;
	var $actex_autocomp_ajax			= false;
	var $actex_autocomp_limit			= 100;

	var $autocomplete_service			= null;
    var $autocomplete_disabled          = false;
    var $autocomplete_minLength         = 3;
    var $autocomplete_delay             = 300;
    var $autocomplete_multi             = false;
    var $autocomplete_cache             = true;
    var $autocomplete_readonly          = true;
    var $autocomplete_combo             = false;
    var $autocomplete_icon				= "";
    var $autocomplete_compare           = "";
    var $autocomplete_compare_having    = "";
    var $autocomplete_operation         = null; //"LIKE [%[VALUE]%]";
    var $autocomplete_strip_char        = "";
    var $autocomplete_label 			= "";
	var $autocomplete_use_own_session	= false;
	var $autocomplete_use_main_db		= false;
	var $autocomplete_hide_result_on_query_empty = false;
	var $autocomplete_res_limit 		= 100;
	var $autocomplete_image_field 		= "image";

    var $autocompletetoken_minLength 		= 3;
    var $autocompletetoken_delay 			= 300;
    var $autocompletetoken_compare 			= "";
    var $autocompletetoken_compare_having   = "";
    var $autocompletetoken_operation 		= null; //"LIKE [%[VALUE]%]";
    var $autocompletetoken_theme 			= "";
    var $autocompletetoken_not_found_label 	= "Not Found";
    var $autocompletetoken_init_label 		= "What you want to search?";
    var $autocompletetoken_searching_label 	= "Searching...";
    var $autocompletetoken_limit 			= "null";
    var $autocompletetoken_delimiter 		= ",";
    var $autocompletetoken_label 			= "";
    //var $autocompletetoken_service 			= null;
    var $autocompletetoken_combo            = false;
    var $autocompletetoken_concat_field		= array();
    var $autocompletetoken_concat_separator = " - ";
    var $autocompletetoken_res_limit 		= 100;

    var $datechooser_type_date				= "mixed";
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
	var $ckeditor_skin = "office2013";		//Altera l'aspetto grafico  di ckeditor
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
										// se omesso verra generato come segue: FF_DISK_UPDIR
    var $ckfinder_storing_path = "";	// percorso assoluto di dove verra salvato il file


    var $editarea_syntax 		= "";
    var $editarea_writable 		= true;


	//uploadify
	var $uploadify_use_own_session = false;



	//uploadifive
    var $file_showfile_plugin = "fancybox";
    var $file_sortable = false;

    									// (se ckfinder_show_file non e valorizzato la preview sara disabilitata)

    var $placeholder = false;

	//slug
	/**
	 * Il nome del campo slug associato
	 * @var String
	 */
	var $slug_title_field = null;
    var $imagepicker_title_field = null;

	// List Splitter Stuffs
	//var $size = 7;
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

    function __construct(/*$disk_path, $site_path, $page_path, $theme*/)
    {
        parent::__construct(/*$disk_path, $site_path, $page_path, $theme*/);

        $this->framework_css = frameworkCSS::findComponent("ffField");

    }

	private function setActiveByTag($value, $attr_name) {
        $res = ' value="' . $value . '"';
	    if($this->value->ori_value !== ""
            && $value === $this->value->getValue($this->get_app_type(), $this->get_locale())
        ) {
	        $res = " " . $attr_name;
        }

	    return $res;
    }

    private function processSelectOptions($properties = null) {
        $res = array();
        if (is_array($this->recordset) && count($this->recordset)) {
            if($this->multi_select_one) {
                $value = ($this->multi_select_one_val
                    ? $this->multi_select_one_val->getValue($this->get_app_type(), $this->get_locale())
                    : ""
                );
                $label = $this->multi_select_one_label;
                $res[] = '<option' . $properties . $this->setActiveByTag($value, "selected") . '>' . $label . '</option>';
            }
            if ($this->multi_select_noone /*&&
                (!$this->multi_limit_select ||
                    ($this->multi_limit_select && $this->multi_select_noone_val->getValue($this->get_app_type(), $this->get_locale()) == $value->getValue($this->get_app_type(), $this->get_locale()))
                )*/
            ) {
                $value = ($this->multi_select_noone_val
                    ? $this->multi_select_noone_val->getValue($this->get_app_type(), $this->get_locale())
                    : ""
                );
                $label = $this->multi_select_noone_label;
                $res[] = '<option' . $properties . $this->setActiveByTag($value, "selected") . '>' . $label . '</option>';
            }
            foreach ($this->recordset as $key => $item) {
                $value = $item[0]->getValue($this->get_app_type(), $this->get_locale());
                $label = $item[1]->getValue($this->get_app_type(), $this->get_locale());
                $res[] = '<option' . $properties . $this->setActiveByTag($value, "selected") . '>' . $label . '</option>';
            }
        }

        return implode("", $res);
    }

    private function processFile() {
        $properties                 = $this->getProperties();
        $control                    = '<input ' . $properties . '/>';

        return $control;
    }
    private function processRadio() {
        $res = array();

	    if(is_array($this->recordset) && count($this->recordset)) {
	        $control_id = $this->properties["id"];
	        foreach($this->recordset AS $i => $item) {

                $value = $item[0]->getValue($this->get_app_type(), $this->get_locale());
                $label = $item[1]->getValue();

                $this->properties["id"] = $control_id . "_" . $i;
                $this->properties["value"]  = $value;

                $properties                 = $this->getProperties();
                $res[]                      = $this->processContainer('<input ' . $properties . '/>', $this->processLabel($label, $this->properties["id"], true));
            }


            $this->framework_css = array_replace_recursive($this->framework_css, $this->framework_css["exception"]["radio-multi"]);
        }

        return implode("", $res);
    }

    private function processControlTag() {
	    $type                                   = $this->type;


        /**
         * Load data
         */
        $id                                     = $this->id;
        if ($this->parent !== null && strlen($this->parent[0]->getIDIF())) {
            $parent_id                          = $this->parent[0]->getPrefix();
        }

        $value_ori                              = (($this->contain_error && $this->error_preserve) || $this->preserve_ori_value
                                                    ? $this->value->ori_value
                                                    : $this->value->getValue($this->get_app_type(), $this->get_locale())
                                                );
        $value                                  = ffCommon_specialchars($value_ori);

            /**
         * Set properties control
         */
        $this->properties["id"]                 = $parent_id . $id;
        $this->properties["class"]              = $this->parent_page[0]->frameworkCSS->getClass($this->framework_css["field"]["control"]);

        if ($this->required) {
            $this->properties["required"]       = null;
        }

        switch($type) {
            /*case "image":
                break;
            case "button":
                break;
            case "hidden":
                break;
            case "reset":
                break;
            case "submit":
                break;*/
            case "file":
            case "file-thumb":
            case "file-custom":
                $this->properties["type"]       = "file";
                $this->properties["name"]       = $parent_id . $id;
                $this->properties["value"]      = $value;
                $control                        = $this->processFile();
                break;
            case "radio":
                $this->properties["type"]       = "radio";
                $this->properties["name"]       = $parent_id . $this->id;

                $control                        = $this->processRadio();
                if(!$control) {
                    $this->properties["value"]  = $this->checked_value->getValue($this->get_app_type(), $this->get_locale());

                    $properties                 = $this->getProperties();
                    $control                    = '<input ' . $properties . '/>';
                }
                break;
            case "select-multi":
                $this->properties["multi"]      = null;
            case "select":
            case "select-custom":
            $this->properties["name"]       = $parent_id . $id;
                $properties                     = $this->getProperties();
                $control                        = '<select ' . $properties . '>' . $this->processSelectOptions() . '</select>';
                break;
            case "textarea":
                $this->properties["name"]       = $parent_id . $id;
                $properties                     = $this->getProperties();

                $control                        = '<textarea ' . $properties . '>' . $value . '</textarea>';
                break;
            case "code":
                //$properties                     = $this->getProperties();

                $control                        = '<pre><code>' . str_replace(
                                                        array("\t", "  ")
                                                        , array("&nbsp;&nbsp;", "&nbsp;&nbsp;")
                                                        , nl2br(htmlspecialchars($value_ori))
                                                ) . '</code></pre>';
                break;
            case "empty":
                $control                        = $value_ori;
                break;
            case "label":
                $this->properties["type"]       = "text";
                $this->properties["readonly"]   = null;
                $this->properties["value"]      = $value;
                $properties                     = $this->getProperties();
                $control                        = '<input ' . $properties . '/>';
                break;
            case "link":
                break;
            case "picture":
                break;
            case "color":
                $this->properties["type"]       = $type;
                $this->properties["name"]       = $parent_id . $id;
                $this->properties["value"]      = ($value
                                                    ? $value
                                                    : "#000000"
                                                );

                $properties                     = $this->getProperties();
                $control                        = '<input ' . $properties . '/>';
                break;
            case "checkbox":
                $this->properties["type"]       = $type;
                $this->properties["name"]       = $parent_id . $id;
                $this->properties["value"]      = $this->checked_value->getValue($this->get_app_type(), $this->get_locale());

                $properties                     = $this->getProperties();
                $control                        = '<input ' . $properties . '/>';
                break;
            case "date":
            case "datetime-local":
            case "email":
            case "month":
            case "number":
            case "password":
            case "range":
            case "search":
            case "tel":
            case "time":
            case "url":
            case "week":
            case "text":
                $this->properties["type"]       = $type;
                $this->properties["name"]       = $parent_id . $id;
                $this->properties["value"]      = $value;

                $properties                     = $this->getProperties();
                $control                        = '<input ' . $properties . '/>';
                break;
            default:
        }

        return $control;
    }


    private function processLabel($label = null, $control_id = null, $skip_required = false) {
	    if(!$label)                             { $label = $this->label; }

        if($label) {
	        $label_properties                   = $this->label_properties;
            if ($this->required && $this->framework_css["required"] && !$skip_required) {
                $required_symbol                = $this->framework_css["required"];
            }

            if(!$control_id) {
                if ($this->parent !== null && strlen($this->parent[0]->getIDIF())) {
                    $parent_id                  = $this->parent[0]->getPrefix();
                }

                $control_id                     = $parent_id . $this->id;
            }

            if($this->framework_css["label-for"]) {
                $label_properties["for"]        = $control_id;
            }

            $res                                = $this->getWrapperByFrameworkCss("label"
                                                    , ($this->encode_label
                                                        ? ffCommon_specialchars($label) . $required_symbol
                                                        : $label . $required_symbol
                                                    )
                                                    , $this->framework_css["label-tag"]
                                                    , $this->getProperties($label_properties)
                                                );
            $res                                = $this->getWrapperByFrameworkCss("label-wrap", $res);
        }

        return $res;
    }
    private function processControlAddon($control = null) {
	    if(!$control)                           { $control = $this->processControlTag(); }
        $fixed_pre_content                      = $this->fixed_pre_content;
        $fixed_post_content                     = $this->fixed_post_content;
        if($fixed_pre_content || $fixed_post_content) {
            if ($this->framework_css["input-group"]) {
                if ($fixed_pre_content) {
                    if (preg_match("/<[^<]+>/", $fixed_pre_content, $m) == 0) {
                        $fixed_pre_content      = '<span class="' . $this->parent_page[0]->frameworkCSS->get("control-text", "form") . '">' . $fixed_pre_content . '</span>';
                    }
                    $fixed_pre_content          = '<div class="' . $this->parent_page[0]->frameworkCSS->get("control-prefix", "form") . '">' . $fixed_pre_content . '</div>';
                }

                if ($fixed_post_content) {
                    if (preg_match("/<[^<]+>/", $fixed_post_content, $m) == 0) {
                        $fixed_post_content     = '<span class="' . $this->parent_page[0]->frameworkCSS->get("control-text", "form") . '">' . $fixed_post_content . '</span>';
                    }
                    $fixed_post_content         = '<div class="' . $this->parent_page[0]->frameworkCSS->get("control-postfix", "form") . '">' . $fixed_post_content . '</div>';
                }

                $control                        = '<div class="' . $this->parent_page[0]->frameworkCSS->getClass($this->framework_css["input-group"]) . '">'
                                                    . $fixed_pre_content
                                                    . $control
                                                    . $fixed_post_content
                                                . '</div>';
            } else {
                $control                        = $fixed_pre_content . $control . $fixed_post_content;
            }
        }
        return $control;
    }

    private function processControl() {
	    $res                                    = $this->processControlAddon();

	    if(strpos($this->framework_css["field"]["prototype"], "[DESCRIPTION]") === false) {
	        $res .= $this->processDescription();
        }

        return $this->getWrapperByFrameworkCss("control-wrap", $res);
    }
    private function processDescription() {
        return ($this->description && $this->framework_css["description-tag"]
            ? '<' . $this->framework_css["description-tag"] . '>' . $this->description . '</' . $this->framework_css["description-tag"] . '>'
            : ''
        );
    }

    private function processContainer($control, $label, $description = null, $placeholder = null) {
        $res                                    = str_replace(
                                                    array(
                                                        "[LABEL]"
                                                        , "[CONTROL]"
                                                        , "[DESCRIPTION]"
                                                        , "[PLACEHOLDER]"
                                                    )
                                                    , array(
                                                        $label
                                                        , $control
                                                        , $description
                                                        , $placeholder
                                                    )
                                                    , $this->framework_css["field"]["prototype"]
                                                );

        return $this->getWrapperByFrameworkCss("container", $res);
    }

    private function getWrapperByFrameworkCss($name, $content = null, $tag = null, $properties = null) {
	    $class                                  = $this->parent_page[0]->frameworkCSS->getClass($this->framework_css["field"][$name]);
        if(!$tag && $class) {
            $tag = "div";
        }
        return ($content
            ? ($tag
                ? '<' . $tag . ($properties ? ' ' . $properties : '') . ($class ? ' class="' . $class . '"' : '') . '>' . $content . '</' . $tag . '>'
                : $content
            )
            : $class
        );
    }


    private function setType() {
        $type = null;
        switch($this->control_type) {
            case "label":
            case "textarea":
            case "checkbox":
            case "radio":
            case "file":
            case "picture":
                $type                       = $this->control_type;
                break;
            case "combo":
                $type                       = "select";
                break;
            case "list":
                $type                       = "select-multi";
                break;
            default:
        }

        if(!$type) {
            switch($this->extended_type) {
                case "Text":
                    $type                       = "textarea";
                    break;
                case "Password":
                    $type                       = "password";
                    break;
                case "Integer":
                    $type                       = "number";
                    break;
                case "Currency":
                case "Float":
                    $type                       = "number";
                    break;
                //               case "DateTime":

                case "Date":
                    $type                       = "date";
                    break;
//                case "Year":
//                case "Month":
//                case "Day":
//                case "Time":
//                case "Hours":
//                case "Minutes":
//                case "Seconds":
                case "Boolean":
                    $type                       = "checkbox";
                    break;
//                case "Flags":
                case "Selection":
                    $type                       = "select";
                    break;
                case "Email":
                    $type                       = "email";
                    break;
                case "Tel":
                    $type                       = "tel";
                    break;
                case "HTML":
                    $type                       = "empty";
                    break;
                case "File":
                    $type                       = "file";
                    break;
                default:
            }
        }
        if(!$type) {
            switch ($this->base_type) {
                case "Number":
                    $type                       = "number";
                    break;
//                case "DateTime":

                case "Date":
                    $type                       = "date";
                    break;
//                case "Time":

                case "Binary":
                    //                   break;
                case "Text":
                default:
                    $type                       = "text";
            }
        }

        $this->type = $type;
    }

    private function initType() {
        if(!$this->type)                        { $this->setType(); }

        switch($this->type) {
            case "file":
            case "file-custom":
                $this->control_type = false;
                break;
            case "radio":
                if(is_array($this->multi_pairs)) {
                    $this->extended_type = "Selection";
                    switch($this->multi_pairs[0][0]->data_type) {
                        case "Number":
                            $this->base_type = "Number";
                            break;
                        default:
                    };

                } elseif($this->base_type == "Text") {
                    $this->base_type = "Number";
                    $this->extended_type = "Boolean";
                    if(!$this->checked_value) {
                        $this->checked_value = new ffData("1", "Number");
                    }
                    if(!$this->unchecked_value) {
                        $this->unchecked_value = new ffData("0", "Number");
                    }
                }
                $this->control_type = false;
                break;
            case "select-multi":
                $this->extended_type = "Selection";
                $this->properties["multiple"] = null;
                $this->control_type = false;
                break;
            case "select":
            case "select-custom":
                $this->extended_type = "Selection";
                $this->control_type = false;
                break;
                break;
            case "textarea":
                $this->extended_type = "Text";
                $this->control_type = false;
                break;
            case "label":
                $this->control_type = false;
                break;
            case "readonly":
                $this->properties["readonly"] = null;
                $this->type = "text";
                $this->control_type = false;
                break;
            case "disabled":
                $this->properties["disabled"] = null;
                $this->type = "text";
                $this->control_type = false;
                break;
            case "color":
                $this->addValidator("htmlcolor");
                $this->control_type = false;
                break;
            case "checkbox":
                if($this->base_type == "Text") { //todo: da impostare il default a null
                    $this->base_type = "Number";
                    $this->extended_type = "Boolean";
                    if(!$this->checked_value) {
                        $this->checked_value = new ffData("1", "Number");
                    }
                    if(!$this->unchecked_value) {
                        $this->unchecked_value = new ffData("0", "Number");
                    }
                }
                $this->control_type = false;
                break;
            case "date":
                if($this->base_type == "Text") {
                    $this->base_type = "Date";
                    $this->addValidator("date");
                }
                $this->control_type = false;
                break;
            case "email":
                $this->addValidator("email");
                $this->control_type = false;
                break;
            case "month":
                if(0 && $this->base_type == "Text") {
                    $this->base_type = "Month"; //todo: da gestire
                }
                break;
            case "week":
                if(0 && $this->base_type == "Text") {
                    $this->base_type = "Week"; //todo: da gestire
                }
                $this->control_type = false;
                break;
            case "time":
                if($this->base_type == "Text") {
                    $this->base_type = "Time";
                    $this->addValidator("time");
                }
                $this->control_type = false;
                break;
            case "datetime":
                if($this->base_type == "Text") {
                    $this->base_type = "DateTime";
                    $this->addValidator("datetime");
                }
                $this->type = "datetime-local";
                $this->control_type = false;
                break;
            case "number":
                $this->base_type = "Number";
                $this->addValidator("number");
                $this->control_type = false;
                break;
            case "password":
                $this->extended_type = "Password";
                if(!$this->crypt_method) {
                    $this->crypt_method = "mysql_password";
                }
                $this->addValidator("password");
                $this->control_type = false;
                break;
            case "range":
                $this->control_type = false;
                break;
            case "search":
                $this->control_type = false;
                break;
            case "tel":
                $this->control_type = false;
                $this->addValidator("tel");
                break;
            case "url":
                $this->control_type = false;
                $this->addValidator("url");
                break;
            case "text":
                $this->control_type = false;
                break;

            case "html":
                $this->type = "empty";
                break;
            case "code":
                break;
            case "empty":
                break;
            case "link":
                break;
            case "picture":
                break;
            default:
        }
        //$this->type = $control_type;
        $this->framework_css["field"]       = ($this->framework_css["types"][$this->type]
                                                ? array_replace_recursive($this->framework_css["field"], $this->framework_css["types"][$this->type])
                                                : array_replace_recursive($this->framework_css["types"]["default"], (array) $this->framework_css["field"])
                                            );
        switch ($this->size) {
            case "small":
                $this->framework_css["field"]["control"]["form"] = array("control", "size-sm");
                break;
            case "large":
                $this->framework_css["field"]["control"]["form"] = array("control", "size-lg");
                break;
            default:
        }

        if($this->framework_css["user"]) {
            $this->framework_css            = array_replace_recursive($this->framework_css, $this->framework_css["user"]);
        }
    }
    function widget_process($id = null, $value = null)
    {
        $this->framework_css["field"]["container"]["class"] = $this->widget;

        return  parent::widget_process($id, $value);
	}


    function process($id = null, $value = null, $output_result = false) {
        $this->initType();

        $this->pre_process(false, $value);

        $buffer = ($this->widget
            ? $this->widget_process()
            : $this->parse()
        );

/*
        if (strlen($this->widget))
        {
            if (($buffer = $this->widget_process($id, $value)) !== null)
            {
                if ($output_result)
                {
                    echo $buffer;
                    return true;
                }
                else
                    return $buffer;
            }
        }*/





//        if($wrap_addon !== null && !$wrap_addon)
//			$buffer = '<div class="' . $this->parent_page[0]->frameworkCSS->get("group", "form") . '">' . $buffer . '</div>';


            ffPage::getInstance()->tplAddJs("ff.ffField");

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

    public function parse() {
        $control = $this->processControl();
        $label = ($this->display_label
            ? $this->processLabel()
            : ""
        );
        $description =  $this->processDescription();
        $placeholder = ($this->placeholder
            ? ($this->placeholder === true
                ? $this->label
                : $this->placeholder
            )
            : ""
        );
        $buffer = $this->processContainer($control, $label, $description, $placeholder);

        if($this->framework_css["outer_wrap"]) {
            $buffer = '<div class="' . $this->parent_page[0]->frameworkCSS->getClass($this->framework_css["outer_wrap"]) . '">'
                . $buffer
                . '</div>'
            ;
        }

        return $buffer;
    }

    function getProperties($property_set = null)
    {
        if ($property_set === null)
        {
            $property_set = $this->properties;

            if ($this->placeholder === true)
                $property_set["placeholder"] = ffCommon_specialchars($this->label);
            else if($this->placeholder)
                $property_set["placeholder"] = $this->placeholder;
        }

        return parent::getProperties($property_set);
    }

    function setWidthComponent($resolution_large_to_small)
    {
        if(is_array($resolution_large_to_small) || is_numeric($resolution_large_to_small)) {
            $this->framework_css["outer_wrap"]["col"] = frameworkCSS::setResolution($resolution_large_to_small);
        } elseif(strlen($resolution_large_to_small)) {
            $this->framework_css["outer_wrap"]["row"] = $resolution_large_to_small;
        }
    }

    function setWidthLabel($resolution_large_to_small, $reverse_control_class = true, $align = "right")
    {
        $this->framework_css["label-wrap"]["col"] = frameworkCSS::setResolution($resolution_large_to_small);
        if($align) {
            $this->framework_css["label-wrap"]["util"] = array(
                "align-" . $align
            );
        }
        if($reverse_control_class && is_array($this->framework_css["label-wrap"]["col"])) {
            $this->framework_css["control-wrap"]["col"] = frameworkCSS::setResolution($this->framework_css["label-wrap"]["col"], true);

            /*			$this->framework_css["control"]["col"] = array(
                            "xs" => ($this->framework_css["label"]["col"]["xs"] == 12 ? 12 : 12 - $this->framework_css["label"]["col"]["xs"])
                            , "sm" => ($this->framework_css["label"]["col"]["sm"] == 12 ? 12 : 12 - $this->framework_css["label"]["col"]["sm"])
                            , "md" => ($this->framework_css["label"]["col"]["md"] == 12 ? 12 : 12 - $this->framework_css["label"]["col"]["md"])
                            , "lg" => ($this->framework_css["label"]["col"]["lg"] == 12 ? 12 : 12 - $this->framework_css["label"]["col"]["lg"])
                        );
            */
        }
    }

    function setWidthControl($resolution_large_to_small)
    {
        $this->framework_css["control-wrap"]["col"] = frameworkCSS::setResolution($resolution_large_to_small);
    }

    function setLabelProperties($properties)
    {
        if(isset($properties["col"]))
            $properties["col"] = frameworkCSS::setResolution($properties["col"]);

        $this->framework_css["label"] = array_replace($this->framework_css["label"], $properties);
    }

    function setControlProperties($properties)
    {
        if(isset($properties["col"])) {
            $properties["col"] = frameworkCSS::setResolution($properties["col"]);
        }

        $this->framework_css["control"] = array_replace($this->framework_css["control"], $properties);
    }


    ////////////////////////////////////////////////////////////////////**/

    function getTemplateDir($control_type)
    {
        $res = $this->doEvent("getTemplateDir", array($this, $control_type));
        $last_res = end($res);
        if ($last_res === null)
        {
            if ($this->template_dir === null)
                return $this->disk_path . "/themes/" . $this->getTheme() . "/ff/ffField";
            else
                return $this->template_dir;
        }
        else
        {
            return $last_res;
        }
    }
    function process_label($id, &$value)
    {
        $this->tpl[0]->set_var("id", $id);
        if($this->encode_entities)
            $this->tpl[0]->set_var("value", ffCommon_specialchars($value->getValue($this->get_app_type(), $this->get_locale())));
        else
            $this->tpl[0]->set_var("value", $value->getValue($this->get_app_type(), $this->get_locale()));

        $this->tpl[0]->set_var("label", $this->getDisplayValue(null, null, $value));

        if ($this->parent !== null && is_subclass_of($this->parent[0], "ffGrid_base") && !$this->parent[0]->force_no_field_params)
            $this->tpl[0]->parse("SectFieldValue", false);
        else
            $this->tpl[0]->set_var("SectFieldValue", "");
    }

    function process_input($id, &$value)
    {
        $this->tpl[0]->set_var("id", $id);

        if (is_array($this->recordset) && count($this->recordset))
        {
            foreach ($this->recordset as $key => $item)
            {
                list($tmp, $item_key) = each($item);
                list($tmp, $item_value) = each($item);

                if ($item_key->getValue($this->get_app_type(), $this->get_locale()) == $value->getValue($this->get_app_type(), $this->get_locale()))
                    $this->tpl[0]->set_var("value", ffCommon_specialchars($item_value->getValue($this->get_multi_app_type(), $this->get_locale())));
            }
            reset($this->recordset);
        }
        else
        {
            if (($this->contain_error && $this->error_preserve) || $this->preserve_ori_value)
                $this->tpl[0]->set_var("value", ffCommon_specialchars($value->ori_value));
            else
                $this->tpl[0]->set_var("value", ffCommon_specialchars($value->getValue($this->get_app_type(), $this->get_locale())));
        }
    }


    function process_textarea($id, &$value)
    {
        $this->process_input($id, $value);

        if($this->parent_page[0] && (!$this->properties || !$this->properties["readonly"])) {
            $this->parent_page[0]->tplAddJs("jquery.plugins.autogrow-textarea"); //todo: da fare meglio
            $this->parent_page[0]->tplAddJs("ff.ffField.autogrow-" . $this->id, array(
                "embed" => 'ff.pluginAddInit("jquery.plugins.autogrow-textarea", function() {
                    var controlId = "' . $this->parent[0]->getPrefix() . $this->id . '";
                    jQuery("#" + controlId.escapeRegExp()).autogrow();
                    });'
            ));
        }
    }

    function process_file($id, &$value)
    {
        $this->tpl[0]->set_var("butt_del_class", $this->parent_page[0]->frameworkCSS->get($this->buttons_options["file"]["delete"]["class"], "icon"));
        $this->tpl[0]->set_var("butt_del_label", $this->buttons_options["file"]["delete"]["label"]);

        $this->tpl[0]->set_var("butt_edit_class", $this->parent_page[0]->frameworkCSS->get($this->buttons_options["file"]["edit"]["class"], "icon"));
        $this->tpl[0]->set_var("butt_edit_label", $this->buttons_options["file"]["edit"]["label"]);

        $this->tpl[0]->set_var("noimg_class", " " . $this->parent_page[0]->frameworkCSS->get("noimg", "icon"));

        $this->process_file_base($id, $value);
    }


    /**
     * Esegue il process del Field nel caso si tratti di un checkbox
     * @param String $id ID del Field
     * @param ffData $value Valore del Field
     */
    function process_checkbox($id, &$value)
    {
        if (!is_object($this->checked_value) || get_class($this->checked_value) != "ffData"
            || !is_object($this->unchecked_value) || get_class($this->unchecked_value) != "ffData")
            ffErrorHandler::raise("checked_value and unchecked_value must be defined for checkboxes.", E_USER_ERROR, $this, get_defined_vars());

        $this->tpl[0]->set_var("id", ffCommon_specialchars($id));
        $this->tpl[0]->set_var("value", ffCommon_specialchars($this->checked_value->getValue($this->get_app_type(), $this->get_locale())));

        if ($value->getValue($this->get_app_type(), $this->get_locale()) == $this->checked_value->getValue($this->get_app_type(), $this->get_locale()))
            $this->tpl[0]->set_var("Checked", "checked=\"checked\"");
        else
            $this->tpl[0]->set_var("Checked", "");
    }

    /**
     * Esegue il process del Field nel caso si tratti di un combo
     * @param String $id ID del Field
     * @param ffData $value Valore del Field
     */
    function process_combo($id, &$value)
    {
        $this->tpl[0]->set_var("id", ffCommon_specialchars($id));

        if ($this->multi_select_one && !$this->multi_limit_select)
        {
            if ($this->multi_select_one_val !== null)
                $this->tpl[0]->set_var("select_one_value", $this->multi_select_one_val->getValue($this->get_app_type(), $this->get_locale()));
            else
                $this->tpl[0]->set_var("select_one_value", "");
            $this->tpl[0]->set_var("select_one_label", $this->multi_select_one_label);
            $this->tpl[0]->parse("SectSelectOne", false);
        }
        else
            $this->tpl[0]->set_var("SectSelectOne", "");

        if ($this->multi_select_noone &&
            (!$this->multi_limit_select ||
                ($this->multi_limit_select && $this->multi_select_noone_val->getValue($this->get_app_type(), $this->get_locale()) == $value->getValue($this->get_app_type(), $this->get_locale()))
            )
        )
        {
            if ($this->multi_select_noone_val === null)
                ffErrorHandler::raise("u must enter a select noone value", E_USER_ERROR, $this, get_defined_vars());

            $this->tpl[0]->set_var("select_noone_value", $this->multi_select_noone_val->getValue($this->get_app_type(), $this->get_locale()));
            $this->tpl[0]->set_var("select_noone_label", $this->multi_select_noone_label);
            if ($value->ori_value !== "" && $this->multi_select_noone_val->getValue($this->get_app_type(), $this->get_locale()) === $value->getValue($this->get_app_type(), $this->get_locale()))
                $this->tpl[0]->set_var("Selected", "selected=\"selected\"");
            else
                $this->tpl[0]->set_var("Selected", "");
            $this->tpl[0]->parse("SectSelectNoOne", false);
        }
        else
            $this->tpl[0]->set_var("SectSelectNoOne", "");

        if (is_array($this->recordset) && count($this->recordset))
        {
            foreach ($this->recordset as $key => $item)
            {
                list($tmp, $item_key) = each($item);
                list($tmp, $item_value) = each($item);

                $this->tpl[0]->set_var("Value", ffCommon_specialchars($item_key->getValue($this->get_app_type(), $this->get_locale())));
                $this->tpl[0]->set_var("Label", ffCommon_specialchars($item_value->getValue($this->get_multi_app_type(), $this->get_locale())));
//                                if ($this->id == "attivo")
//                                    ffErrorHandler::raise("DEBUG IN CORSO", E_USER_ERROR, $this, get_defined_vars());
                if ($value->ori_value !== "" && $item_key->getValue($this->get_app_type(), $this->get_locale()) === $value->getValue($this->get_app_type(), $this->get_locale()))
                {
                    $this->tpl[0]->set_var("Selected", "selected=\"selected\"");
                    $this->tpl[0]->parse("SectRow", true);
                }
                else
                {
                    $this->tpl[0]->set_var("Selected", "");
                    if (!$this->multi_limit_select)
                        $this->tpl[0]->parse("SectRow", true);
                }
            }
            reset($this->recordset);
        }
        else
        {
            $this->tpl[0]->set_var("SectRow", "");
        }
    }

    function process_file_base($id, &$value)
    {
        static $loaded_dialog_fields = null;

        $this->tpl[0]->set_var("id", ffCommon_specialchars($id));

        $suffix_file 		= "file";
        $suffix_name 		= "name";
        $suffix_tmpname 	= "tmpname";
        $suffix_delete 		= "delete";

        if (substr($id, -1) == "]")
        {
            $suffix_file 		= ""; //"[" . $suffix_file . "]";
            $suffix_name 		= "[" . $suffix_name . "]";
            $suffix_tmpname 	= "[" . $suffix_tmpname . "]";
            $suffix_delete 		= "[" . $suffix_delete . "]";
        }
        else
        {
            $suffix_file 		= "_" . $suffix_file;
            $suffix_name 		= ""; //"_" . $suffix_name;
            $suffix_tmpname 	= "_" . $suffix_tmpname;
            $suffix_delete 		= "_" . $suffix_delete;
        }

        $file_thumb = (is_array($this->file_thumb)
            ? implode("x", $this->file_thumb)
            : (strlen($this->file_thumb)
                ? $this->file_thumb
                : ""
            )
        );

        $this->tpl[0]->set_var("theme", $this->parent_page[0]->theme);
        $this->tpl[0]->set_var("suffix_file", $suffix_file);
        $this->tpl[0]->set_var("suffix_name", $suffix_name);
        $this->tpl[0]->set_var("suffix_tmpname", $suffix_tmpname);
        $this->tpl[0]->set_var("suffix_delete", $suffix_delete);

        $this->tpl[0]->set_var("filename", ffCommon_specialchars($value->getValue($this->get_app_type(), $this->get_locale())));
        $this->tpl[0]->set_var("filename_normalized", preg_replace('/[^a-zA-Z0-9]/', '', basename($value->getValue($this->get_app_type(), $this->get_locale()))));
        $this->tpl[0]->set_var("tmpname", ffCommon_specialchars($this->file_tmpname));
        $this->tpl[0]->set_var("encoded_filename", urlencode($value->getValue($this->get_app_type(), $this->get_locale())));

        if($this->file_show_edit) {
            $file_modify_path = ffMedia::MODIFY_PATH . "?key=" . $this->file_modify_referer . "&path=";

            if($this->file_modify_dialog === true) {
                $this->file_modify_dialog = $this->id . "_media";

                if(!$loaded_dialog_fields[$this->file_modify_dialog]) {
                    $params = array(
                        "title" 				=> ffTemplate::_get_word_by_code("ffField_modify") . " " . $this->label
                    , "class" 				=> null
                    , "width" 				=> null
                    , "height" 				=> null
                    , "type" 				=> null
                    );


                    $this->parent_page[0]->widgetLoad("dialog");
                    $this->parent_page[0]->widgets["dialog"]->process(
                        $this->file_modify_dialog
                        , array(
                            "title"          	=> $params["title"]
                        , "tpl_id"        	=> null
                        , "width"        	=> $params["width"]
                        , "height"        	=> $params["height"]
                        , "dialogClass"     => $params["class"]
                        , "type"			=> $params["type"]
                        )
                        , $this->parent_page[0]
                    );
                }
            }
        }


        if (strlen($this->file_tmpname))
        {
            $filename = $this->file_tmpname;
            if(substr($filename, 0,1) == "/")
                $this->file_full_path = true;

            if ($this->file_full_path)
            {
                $tmp_path = str_replace($this->getFileBasePath(), "", $this->file_temp_path);
                $filename = $tmp_path . "/" . basename($filename);
                if (
                    substr(strtolower($filename), 0, 7) == "http://"
                    || substr(strtolower($filename), 0, 8) == "https://"
                    || substr($filename, 0, 2) == "//"
                ) {
                    $this->file_check_exist = false;
                    $this->file_show_filesize = false;
                    $is_local = false;
                } else {
                    $is_local = true;
                }
            }
            else
                $is_local = true;

            $view_url               = ($this->file_temp_view_url
                ? $this->file_temp_view_url
                : ($is_local
                    ? ""
                    : ""
                ) . $filename
            );
            $view_query_string      = ($this->file_temp_view_query_string ? $this->file_temp_view_query_string :
                ($this->file_saved_view_query_string ? $this->file_saved_view_query_string : $this->file_query_string)
            );

            $preview_url = ($this->file_temp_preview_url
                ? $this->file_temp_preview_url
                : ($is_local
                    ? str_replace($this->getFileBasePath(), "", $this->file_temp_path)
                    : $filename
                )
            );
            $preview_query_string   = ($this->file_temp_preview_query_string ? $this->file_temp_preview_query_string :
                ($this->file_saved_preview_query_string ? $this->file_saved_preview_query_string : $this->file_query_string)
            );

//SBAGLIATO DA SISTEMARE

            $is_tmpfile = true;
            $mode = "normal";
        }
        else
        {
            $storing_path = null;
            $filename = $value->getValue();
            if($this->file_multi) {
                $arrFilename = explode($this->file_separator, $filename);
                if(count($arrFilename))
                    $filename = $arrFilename[0];
            }

            if(substr($filename, 0,1) == "/")
                $this->file_full_path = true;


            if ($this->file_full_path)
            {
                if (
                    substr(strtolower($filename), 0, 7) == "http://"
                    || substr(strtolower($filename), 0, 8) == "https://"
                    || substr($filename, 0, 2) == "//"
                ) {
                    $this->file_check_exist = false;
                    $this->file_show_filesize = false;
                    $is_local = false;
                } else {
                    if(strlen($filename))
                        $storing_path = $this->getFileBasePath() . ffCommon_dirname($filename);

                    $is_local = true;
                }
            }
            else
                $is_local = true;

            if(!$storing_path)
                $storing_path = $this->file_storing_path;

            $view_url = ($this->file_saved_view_url
                ? $this->file_saved_view_url
                : ($is_local
                    ? str_replace($this->getFileBasePath(), "", $storing_path)
                    : $storing_path
                ) . "/[_FILENAME_]"
            );
            $view_query_string		= ($this->file_saved_view_query_string ? $this->file_saved_view_query_string : $this->file_query_string);
            $preview_url = ($this->file_saved_preview_url
                ? $this->file_saved_preview_url
                : ($is_local
                    ? str_replace($this->getFileBasePath(), "", $storing_path)
                    : $storing_path
                ) . "/[_FILENAME_]"
            );
            $preview_query_string	= ($this->file_saved_preview_query_string ? $this->file_saved_preview_query_string : $this->file_query_string);


            $is_tmpfile = false;
            $mode = "ori";
        }

        if($is_local)
        {
            $view_url = ffProcessTags($view_url, $this->getKeysArray(), $this->getDataArray(), $mode);
            // $view_url = str_replace("[_FILENAME_]", ($this->file_full_path ? ltrim($filename, "/") : $filename), $view_url);

            //$view_url = ffProcessTags($view_url, $this->getKeysArray(), $this->getDataArray(), $mode);
            //$view_url = str_replace("//", "/0/", $view_url); //procedura per fixare il bug nel ffprocesstag che con un valore di tipo numerico ritorna "" al posto di 0
            $view_query_string = ffProcessTags($view_query_string, $this->getKeysArray(), $this->getDataArray(), $mode);
            $preview_url = ffProcessTags($preview_url, $this->getKeysArray(), $this->getDataArray(), $mode);

            // $preview_url = str_replace("[_FILENAME_]", ($this->file_full_path ? ltrim($filename, "/") : $filename), $preview_url);
            //$preview_url = ffProcessTags($preview_url, $this->getKeysArray(), $this->getDataArray(), $mode);
            //$preview_url = str_replace("//", "/0/", $preview_url);  //procedura per fixare il bug nel ffprocesstag che con un valore di tipo numerico ritorna "" al posto di 0
            $preview_query_string = ffProcessTags($preview_query_string, $this->getKeysArray(), $this->getDataArray(), $mode);
        }
        else
        {
            $view_url = $filename;
            $preview_url = $filename;
        }

        if (strlen($view_query_string) && strpos($view_query_string, "?") !== 0)
            $view_query_string = "?" . $view_query_string;
        if (strlen($preview_query_string) && strpos($preview_query_string, "?") !== 0)
            $preview_query_string = "?" . $preview_query_string;

        /*	if (strlen($this->file_tmpname))
            {
                $this->file_temp_view_url_tmp = $view_url;
                $this->file_temp_view_query_string_tmp = $view_query_string;

                $this->file_temp_preview_url_tmp = $preview_url;
                $this->file_temp_preview_query_string_tmp = $preview_query_string;
            }
            else
            {
                $this->file_saved_view_url_tmp	= $view_url;
                $this->file_saved_view_query_string_tmp = $view_query_string;

                $this->file_saved_preview_url_tmp = $preview_url;
                $this->file_saved_preview_query_string_tmp = $preview_query_string;
            }*/
        if (count($this->parent) && is_subclass_of($this->parent[0], "ffDetails_base"))
        {
            foreach ($this->parent[0]->fields_relationship as $el_key => $el_value)
            {
                $view_url = str_replace("[" . $el_value . "_FATHER]", $this->parent[0]->main_record[0]->key_fields[$el_value]->value->getValue($this->parent[0]->main_record[0]->key_fields[$el_value]->base_type, FF_SYSTEM_LOCALE), $view_url);
                $preview_url = str_replace("[" . $el_value . "_FATHER]", $this->parent[0]->main_record[0]->key_fields[$el_value]->value->getValue($this->parent[0]->main_record[0]->key_fields[$el_value]->base_type, FF_SYSTEM_LOCALE), $preview_url);
            }
            reset ($this->parent[0]->fields_relationship);

            foreach ($this->parent[0]->main_record[0]->form_fields as $el_key => $el_value)
            {
                if ($this->parent[0]->main_record[0]->form_fields[$el_key]->multi_fields === null)
                {
                    $view_url = str_replace("[" . $el_key . "_FATHER]", $this->parent[0]->main_record[0]->form_fields[$el_key]->value->getValue($this->parent[0]->main_record[0]->form_fields[$el_key]->base_type, FF_SYSTEM_LOCALE), $view_url);
                    $preview_url = str_replace("[" . $el_key . "_FATHER]", $this->parent[0]->main_record[0]->form_fields[$el_key]->value->getValue($this->parent[0]->main_record[0]->form_fields[$el_key]->base_type, FF_SYSTEM_LOCALE), $preview_url);
                }
            }
            reset ($this->parent[0]->main_record[0]->form_fields);
        }

        if (strlen($value->getValue()))
        {
            $arrFileValue = explode($this->file_separator, $value->getValue());
            if($this->file_show_preview && is_array($arrFileValue) && count($arrFileValue))	{
                $preview_class = "uploaded-preview";

                foreach($arrFileValue AS $file_key => $file_value) {
                    if(!$is_tmpfile && $this->file_full_path) {
                        $file_full_path = $this->getFileBasePath() . "/" . ltrim($file_value, "/");
                        $real_file_value = basename($file_value);
                    } else {
                        if($this->file_storing_path === null) {
                            $file_full_path = $this->getFileBasePath() . "/" . ltrim($file_value, "/");
                            $real_file_value = ltrim($file_value, "/");
                        } else {
                            $file_full_path = $this->getFileFullPath(basename($file_value), $is_tmpfile);
                            $real_file_value = basename($file_value);
                        }
                    }
                    $this->tpl[0]->set_var("show_name", ffCommon_specialchars(basename($file_value)));
                    $this->tpl[0]->set_var("filevalue", ffCommon_specialchars(basename($file_value)));
                    $this->tpl[0]->set_var("filevalue_normalized", preg_replace('/[^a-zA-Z0-9]/', '', basename($file_value)));
                    $this->tpl[0]->set_var("filename_full", ffCommon_specialchars($file_value));
                    if($this->file_full_path)
                        $filename_base = preg_replace('/[^a-zA-Z0-9]/', '', basename($file_value));
                    else
                        $filename_base = preg_replace('/[^a-zA-Z0-9]/', '', $file_value);

                    if($this->widget) {
                        $this->tpl[0]->set_var("filename_base", $filename_base);
                        $this->tpl[0]->set_var("widget_name", $this->widget);
                        $this->tpl[0]->set_var("SectBaseDel", "");
                        $this->tpl[0]->parse("SectWidgetDel", false);
                    } else {
                        $this->tpl[0]->parse("SectBaseDel", false);
                        $this->tpl[0]->set_var("SectWidgetDel", "");
                    }

                    $check_file = true;

                    if($is_local && $this->file_check_exist && !@is_file($file_full_path))
                        $check_file = false;

                    if ($this->file_show_delete) {
                        $this->tpl[0]->parse("ShowDelete", false);
                    } else {
                        $this->tpl[0]->set_var("ShowDelete", "");
                    }

                    if($check_file) {
                        $processed_view_url = ffCommon_specialchars(str_replace("[_FILENAME_]", $real_file_value, $view_url));
                        if($file_modify_path) {
                            $file_path = substr($file_full_path, strlen($this->getFileBasePath()));
                            if($this->file_modify_dialog) {
                                $this->tpl[0]->set_var("ajax_view_url", " onclick=\"ff.ffPage.dialog.doOpen('" . $this->file_modify_dialog ."', '" . ffCommon_specialchars(FF_SITE_PATH . $file_modify_path . $file_path) . "');\"");
                                $this->tpl[0]->set_var("view_url", "javascript:void(0);");
                            } else {
                                $this->tpl[0]->set_var("ajax_view_url", "");
                                $this->tpl[0]->set_var("view_url", ffCommon_specialchars($file_modify_path . $file_path));
                            }
                        } else {
                            $this->tpl[0]->set_var("ajax_view_url", "");
                            $this->tpl[0]->set_var("view_url", ffMedia::getUrl($processed_view_url, null, "url"));
                        }
                        $this->tpl[0]->set_var("preview_url", ffMedia::getUrl(ffCommon_specialchars(str_replace("[_FILENAME_]", $real_file_value, $preview_url)), $file_thumb, "url"));

                        /*
                        if($is_tmpfile)
                            $this->tpl[0]->set_var("show_file", ffCommon_dirname(str_replace(ffCommon_dirname($file_value), "", $preview_url)));
                        else
                            $this->tpl[0]->set_var("show_file", ffCommon_dirname(str_replace(ffCommon_dirname($file_value), "", $preview_url)));
                        */
                        $this->tpl[0]->set_var("view_query_string", $view_query_string);
                        $this->tpl[0]->set_var("preview_query_string", $preview_query_string);

                        $this->tpl[0]->set_var("ShowFilesize", "");
                        if($this->file_show_filesize) {
                            $file_size = @filesize($check_file);
                            if($file_size) {
                                $this->tpl[0]->set_var("show_size", $this->get_literal_size($file_size));
                                $this->tpl[0]->parse("ShowFilesize", false);
                            }
                        }

                        $this->tpl[0]->set_var("SectWritable", "");
                        if($this->file_writable)
                            $this->tpl[0]->parse("SectWritable", false);

                        if ($this->file_show_edit && is_array($this->file_edit_params[$this->file_edit_type])) {
                            if(!$this->file_edit_params[$this->file_edit_type]["key"]) {
                                $cache = ffCache::getInstance();
                                $this->file_edit_params[$this->file_edit_type]["key"] = $cache->get($this->file_edit_type . "/key");
                            }
                            if($this->file_edit_params[$this->file_edit_type]["key"]) {
                                $tmp = md5($id . "-" . $file_full_path);
                                $ff = get_session("ff");
                                $ff["aviary"][$tmp]["folder"] = str_replace($this->getFileBasePath(), "", ffCommon_dirname($file_full_path));
                                $ff["aviary"][$tmp]["base_path"] = $this->getFileBasePath();
                                set_session("ff", $ff);


                                foreach($this->file_edit_params[$this->file_edit_type] AS $params_key => $params_value) {
                                    $this->tpl[0]->set_var(strtolower($this->file_edit_type) . "_" . $params_key, $params_value);
                                }

                                $this->tpl[0]->set_var(strtolower($this->file_edit_type) . "_url", $file_value);
                                $this->tpl[0]->set_var(strtolower($this->file_edit_type) . "_hash_img", $tmp);
                                $this->tpl[0]->parse("SezEdit" . $this->file_edit_type, false);
                            }
                        }

                        $this->tpl[0]->set_var("showfile_plugin", ($this->file_showfile_plugin
                            ? $this->file_showfile_plugin
                            : "origin-file"
                        ));


                        $this->tpl[0]->set_var("filename_base", $filename_base);
                        $this->tpl[0]->parse("ShowPreviewImg", false);
                        $this->tpl[0]->set_var("ShowPreviewNoImg", "");
                    } else {
                        $this->tpl[0]->set_var("filename_base", $filename_base . " noimg");
                        $this->tpl[0]->set_var("ShowPreviewImg", "");
                        $this->tpl[0]->parse("ShowPreviewNoImg", false);
                    }
                    $this->tpl[0]->parse("ShowPreview", true);
                }

                if ($this->file_show_link) {
                    $this->tpl[0]->parse("ShowLink", false);
                }

                $this->tpl[0]->set_var("preview_class", $preview_class);
                $this->tpl[0]->parse("SectAddon", false);
            } else {
                $this->tpl[0]->set_var("SectAddon", "");
            }
            //$this->file_show_filename
        }
        else
        {
            $this->tpl[0]->set_var("SectAddon", "");
        }

        if($this->file_show_control ) {
            $this->tpl[0]->parse("SectControl", false);
        } else {
            $this->tpl[0]->set_var("SectControl", "");
        }

    }

    function process_picture($id, &$value)
    {
        $this->tpl[0]->set_var("id", ffCommon_specialchars($id));

        $suffix_file 		= "file";
        $suffix_name 		= "name";
        $suffix_tmpname 	= "tmpname";
        $suffix_delete 		= "delete";

        if (substr($id, -1) == "]")
        {
            $suffix_file 		= ""; //"[" . $suffix_file . "]";
            $suffix_name 		= "[" . $suffix_name . "]";
            $suffix_tmpname 	= "[" . $suffix_tmpname . "]";
            $suffix_delete 		= "[" . $suffix_delete . "]";
        }
        else
        {
            $suffix_file 		= "_" . $suffix_file;
            $suffix_name 		= ""; //"_" . $suffix_name;
            $suffix_tmpname 	= "_" . $suffix_tmpname;
            $suffix_delete 		= "_" . $suffix_delete;
        }

        $this->tpl[0]->set_var("suffix_file", $suffix_file);
        $this->tpl[0]->set_var("suffix_name", $suffix_name);
        $this->tpl[0]->set_var("suffix_tmpname", $suffix_tmpname);
        $this->tpl[0]->set_var("suffix_delete", $suffix_delete);

        $this->tpl[0]->set_var("filename", $value->getValue($this->get_app_type(), $this->get_locale()));
        $this->tpl[0]->set_var("tmpname", $this->file_tmpname);
        $this->tpl[0]->set_var("encoded_filename", urlencode($value->getValue($this->get_app_type(), $this->get_locale())));

        $query_string = ffProcessTags($this->file_query_string, $this->getKeysArray(), $this->getDataArray(), "normal");
        $this->tpl[0]->set_var("query_string", $query_string);

        if (strlen($this->file_view_query_string))
        {
            $view_query_string = ffProcessTags($this->file_view_query_string, $this->getKeysArray(), $this->getDataArray(), "normal");
            $this->tpl[0]->set_var("view_query_string", $view_query_string);
        }
        else
        {
            $this->tpl[0]->set_var("view_query_string", $query_string);
        }

        $this->tpl[0]->set_var("show_path", $this->file_show_path);
        if (strlen($this->file_tmpname))
        {
            $this->tpl[0]->set_var("show_name", $this->file_tmpname);
            $this->tpl[0]->set_var("encoded_show_name", urlencode($this->file_tmpname));
            $this->tpl[0]->set_var("show_temp", 1);
        }
        else
        {
            $this->tpl[0]->set_var("show_name", $value->getValue($this->get_app_type(), $this->get_locale()));
            $this->tpl[0]->set_var("encoded_show_name", urlencode($value->getValue($this->get_app_type(), $this->get_locale())));
            $this->tpl[0]->set_var("show_temp", 0);
        }
    }

    /**
     * Esegue il process del Field nel caso si tratti di un radio
     * @param String $id ID del campo
     * @param ffData $value Valore del campo
     */
    function process_radio($id, &$value)
    {
        $this->tpl[0]->set_var("id", ffCommon_specialchars($id));

        if (is_array($this->recordset) && count($this->recordset))
        {
            foreach ($this->recordset as $key => $item)
            {
                list($tmp, $item_key) = each($item);
                list($tmp, $item_value) = each($item);

                $this->tpl[0]->set_var("Value", ffCommon_specialchars($item_key->getValue($this->get_app_type(), $this->get_locale())));
                if($this->encode_entities)
                    $this->tpl[0]->set_var("Label", ffCommon_specialchars($item_value->getValue($this->get_multi_app_type(), $this->get_locale())));
                else
                    $this->tpl[0]->set_var("Label", $item_value->getValue($this->get_multi_app_type(), $this->get_locale()));

                if ($item_key->getValue($this->base_type, FF_SYSTEM_LOCALE) == $value->getValue($this->base_type, FF_SYSTEM_LOCALE) || $this->default_selected == ($key + 1))
                    $this->tpl[0]->set_var("Checked", "checked");
                else
                    $this->tpl[0]->set_var("Checked", "");

                $this->tpl[0]->set_var("properties", $this->getProperties());

                if ($this->radio_display_label)
                {
                    $this->tpl[0]->parse("LabelPre", false);
                    $this->tpl[0]->parse("LabelPost", false);
                }
                else
                {
                    $this->tpl[0]->set_var("LabelPre", "");
                    $this->tpl[0]->set_var("LabelPost", "");
                }

                if ($this->radio_hyphen)
                {
                    $this->tpl[0]->parse("HyphenPre", false);
                    $this->tpl[0]->parse("HyphenPost", false);
                }
                else
                {
                    $this->tpl[0]->set_var("HyphenPre", "");
                    $this->tpl[0]->set_var("HyphenPost", "");
                }
                $this->tpl[0]->set_var("count", "_" . $key);
                $this->tpl[0]->parse("SectRow", true);
            }
        }
        else
        {
            $this->tpl[0]->set_var("SectRow", "");
        }
    }


    function process_old($id = null, $value = null, $output_result = false, $control_type = null)
    {
        $this->pre_process(false, $value);

        if ($id === null)
            $id = $this->id;

        if ($value === null)
            $value = $this->value;

        if ($control_type === null)
            $control_type = $this->get_control_type();

        $this->tplLoad($control_type);

        if (strlen($this->widget))
        {
            if (($buffer = $this->widget_process($id, $value)) !== null)
            {
                if ($output_result)
                {
                    echo $buffer;
                    return true;
                }
                else
                    return $buffer;
            }
        }

        switch(strtolower($control_type))
        {
            case "radio":
                $this->process_radio($id, $value);
                break;

            case "combo":
                $this->process_combo($id, $value);
                break;

            default:
            case "email":
            case "label":
                $this->process_label($id, $value);
                break;
            case "textarea":
                $this->process_textarea($id, $value);
                break;
            case "html":
            case "date":
            case "time":
            case "input":
            case "password":
                $this->process_input($id, $value);
                break;

            case "checkbox":
                $this->process_checkbox($id, $value);
                break;

            case "picture":
            case "picture_no_link":
                //$this->process_picture($id, $value);
                //break;
            case "file_label":
            case "file":
                $this->process_file($id, $value);
                break;
        }

        return $this->tplParse($output_result);
    }

	/**
	 * carica l'oggetto template dentro $tpl
	 */
	public function tplLoad($control_type)
	{


	    $type                               = ($this->type
                                                ? $this->type
                                                : $control_type
                                            );
        $this->framework_css["field"]       = ($this->framework_css["types"][$type]
                                                    ? array_replace_recursive($this->framework_css["field"], $this->framework_css["types"][$type])
                                                    : array_replace_recursive($this->framework_css["types"]["default"], (array) $this->framework_css["field"])
                                                );

        if($this->framework_css["user"]) {
            $this->framework_css            = array_replace_recursive($this->framework_css, $this->framework_css["user"]);
        }

        $this->tpl[0]                           = $this->parent_page[0]->loadTemplate(pathinfo($this->getTemplateFile($control_type), PATHINFO_FILENAME));
        //$this->tpl[0] = ffTemplate::factory($this->getTemplateDir($control_type));
		//$this->tpl[0]->load_file($this->getTemplateFile($control_type), "main");

		if ($this->parent !== null && strlen($this->parent[0]->getIDIF()))
		{
			if (!$this->omit_parent_id) {
                $this->tpl[0]->set_var("container", $this->parent[0]->getPrefix());
            }

		}

		if($this->parent_page !== NULL) {
			$this->tpl[0]->set_var("keys", $this->parent_page[0]->get_params("", "", false));
			$this->tpl[0]->set_var("query_string", $this->parent_page[0]->get_script_params());
		}

		$this->tpl[0]->set_var("site_path", ffCommon_specialchars($this->site_path));
		$this->tpl[0]->set_var("page_path", ffCommon_specialchars($this->page_path));

		//if (!strlen($this->widget))
			$this->tpl[0]->set_var("class", ffCommon_specialchars($this->get_control_class($control_type)));

        if (is_array($this->fixed_vars) && count($this->fixed_vars))
        {
            foreach ($this->fixed_vars as $key => $value)
            {
                $this->tpl[0]->set_var($key, $value);
            }
            reset($this->fixed_vars);
        }

		$res = $this->doEvent("on_tpl_load", array(&$this));
	}

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
    function get_control_class($control_type = null, $params = false)
    {
        $arrClass = array();


        $arrClass[] = $this->parent_page[0]->frameworkCSS->getClass($this->framework_css["field"]["control"]);

        if (strlen($this->class))
            $arrClass[] = $this->class;
        else if(strlen($this->widget))
            $arrClass[] = $this->widget;
        elseif(!isset($params) || !isset($params["control_type"]) || $params["control_type"]) {
            if ($control_type === null)
                $control_type = $this->get_control_type();

            switch($control_type)
            {
                case "label":
                    $arrClass[] = "readonly";
                    break;
                case "checkbox":
                    $arrClass[] = "check";
                    break;
                case "picture":
                case "picture_no_link":
                    $arrClass[] = "picture";
                case "input":
                    if($this->app_type == "Currency")
                        $arrClass[] = $this->parent_page[0]->frameworkCSS->get("align-right", "util");
                    elseif($this->base_type == "Number")
                        $arrClass[] = $this->parent_page[0]->frameworkCSS->get("align-center", "util");
                default:
                    $arrClass[] = $control_type;
            }
        }
        return implode(" " , array_filter($arrClass));
    }


    /**
     * Esegue il parsing del template
     * @param Boolean $output_result se true visualizza a video il risultato del processing, se false restituisce il contenuto del processing
     * @return Mixed può essere string o true, a seconda di output_result
     */
    public function tplParse($output_result)
    {
        /* if ($this->required && $this->framework_css["required"]) {
             $required_symbol                = $this->framework_css["required"];
             $this->properties["required"]   = "";
         }



         $this->tpl[0]->set_var("properties", $this->getProperties());

         $field_type = $this->get_control_type();
         $fixed_pre_content = $this->fixed_pre_content;
         $fixed_post_content = $this->fixed_post_content;
         $buffer = $this->tpl[0]->rpparse("main", false);*/


        $control = $this->processControl();
        $label = ($this->display_label
            ? $this->processLabel()
            : ""
        );
        $description =  $this->processDescription();

        $buffer = $this->processContainer($control, $label, $description);

        /**
         * Group
         */
        //	$wrap_addon = null;
        /*if($this->framework_css["input-group"]
            && ($fixed_pre_content || $fixed_post_content)
        ) {

            if($fixed_pre_content) {
                if(preg_match("/<[^<]+>/", $fixed_pre_content,$m) == 0) {
                    $fixed_pre_content = '<span class="' . $this->parent_page[0]->frameworkCSS->get("control-text", "form") . '">' . $fixed_pre_content . '</span>';
                }
                $fixed_pre_content = '<div class="' . $this->parent_page[0]->frameworkCSS->get("control-prefix", "form") . '">' . $fixed_pre_content . '</div>';
            }

            if($fixed_post_content) {
                if(preg_match("/<[^<]+>/", $fixed_post_content,$m) == 0) {
                    $fixed_post_content = '<span class="' . $this->parent_page[0]->frameworkCSS->get("control-text", "form") . '">' . $fixed_post_content . '</span>';
                }
                $fixed_post_content = '<div class="' . $this->parent_page[0]->frameworkCSS->get("control-postfix", "form") . '">' . $fixed_post_content . '</div>';
            }

            $buffer = '<div class="' . $this->parent_page[0]->frameworkCSS->getClass($this->framework_css["input-group"]) . '">'
                    . $fixed_pre_content
                    . $buffer
                    . $fixed_post_content
                . '</div>';

        } else {
            $buffer = $fixed_pre_content . $buffer . $fixed_post_content;
        }*/



        /**
         * Control
         */
        //$html_block["control"] = $buffer;


        /*if($this->description) {
            $html_block["control"] .= '<small>' . $this->description . '</small>';
        }*/



        /*if($this->framework_css["field"]["control-wrap"]) {
            $html_block["control"] = '<div class="' . $this->parent_page[0]->frameworkCSS->getClass($this->framework_css["field"]["control-wrap"]) . '">'
                . $html_block["control"]
                . '</div>';
        }*/


        /**
         * Container
         */
        /*if($field_type == "checkbox" || $field_type == "radio") {
            $buffer = $html_block["control"] . $html_block["label"];
           // $this->framework_css["field"]["container"]["form"] = array("row-check");
        } else {
            $buffer = $html_block["label"] . $html_block["control"];
        }


        $container_class = $this->parent_page[0]->frameworkCSS->getClass($this->framework_css["field"]["container"]);
        if($container_class) {
            $buffer = '<div class="' . $this->parent_page[0]->frameworkCSS->getClass($this->framework_css["field"]["container"]) . '">'
                . $buffer
                . '</div>';
        }*/



        if($this->framework_css["outer_wrap"]) {
            $buffer = '<div class="' . $this->parent_page[0]->frameworkCSS->getClass($this->framework_css["outer_wrap"]) . '">'
                . $buffer
                . '</div>'
            ;
        }

//        if($wrap_addon !== null && !$wrap_addon)
//			$buffer = '<div class="' . $this->parent_page[0]->frameworkCSS->get("group", "form") . '">' . $buffer . '</div>';

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



}
