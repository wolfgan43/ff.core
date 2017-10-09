<?php
/**
 * @package theme_default
 * @subpackage interface
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

/**
 * @package theme_default
 * @subpackage interface
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */
class ffButton_html extends ffButton_base
{
	public function getTemplateFile()
	{
		if (strlen($this->template_file))
			return $this->template_file;
		else
			return "ffButton_" . $this->aspect . ".html";
	}
	
	public function tplLoad()
	{
		$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
	
		$this->tpl[0]->load_file($this->getTemplateFile(), "main");

		if ($this->parent !== NULL && strlen($this->parent[0]->id))
			$this->tpl[0]->set_var("container", $this->parent[0]->id . "_");
		
		$this->tpl[0]->set_var("site_path", $this->site_path);
		$this->tpl[0]->set_var("page_path", $this->page_path);
		$this->tpl[0]->set_var("theme",  $this->getTheme());
		$this->tpl[0]->set_var("class", $this->get_class());
		
		$this->tpl[0]->set_var("properties", $this->getProperties());

		$this->tpl[0]->set_var("id", $this->id);
		$this->tpl[0]->set_var("label", $this->label);

		if (is_array($this->fixed_vars) && count($this->fixed_vars))
		{
			foreach ($this->fixed_vars as $key => $value)
			{
				$this->tpl[0]->set_var($key, $value);
			}
			reset($this->fixed_vars);
		}
	}
		
	public function tplParse($output_result)
	{
		if ($output_result)
		{
			$this->tpl[0]->pparse("main", false);
			return true;
		}
		else
		{
			return $this->tpl[0]->rpparse("main", false);
		}
	}
}
