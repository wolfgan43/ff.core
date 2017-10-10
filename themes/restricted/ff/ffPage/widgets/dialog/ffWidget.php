<?php

class ffWidget_dialog extends ffCommon
{

	// ---------------------------------------------------------------
	//  PRIVATE VARS (used by code, don't touch or may be explode! :-)

	var $template_file 	 = "ffWidget.html";

	var $class			= "ffWidget_dialog";

	var $widget_deps	= array();
	
	var $libraries		= array();
	
    var $js_deps		= array(
							"ff.ffPage.dialog"	=> null
		);
    var $css_deps 		= array(
    	);
	
	// PRIVATE VARS

	var $oPage			= null;
	var $source_path	= null;
	var $style_path		= null;

	var $tpl 			= null;

	var $processed_id	= array();

	function __construct(ffPage_base $oPage = null, $source_path = null, $style_path = null)
	{
		$this->get_defaults();

		$this->oPage = array(&$oPage);

		if ($source_path !== null)
			$this->source_path = $source_path;
		elseif ($oPage !== null)
			$this->source_path = $oPage->getThemePath();

		$this->style_path = $style_path;
		
		//if ($this->libraries === null)
		//	$this->libraries = ffTheme_restricted_get_libs($this, "theme/ff/ffPage/widgets/dialog");
	}

	function prepare_template($id)
	{
		$this->tpl[$id] = ffTemplate::factory(ffCommon_dirname(__FILE__));
		$this->tpl[$id]->load_file($this->template_file, "main");

		$this->tpl[$id]->set_var("site_path", $this->oPage[0]->site_path);

		$this->tpl[$id]->set_var("source_path", $this->source_path);

        if ($this->style_path !== null)
			$this->tpl[$id]->set_var("style_path", $this->style_path);
		elseif ($this->oPage !== null)
			$this->tpl[$id]->set_var("style_path", $this->oPage[0]->getThemePath());
	}

	function process($id, $options, ffPage_base &$oPage)
	{
		$tpl_id = $options["tpl_id"];
		if (!strlen($tpl_id))
			$tpl_id = "main";

		if (!isset($this->tpl[$tpl_id]))
			$this->prepare_template($tpl_id);

		$this->tpl[$tpl_id]->set_var("id", $id);
		$this->tpl[$tpl_id]->set_var("name", $options["name"]);
		$this->tpl[$tpl_id]->set_var("url", $options["url"]);
		$this->tpl[$tpl_id]->set_var("title", $options["title"]);
		if (array_key_exists("addjs", $options))
			$this->tpl[$tpl_id]->set_var("addjs", $options["addjs"]);
		else
			$this->tpl[$tpl_id]->set_var("addjs", "");

		if (!ffIsset($options, "modal") || $options["modal"] === true)
			$this->tpl[$tpl_id]->set_var("SectModal", "");
		else
			$this->tpl[$tpl_id]->parse("SectModal", false);
		
		if ($options["resizable"] === false)
			$this->tpl[$tpl_id]->set_var("resizable", "false");
		else 
			$this->tpl[$tpl_id]->set_var("resizable", "true");

		if (is_array($options["position"])) {
			foreach($options["position"] AS $position_value) {
				if(strlen($strPosition))
					$strPosition .= ",";
				$strPosition .= '' . $position_value . '';
			}
			$this->tpl[$tpl_id]->set_var("position", "[" . $strPosition . "]");
		} elseif(strlen($options["position"])) {
			$this->tpl[$tpl_id]->set_var("position", '"' . $options["position"] . '"');
		} else {
			$this->tpl[$tpl_id]->set_var("position", '"center"');
		}
			
		if ($options["draggable"] === false)
			$this->tpl[$tpl_id]->set_var("draggable", "false");
		else
			$this->tpl[$tpl_id]->set_var("draggable", "true");
		
		$this->tpl[$tpl_id]->set_var("callback", $options["callback"]);
		$this->tpl[$tpl_id]->set_var("class", ($options["class"] ? $options["class"] : "add"));
		if($options["class"]) {
			$this->tpl[$tpl_id]->set_var("dialog_class", $options["class"]);
			$this->tpl[$tpl_id]->parse("SectClass", false);
		} else {
			$this->tpl[$tpl_id]->set_var("SectClass", "");
		}
		if ($options["height"])
		{
			$this->tpl[$tpl_id]->set_var("height", $options["height"]);
			$this->tpl[$tpl_id]->parse("SectHeight", false);
		}
		else
		{
			$this->tpl[$tpl_id]->set_var("SectHeight", "");
		}

		if ($options["width"])
		{
			$this->tpl[$tpl_id]->set_var("width", $options["width"]);
			$this->tpl[$tpl_id]->parse("SectWidth", false);
		}
		else
		{
			$this->tpl[$tpl_id]->set_var("SectWidth", "");
		}

		if ($options["doredirects"])
		{
			$this->tpl[$tpl_id]->set_var("doredirects", "true");
		}
		else
		{
			$this->tpl[$tpl_id]->set_var("doredirects", "false");
		}
		
		if ($options["responsive"])
		{
			$this->tpl[$tpl_id]->set_var("responsive", "true");
		}
		else
		{
			$this->tpl[$tpl_id]->set_var("responsive", "false");
		}

		if ($options["unique"])
		{
			$this->tpl[$tpl_id]->set_var("unique", "true");
		}
		else
		{
			$this->tpl[$tpl_id]->set_var("unique", "false");
		}

		if (is_array($options["params"]) && count($options["params"]))
		{
			$this->tpl[$tpl_id]->set_var("SectParam", "");
			$i = 0;
			foreach ($options["params"] as $key => $value)
			{
				$this->tpl[$tpl_id]->set_var("param_name", $key);
				$this->tpl[$tpl_id]->set_var("param_value", $value);
				$i++;
				if ($i < count($options["params"]))
					$this->tpl[$tpl_id]->set_var("param_colon", ",");
				else
					$this->tpl[$tpl_id]->set_var("param_colon", "");
				$this->tpl[$tpl_id]->parse("SectParam", true);
			}
			$this->tpl[$tpl_id]->parse("SectParams", false);
		}
		else
			$this->tpl[$tpl_id]->set_var("SectParams", "");

		if (!isset($this->processed_id[$id]))
		{
			$this->processed_id[$id] = true;
			$this->tpl[$tpl_id]->parse("SectIstance", true);
		}

		return $this->tpl[$tpl_id]->rpparse("SectControl", false);
	}

	function get_component_headers($id)
	{
		if (!isset($this->tpl[$id]))
			return;

		return $this->tpl[$id]->rpparse("SectHeaders", false);
	}

	function get_component_footers($id)
	{
		if (!isset($this->tpl[$id]))
			return;

		return $this->tpl[$id]->rpparse("SectFooters", false);
	}

	function process_headers()
	{
		if (!isset($this->tpl["main"]))
			return;

		return $this->tpl["main"]->rpparse("SectHeaders", false);
	}

	function process_footers()
	{
		if (!isset($this->tpl["main"]))
			return;

		return $this->tpl["main"]->rpparse("SectFooters", false);
	}
}
