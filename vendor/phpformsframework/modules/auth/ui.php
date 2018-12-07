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


/***
 * Area Restricted Blocchi di layout aggiuntivi
 */
function on_load_section_multidomain($page, $tpl, $attr) {
    $cm = cm::getInstance();

    $attr["location_default"] = "multidomain";

    if($cm::env("MOD_AUTH_MULTIDOMAIN")) {
        //if(!$ID_domain)
        //	$host_class = " hidden";

        //$tpl->set_var("host_class", $cm->oPage->frameworkCSS->getClass($framework_css["fullbar"]["nav"]["left"]) . $host_class);
        //$tpl->set_var("host_name", get_session("Domain"));
        //$tpl->set_var("host_icon", $cm->oPage->frameworkCSS->get("external-link", "icon-tag"));

        $field = ffField::factory($page);
        $field->id = "accounts";
        $field->base_type = "Number";
        $field->widget = "actex";
        $field->actex_update_from_db = true;
        $field->multi_select_one_label = ffTemplate::_get_word_by_code("master_domain");
        $field->source_SQL = "SELECT ID, nome FROM " . CM_TABLE_PREFIX . "mod_security_domains ORDER BY nome";
        $mod_sec_setparams = $cm->router->getRuleById("mod_sec_setparams");
        if($mod_sec_setparams->reverse) {
            $field->actex_on_change  = "function(obj, old_value, action) {
                    if(action == 'change') {
                        jQuery.get('" . $mod_sec_setparams->reverse . "?accounts=' + obj.value, function(data) {
                            if(data['id'] > 0) {
                                jQuery('#domain-title').text(data['name']);
                                jQuery('#domain-title').attr('href', 'http://' + data['name']);
                                jQuery('#domain-title').parent().removeClass('hidden');
                            } else {
                                jQuery('#domain-title').parent().addClass('hidden');
                            }
                            jQuery('body').addClass('loading');
                            window.location.reload();
                        });
                    }
                }";
        } else {
            $field->actex_on_change  = "function(obj, old_value, action) {
                    if(action == 'change') {
                        if(obj.value > 0) {
                            window.location.href = ff.urlAddParam(window.location.href, 'accounts', obj.value);
                        } else {
                            window.location.href = ff.urlAddParam(window.location.href, 'accounts').replace('accounts&', '');
                        }
                    }					
                }";
        }
        $field->value = new ffData($ID_domain, "Number");
        $field->parent_page = array(&$page);
        $field->db = array( new ffDB_Sql());
        $tpl->set_var("domain_switch", $field->process());

    }
}
function on_load_section_accountpanel($page, $tpl, $attr)
{
    $cm = cm::getInstance();

    $attr["location_default"] = "accountpanel";

    on_load_section_account($page, $tpl, $attr);
}
function on_load_section_account($page, $tpl, $attr)
{
    $cm = cm::getInstance();

    $framework_css = mod_auth_get_framework_css();
    if(!$attr["location_default"]) {
        $attr["location_default"] = "account";
    }


    $tpl->set_var("toggle_properties", $framework_css["collapse"]["action"]);
    $tpl->set_var("img_class", $cm->oPage->frameworkCSS->getClass($framework_css["image"]));
    $tpl->set_var("panel_class", $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["container"]));
    $tpl->set_var("panel_body_class", $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["body"]["def"]));
    $tpl->set_var("panel_img_class", $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["body"]["img"]));
    $tpl->set_var("panel_desc_class", $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["body"]["desc"]));
    $tpl->set_var("panel_links_class", $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["body"]["links"]));
    $tpl->set_var("panel_footer_class", $cm->oPage->frameworkCSS->getClass($framework_css["dropdown"]["footer"]));

    $tpl->set_var("list_group_class", $framework_css["list"]["container"]);
    $tpl->set_var("list_group_horizontal_class", $framework_css["list"]["horizontal"]);
    $tpl->set_var("list_group_item_class", $framework_css["list"]["item"]);

    $username = Auth::get("user")->username;

    if(cm::env("MOD_AUTH_USER_AVATAR")) {
        $tpl->set_var("avatar", Auth::getUserAvatar(cm::env("MOD_AUTH_USER_AVATAR")));
        $tpl->parse("SectUserAvatar", false);
        $tpl->parse("SectAvatar", false);
    }

    $tpl->set_var("account", ffCommon_specialchars($username));


    if(is_array($cm->modules["restricted"]["sections"][$attr["location_default"]]["elements"])) {
        $framework_css["actions"] = $framework_css["dropdown"]["actions"];
        $res_navbar = $cm->modules["restricted"]["obj"]->parseMenu(
            $tpl
            , $cm->modules["restricted"]["sections"][$attr["location_default"]]
            , array(
                "framework_css" => $framework_css
            )
        );
        if(is_array($res_navbar["count_position"]) && count($res_navbar["count_position"])) {
            foreach($res_navbar["count_position"] AS $position_name => $position_count) {
                $tpl->parse("Sect" . $position_name, false);
            }
        }
    }

}
