<?php
/**
 * @package theme_default
 * @subpackage base
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

/**
 * @package theme_default
 * @subpackage base
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */
class ffPageNavigator_html extends ffPageNavigator_base
{
	public function tplLoad()
	{
		$this->tpl[0] = ffTemplate::factory($this->getTemplateDir());
		$this->tpl[0]->load_file($this->template_file, "main");
		
		if($this->id !== null)
			$this->prefix = $this->id . "_";
		if ($this->parent !== NULL && strlen($this->parent[0]->id))
			$this->prefix = $this->parent[0]->id . "_";
		
		$this->tpl[0]->set_var("site_path", $this->site_path);
		$this->tpl[0]->set_var("page_path", $this->page_path);
		$this->tpl[0]->set_var("theme", $this->getTheme());

		$this->tpl[0]->set_var("component", $this->prefix);

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

	function process($output_result = FALSE)
	{
		$this->tplLoad();

		$totpage = ceil($this->num_rows / $this->records_per_page);
		if ($this->page > $totpage)
			$this->page = $totpage;
		else if ($this->page < 1)
			$this->page = 1;

		if($totpage)
		{
			$this->tpl[0]->set_var("SectNoPage", "");

			if ($this->with_frames)
			{
				$this->tpl[0]->set_var("records_per_page", $this->records_per_page);

				$totframe =  ceil($totpage / $this->PagePerFrame);
				$FrameOffSet = ceil($this->page / $this->PagePerFrame) - 1;

				//$StartPage = $FrameOffSet * $this->PagePerFrame + 1;
				$StartPage = ceil($this->page - ($this->PagePerFrame / 2));
				if ($StartPage < 1)
					$StartPage = 1;

				$EndPage = $StartPage + $this->PagePerFrame - 1;
				if ($EndPage > $totpage)
					$EndPage = $totpage;

				for ($i = $StartPage; $i <= $EndPage; $i++)
				{
					$this->tpl[0]->set_var("page", $i);
					if ($i == $this->page)
					{
						$this->tpl[0]->parse("SectPageSelected", false);
						$this->tpl[0]->set_var("SectPageLink", "");
					}
					else
					{
						$this->tpl[0]->parse("SectPageLink", false);
						$this->tpl[0]->set_var("SectPageSelected", "");
					}
					$this->tpl[0]->parse("SectPage", true);
				}

				$this->tpl[0]->set_var("page", $this->page);
				$this->tpl[0]->set_var("records_per_page", $this->records_per_page);

				if ($StartPage <= 1)
					$this->tpl[0]->set_var("hidden", "hidden");
				else
					$this->tpl[0]->set_var("hidden", "");

				$this->tpl[0]->set_var("page", ($FrameOffSet * $this->PagePerFrame) - $this->PagePerFrame + 1);
				$this->tpl[0]->set_var("rec_per_frame", $this->PagePerFrame);

				if($this->show_frame_button == true)
					$this->tpl[0]->parse("SectPrevFrameButton", false);
				else
					$this->tpl[0]->set_var("SectPrevFrameButton", "");

				if ($EndPage >= $totpage)
					$this->tpl[0]->set_var("hidden", "hidden");
				else
					$this->tpl[0]->set_var("hidden", "");

				$this->tpl[0]->set_var("page", ($FrameOffSet + 1) * $this->PagePerFrame + 1);
				$this->tpl[0]->set_var("tot_page", $totpage);
				$this->tpl[0]->set_var("rec_per_frame", $this->PagePerFrame);
                $this->tpl[0]->set_var("totrec", $this->num_rows);
				
				if($this->show_frame_button == true)
					$this->tpl[0]->parse("SectNextFrameButton", false);
				else
					$this->tpl[0]->set_var("SectNextFrameButton", "");
			}
			else
			{
				$this->tpl[0]->set_var("records_per_page", $this->records_per_page);
				for ($i = 1; $i <= $totpage; $i++)
				{
					$this->tpl[0]->set_var("page", $i);
					if ($i == $this->page)
					{
						$this->tpl[0]->parse("SectPageSelected", false);
						$this->tpl[0]->set_var("SectPageLink", "");
					}
					else
					{
						$this->tpl[0]->parse("SectPageLink", false);
						$this->tpl[0]->set_var("SectPageSelected", "");
					}
					$this->tpl[0]->parse("SectPage", true);
				}
			}

			if($this->with_choice && $totpage > 1)
			{
				$this->tpl[0]->set_var("records_per_page", $this->records_per_page);
				$this->tpl[0]->set_var("page", $this->page);
				$this->tpl[0]->set_var("tot_page", $totpage);
                $this->tpl[0]->set_var("totrec", $this->num_rows);
				$this->tpl[0]->parse("SectChoice", false);
			} else {
				$this->tpl[0]->set_var("SectChoice", "");
			}

			$this->tpl[0]->set_var("records_per_page", $this->records_per_page);
            
            if($this->with_totelem) {
                $this->tpl[0]->set_var("totelem", $this->num_rows);
                $this->tpl[0]->parse("SectTotElem", false);
            } else {
                $this->tpl[0]->set_var("SectTotElem", "");
            }
            
			if ($this->page > 1 && $this->display_prev)
			{
				$this->tpl[0]->set_var("page", ($this->page - 1));
				$this->tpl[0]->set_var("hidden", "");
			}
			else
			{
				$this->tpl[0]->set_var("page", $this->page);
				$this->tpl[0]->set_var("hidden", "hidden");
			}
			$this->tpl[0]->parse("SectPrevButton", false);

			if ($this->page > 1 && $this->display_first)
			{
				$this->tpl[0]->set_var("page", 1);
				$this->tpl[0]->set_var("hidden", "");
			}
			else
			{
				$this->tpl[0]->set_var("page", $this->page);
				$this->tpl[0]->set_var("hidden", "hidden");
			}
			$this->tpl[0]->parse("SectFirstButton", false);


			if ($this->page < $totpage && $this->display_next)
			{
				$this->tpl[0]->set_var("page", ($this->page + 1));
				$this->tpl[0]->set_var("hidden", "");
			}
			else
			{
				$this->tpl[0]->set_var("page", $this->page);
				$this->tpl[0]->set_var("hidden", "hidden");
			}
			$this->tpl[0]->parse("SectNextButton", false);

			if ($this->page < $totpage && $this->display_last)
			{
				$this->tpl[0]->set_var("page", $totpage);
				$this->tpl[0]->set_var("hidden", "");
				$this->tpl[0]->parse("SectLastButton", false);
			}
			else
			{
				$this->tpl[0]->set_var("page", $this->page);
				$this->tpl[0]->set_var("hidden", "hidden");
				$this->tpl[0]->set_var("SectLastButton", "");
			}
			
		}
		else
		{
			$this->tpl[0]->set_var("SectPrevFrameButton", "");
			$this->tpl[0]->set_var("SectNextFrameButton", "");
			$this->tpl[0]->set_var("SectFirstButton", "");
			$this->tpl[0]->set_var("SectLastButton", "");
			$this->tpl[0]->set_var("SectPrevButton", "");
			$this->tpl[0]->set_var("SectNextButton", "");
			$this->tpl[0]->set_var("SectPage", "");
			$this->tpl[0]->parse("SectNoPage", false);
		}
		$this->process_selector(); // do at last so variables have the correct values

		return $this->tplParse($output_result);
	}

	function process_selector()
	{
		if ($this->nav_display_selector && count($this->nav_selector_elements))
		{
			$this->tpl[0]->set_var("page", $this->page);
			foreach ($this->nav_selector_elements as $key => $value)
			{
				$this->tpl[0]->set_var("records_per_page", $value);
				if ($value == $this->records_per_page)
				{
					$this->tpl[0]->parse("SectSelectorPageSelected", false);
					$this->tpl[0]->set_var("SectSelectorPageLink", "");
				}
				else
				{
					$this->tpl[0]->parse("SectSelectorPageLink", false);
					$this->tpl[0]->set_var("SectSelectorPageSelected", "");
				}
				$this->tpl[0]->parse("SectSelectorPage", true);
			}
			reset($this->nav_selector_elements);
            if($this->nav_selector_elements_all) {
                $this->tpl[0]->set_var("totelem", $this->num_rows);
                $this->tpl[0]->parse("SectSelectorPageAll", false);
            }
			$this->tpl[0]->parse("SectSelector", false);
		}
		else
			$this->tpl[0]->set_var("SectSelector", "");
	}
}