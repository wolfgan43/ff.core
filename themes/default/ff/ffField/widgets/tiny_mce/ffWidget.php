<?php
/**
 * @package theme_default
 * @subpackage widgets
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

/**
 * @package theme_default
 * @subpackage widgets
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */
class ffWidget_tiny_mce extends ffCommon
{

    // ---------------------------------------------------------------
    //  PRIVATE VARS (used by code, don't touch or may be explode! :-)

    var $template_file  = "ffWidget.html";
    
    var $class          = "ffWidget_tinymce";
    
    var $widget_deps    = array();
    var $js_deps        = array();        
    var $css_deps       = array();
    // PRIVATE VARS
    
    var $source_path    = null;
    var $style_path     = null;
    
    var $tpl            = null;

    function __construct(ffPage_base $oPage = null, $source_path = null, $style_path = null)
    {
        $this->get_defaults();

        $this->tpl[0] = ffTemplate::factory(ffCommon_dirname(__FILE__));
        $this->tpl[0]->load_file($this->template_file, "main");

        if ($source_path !== null)
            $this->source_path = $source_path;
        elseif ($oPage !== null)                              
            $this->source_path = $oPage->getThemePath();

        if ($this->style_path !== null)
            $this->style_path = $style_path;
        elseif ($oPage !== null)
            $this->style_path = $oPage->getThemePath();
            
        $this->tpl[0]->set_var("source_path", $oPage->site_path . "/themes/library");
        $this->tpl[0]->set_var("style_path", $oPage->site_path . "/themes/library");
    }

    function process($id, &$value, &$Field)
    {
        static $count_elem;
        
        $this->tpl[0]->set_var("site_path", $Field->parent_page[0]->site_path);
        $this->tpl[0]->set_var("theme", $Field->parent_page[0]->theme);
        $this->tpl[0]->set_var("id", $id);
        
        if ($Field->contain_error && $Field->error_preserve)
            $this->tpl[0]->set_var("value", ffCommon_specialchars($value->ori_value));
        else
            $this->tpl[0]->set_var("value", ffCommon_specialchars($value->getValue($Field->get_app_type(), $Field->get_locale())));
        $this->tpl[0]->set_var("properties", $Field->getProperties());

        if(MOD_SEC_GROUPS) {
            $user_permission = get_session("user_permission");
            $class_groups = "_" . preg_replace('/[^a-zA-Z0-9]/', '', $user_permission["primary_gid_name"]);
        	$this->tpl[0]->set_var("mode", "specific_textareas");
        	$this->tpl[0]->set_var("SectExact", "");
        } else {
            $class_groups = "";
        	$this->tpl[0]->set_var("mode", "exact");
        	$this->tpl[0]->parse("SectExact", false);
        }

        if (strlen($Field->class))
            $this->tpl[0]->set_var("class", $Field->class);
        else
            $this->tpl[0]->set_var("class", $this->class . $class_groups);
        
        if ($Field->parent !== NULL && strlen($Field->parent[0]->id))
            $this->tpl[0]->set_var("container", $Field->parent[0]->id . "_");
            
        $this->tpl[0]->set_var("script_name", rawurlencode($_SERVER["SCRIPT_NAME"]));
        $this->tpl[0]->set_var("php_self", rawurlencode($_SERVER["PHP_SELF"]));
        $this->tpl[0]->set_var("orig_path_info", rawurlencode($_SERVER["ORIG_PATH_INFO"]));
        $this->tpl[0]->set_var("path_info", rawurlencode($_SERVER["PATH_INFO"]));
        $this->tpl[0]->set_var("query_string", rawurlencode($_SERVER["QUERY_STRING"]));
        $this->tpl[0]->set_var("selectedlang", rawurlencode(strtoupper($_REQUEST["selectedlang"])));
        $this->tpl[0]->set_var("language_inset", rawurlencode(strtolower(substr(LANGUAGE_INSET, 0, -1))));
        
        if($count_elem)
            $this->tpl[0]->set_var("colon", ", ");
        else 
            $this->tpl[0]->set_var("colon", "");

		if(!MOD_SEC_GROUPS) 
        	$this->tpl[0]->parse("SectBinding", true);

        $count_elem++;
        
        return $this->tpl[0]->rpparse("SectControl", FALSE);
    }
    
    function process_headers()
    {
        return $this->tpl[0]->rpparse("SectHeaders", FALSE);
    }
    
    function process_footers()
    {
        return $this->tpl[0]->rpparse("SectFooters", FALSE);
    }
}
