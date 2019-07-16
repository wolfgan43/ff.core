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

class ffField_select extends ffField_html {
    protected function processControlTag() {
        $control                            = null;
        $this->getControlTagData();

        $properties                         = $this->getProperties();
        $control                            = '<select ' . $properties . '>' . $this->processSelectOptions() . '</select>';

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

}

ffField::$Select = 'ffField_select';