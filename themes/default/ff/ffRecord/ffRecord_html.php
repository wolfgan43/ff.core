<?php
/**
 * @package theme_default
 * @subpackage components
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

/**
 * @package theme_default
 * @subpackage components
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */
class ffRecord_html extends ffRecord_base
{
	public $template_file 	= "ffRecord.html";
	
	public $tpl				= null;					// Internal ffTemplate() object  
	
	public $prefix			= null;
	
	var $widget_discl_enable = false;
	
	public function tplDisplay()
	{
		$this->tplDisplayContents();
		if ($this->detail !== null)
		{
			foreach ($this->detail as $key => $value)
			{
				$this->detail[$key]->display_rows();
			}
			reset($this->detail);
		}

		// display selected record controls (delete, insert..)
		$this->tplDisplayControls();

		// display error
		$this->tplDisplayError();
		if ($this->detail !== null)
		{
			foreach ($this->detail as $key => $value)
			{
				$this->detail[$key]->displayError();
			}
			reset($this->detail);
		}
	}

	protected function tplLoad()
	{
		if ($this->tpl === null)
		{
			$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
			$this->tpl[0]->load_file($this->template_file, "main");
		}
		
		if (strlen($this->id))
			$this->prefix = $this->id . "_";
		$this->tpl[0]->set_var("component", $this->prefix);
		$this->tpl[0]->set_var("component_id", $this->id);
		
		$this->tpl[0]->set_var("site_path", $this->site_path);
		$this->tpl[0]->set_var("page_path", $this->page_path);
		$this->tpl[0]->set_var("theme", $this->getTheme());
		$this->tpl[0]->set_var("class", $this->class);

		$this->tpl[0]->set_var("title", $this->title);

		$this->tpl[0]->set_var("ret_url", ffCommon_specialchars($this->parent[0]->ret_url));
		
		$this->tplSetFixedVars();
		
		// EVENT HANDLER
		$res = $this->doEvent("on_process_template", array(&$this, $this->tpl[0]));
	}
	
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

	public function tplParse($output_result)
	{
		$res = $this->doEvent("on_tpl_parse", array(&$this, $this->tpl[0]));

		$output_buffer = "";

		$this->tpl[0]->set_var("fixed_pre_content", $this->fixed_pre_content);
		$this->tpl[0]->set_var("fixed_post_content", $this->fixed_post_content);
		if ($this->detail !== null)
		{
			foreach ($this->detail as $key => $value)
			{
				$detail_output .= $this->detail[$key]->tplParse(false);
			}
			reset($this->detail);
			$this->tpl[0]->set_var("Details", $detail_output);
			$this->tpl[0]->parse("SectDetails", false);
		}
		else
			$this->tpl[0]->set_var("SectDetails", "");

		$this->tplSetFixedVars();

		$output_buffer = $this->tpl[0]->rpparse("main", false);

		if ($this->parent !== NULL) //code for ff.js
			$this->parent[0]->tplAddJs("ff.ffRecord", "ffRecord.js", FF_THEME_DIR . "/library/ff");

		if ($output_result)
		{
			echo $output_buffer;
			return true;
		}
		else
			return $output_buffer;
	}
	
	protected function tplDisplayContents()
	{
		foreach($this->key_fields as $key => $FormField)
		{
			if (!isset($this->form_fields[$key]) && strlen($this->key_fields[$key]->value->getValue()))
			{
				$this->tpl[0]->set_var("id", "keys[" . $key . "]");
				$this->tpl[0]->set_var("value", ffCommon_specialchars($this->key_fields[$key]->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE)));
				$this->tpl[0]->parse("SectHiddenField", true);

				$this->tpl[0]->set_var("Key_" . $key, $this->key_fields[$key]->getValue($this->key_fields[$key]->base_type, FF_SYSTEM_LOCALE));
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
				}
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

		$this->tpl[0]->set_var("maxspan", ($this->cols * 2));

		if ($this->display_required && $this->display_required_note)
		{
			$this->tpl[0]->set_var("required_symbol", $this->required_symbol);
			$this->tpl[0]->parse("SectRequiredNote", false);
		}
		else
			$this->tpl[0]->set_var("SectRequiredNote", "");

		$col = 0;
		$fields = 0;
		foreach($this->form_fields as $key => $FormField)
		{
			if (
					(isset($this->form_fields[$key]->group) && strlen($this->form_fields[$key]->group))
					|| (/*!$this->use_fixed_fields && ($this->display_values &&*/ $this->form_fields[$key]->skip_if_empty && !strlen($this->form_fields[$key]->value->ori_value))
				)
				continue;
			
			$fields++;
			$col ++;
			if ($col > $this->cols)
			{
				$this->tpl[0]->parse("SectFormRow", true);
				$this->tpl[0]->set_var("SectFormCol", "");
				$col = 1;
			}

			// EVENT HANDLER
			$res = $this->doEvent("on_process_field", array(&$this, $key));

			$rc = false;
			
			$rc |= $this->tpl[0]->set_var($key . "_value", $this->form_fields[$key]->getValue());
			$rc |= $this->tpl[0]->set_var($key . "_display_value", $this->form_fields[$key]->getDisplayValue());
			if ($this->tpl[0]->isset_var($key . "_field"))
				$rc |= $this->tpl[0]->set_var($key . "_field", $this->form_fields[$key]->process());
			
			if (!$rc)
			{

                $this->tpl[0]->set_var("container_properties", $this->form_fields[$key]->getProperties($this->form_fields[$key]->container_properties));

				$class = $this->form_fields[$key]->container_class;
				if(!strlen($class))
				    $class = $this->form_fields[$key]->class;

                if($this->form_fields[$key]->display_label) {
                    $this->tpl[0]->set_var("label", ffCommon_specialchars($this->form_fields[$key]->label));
                    $this->tpl[0]->set_var("label_properties", $this->form_fields[$key]->getProperties($this->form_fields[$key]->label_properties));
					
				    if ($this->display_required && $this->form_fields[$key]->required) {
					    $this->tpl[0]->parse("SectRequiredSymbol", false);
					    $class = $class . (strlen($class) ? " " : "") . "required";
				    } else {
					    $this->tpl[0]->set_var("SectRequiredSymbol", "");
				    }
				    	
                    $this->tpl[0]->parse("SectLabel", false);
                } else {
                    $this->tpl[0]->set_var("SectLabel", "");
                }
				
				$this->tpl[0]->set_var("container_class", $class);                            
				
				if (!$this->display_values || strlen($this->form_fields[$key]->control_type))
				{
					if ($this->tpl[0]->isset_var("content"))
						$this->tpl[0]->set_var("content", $this->form_fields[$key]->process());
				}
				else
					$this->tpl[0]->set_var("content", $this->form_fields[$key]->getDisplayValue());

				$this->tpl[0]->parse("SectFormCol", true);
			}

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
			
			if ($this->form_fields[$key]->value->getValue())
			{
				$this->tpl[0]->parse("SectVal_$key", false);
				$this->tpl[0]->set_var("SectNotVal_$key", "");
			}
			else
			{
				$this->tpl[0]->set_var("SectVal_$key", "");
				$this->tpl[0]->parse("SectNotVal_$key", false);
			}

			if ($this->form_fields[$key]->extended_type == "Selection")
			{
				$this->tpl[0]->set_regexp_var("/SectSet_" . $key . "_.+/", "");
				$this->tpl[0]->parse("SectSet_" . $key . "_" . $this->form_fields[$key]->value->getValue($this->form_fields[$key]->base_type, FF_SYSTEM_LOCALE), false);
			}

			$this->tpl[0]->parse("Sect_$key", false);

			// "display" hidden original values
			if (!isset($this->key_fields[$key])) // displayed key fields are threated previously
			{
				$this->tpl[0]->set_var("id", $key . "_ori");
				$this->tpl[0]->set_var("value", ffCommon_specialchars($this->form_fields[$key]->value_ori->getValue($this->form_fields[$key]->base_type, FF_SYSTEM_LOCALE)));
				$this->tpl[0]->parse("SectHiddenField", true);
			}

			$this->tplSetFixedVars();
		}
		reset($this->form_fields);
		if($fields) {
			$this->tpl[0]->parse("SectFormRow", true);
			$this->tpl[0]->parse("SectForm", false);
		} else {
			$this->tpl[0]->set_var("SectForm", "");
		}
		
		if (isset($this->groups) && is_array($this->groups) && count($this->groups))
		{
			foreach ($this->groups as $key => $value)
			{
				$suffix = "_" . $key;
				$this->tpl[0]->set_var("group_class", $key);
				$this->tpl[0]->set_var("GroupTitle", $this->groups[$key]["title"]);
				
				if ($this->groups[$key]["hide_title"])
				{
					$this->tpl[0]->set_var("SectGroupTitle", "");
				}
				else
				{ 
					$this->tpl[0]->parse("SectGroupTitle", false);
				}
				$cols = $this->groups[$key]["cols"];
				if ($cols < 1)
					$cols = 1;
				$this->tpl[0]->set_var("maxspan", $cols);
				
				$col = 0;
				$i = 0;
				$this->tpl[0]->set_var("SectGroupRow", "");
				$this->tpl[0]->set_var("SectGroupCol", "");
				$this->tpl[0]->set_var("SectGroupRow" . $suffix, "");
				$this->tpl[0]->set_var("SectGroupCol" . $suffix, "");
				
				if (isset($this->groups[$key]["contents"]) && is_array($this->groups[$key]["contents"]) && count($this->groups[$key]["contents"]))
				{
					foreach ($this->groups[$key]["contents"] as $subkey => $subvalue)
					{
						if (
								(/*!$this->use_fixed_fields && $this->display_values && */$this->form_fields[$subkey]->skip_if_empty && !strlen($this->form_fields[$subkey]->value->ori_value))
							)
							continue;
						$i++;

						$oldspan = $span;
						$span = $this->groups[$key]["contents"][$subkey]["data"]->span;
						if ($span > $cols)
							$span = $cols;
							
						// completo la colonna precedente, se esiste
						if ($i > 1)
						{
							// se abbiamo raggiunto il limite, adatto la precedente e passo alla riga successiva
							if ($col + $span > $cols)
							{
								$this->tpl[0]->set_var("colspan", $cols - ($col - $oldspan));
								
								$rc = false;
								$rc = $this->tpl[0]->parse("SectGroupCol" . $suffix, true);
								if (!$rc) $this->tpl[0]->parse("SectGroupCol", true);
								$rc = $this->tpl[0]->parse("SectGroupRow" . $suffix, true);
								if (!$rc) $this->tpl[0]->parse("SectGroupRow", true);
								$rc = $this->tpl[0]->set_var("SectGroupCol" . $suffix, "");
								if (!$rc) $this->tpl[0]->set_var("SectGroupCol", "");
								
								$col = 0;
							}
							else
							{
								$rc = $this->tpl[0]->parse("SectGroupCol" . $suffix, true);
								if (!$rc) $this->tpl[0]->parse("SectGroupCol", true);
							}

						}
						
						$this->tpl[0]->set_var("colspan", $span);
						$col += $span;

						// EVENT HANDLER
						$res = $this->doEvent("on_process_field", array(&$this, $subkey));

						// container
						$this->tpl[0]->set_var("container_properties", $this->form_fields[$subkey]->getProperties($this->form_fields[$subkey]->container_properties));
						$class = $this->form_fields[$subkey]->container_class;
						if(!strlen($class))
				    		$class = $this->form_fields[$subkey]->class;

						// LABEL
							$class = $this->form_fields[$subkey]->container_class;
						    if(!strlen($class))
				    			$class = $this->form_fields[$subkey]->class;

                        if($this->form_fields[$subkey]->display_label) {
							if ($this->form_fields[$subkey]->description !== null) 
							{
								$this->tpl[0]->set_var("description", ffCommon_specialchars($this->form_fields[$subkey]->description));
								$this->tpl[0]->parse("SectDescriptionLabel", false);
							}	
							else
							{
								$this->tpl[0]->set_var("description", "");
								$this->tpl[0]->set_var("SectDescriptionLabel", "");
							}
						    $this->tpl[0]->set_var("label", ffCommon_specialchars($this->form_fields[$subkey]->label));
						    $this->tpl[0]->set_var("label_properties", $this->form_fields[$subkey]->getProperties($this->form_fields[$subkey]->label_properties));
							
						    if ($this->display_required && $this->form_fields[$subkey]->required) {
							    $this->tpl[0]->parse("SectRequiredSymbol", false);
							    $class = $class . (strlen($class) ? " " : "") . "required";
						    } else {
							    $this->tpl[0]->set_var("SectRequiredSymbol", "");
						    }
				    			
                            $this->tpl[0]->parse("SectGroupLabel", false);
                        } else { 
                            $this->tpl[0]->set_var("SectGroupLabel", "");
                        }

                        $this->tpl[0]->set_var("container_class", $class);
						// CONTROL/VALUE SECTION
						$rc = false;
						
						$rc |= $this->tpl[0]->set_var($subkey . "_value", $this->form_fields[$subkey]->getValue());
						$rc |= $this->tpl[0]->set_var($subkey . "_display_value", $this->form_fields[$subkey]->getDisplayValue());
						if ($this->tpl[0]->isset_var($subkey . "_field"))
							$rc |= $this->tpl[0]->set_var($subkey . "_field", $this->form_fields[$subkey]->process());
						
						$rc |= $this->tpl[0]->set_var("FIELD_value", $this->form_fields[$subkey]->getValue());
						$rc |= $this->tpl[0]->set_var("FIELD_display_value", $this->form_fields[$subkey]->getDisplayValue());
						if ($this->tpl[0]->isset_var("FIELD_field"))
							$rc |= $this->tpl[0]->set_var("FIELD_field", $this->form_fields[$subkey]->process());

						if (!$rc)
						{
							if (!$this->display_values || strlen($this->form_fields[$subkey]->control_type))
							{
								if ($this->tpl[0]->isset_var("content"))
									$this->tpl[0]->set_var("content", $this->form_fields[$subkey]->process());
							}
							else
								$this->tpl[0]->set_var("content", $this->form_fields[$subkey]->getDisplayValue());
						}

						$fieldset = false;
						
						switch ($this->form_fields[$subkey]->extended_type)
						{
							case "Selection":
								switch ($this->form_fields[$subkey]->get_control_type())
								{
									case "radio":
										$fieldset = true;
										break;
								}
								break;
						}
						
						if ($fieldset)
						{
							$this->tpl[0]->set_var("SectField", "");
							$this->tpl[0]->parse("SectFieldSet", false);
						}
						else
						{
							$this->tpl[0]->parse("SectField", false);
							$this->tpl[0]->set_var("SectFieldSet", "");
						}
							
						// Manage Fixed Sections
						if (strlen($this->form_fields[$subkey]->value->ori_value))
						{
							$this->tpl[0]->parse("SectSet_$subkey", false);
							$this->tpl[0]->set_var("SectNotSet_$subkey", "");
						}
						else
						{
							$this->tpl[0]->set_var("SectSet_$subkey", "");
							$this->tpl[0]->parse("SectNotSet_$subkey", false);
						}
						
						if ($this->form_fields[$subkey]->extended_type == "Selection")
						{
							$this->tpl[0]->set_regexp_var("/SectSet_" . $subkey . "_.+/", "");
							$this->tpl[0]->parse("SectSet_" . $subkey . "_" . $this->form_fields[$subkey]->value->getValue($this->form_fields[$subkey]->base_type, FF_SYSTEM_LOCALE), false);
						}

						$this->tpl[0]->parse("Sect_$subkey", false);

						// "display" hidden original values
						if (!isset($this->key_fields[$subkey])) // displayed key fields are threated previously
						{
							$this->tpl[0]->set_var("id", $subkey . "_ori");
							$this->tpl[0]->set_var("value", ffCommon_specialchars($this->form_fields[$subkey]->value_ori->getValue($this->form_fields[$subkey]->base_type, FF_SYSTEM_LOCALE)));
							$this->tpl[0]->parse("SectHiddenField", true);
						}
					}
					reset($this->groups[$key]["contents"]);
					if ($i > 0)
					{
						$this->tpl[0]->set_var("colspan", $cols - ($col - $span));

						$rc = $this->tpl[0]->parse("SectGroupCol" . $suffix, true);
						if (!$rc) $this->tpl[0]->parse("SectGroupCol", true);
						$rc = $this->tpl[0]->parse("SectGroupRow" . $suffix, true);
						if (!$rc) $this->tpl[0]->parse("SectGroupRow", true);
						$rc = $this->tpl[0]->parse("SectGroup" . $suffix, true);
						if (!$rc) $this->tpl[0]->parse("SectGroup", true);
					}
				}
			}
			reset($this->groups);
		}
	}

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
			$buffer = $tmp_action_buttons[$key]["obj"]->process() . $buffer;
		}
		reset($tmp_action_buttons);

		$this->tpl[0]->set_var("ActionButtons", $buffer);
		$this->tpl[0]->parse("SectControls", "");
	}

	public function tplDisplayError($sError = null)
	{
		if ($sError !== null)
			$this->strError = $sError;

		if (strlen($this->strError))
		{
			$this->tpl[0]->set_var("strError", ffCommon_specialchars($this->strError));
			$this->tpl[0]->parse("SectError", false);
		}
		else
			$this->tpl[0]->set_var("SectError", "");
	}
	public function structProcess($tpl)
	{
	}
}