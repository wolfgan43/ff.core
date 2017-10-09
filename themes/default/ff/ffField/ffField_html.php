<?php
/**
 * Interface Field, html version
 *
 * @package FormsFramework
 * @subpackage interface
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

/**
 * Interface Field, html version
 *
 * @package FormsFramework
 * @subpackage interface
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */
class ffField_html extends ffField_base
{
	var $url = null;

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

	// Active Combo Stuffs
	var $actex_father		= null;
	var $actex_child		= null;

	// Active Combo EX Stuffs
	var $actex_update_from_db		= false;
	var $actex_related_field 		= "";

	var $actex_dialog_by_ajax   = false;
    var $actex_dialog_url 	    = "";
	var $actex_dialog_title     = "";
	var $actex_use_main_db	= false;
	var $actex_use_own_session = false;

    var $display_label = true;

    var $data_info = array("field" => null
    						, "base_type", "Text"
    						, "multilang", false
    					);	
    
	var $label_class = "";

	// gmap stuffs
	var $gmap_key = null;
	var $gmap_draggable = true;
	var $gmap_start_zoom = 6;
	var $gmap_start_lat = 45;
	var $gmap_start_lng = 9;

	//ckeditor
	var $ckeditor_theme = "default";	//Altera la struttura di ckeditor
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
	
	// Slider
	var $step = "1";
	var $desc_label = array();

	// List Splitter Stuffs
	var $size = 7;
	var $grouping_separator	= ",";							// the string used to separate groups of strings

	var $description = null;

	public function getTemplateFile($control_type)
	{
		if (strlen($this->template_file))
			return $this->template_file;
		else
			return "ffControl_" . $control_type . ".html";
	}
	
	public function tplParse($output_result)
	{
		$buffer = $this->fixed_pre_content . $this->tpl[0]->rpparse("main", false) . $this->fixed_post_content;

		if ($this->parent_page !== NULL) //code for ff.js
			$this->parent_page[0]->tplAddJs("ff.ffField", "ffField.js", FF_THEME_DIR . "/library/ff");
	
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

	public function tplLoad($control_type)
	{
		$this->tpl[0] = ffTemplate::factory($this->getTemplateDir($control_type));
		$this->tpl[0]->load_file($this->getTemplateFile($control_type), "main");

		if ($this->parent !== null && strlen($this->parent[0]->id))
		{
			if (!$this->omit_parent_id)
				$this->tpl[0]->set_var("container", $this->parent[0]->id . "_");
			$this->tpl[0]->set_var("keys", $this->parent_page[0]->get_params("", "", false));
			$this->tpl[0]->set_var("query_string", $this->parent_page[0]->get_script_params());
		}

		$this->tpl[0]->set_var("site_path", ffCommon_specialchars($this->site_path));
		$this->tpl[0]->set_var("page_path", ffCommon_specialchars($this->page_path));

		if (!strlen($this->widget))
			$this->tpl[0]->set_var("class", ffCommon_specialchars($this->get_control_class($control_type)));

		$this->tpl[0]->set_var("properties", $this->getProperties());

		$res = $this->doEvent("on_tpl_load", array(&$this));
	}
}
