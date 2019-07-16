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

namespace ff;

frameworkCSS::extend(array(
        "types" => array(
            "default" => array(
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
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "select-custom" => array(
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
                    "class" => "custom-select"
                    , "form" => "control"
                )
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "checkbox" => array(
                "container" => array(
                    "class" => null
                    , "form" => "row-check"
                )
                , "label-wrap" => false
                , "label" => array(
                    "class" => null
                    , "form" => "label-check"
                )
                , "control-wrap" => false
                , "control" => array(
                    "form" => "control-check"
                )
                , "prototype" => "[CONTROL][LABEL]"
            )
            , "radio" => array(
                "container" => array(
                    "class" => null
                    , "form" => "row-check"
                )
                , "label-wrap" => false
                , "label" => array(
                    "class" => null
                    , "form" => "label-check"
                )
                , "control-wrap" => false
                , "control" => array(
                    "form" => "control-check"
                )
                , "prototype" => "[CONTROL][LABEL]"
            )
            , "label" => array(
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
                    "form" => "control-plaintext"
                )
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "file" => array(
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
                    "form" => "control-file"
                )
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "file-custom" => array(
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
                    "class" => "custom-file-input"
                    , "form" => false
                )
                , "prototype" => '[LABEL] <div class="custom-file">[CONTROL] <label class="custom-file-label">[PLACEHOLDER]</label></div>[DESCRIPTION]'
            )
            , "file-thumb" => array( //todo: da finire
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
                    "class" => null
                    , "form" => false
                )
                , "prototype" => '[LABEL] <div class="input-file-container">[CONTROL] <label class="input-file-trigger">[PLACEHOLDER]</label></div>[DESCRIPTION]'
            )
            , "picture" => array(
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
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "currency" => array(
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
                    , "util" => "align-right"
                )
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "number" => array(
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
                    , "util" => "center"
                )
                , "prototype" => "[LABEL][CONTROL]"
            )
            , "range" => array(
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
                , "prototype" => "[LABEL][CONTROL]"
            )

        )
        , "outer_wrap" => false
        , "field" => null
        , "label-for" => true
        , "label-tag" => "label"
        , "description-tag" => "small"
        , "required" => "*"
        , "input-group" => array(
            "class" => null
            , "form" => array("group")
        )
        , "exception" => array(
            "radio-multi" => array(
                "field" => array(
                    "container" => array(
                        "class" => "mt-3 mb-2"
                        , "form" => false
                    )
                    , "label" => false
                    , "prototype" => '<h5>[LABEL] [DESCRIPTION]</h5> [CONTROL]'
                )
                , "label-for" => false
                , "label-tag" => false
                , "description-tag" => "small"
            )
        )
    ), "ffField");



class ffField_html extends ffField_base {
    public $framework_css			        = null;
    private $type					        = null;
    public $size					        = null; //small, normal, large
    public $url                             = null; //usato nelle grid
    public $url_ajax                        = null; //usato nelle grid
    public $url_parsed                      = null; //usato nelle grid
    public $label_encode_entities           = null; //usato nelle grid
    public $file_showfile_plugin           = null; //usato nelle record
    public $actex_use_own_session           = null; //usato nelle record
    public $file_widget_preview           = null; //usato nelle record
    public $file_sortable           = null; //usato nelle record

    public function __construct($type = null) {
        parent::__construct();

        $this->framework_css = frameworkCSS::findComponent("ffField");

        if($type) {
            $this->enchant($type);
        }
    }

    function setWidthComponent($resolution_large_to_small)
    {
        if(is_array($resolution_large_to_small) || is_numeric($resolution_large_to_small)) {
            $this->framework_css["outer_wrap"]["col"] = frameworkCSS::setResolution($resolution_large_to_small);
        } elseif(strlen($resolution_large_to_small)) {
            $this->framework_css["outer_wrap"]["row"] = $resolution_large_to_small;
        }
    }

    function setWidthLabel($resolution_large_to_small, $reverse_control_class = true, $align = "right")
    {
        $this->framework_css["label-wrap"]["col"] = frameworkCSS::setResolution($resolution_large_to_small);
        if($align) {
            $this->framework_css["label-wrap"]["util"] = array(
                "align-" . $align
            );
        }
        if($reverse_control_class && is_array($this->framework_css["label-wrap"]["col"])) {
            $this->framework_css["control-wrap"]["col"] = frameworkCSS::setResolution($this->framework_css["label-wrap"]["col"], true);

            /*			$this->framework_css["control"]["col"] = array(
                            "xs" => ($this->framework_css["label"]["col"]["xs"] == 12 ? 12 : 12 - $this->framework_css["label"]["col"]["xs"])
                            , "sm" => ($this->framework_css["label"]["col"]["sm"] == 12 ? 12 : 12 - $this->framework_css["label"]["col"]["sm"])
                            , "md" => ($this->framework_css["label"]["col"]["md"] == 12 ? 12 : 12 - $this->framework_css["label"]["col"]["md"])
                            , "lg" => ($this->framework_css["label"]["col"]["lg"] == 12 ? 12 : 12 - $this->framework_css["label"]["col"]["lg"])
                        );
            */
        }
    }

    function setWidthControl($resolution_large_to_small)
    {
        $this->framework_css["control-wrap"]["col"] = frameworkCSS::setResolution($resolution_large_to_small);
    }

    private function enchant($type = null) {
        $this->type = ($type
            ? $type
            : $this->findType()
        );

        switch($this->type) {
            case "file":
            case "file-custom":
                //$this->control_type = false;
                break;
            case "radio":
                //$this->control_type = false;
                break;
            case "select-multi":
                $this->extended_type = "Selection";
                $this->properties["multiple"] = null;
                //$this->control_type = false;
                break;
            case "select":
            case "select-custom":
                $this->extended_type = "Selection";
                //$this->control_type = false;
                break;
            case "textarea":
                $this->extended_type = "Text";
                //$this->control_type = false;
                break;
            case "label":
                $this->control_type = false;
                break;
            case "readonly":
                $this->properties["readonly"] = null;
                $this->type = "text";
                //$this->control_type = false;
                break;
            case "disabled":
                $this->properties["disabled"] = null;
                $this->type = "text";
                //$this->control_type = false;
                break;
            case "color":
                $this->addValidator("htmlcolor");
                //$this->control_type = false;
                break;
            case "checkbox":
                $this->base_type = "Number";
                $this->extended_type = "Boolean";
                if(!$this->checked_value) {
                    $this->checked_value = new ffData("1", "Number");
                }
                if(!$this->unchecked_value) {
                    $this->unchecked_value = new ffData("0", "Number");
                }
                //$this->control_type = false;
                break;
            case "date":
                $this->base_type = "Date";
                $this->addValidator("date");
                //$this->control_type = false;
                break;
            case "email":
                $this->addValidator("email");
                //$this->control_type = false;
                break;
            case "month":
                if(0) {
                    $this->base_type = "Month"; //todo: da gestire
                }
                //$this->control_type = false;
                break;
            case "week":
                if(0) {
                    $this->base_type = "Week"; //todo: da gestire
                }
                //$this->control_type = false;
                break;
            case "time":
                $this->base_type = "Time";
                $this->addValidator("time");
                //$this->control_type = false;
                break;
            case "datetime":
                $this->base_type = "DateTime";
                $this->addValidator("datetime");
                $this->type = "datetime-local";
                //$this->control_type = false;
                break;
            case "number":
                $this->base_type = "Number";
                $this->addValidator("number");
                //$this->control_type = false;
                break;
            case "currency":
                $this->base_type = "Number";
                $this->app_type = "Currency";
                $this->fixed_post_content= "&euro;";

                //$this->control_type = false;
                break;
            case "password":
                $this->extended_type = "Password";
                $this->crypt_method = "mysql_password";
                $this->addValidator("password");
                //$this->control_type = false;
                break;
            case "range":
                //$this->control_type = false;
                break;
            case "search":
                //$this->control_type = false;
                break;
            case "tel":
                //$this->control_type = false;
                $this->addValidator("tel");
                break;
            case "url":
                //$this->control_type = false;
                $this->addValidator("url");
                break;
            case "input":
            case "text":
                //$this->control_type = false;
                break;

            case "html":
                $this->type = "empty";
                break;
            case "code":
                break;
            case "empty":
                break;
            case "link":
                break;
            case "picture":
                break;
            default:
        }
    }

    private function findType() {
        $type = null;
        switch($this->control_type) {
            case "label":
            case "textarea":
            case "checkbox":
            case "radio":
            case "file":
            case "picture":
                $type                       = $this->control_type;
                break;
            case "combo":
                $type                       = "select";
                break;
            case "list":
                $type                       = "select-multi";
                break;
            default:
        }

        if(!$type) {
            switch($this->extended_type) {
                case "Text":
                    $type                       = "textarea";
                    break;
                case "Password":
                    $type                       = "password";
                    break;
                case "Integer":
                    $type                       = "number";
                    break;
                case "Currency":
                    $type                       = "currency";
                    break;
                case "Float":
                    $type                       = "number";
                    break;
                //               case "DateTime":

                case "Date":
                    $type                       = "date";
                    break;
//                case "Year":
//                case "Month":
//                case "Day":
//                case "Time":
//                case "Hours":
//                case "Minutes":
//                case "Seconds":
                case "Boolean":
                    $type                       = "checkbox";
                    break;
//                case "Flags":
                case "Selection":
                    $type                       = "select";
                    break;
                case "Email":
                    $type                       = "email";
                    break;
                case "Tel":
                    $type                       = "tel";
                    break;
                case "HTML":
                    $type                       = "empty";
                    break;
                case "File":
                    $type                       = "file";
                    break;
                default:
            }
        }
        if(!$type) {
            switch ($this->base_type) {
                case "Number":
                    $type                       = "number";
                    break;
//                case "DateTime":

                case "Date":
                    $type                       = "date";
                    break;
//                case "Time":

                case "Binary":
                    //                   break;
                case "Text":
                default:
                    $type                       = "text";
            }
        }

        $this->type = $type;
    }

    public function widget_process($id = null, $value = null)
    {
        $this->framework_css["field"]["container"]["class"] = $this->widget;

        return  parent::widget_process($id, $value);
    }

    function pre_process($reset = false, $value = null) {
        if ($this->pre_processed && !$reset)
            return;

        if(!$this->type) {
            $this->enchant();
        }
        switch($this->type) {
            case "radio":
                if(is_array($this->multi_pairs)) {
                    $this->extended_type = "Selection";
                    switch($this->multi_pairs[0][0]->data_type) {
                        case "Number":
                            $this->base_type = "Number";
                            break;
                        default:
                    };

                } elseif($this->base_type == "Text") {
                    $this->base_type = "Number";
                    $this->extended_type = "Boolean";
                    if(!$this->checked_value) {
                        $this->checked_value = new ffData("1", "Number");
                    }
                    if(!$this->unchecked_value) {
                        $this->unchecked_value = new ffData("0", "Number");
                    }
                }
                break;
            default:

        }

        if ($this->placeholder === true) {
            $this->placeholder = ffCommon_specialchars($this->label);
        }

        parent::pre_process($reset, $value);
    }
    public function process($output_result = false) {
        $this->pre_process();

        $buffer = ($this->widget
            ? $this->widget_process()
            : $this->parse()
        );

        ffPage::getInstance()->tplAddJs("ff.ffField");

        if ($output_result) {
            echo $buffer;
            return true;
        } else {
            return $buffer;
        }
    }

    private function processFrameworkCSS() {
        $this->framework_css["field"]       = ($this->framework_css["types"][$this->type]
            ? array_replace_recursive($this->framework_css["field"], $this->framework_css["types"][$this->type])
            : array_replace_recursive($this->framework_css["types"]["default"], (array) $this->framework_css["field"])
        );
        switch ($this->size) {
            case "small":
                $this->framework_css["field"]["control"]["form"] = array("control", "size-sm");
                break;
            case "large":
                $this->framework_css["field"]["control"]["form"] = array("control", "size-lg");
                break;
            default:
        }

        /*if($this->framework_css["user"]) {
            $this->framework_css            = array_replace_recursive($this->framework_css, $this->framework_css["user"]);
        }*/
    }

    public function parse() {
        $this->processFrameworkCSS();

        $control = $this->processControl();
        $label = ($this->display_label
            ? $this->processLabel()
            : ""
        );
        $description =  $this->processDescription();
        $placeholder = $this->placeholder;

        $buffer = $this->processContainer($control, $label, $description, $placeholder);

        if($this->framework_css["outer_wrap"]) {
            $buffer = '<div class="' . $this->parent_page[0]->frameworkCSS->getClass($this->framework_css["outer_wrap"]) . '">'
                . $buffer
                . '</div>'
            ;
        }

        return $buffer;
    }

    private function processContainer($control, $label, $description = null, $placeholder = null) {
        $res                                    = str_replace(
                                                    array(
                                                        "[LABEL]"
                                                        , "[CONTROL]"
                                                        , "[DESCRIPTION]"
                                                        , "[PLACEHOLDER]"
                                                    )
                                                    , array(
                                                        $label
                                                        , $control
                                                        , $description
                                                        , $placeholder
                                                    )
                                                    , $this->framework_css["field"]["prototype"]
                                                );

        return $this->getWrapperByFrameworkCss("container", $res);
    }
    
    private function processLabel($label = null, $control_id = null, $skip_required = false) {
	    if(!$label)                             { $label = $this->label; }

        if($label) {
	        $label_properties                   = $this->label_properties;
            if ($this->required && $this->framework_css["required"] && !$skip_required) {
                $required_symbol                = $this->framework_css["required"];
            }

            if(!$control_id) {
                if ($this->parent !== null && strlen($this->parent[0]->getIDIF())) {
                    $parent_id                  = $this->parent[0]->getPrefix();
                }

                $control_id                     = $parent_id . $this->id;
            }

            if($this->framework_css["label-for"]) {
                $label_properties["for"]        = $control_id;
            }

            $res                                = $this->getWrapperByFrameworkCss("label"
                                                    , ($this->encode_label
                                                        ? ffCommon_specialchars($label) . $required_symbol
                                                        : $label . $required_symbol
                                                    )
                                                    , $this->framework_css["label-tag"]
                                                    , $this->getProperties($label_properties)
                                                );
            $res                                = $this->getWrapperByFrameworkCss("label-wrap", $res);
        }

        return $res;
    }

    private function processControl() {
        $res                                    = $this->processControlAddon();

	    if(strpos($this->framework_css["field"]["prototype"], "[DESCRIPTION]") === false) {
	        $res .= $this->processDescription();
        }

        return $this->getWrapperByFrameworkCss("control-wrap", $res);
    }

    private function processControlAddon($control = null) {
        if(!$control)                           { $control = $this->processControlTag(); }
        $fixed_pre_content                      = $this->fixed_pre_content;
        $fixed_post_content                     = $this->fixed_post_content;
        if($fixed_pre_content || $fixed_post_content) {
            if ($this->framework_css["input-group"]) {
                if ($fixed_pre_content) {
                    if (preg_match("/<[^<]+>/", $fixed_pre_content, $m) == 0) {
                        $fixed_pre_content      = '<span class="' . $this->parent_page[0]->frameworkCSS->get("control-text", "form") . '">' . $fixed_pre_content . '</span>';
                    }
                    $fixed_pre_content          = '<div class="' . $this->parent_page[0]->frameworkCSS->get("control-prefix", "form") . '">' . $fixed_pre_content . '</div>';
                }

                if ($fixed_post_content) {
                    if (preg_match("/<[^<]+>/", $fixed_post_content, $m) == 0) {
                        $fixed_post_content     = '<span class="' . $this->parent_page[0]->frameworkCSS->get("control-text", "form") . '">' . $fixed_post_content . '</span>';
                    }
                    $fixed_post_content         = '<div class="' . $this->parent_page[0]->frameworkCSS->get("control-postfix", "form") . '">' . $fixed_post_content . '</div>';
                }

                $control                        = '<div class="' . $this->parent_page[0]->frameworkCSS->getClass($this->framework_css["input-group"]) . '">'
                                                    . $fixed_pre_content
                                                    . $control
                                                    . $fixed_post_content
                                                . '</div>';
            } else {
                $control                        = $fixed_pre_content . $control . $fixed_post_content;
            }
        }
        return $control;
    }

    private function processDescription() {
        return ($this->description && $this->framework_css["description-tag"]
            ? '<' . $this->framework_css["description-tag"] . '>' . $this->description . '</' . $this->framework_css["description-tag"] . '>'
            : ''
        );
    }

    private function getWrapperByFrameworkCss($name, $content = null, $tag = null, $properties = null) {
        $class                                  = $this->parent_page[0]->frameworkCSS->getClass($this->framework_css["field"][$name]);
        if(!$tag && $class) {
            $tag = "div";
        }
        return ($content
            ? ($tag
                ? '<' . $tag . ($properties ? ' ' . $properties : '') . ($class ? ' class="' . $class . '"' : '') . '>' . $content . '</' . $tag . '>'
                : $content
            )
            : $class
        );
    }

    private function getControlTagData() {
        //static $data                            = null;

        //if(!$data) {
            /**
             * Load data
             */
            $data["id"]                         = $this->id;
            if ($this->parent !== null && strlen($this->parent[0]->getIDIF())) {
                $data["parent_id"]              = $this->parent[0]->getPrefix();
            }

            $data["value_ori"]                  = (($this->contain_error && $this->error_preserve) || $this->preserve_ori_value
                                                    ? $this->value->ori_value
                                                    : $this->value->getValue($this->get_app_type(), $this->get_locale())
                                                );
            $data["value"]                      = ffCommon_specialchars($data["value_ori"]);
        //}

        return $data;
    }

    private function processControlTag() {
        $type                                   = $this->type;


        $data                                   = $this->getControlTagData();

        $this->properties["name"]               = $data["parent_id"] . $data["id"];
        $this->properties["id"]                 = $data["parent_id"] . $data["id"];
        $this->properties["class"]              = ffPage::getInstance()->frameworkCSS->getClass($this->framework_css["field"]["control"]);
        if ($this->required) {
            $this->properties["required"]       = null;
        }
        if($this->placeholder) {
            $this->properties["placeholder"]    = $this->placeholder;
        }

        switch($type) {
            /*case "image":
                break;
            case "button":
                break;
            case "hidden":
                break;
            case "reset":
                break;
            case "submit":
                break;*/
            case "file":
            case "file-thumb":
            case "file-custom":
                $this->properties["type"]       = "file";
                $this->properties["name"]       = $data["parent_id"] . $data["id"];
                $this->properties["value"]      = $data["value"];
                $control                        = $this->processFile();
                break;
            case "radio":
                $this->properties["type"]       = "radio";
                $this->properties["name"]       = $data["parent_id"] . $data["id"];

                $control                        = $this->processRadio();
                if(!$control) {
                    $this->properties["value"]  = $this->checked_value->getValue($this->get_app_type(), $this->get_locale());

                    $properties                 = $this->getProperties();
                    $control                    = '<input ' . $properties . '/>';
                }
                break;
            case "select-multi":
                $this->properties["multi"]      = null;
            case "select":
            case "select-custom":
            $this->properties["name"]       = $data["parent_id"] . $data["id"];
                $properties                     = $this->getProperties();
                $control                        = '<select ' . $properties . '>' . $this->processSelectOptions() . '</select>';
                break;
            case "textarea":
                $this->properties["name"]       = $data["parent_id"] . $data["id"];
                $properties                     = $this->getProperties();

                $control                        = '<textarea ' . $properties . '>' . $data["value"] . '</textarea>';
                break;
            case "code":
                //$properties                     = $this->getProperties();

                $control                        = '<pre><code>' . str_replace(
                                                        array("\t", "  ")
                                                        , array("&nbsp;&nbsp;", "&nbsp;&nbsp;")
                                                        , nl2br(htmlspecialchars($data["value_ori"]))
                                                ) . '</code></pre>';
                break;
            case "empty":
                $control                        = $data["value_ori"];
                break;
            case "label":
                $this->properties["type"]       = "text";
                $this->properties["readonly"]   = null;
                $this->properties["value"]      = $data["value"];
                $properties                     = $this->getProperties();
                $control                        = '<input ' . $properties . '/>';
                break;
            case "link":
                break;
            case "picture":
                break;
            case "color":
                $this->properties["type"]       = $type;
                $this->properties["name"]       = $data["parent_id"] . $data["id"];
                $this->properties["value"]      = ($data["value"]
                                                    ? $data["value"]
                                                    : "#000000"
                                                );

                $properties                     = $this->getProperties();
                $control                        = '<input ' . $properties . '/>';
                break;
            case "checkbox":
                $this->properties["type"]       = $type;
                $this->properties["name"]       = $data["parent_id"] . $data["id"];
                $this->properties["value"]      = $this->checked_value->getValue($this->get_app_type(), $this->get_locale());

                $properties                     = $this->getProperties();
                $control                        = '<input ' . $properties . '/>';
                break;
            case "date":
            case "datetime-local":
            case "email":
            case "month":
            case "number":
            case "currency":
            case "password":
            case "range":
            case "search":
            case "tel":
            case "time":
            case "url":
            case "week":
            case "input":
            case "text":
                $this->properties["type"]       = $type;
                $this->properties["name"]       = $data["parent_id"] . $data["id"];
                $this->properties["value"]      = $data["value"];

                $properties                     = $this->getProperties();
                $control                        = '<input ' . $properties . '/>';
                break;
            default:
        }

        return $control;
    }




        private function setActiveByTag($value, $attr_name) {
        $res                                = ' value="' . $value . '"';
	    if($this->value->ori_value !== ""
            && $value === $this->value->getValue($this->get_app_type(), $this->get_locale())
        ) {
	        $res                            = " " . $attr_name;
        }

	    return $res;
    }

    private function processSelectOptions($properties = null) {
        $res = array();
        if (is_array($this->recordset) && count($this->recordset)) {
            if($this->multi_select_one) {
                $value = ($this->multi_select_one_val
                    ? $this->multi_select_one_val->getValue($this->get_app_type(), $this->get_locale())
                    : ""
                );
                $label = $this->multi_select_one_label;
                $res[] = '<option' . $properties . $this->setActiveByTag($value, "selected") . '>' . $label . '</option>';
            }
            if ($this->multi_select_noone /*&&
                (!$this->multi_limit_select ||
                    ($this->multi_limit_select && $this->multi_select_noone_val->getValue($this->get_app_type(), $this->get_locale()) == $value->getValue($this->get_app_type(), $this->get_locale()))
                )*/
            ) {
                $value = ($this->multi_select_noone_val
                    ? $this->multi_select_noone_val->getValue($this->get_app_type(), $this->get_locale())
                    : ""
                );
                $label = $this->multi_select_noone_label;
                $res[] = '<option' . $properties . $this->setActiveByTag($value, "selected") . '>' . $label . '</option>';
            }
            foreach ($this->recordset as $key => $item) {
                $value = $item[0]->getValue($this->get_app_type(), $this->get_locale());
                $label = $item[1]->getValue($this->get_app_type(), $this->get_locale());
                $res[] = '<option' . $properties . $this->setActiveByTag($value, "selected") . '>' . $label . '</option>';
            }
        }

        return implode("", $res);
    }

    private function processFile() {
        $properties                 = $this->getProperties();
        $control                    = '<input ' . $properties . '/>';

        return $control;
    }

    private function processRadio() {
        $res = array();

	    if(is_array($this->recordset) && count($this->recordset)) {
	        $control_id = $this->properties["id"];
	        foreach($this->recordset AS $i => $item) {

                $value = $item[0]->getValue($this->get_app_type(), $this->get_locale());
                $label = $item[1]->getValue();

                $this->properties["id"] = $control_id . "_" . $i;
                $this->properties["value"]  = $value;

                $properties                 = $this->getProperties();
                $res[]                      = $this->processContainer('<input ' . $properties . '/>', $this->processLabel($label, $this->properties["id"], true));
            }


            $this->framework_css = array_replace_recursive($this->framework_css, $this->framework_css["exception"]["radio-multi"]);
        }

        return implode("", $res);
    }

}