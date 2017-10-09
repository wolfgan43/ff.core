<?php
class ffPageNavigator_html extends ffPageNavigator_base
{
	var $id_if = null;
	
	/**
	 * Determina se le azioni devono essere eseguite con richieste Ajax
	 * @var Boolean
	 */
	var $doAjax = true;
    var $prefix = null;
    
	function getIDIF()
	{
		if ($this->id_if !== null)
			return $this->id_if;
		else
			return $this->id;
	}

	function getPrefix($tmp = null)
	{
		if($this->prefix === null) {
			if($tmp === null)
				$tmp = $this->getIDIF();

			if (strlen($tmp))
				return $tmp . "_";
		} else {
			return $this->prefix;
		}
	}
	
	/**
	 * Carica il template nell'oggetto $tpl
	 */
	public function tplLoad()
	{
		$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
		$this->tpl[0]->load_file($this->template_file, "main");
		
		if($this->id !== null)
		{
			$this->tpl[0]->set_var("XHRcomponent", $this->getIDIF());
			$this->tpl[0]->set_var("component", $this->getPrefix());
		} 
		elseif ($this->parent !== NULL && strlen($this->parent[0]->getIDIF()))
		{
			$this->tpl[0]->set_var("XHRcomponent", $this->parent[0]->getIDIF());
			$this->tpl[0]->set_var("component", $this->parent[0]->getPrefix());
		}
		
		$this->tpl[0]->set_var("site_path", $this->site_path);
		$this->tpl[0]->set_var("page_path", $this->page_path);
		$this->tpl[0]->set_var("theme", $this->getTheme());

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
		// determina se è stand-alone o attaccato ad una page/componente
		if ($this->parent === NULL)
		{
			$this->process_headers();
			$this->process_footers();
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
	
	function process_headers($stand_alone = false)
	{
		if($this->oPage !== NULL)
			$this->oPage[0]->tplAddJs("ff.ffPageNavigator");

		if (!isset($this->tpl[0]))
			return;
 
		if ($this->parent === NULL)
			$this->tpl[0]->parse("SectHeaders", false);
		else
		{
			return $this->tpl[0]->rpparse("SectHeaders", false);
		}
	}

	function process_footers($stand_alone = false)
	{
		if (!isset($this->tpl[0]))
			return;

		if(isset($_REQUEST["XHR_CTX_ID"])) 
		{
			$this->tpl[0]->set_var("SectNoXHRDialog", "");
			$this->tpl[0]->parse("SectXHRDialog", false);
		}
		else
		{
			$this->tpl[0]->parse("SectNoXHRDialog", false);
			$this->tpl[0]->set_var("SectXHRDialog", "");
		}
		
		if ($this->parent === NULL)
			$this->tpl[0]->parse("SectFooters", false);
		else
		{
			return $this->tpl[0]->rpparse("SectFooters", false);
		}
	}
	
	/**
	 * process è la funzione di elaborazione principale dell'oggetto
	 * @param Boolean $output_result se true visualizza a video il risultato del processing, se false restituisce il contenuto del processing
	 * @return Mixed può essere string o true, a seconda di output_result
	 */
	function process($output_result = FALSE) // discrepanza con gli altri oggetti. Dovrebbe essere separato fra process e process_interface
	{
		$this->tplLoad();

		$totpage = ceil($this->num_rows / $this->records_per_page);
		if ($this->page > $totpage)
			$this->page = $totpage;
		else if ($this->page < 1)
			$this->page = 1;

		if ($this->doAjax)
			$this->tpl[0]->set_var("doAjax", "true");
		else
			$this->tpl[0]->set_var("doAjax", "false");

		$this->tpl[0]->set_var("page_per_frame", $this->PagePerFrame);
		$this->tpl[0]->set_var("selected_records_per_page", $this->records_per_page);
		
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

		if($this->with_totelem) {
			$this->tpl[0]->set_var("totelem", $this->num_rows);
			$this->tpl[0]->parse("SectTotElem", false);
		} else {
			$this->tpl[0]->set_var("SectTotElem", "");
		}
		
		if($this->display_first)
			$this->tpl[0]->parse("SectFirstButton", false);
		else
			$this->tpl[0]->set_var("SectFirstButton", "");

		if($this->display_prev)
			$this->tpl[0]->parse("SectPrevButton", false);
		else
			$this->tpl[0]->set_var("SectPrevButton", "");
		
		if($this->display_next)
			$this->tpl[0]->parse("SectNextButton", false);
		else
			$this->tpl[0]->set_var("SectNextButton", "");

		if($this->display_last)
			$this->tpl[0]->parse("SectLastButton", false);
		else
			$this->tpl[0]->set_var("SectLastButton", "");

		if ($this->with_frames)
		{
			$this->tpl[0]->parse("SectPrevFrameButton", false);
			$this->tpl[0]->parse("SectNextFrameButton", false);
		}
		else
		{
			$this->tpl[0]->set_var("SectPrevFrameButton", "");
			$this->tpl[0]->set_var("SectNextFrameButton", "");
		}
		
		$this->process_selector(); // do at last so variables have the correct values

		return $this->tplParse($output_result);
	}

	/**
	 * Elabora la parte di selezione degli elementi totali e dell'input di selezione della pagina
	 */
	function process_selector()
	{
		if ($this->nav_display_selector && count($this->nav_selector_elements))
		{
			foreach ($this->nav_selector_elements as $key => $value)
			{
				$this->tpl[0]->set_var("records_per_page", $value);
				$this->tpl[0]->parse("SectSelectorPage", true);
			}
			reset($this->nav_selector_elements);
		}
		
        if($this->nav_selector_elements_all) {
            $this->tpl[0]->set_var("totelem", $this->num_rows);
            $this->tpl[0]->parse("SectSelectorPageAll", false);
        }

        if(($this->nav_display_selector && count($this->nav_selector_elements)) || $this->nav_selector_elements_all) {
			$this->tpl[0]->parse("SectSelector", false);
		} else {
			$this->tpl[0]->set_var("SectSelector", "");
		}
		
	}
}