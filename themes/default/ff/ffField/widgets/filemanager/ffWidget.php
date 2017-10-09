<?php
/**
 * @package theme_default
 * @subpackage widgets
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

/**
 * @package theme_default
 * @subpackage widgets
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */
class ffWidget_filemanager extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)

    var $template_file  = "ffWidget.html";
    
    var $class          = "ffWidget_filemanager";
    
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
        $this->tpl[0]->set_var("site_path", $Field->parent_page[0]->site_path);
        $this->tpl[0]->set_var("theme", $Field->parent_page[0]->theme);
        $this->tpl[0]->set_var("id", $id);
        
        if ($Field->contain_error && $Field->error_preserve)
            $this->tpl[0]->set_var("value", ffCommon_specialchars($value->ori_value));
        else
            $this->tpl[0]->set_var("value", ffCommon_specialchars($value->getValue($Field->get_app_type(), $Field->get_locale())));
        $this->tpl[0]->set_var("properties", $Field->getProperties());

        if (strlen($Field->class))
            $this->tpl[0]->set_var("class", $Field->class);
        else
            $this->tpl[0]->set_var("class", $this->class . $class_groups);
        
        if ($Field->parent !== NULL && strlen($Field->parent[0]->id))
            $this->tpl[0]->set_var("container", $Field->parent[0]->id . "_");

        $this->tpl[0]->parse("SectBinding", true);

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
