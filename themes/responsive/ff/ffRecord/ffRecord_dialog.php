<?php
/**
 * VGallery: CMS based on FormsFramework
 * Copyright (C) 2004-2015 Alessandro Stucchi <wolfgan@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @package VGallery
 *  @subpackage core
 *  @author Alessandro Stucchi <wolfgan@gmail.com>
 *  @copyright Copyright (c) 2004, Alessandro Stucchi
 *  @license http://opensource.org/licenses/gpl-3.0.html
 *  @link https://github.com/wolfgan43/vgallery
 */

frameworkCSS::extend(array(
            "records" => array(
                "default" => array(
                    "field" => array(
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
                    )
                    , "required" => "*"
                )
                , "inline" => array(
                    "field" => array(
                        "container" => array(
                            "class" => null
                            , "form" => "row-inline"
                        )
                        , "label-wrap" => array(
                            "class" => null
                            , "col" => array(
                                "xs" => 12
                                , "sm" => 12
                                , "md" => 4
                                , "lg" => 3
                            )
                        )
                        , "label" => array(
                            "class" => null
                            , "form" => "label-inline"
                        )
                        , "control-wrap" => array(
                            "class" => null
                            , "col" => array(
                                "xs" => 12
                                , "sm" => 12
                                , "md" => 8
                                , "lg" => 9
                            )
                        )
                        , "control" => array(
                            "form" => "control"
                        )
                    )
                    , "required" => "*"
                )
            )
            , "outer_wrap" => array(
                "class" => null
                , "dialog" => "window"
            )
			, "component" => array(
                "inner_wrap" => array(
                    "dialog" => "container"
                )
                , "header_wrap" => array(
                    "class" => "d-block"
                    , "dialog" => "header"
                )
                , "body_wrap" => array(
                    "dialog" => "body"
                )
                , "footer_wrap" => array(
                    "class" => "d-block"
                    , "dialog" => "footer"
                )
                , "grid" => false        //false OR array(xs, sm, md, lg) OR 'row' OR 'row-fluid'
			)
			, "title" => array(
				"class" => null
				, "dialog" => "title"
			)
            , "description" => array(
                "class" => null
                , "dialog" => "sub-title"
            )
            , "title-alt" => array(
				"class" => null
				, "dialog" => "title"
			)
            , "description-alt" => array(
                "class" => "mb-2"
                , "dialog" => "sub-title"
            )

			, "group" => array(
				"container" => array(
				    "class" => null
                )
				, "title" => array(
					"class" => "null"
				)
                , "description" => array(
                    "class" => null
                    //, "callout" => "info"
                )
			)
            , "footer" => array(
                "def" => array(
                    "class" => "d-flex"
                )
                , "require-note" => array(
                    "class" => "align-self-center"
                )
                , "actions" => array(
                    "class" => "ml-auto actions"
                )
            )
			/*, "record" => array(
				"row" => true
			)*/
			/*, "field" => array(
				"label" => array(
					"col" => null
				)
				, "control" => array(
					"col" => null
				)
			)
			, "field-inline" => array(
				"label" => array(
					"col" => array(
						"xs" => 0
						, "sm" => 0
						, "md" => 12
						, "lg" => 4
					)
				)
				, "control" => array(
					"col" => array(
						"xs" => 12
						, "sm" => 12
						, "md" => 12
						, "lg" => 8
					)
                    , "form" => "control"
				)
			)*/
            , "field" => null

			, "error" => array(
				"class" => "error"
				, "callout" => "danger"
			)
			, "widget" => array(
				"tab" => array(
					"menu" => array(
						"class" => null
						//, "tab" => null //menu OR menu-vertical OR menu-vertical-right
						, "wrap_menu" => null	// null OR array(xs, sm, md, lg)
						, "wrap_pane" => null	// null OR array(xs, sm, md, lg)
					)
					, "menu-item" => array(
						"class" => null
						, "tab" => "menu-item"
					)
					, "pane" => array(
						"class" => null
						, "tab" => "pane"
					)
					, "pane-item" => array(
						"class" => null
						, "tab" => "pane-item" // pane-item-effect OR pane-item
					)
				)
			)
	), "ffRecord_dialog");

class ffRecord_dialog extends ffRecord_html {
    var $buttons_options		= array(
											"insert" => array(
														  "display" => true
														, "index" 	=> 1
														, "obj" 	=> null
														, "label" 	=> null
                                                        , "aspect"  => "link"
                                                        , "icon"	=> null
                                                        , "activebuttons" => true
											  				)
										  , "update" => array(
														  "display" => true
														, "index" 	=> 2
														, "obj" 	=> null
														, "label" 	=> null
                                                        , "aspect"  => "link"
                                                        , "icon"	=> null
                                                        , "activebuttons" => true
											  				)

										  , "delete" => array(
														  "display" => true
														, "index" 	=> 1
														, "obj" 	=> null
														, "label" 	=> null
                                                        , "aspect"  => "link"
                                                        , "icon"	=> null
                                                        , "activebuttons" => true
											  				)

										  , "cancel" => array(
														  "display" => true
														, "index" 	=> 0
														, "obj" 	=> null
														, "label" 	=> null
                                                        , "aspect"  => "link"
                                                        , "icon"	=> null
											  				)
										  , "cursor_first" => array(
														  "display" => true
														, "index" 	=> 4
														, "obj" 	=> null
														, "label" 	=> ""
								                        , "aspect"  => "button"
														, "newicon" => "NewIcon_firstl"
															)
										  , "cursor_prev" => array(
														  "display" => true
														, "index" 	=> 3
														, "obj" 	=> null
														, "label" 	=> ""
								                        , "aspect"  => "button"
														, "newicon" => "NewIcon_left"
															)
										  , "cursor_next" => array(
														  "display" => true
														, "index" 	=> 0
														, "obj" 	=> null
														, "label" 	=> ""
								                        , "aspect"  => "button"
														, "newicon" => "NewIcon_right"
															)
										  , "cursor_last" => array(
														  "display" => true
														, "index" 	=> -1
														, "obj" 	=> null
														, "label" 	=> ""
								                        , "aspect"  => "button"
														, "newicon" => "NewIcon_lastr"
															)
										);
    /**
     * Inizializza i controlli di default del record
     * Al momento consistono nei pulsanti insert, update, delete e cancel
     */
    function initControls()
    {
        if ($this->hide_all_controls) {
            return;
        }

        if ($this->cursor_dialog && $this->record_exist)
        {
            // -------------
            //  FIRST

            $tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->getTheme());
            $tmp->id 			= "ActionButtonFirst";
            $tmp->label 		= $this->buttons_options["cursor_first"]["label"];
            $tmp->aspect 		= $this->buttons_options["cursor_first"]["aspect"];
            $tmp->action_type 	= "submit";
            $tmp->frmAction		= ($this->buttons_options["cursor_first"]["frmAction"] ? $this->buttons_options["cursor_first"]["frmAction"] : "first");

            if ($this->buttons_options["cursor_first"]["jsaction"])
                $tmp->jsaction = $this->buttons_options["cursor_first"]["jsaction"];
            else
                $tmp->jsaction = "javascript:ff.ffRecord.cursor.first('[[XHR_CTX_ID]]');";

            if (isset($this->buttons_options["cursor_first"]["class"]))
                $tmp->class			= $this->buttons_options["cursor_first"]["class"];
            else
                $tmp->class			= "noactivebuttons";

            if ($this->buttons_options["cursor_first"]["image"])
                $tmp->image = $this->buttons_options["cursor_first"]["image"];

            if ($this->buttons_options["cursor_first"]["newicon"])
                $tmp->newicon = $this->buttons_options["cursor_first"]["newicon"];

            $this->addActionButton(	  $tmp
                , $this->buttons_options["cursor_first"]["index"]);

            // -------------
            //  PREV

            $tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->getTheme());
            $tmp->id 			= "ActionButtonPrev";
            $tmp->label 		= $this->buttons_options["cursor_prev"]["label"];
            $tmp->aspect 		= $this->buttons_options["cursor_prev"]["aspect"];
            $tmp->action_type 	= "submit";
            $tmp->frmAction		= ($this->buttons_options["cursor_prev"]["frmAction"] ? $this->buttons_options["cursor_prev"]["frmAction"] : "prev");

            if ($this->buttons_options["cursor_prev"]["jsaction"])
                $tmp->jsaction = $this->buttons_options["cursor_prev"]["jsaction"];
            else
                $tmp->jsaction = "javascript:ff.ffRecord.cursor.prev('[[XHR_CTX_ID]]');";

            if (isset($this->buttons_options["cursor_prev"]["class"]))
                $tmp->class			= $this->buttons_options["cursor_prev"]["class"];
            else
                $tmp->class			= "noactivebuttons";

            if ($this->buttons_options["cursor_prev"]["image"])
                $tmp->image = $this->buttons_options["cursor_prev"]["image"];

            if ($this->buttons_options["cursor_prev"]["newicon"])
                $tmp->newicon = $this->buttons_options["cursor_prev"]["newicon"];

            $this->addActionButton(	  $tmp
                , $this->buttons_options["cursor_prev"]["index"]);
        }

        // PREPARE DEFAULT BUTTONS
        if ($this->buttons_options["cancel"]["display"])
        {
            if ($this->buttons_options["cancel"]["obj"] === null)
            {
                if($this->buttons_options["cancel"]["label"] === null)
                    $this->buttons_options["cancel"]["label"] = ffTemplate::_get_word_by_code("ffRecord_close");

                $tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->getTheme());
                $tmp->id 			= "ActionButtonCancel";
                $tmp->label 		= $this->buttons_options["cancel"]["label"];
                $tmp->aspect 		= $this->buttons_options["cancel"]["aspect"];
                $tmp->action_type 	= "submit";
                $tmp->frmAction		= "close";

                if (isset($this->buttons_options["cancel"]["class"]))
                    $tmp->class	= $this->buttons_options["cancel"]["class"];
                $tmp->icon  = $this->buttons_options["cancel"]["icon"];


                /*				$tmp->class	= $this->parent[0]->frameworkCSS->get("cancel", $this->buttons_options["cancel"]["aspect"]);
                                if ($this->buttons_options["cancel"]["class"])
                                    $tmp->class	.= " " . $this->buttons_options["cancel"]["class"];
                */
                /*                if (isset($this->buttons_options["cancel"]["class"]))
                                    $tmp->class     = $this->buttons_options["cancel"]["class"];
                */
                //$this->addDefaultButton("cancel", $tmp);

                $this->buttons_options["cancel"]["obj"] =& $tmp;
            }
        }

        parent::initControls();

        if ($this->cursor_dialog && $this->record_exist)
        {
            // -------------
            //  NEXT

            $tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->getTheme());
            $tmp->id 			= "ActionButtonNext";
            $tmp->label 		= $this->buttons_options["cursor_next"]["label"];
            $tmp->aspect 		= $this->buttons_options["cursor_next"]["aspect"];
            $tmp->action_type 	= "submit";
            $tmp->frmAction		= ($this->buttons_options["cursor_next"]["frmAction"] ? $this->buttons_options["cursor_next"]["frmAction"] : "next");

            if ($this->buttons_options["cursor_next"]["jsaction"])
                $tmp->jsaction = $this->buttons_options["cursor_next"]["jsaction"];
            else
                $tmp->jsaction = "javascript:ff.ffRecord.cursor.next('[[XHR_CTX_ID]]');";

            if (isset($this->buttons_options["cursor_next"]["class"]))
                $tmp->class			= $this->buttons_options["cursor_next"]["class"];
            else
                $tmp->class			= "noactivebuttons";

            if ($this->buttons_options["cursor_next"]["image"])
                $tmp->image = $this->buttons_options["cursor_next"]["image"];

            if ($this->buttons_options["cursor_next"]["newicon"])
                $tmp->newicon = $this->buttons_options["cursor_next"]["newicon"];

            $this->addActionButton(	  $tmp
                , $this->buttons_options["cursor_next"]["index"]);

            // -------------
            //  LAST

            $tmp = ffButton::factory(null, $this->disk_path, $this->site_path, $this->page_path, $this->getTheme());
            $tmp->id 			= "ActionButtonLast";
            $tmp->label 		= $this->buttons_options["cursor_last"]["label"];
            $tmp->aspect 		= $this->buttons_options["cursor_last"]["aspect"];
            $tmp->action_type 	= "submit";
            $tmp->frmAction		= ($this->buttons_options["cursor_last"]["frmAction"] ? $this->buttons_options["cursor_last"]["frmAction"] : "last");

            if ($this->buttons_options["cursor_last"]["jsaction"])
                $tmp->jsaction = $this->buttons_options["cursor_last"]["jsaction"];
            else
                $tmp->jsaction = "javascript:ff.ffRecord.cursor.last('[[XHR_CTX_ID]]');";

            if (isset($this->buttons_options["cursor_last"]["class"]))
                $tmp->class			= $this->buttons_options["cursor_last"]["class"];
            else
                $tmp->class			= "noactivebuttons";

            if ($this->buttons_options["cursor_last"]["image"])
                $tmp->image = $this->buttons_options["cursor_last"]["image"];

            if ($this->buttons_options["cursor_last"]["newicon"])
                $tmp->newicon = $this->buttons_options["cursor_last"]["newicon"];

            $this->addActionButton(	  $tmp
                , $this->buttons_options["cursor_last"]["index"]);
        }
    }


    function setWidthComponent($resolution_large_to_small)
    {

    }

}