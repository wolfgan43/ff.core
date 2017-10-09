<?php
class ffPageNavigator_html extends ffPageNavigator_base
{
	/**
	 * Determina se le azioni devono essere eseguite con richieste Ajax
	 * @var Boolean
	 */
	var $doAjax = true;
    var $callback = "null";
    var $callback_params = "{}";
    var $infinite = false;

	/**
	 * Carica il template nell'oggetto $tpl
	 */
	public function tplLoad()
	{
		$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
		$this->tpl[0]->load_file($this->template_file, "main");
		
		if($this->id !== null)
		{
			$this->prefix = $this->id . "_";
			$this->tpl[0]->set_var("XHRcomponent", $this->id);
		} elseif ($this->parent !== NULL && strlen($this->parent[0]->id))
		{
			$this->prefix = $this->parent[0]->id . "_";
			$this->tpl[0]->set_var("XHRcomponent", $this->parent[0]->id);
		}
		
		$this->tpl[0]->set_var("site_path", $this->site_path);
		$this->tpl[0]->set_var("page_path", $this->page_path);
		$this->tpl[0]->set_var("theme", $this->getTheme());
		
		$this->tpl[0]->set_var("component", $this->prefix);

		$navigator_class["default"] = $this->prefix . "pageNavigator";
		$this->tpl[0]->set_var("component_class", cm_getClassByDef($this->framework_css["component"], $navigator_class));
		
		$this->tpl[0]->set_var("form_action", $this->form_action);
		$this->tpl[0]->set_var("form_name", $this->form_name);

		$this->tpl[0]->set_var("page_parname", $this->page_parname);
		$this->tpl[0]->set_var("records_per_page_parname", $this->records_per_page_parname);
	
		if (is_array($this->fixed_vars) && count($this->fixed_vars))
		{
			foreach ($this->fixed_vars as $key => $value)
			{
				$this->tpl[0]->set_var($key, $value);
			}
			reset($this->fixed_vars);
		}
	}

	/**
	 * Esegue il parsing del template
	 * @param Boolean $output_result se true visualizza a video il risultato del processing, se false restituisce il contenuto del processing
	 * @return Mixed può essere string o true, a seconda di output_result
	 */
	public function tplParse($output_result)
	{
		if($this->parent === NULL) { 
			if($this->oPage !== NULL && is_subclass_of($this->oPage[0], "ffPage_base"))
				$this->oPage[0]->tplAddJs("ff.ffPageNavigator", "ffPageNavigator.js", FF_THEME_DIR . "/library/ff");

			$this->tpl[0]->parse("SectHeaders", false);
			$this->tpl[0]->parse("SectFooters", false);
		}

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

	function process_headers()
	{
		if ($this->parent !== NULL) {//code for ff.js
			if(is_subclass_of($this->parent[0], "ffPage_base")) {
				//$this->parent[0]->tplAddJs("ff.history", "history.js", FF_THEME_DIR . "/library/ff");
				$this->parent[0]->tplAddJs("ff.ffPageNavigator", "ffPageNavigator.js", FF_THEME_DIR . "/library/ff");
			} elseif($this->parent[0]->parent !== NULL && is_subclass_of($this->parent[0]->parent[0], "ffPage_base")) {
				//$this->parent[0]->parent[0]->tplAddJs("ff.history", "history.js", FF_THEME_DIR . "/library/ff");
				$this->parent[0]->parent[0]->tplAddJs("ff.ffPageNavigator", "ffPageNavigator.js", FF_THEME_DIR . "/library/ff");
			}
		}

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
	 * process è la funzione di elaborazione principale dell'oggetto
	 * @param Boolean $output_result se true visualizza a video il risultato del processing, se false restituisce il contenuto del processing
	 * @return Mixed può essere string o true, a seconda di output_result
	 */
	function process($output_result = FALSE)
	{
		$this->tplLoad();

        $current_class = cm_getClassByFrameworkCss("current", "pagination");
		$totpage = ceil($this->num_rows / $this->records_per_page);
		if ($this->page > $totpage)
			$this->page = $totpage;
		else if ($this->page < 1)
			$this->page = 1;

		if ($this->doAjax)
			$this->tpl[0]->set_var("doAjax", "true");
		else
			$this->tpl[0]->set_var("doAjax", "false");
        
        $this->tpl[0]->set_var("callback", $this->callback);
        $this->tpl[0]->set_var("callback_params", $this->callback_params);
		$this->tpl[0]->set_var("page_per_frame", $this->PagePerFrame);
		$this->tpl[0]->set_var("selected_records_per_page", $this->records_per_page);
		$this->tpl[0]->set_var("current_class", $current_class);
		
		if($this->with_totelem) {
        	$totelem_class["default"] = "totelem";
			$totelem_class["pages"] = cm_getClassByFrameworkCss("pages", "pagination");
        	$this->tpl[0]->set_var("totelem_class", cm_getClassByDef($this->framework_css["totelem"], $totelem_class));
		
			$this->tpl[0]->set_var("totelem", $this->num_rows);
			$this->tpl[0]->parse("SectTotElem", false);
		} else {
            if(is_array($this->framework_css["pagination"]["col"]) && count($this->framework_css["pagination"]["col"])) {
                foreach($this->framework_css["pagination"]["col"] AS $col_key => $col_value) {
                    $this->framework_css["pagination"]["col"][$col_key] = $this->framework_css["pagination"]["col"][$col_key] + $this->framework_css["totelem"]["col"][$col_key];
                    if($this->framework_css["pagination"]["col"][$col_key] > 12)
                        $this->framework_css["pagination"]["col"][$col_key] = 12;
                }
            }
            
			$this->tpl[0]->set_var("SectTotElem", "");
		}
				
		if($totpage)
		{
			$this->tpl[0]->set_var("selected_page", $this->page);
			$this->tpl[0]->set_var("totpage", $totpage);
			$this->tpl[0]->set_var("totrec", $this->num_rows);

			if ($this->doAjax)
				$this->tpl[0]->set_var("doAjax", "true");
			else
				$this->tpl[0]->set_var("doAjax", "false");

			if($this->with_choice && $totpage > 1)
			{
				$choice_class["default"] = "choice";
				$choice_class["pages"] = cm_getClassByFrameworkCss("pages", "pagination");
				$this->tpl[0]->set_var("choice_class", cm_getClassByDef($this->framework_css["choice"], $choice_class));
				$this->tpl[0]->set_var("choice_box_class", cm_getClassByFrameworkCss("group", "form"));

				$buffer_choice_label = '<span class="' . cm_getClassByFrameworkCss("control-prefix", "form") . '">' . ffTemplate::_get_word_by_code("go_to_page") . '</span>';
				$buffer_choice_tot_page = '<span class="' . cm_getClassByFrameworkCss("control-postfix", "form") . '">' . ffTemplate::_get_word_by_code("of") . ' <span class="totpage">' . $totpage . '</span></span>';
				
				$wrap_addon = cm_getClassByFrameworkCss("wrap-addon", "form");
				if($wrap_addon) {
					$buffer_choice_label = '<div class="' . cm_getClassByFrameworkCss(array(3), "col") . '">' . $buffer_choice_label . '</div>';
					$buffer_choice_tot_page = '<div class="' . cm_getClassByFrameworkCss(array(5), "col") . '">' . $buffer_choice_tot_page . '</div>';
					$this->tpl[0]->set_var("choice_input_box_class", cm_getClassByFrameworkCss(array(4), "col"));
					$this->tpl[0]->parse("SectChoiceInputStart", false);
					$this->tpl[0]->parse("SectChoiceInputEnd", false);
				}
				$this->tpl[0]->set_var("current_page", $this->page);
				$this->tpl[0]->set_var("choice_input_class", cm_getClassByFrameworkCss("control", "form", "currentpage"));
				$this->tpl[0]->set_var("choice_label", $buffer_choice_label);
				$this->tpl[0]->set_var("choice_tot_page", $buffer_choice_tot_page);
				
				$this->tpl[0]->parse("SectChoice", false);
			} else {
				$this->tpl[0]->set_var("SectChoice", "");
			}
		}
		else
		{
			$this->tpl[0]->set_var("selected_page", 1);
			$this->tpl[0]->set_var("totrec", 0);

			$this->tpl[0]->parse("SectNoPage", false);
		}

        if(!$totpage || !$this->with_choice) 
        {
            if(is_array($this->framework_css["pagination"]["col"]) && count($this->framework_css["pagination"]["col"])) {
                foreach($this->framework_css["pagination"]["col"] AS $col_key => $col_value) {
                    $this->framework_css["pagination"]["col"][$col_key] = $this->framework_css["pagination"]["col"][$col_key] + $this->framework_css["choice"]["col"][$col_key];
                    if($this->framework_css["pagination"]["col"][$col_key] > 12)
                        $this->framework_css["pagination"]["col"][$col_key] = 12;
                }
            }            
        }

		if($this->page == 1)
			$first_arrows_class = "hidden";

		if($this->page == $totpage)
			$last_arrows_class = "hidden";

		$arrows_class = cm_getClassByFrameworkCss("arrows", "pagination");
		if(strlen($arrows_class)) {
			$first_arrows_class .= ($first_arrows_class ? " " : "") . $arrows_class;
			$first_frame_arrows_class = $first_arrows_class;

			$last_arrows_class .= ($last_arrows_class ? " " : "") . $arrows_class;
			$last_frame_arrows_class = $last_arrows_class;
		}
		
		if($this->display_first)
        {
			if(strlen($first_arrows_class)) 
				$this->tpl[0]->set_var("first_arrows_class", ' class="' . $first_arrows_class . '"');

            $this->tpl[0]->set_var("first_class", cm_getClassByFrameworkCss("first", "icon"));
            //$this->tpl[0]->set_var("first_icon", cm_getClassByFrameworkCss("nav_first", "ico-link-tag"));
			$this->tpl[0]->parse("SectFirstButton", false);
		} else
			$this->tpl[0]->set_var("SectFirstButton", "");

		if($this->display_prev) 
        {
			if(strlen($arrows_class)) 
				$this->tpl[0]->set_var("prev_arrows_class", ' class="' . $arrows_class . '"');

            $this->tpl[0]->set_var("prev_class", cm_getClassByFrameworkCss("prev", "icon"));
            //$this->tpl[0]->set_var("prev_icon", cm_getClassByFrameworkCss("nav_prev", "ico-link-tag"));
			$this->tpl[0]->parse("SectPrevButton", false);
		} else
			$this->tpl[0]->set_var("SectPrevButton", "");
		
		if($this->display_next) 
        {
        	$page_inject = "pinject";
        	
            $this->tpl[0]->set_var("next_arrows_class", ' class="' . $page_inject . ($arrows_class ? " " : "") . $arrows_class . '"');
            $this->tpl[0]->set_var("next_class", cm_getClassByFrameworkCss("next", "icon"));
            //$this->tpl[0]->set_var("next_icon", cm_getClassByFrameworkCss("play", "ico-link-tag"));
			$this->tpl[0]->parse("SectNextButton", false);
		} else
			$this->tpl[0]->set_var("SectNextButton", "");

		if ($this->with_frames)
		{
			if(strlen($first_frame_arrows_class)) 
				$this->tpl[0]->set_var("first_frame_arrows_class", ' class="' . $first_frame_arrows_class . '"');

	        $this->tpl[0]->set_var("prevframe_class", cm_getClassByFrameworkCss("prev-frame", "icon"));
			$this->tpl[0]->parse("SectPrevFrameButton", false);
            
        	if(!$page_inject) {
        		$page_inject = "pinject";
        		$last_frame_arrows_class .= ($last_frame_arrows_class ? " " : "") . $page_inject;
			}
			if(strlen($last_frame_arrows_class)) 
				$this->tpl[0]->set_var("last_frame_arrows_class", ' class="' . $last_frame_arrows_class . '"');
            
            $this->tpl[0]->set_var("nextframe_class", cm_getClassByFrameworkCss("next-frame", "icon"));
			$this->tpl[0]->parse("SectNextFrameButton", false);
		}
		else
		{
			$this->tpl[0]->set_var("SectPrevFrameButton", "");
			$this->tpl[0]->set_var("SectNextFrameButton", "");
		}

		if($this->display_last)
        {
        	if(!$page_inject) {
        		$page_inject = "pinject";
        		$last_frame_arrows_class .= ($last_frame_arrows_class ? " " : "") . $page_inject;
			}
		
			if(strlen($last_arrows_class)) 
				$this->tpl[0]->set_var("last_arrows_class", ' class="' . $last_arrows_class . '"');
        	
            $this->tpl[0]->set_var("last_class", cm_getClassByFrameworkCss("last", "icon"));
            //$this->tpl[0]->set_var("last_icon", cm_getClassByFrameworkCss("nav_last", "ico-link-tag"));
			$this->tpl[0]->parse("SectLastButton", false);
		} else
			$this->tpl[0]->set_var("SectLastButton", "");

		$this->process_selector($current_class); // do at last so variables have the correct values

		$pagination_class["default"] = "pages";
		$pagination_class["pages"] = cm_getClassByFrameworkCss("pages", "pagination");

        $this->tpl[0]->set_var("pagination_class", cm_getClassByDef($this->framework_css["pagination"], $pagination_class));



        if ($totpage > $this->PagePerFrame) {
            $start_page = $this->page - floor($this->PagePerFrame / 2);
            if ($start_page < 1)
                $start_page = 1;

            $end_page = $start_page + $this->PagePerFrame - 1;
            if ($end_page > $totpage)
                $end_page = $totpage;

            $start_page = $end_page - $this->PagePerFrame + 1;
        } else {
            $start_page = 1;
            $end_page = $totpage;
        }
        if($end_page > $start_page) {
            for ($i = $start_page; $i <= $end_page; $i++) {
                if($i == $this->page) {
                    $this->tpl[0]->set_var("page_class", ' class="' . $current_class . '"');
                } else {
                    $this->tpl[0]->set_var("page_class", "");
                }
                $this->tpl[0]->set_var("num_page", $i);
                $this->tpl[0]->parse("SectPageButton", true);
            }    
        }
		return $this->tplParse($output_result);
	}

	/**
	 * Elabora la parte di selezione degli elementi totali e dell'input di selezione della pagina
	 */
	function process_selector($current_class)
	{
		if ($this->nav_display_selector && count($this->nav_selector_elements))
		{
			foreach ($this->nav_selector_elements as $key => $value)
			{
                if($this->num_rows >= $value) {
                    if(!$current_selector_isset && $value >= $this->records_per_page) {
                        $this->tpl[0]->set_var("rec_per_page_class", ' class="' . $current_class . '"');
                        $current_selector_isset = true;
                    } else {
                        $this->tpl[0]->set_var("rec_per_page_class", "");
                    }                
				    $this->tpl[0]->set_var("records_per_page", $value);
                   // $this->tpl[0]->set_var("records_per_page_class", "rec-x-page-" . $value);
				    $this->tpl[0]->parse("SectSelectorPage", true);
                }
			}
			reset($this->nav_selector_elements);
		}
		  
        if($this->nav_selector_elements_all) {
            if(!$current_selector_isset && $this->records_per_page >= $this->num_rows) 
                $this->tpl[0]->set_var("rec_per_page_class", ' class="' . $current_class . '"');

            $this->tpl[0]->set_var("totelem", $this->num_rows);
            //$this->tpl[0]->set_var("records_per_page_all_class", "rec-x-page-all");
            $this->tpl[0]->parse("SectSelectorPageAll", false);
        }

        if(($this->nav_display_selector && count($this->nav_selector_elements)) || $this->nav_selector_elements_all) {
        	$perpage_class["default"] = "perPage";
        	$perpage_class["pages"] = cm_getClassByFrameworkCss("pages", "pagination");

        	$this->tpl[0]->set_var("perpage_class", cm_getClassByDef($this->framework_css["perPage"], $perpage_class));
			$this->tpl[0]->parse("SectSelector", false);
		} else {
            if(is_array($this->framework_css["pagination"]["col"]) && count($this->framework_css["pagination"]["col"])) {
                foreach($this->framework_css["pagination"]["col"] AS $col_key => $col_value) {
                    $this->framework_css["pagination"]["col"][$col_key] = $this->framework_css["pagination"]["col"][$col_key] + $this->framework_css["perPage"]["col"][$col_key];
                    if($this->framework_css["pagination"]["col"][$col_key] > 12)
                        $this->framework_css["pagination"]["col"][$col_key] = 12;
                }
            }
            
			$this->tpl[0]->set_var("SectSelector", "");
		}
		
	}
}