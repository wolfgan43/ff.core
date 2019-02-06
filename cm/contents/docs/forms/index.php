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
$oRecord = ffRecord::factory($cm->oPage);
$oRecord->type = "default";

$oRecord->addGroup("Col1", array(
    "width" => array(6)
    , "title" => ""
));
$oRecord->addGroup("Col2", array(
    "width" => array(6)
    , "title" => ""
));

$oRecord->title = "Input Types";
$oRecord->description = "Most common form control, text-based input fields. Includes support for all HTML5 types: <code>text, password, datetime, datetime-local, date, month, time, week, number, email, url, search, tel, and color.</code>";
$oRecord->framework_css["component"]["header_wrap"] = false;

    $oField = ffField::factory($cm->oPage);
    $oField->type = "text";
    $oField->label = "Text";
    $oRecord->addContent($oField, "Col1");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "email";
    $oField->label = "Email";
    //$oField->addValidator("email");
    $oRecord->addContent($oField, "Col1");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "password";
    $oField->label = "Password";
    //$oField->extended_type = "Password";
    //$oField->crypt_method = "mysql_password";
    $oField->default_value = new ffData("password");
    $oRecord->addContent($oField, "Col1");

    $oField = ffField::factory($cm->oPage);
    //$oField->type = "text";
    $oField->label = "Placeholder";
    $oField->placeholder = true;
    $oRecord->addContent($oField, "Col1");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "textarea";
    $oField->label = "Text area";
    //$oField->extended_type = "Text";
    $oRecord->addContent($oField, "Col1");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "readonly";
    $oField->label = "Read Only";
    //$oField->properties["readonly"] = null;
    $oField->default_value = new ffData("Readonly value");
    $oRecord->addContent($oField, "Col1");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "disabled";
    $oField->label = "Disabled";
    //$oField->properties["disabled"] = null;
    $oField->default_value = new ffData("Disabled value");
    $oRecord->addContent($oField, "Col1");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "label";
    $oField->label = "Static control";
    //$oField->control_type = "label";
    $oField->default_value = new ffData("email@example.com");
    $oRecord->addContent($oField, "Col1");

    $oField = ffField::factory($cm->oPage);
    //$oField->type = "text";
    $oField->id = "helpingText";
    $oField->label = "Helping text";
    $oField->placeholder = true;
    $oField->description = "A block of help text that breaks onto a new line and may extend beyond one line.";
    $oRecord->addContent($oField, "Col1");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "search";
    $oField->label = "Search";
    $oRecord->addContent($oField, "Col1");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "tel";
    $oField->label = "Tel";
    $oRecord->addContent($oField, "Col1");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "url";
    $oField->label = "Url";
    $oRecord->addContent($oField, "Col1");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "select";
    $oField->label = "Input Select";
   // $oField->extended_type = "Selection";
    $oField->multi_select_one = false;
    $oField->multi_pairs = array(
        array(new ffData("1"), new ffData("1")),
        array(new ffData("2"), new ffData("2")),
        array(new ffData("3"), new ffData("3")),
        array(new ffData("4"), new ffData("4")),
        array(new ffData("5"), new ffData("5"))
    );
    $oRecord->addContent($oField, "Col2");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "select-multi";
    $oField->label = "Multiple Select";
    //$oField->extended_type = "Selection";
    //$oField->properties["multiple"] = null;
    $oField->multi_select_one = false;
    $oField->multi_pairs = array(
        array(new ffData("1"), new ffData("1")),
        array(new ffData("2"), new ffData("2")),
        array(new ffData("3"), new ffData("3")),
        array(new ffData("4"), new ffData("4")),
        array(new ffData("5"), new ffData("5"))
    );
    $oRecord->addContent($oField, "Col2");


    $oField = ffField::factory($cm->oPage);
    $oField->type = "file";
    $oField->label = "Default file input";
    //$oField->control_type = "file";
    $oRecord->addContent($oField, "Col2");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "date";
    $oField->label = "Date";
    //$oField->base_type = "Date";
    $oRecord->addContent($oField, "Col2");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "month";
    $oField->label = "Month";
    $oRecord->addContent($oField, "Col2");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "time";
    $oField->label = "Time";
    //$oField->base_type = "Time";
    $oRecord->addContent($oField, "Col2");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "week";
    $oField->label = "Week";
    $oRecord->addContent($oField, "Col2");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "datetime";
    $oField->label = "Date time";
    //$oField->base_type = "DateTime";
    $oRecord->addContent($oField, "Col2");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "number";
    $oField->label = "Number";
    //$oField->base_type = "Number";
    $oRecord->addContent($oField, "Col2");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "color";
    $oField->label = "Color";
    $oRecord->addContent($oField, "Col2");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "range";
    $oField->label = "Range";
    $oRecord->addContent($oField, "Col2");

$cm->oPage->addContent($oRecord);


$oRecord = ffRecord::factory($cm->oPage);
$oRecord->type = "default";
$oRecord->framework_css["component"]["header_wrap"] = false;

    $oField = ffField::factory($cm->oPage);
    $oField->type = "select-custom";
    //$oField->extended_type = "Selection";
    //$oField->class = "custom-select";
    $oField->multi_pairs = array(
        array(new ffData("One"), new ffData("One")),
        array(new ffData("Two"), new ffData("Two")),
        array(new ffData("Three"), new ffData("Three"))
    );
    $oRecord->addContent($oField, "Col1");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "checkbox";
    //$oField->base_type = "Number";
    //$oField->control_type = "checkbox";
    $oField->label = "Check this custom checkbox";
    //$oField->checked_value = new ffData("0", "Number");
    //$oField->unchecked_value = new ffData("0", "Number");
    $oRecord->addContent($oField, "Col2");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "checkbox";
    //$oField->base_type = "Number";
    //$oField->control_type = "checkbox";
    $oField->label = "Check this custom checkbox";
    //$oField->checked_value = new ffData("1", "Number");
    //$oField->unchecked_value = new ffData("0", "Number");
    $oRecord->addContent($oField, "Col2");


    $oField = ffField::factory($cm->oPage);
    $oField->type = "radio";
    //$oField->base_type = "Number";
    $oField->label = "Label Header";
    $oField->description = "Additional Description";
    //$oField->control_type = "radio";
    //$oField->extended_type = "Selection";
    $oField->multi_pairs = array(
        array(new ffData("1", "Number"), new ffData("Toggle this custom radio")),
        array(new ffData("2", "Number"), new ffData("Or toggle this other custom radio"))
    );
    $oRecord->addContent($oField, "Col2");

    $oField = ffField::factory($cm->oPage);
    $oField->type = "radio";
    //$oField->base_type = "Number";
    //$oField->control_type = "radio";
    $oField->label = "Or toggle this other custom radio";
    //$oField->checked_value = new ffData("1", "Number");
    //$oField->unchecked_value = new ffData("0", "Number");
    $oRecord->addContent($oField, "Col2");

    $oRecord->addGroup("Col1", array(
        "width" => array(6)
        , "title" => "Select Menu"
        , "description" => "Custom <code>&lt;select&gt;</code> menus need only a custom class, <code>.custom-select</code> to trigger the custom styles."
    ));
    $oRecord->addGroup("Col2", array(
        "width" => array(6)
        , "title" => "CheckBoxes and Radios"
    ));
$cm->oPage->addContent($oRecord);



$oRecord = ffRecord::factory($cm->oPage);
$oRecord->title = "Input sizes";
$oRecord->description = "Set heights using classes like <code>.input-lg</code>, and set widths using grid column classes like <code>.col-lg-*.</code>";
$oRecord->type = "default";
$oRecord->framework_css["component"]["header_wrap"] = false;
$oRecord->setWidthComponent(6);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Small";
    $oField->placeholder = ".input-sm";
    $oField->size = "small";
    //$oField->framework_css["user"]["field"]["control"]["form"] = array("control", "size-sm");
    $oRecord->addContent($oField);


    $oField = ffField::factory($cm->oPage);
    $oField->label = "Normal";
    $oField->placeholder = "Normal";
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Large";
    $oField->placeholder = ".input-lg";
    $oField->size = "large";
    //$oField->framework_css["user"]["field"]["control"]["form"] = array("control", "size-lg");
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Grid Sizes";
    $oField->placeholder = "col-xs-4";
    $oField->setWidthComponent("4");
    $oRecord->addContent($oField);

$cm->oPage->addContent($oRecord);



$oRecord = ffRecord::factory($cm->oPage);
$oRecord->title = "Input group";
$oRecord->description = "Easily extend form controls by adding text, buttons, or button groups on either side of textual inputs, custom selects, and custom file inputs";
$oRecord->type = "default";
$oRecord->framework_css["component"]["header_wrap"] = false;
$oRecord->setWidthComponent(6);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Static";
    $oField->placeholder = "Username";
    $oField->fixed_pre_content = "@";
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Dropdowns";
    $oField->fixed_pre_content = array(
        "label" => "Dropdown",
        "items" => array(
            array("label" => "Action", "url" => "javascript:void();"),
            array("label" => "Ather Action", "url" => "javascript:void();"),
            array("label" => "Something else here", "url" => "javascript:void();")
        )
    );
    $oRecord->addContent($oField);


    $oField = ffField::factory($cm->oPage);
    $oField->label = "Buttons";
    $oField->placeholder = "Recipient's username";
    $oField->fixed_post_content = array(
        "label" => "Button",
        "url" => "javascript:void(0);",
        "type" => "button"
    );
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->type = "file-custom";
    $oField->label = "Custom file input";
    $oField->placeholder = "Choose file";
    //$oField->control_type = "file";
    $oRecord->addContent($oField);

$cm->oPage->addContent($oRecord);

$html_fake = '
<h2>Header Level 2</h2>

<ol>
   <li>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</li>
   <li>Aliquam tincidunt mauris eu risus.</li>
</ol>

<blockquote><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus magna. Cras in mi at felis aliquet congue. Ut a est eget ligula molestie gravida. Curabitur massa. Donec eleifend, libero at sagittis mollis, tellus est malesuada tellus, at luctus turpis elit sit amet quam. Vivamus pretium ornare est.</p></blockquote>
';

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->title = "Other Component";
$oRecord->type = "default";
$oRecord->framework_css["component"]["header_wrap"] = false;

$oField = ffField::factory($cm->oPage);
$oField->type = "code";
$oField->label = "Code example";
$oField->default_value = new ffData($html_fake);
$oField->setWidthComponent(6);
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->type = "html";
$oField->label = "Html example";
$oField->default_value = new ffData($html_fake);
$oField->setWidthComponent(6);
$oRecord->addContent($oField);

$oField = ffField::factory($cm->oPage);
$oField->type = "file-thumb";
$oField->label = "Upload";
$oRecord->addContent($oField);


$cm->oPage->addContent($oRecord);



$oRecord = ffRecord::factory($cm->oPage);
$oRecord->title = "Basic example";
$oRecord->type = "default";
$oRecord->framework_css["component"]["header_wrap"] = false;
$oRecord->src_table = "access_users"; //todo:da togliere
$oRecord->setWidthComponent(6);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Email address";
    $oField->placeholder = "Enter email";
    $oField->description = "We'll never share your email with anyone else.";
    $oField->addValidator("email");
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Password";
    $oField->placeholder = "Password";
    $oField->extended_type = "Password";
    $oField->crypt_method = "mysql_password";
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Check this custom checkbox";
    $oField->base_type = "Number";
    $oField->control_type = "checkbox";
    $oField->checked_value = new ffData("1", "Number");
    $oField->unchecked_value = new ffData("0", "Number");
    $oRecord->addContent($oField);

    $oButton = ffButton::factory($cm->oPage);
    $oButton->label = "Submit";
    $oButton->aspect = "button";
    $oButton->action_type = "submit";
    $oButton->url = "javascript:void(0);";

    $oRecord->addActionButton($oButton); //todo: da sistemare con addcontent

$cm->oPage->addContent($oRecord);



$oRecord = ffRecord::factory($cm->oPage);
$oRecord->title = "Horizzontal form";
$oRecord->type = "inline";
$oRecord->framework_css["component"]["header_wrap"] = false;
$oRecord->src_table = "access_users";
$oRecord->setWidthComponent(6);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Email";
    $oField->placeholder = true;
    $oField->addValidator("email");
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Password";
    $oField->placeholder = true;
    $oField->extended_type = "Password";
    $oField->crypt_method = "mysql_password";
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Re Password";
    $oField->placeholder = true;
    $oField->extended_type = "Password";
    $oField->crypt_method = "mysql_password";
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Check this custom checkbox";
    $oField->base_type = "Number";
    $oField->control_type = "checkbox";
    $oField->checked_value = new ffData("1", "Number");
    $oField->unchecked_value = new ffData("0", "Number");
    $oRecord->addContent($oField);

    $oButton = ffButton::factory($cm->oPage);
    $oButton->label = "Submit";
    $oButton->aspect = "button";
    $oButton->action_type = "submit";
    $oButton->url = "javascript:void(0);";

    $oRecord->addActionButton($oButton); //todo: da sistemare con addcontent


$cm->oPage->addContent($oRecord);


$oRecord = ffRecord::factory($cm->oPage);
$oRecord->title = "Inline form";
$oRecord->description = "Use the <code>.form-inline</code> class to display a series of labels, form controls, and buttons on a single horizontal row. Form controls within inline forms vary slightly from their default states. Controls only appear inline in viewports that are at least 576px wide to account for narrow viewports on mobile devices.";
$oRecord->type = "inline";
$oRecord->framework_css["component"]["header_wrap"] = false;
$oRecord->src_table = "access_users"; //todo:da togliere


    $oField = ffField::factory($cm->oPage);
    $oField->control_type = "label";
    $oField->default_value = new ffData("email@example.com");
    $oField->setWidthComponent(4);
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->placeholder = "Password";
    $oField->extended_type = "Password";
    $oField->crypt_method = "mysql_password";
    $oField->setWidthComponent(4);
    $oRecord->addContent($oField);

    $oButton = ffButton::factory($cm->oPage);
    $oButton->label = "Confirm Identity";
    $oButton->aspect = "button";
    $oButton->action_type = "submit";
    $oButton->url = "javascript:void(0);";
    $oRecord->addContent($oButton); //todo: da sistemare con addcontent




    $oField = ffField::factory($cm->oPage);
    $oField->label = "auto-sizing";
    $oField->placeholder = "Jane Doe";
    $oField->setWidthComponent(4);
    $oRecord->addContent($oField);


    $oField = ffField::factory($cm->oPage);
    $oField->placeholder = "Username";
    $oField->setWidthComponent(4);
    $oField->fixed_pre_content = "@";
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Remember me";
    $oField->base_type = "Number";
    $oField->control_type = "checkbox";
    $oField->checked_value = new ffData("1", "Number");
    $oField->unchecked_value = new ffData("0", "Number");
    $oRecord->addContent($oField);


    $oButton = ffButton::factory($cm->oPage);
    $oButton->label = "Submit";
    $oButton->aspect = "button";
    $oButton->action_type = "submit";
    $oButton->url = "javascript:void(0);";
    $oRecord->addContent($oButton); //todo: da sistemare con addcontent

$cm->oPage->addContent($oRecord);



$oRecord = ffRecord::factory($cm->oPage);
$oRecord->title = "Form row";
$oRecord->description = "You may also swap <code>.row</code> for <code>.form-row</code>, a variation of our standard grid row that overrides the default column gutters for tighter and more compact layouts.";
$oRecord->type = "default";
$oRecord->framework_css["component"]["header_wrap"] = false;
$oRecord->src_table = "access_users"; //todo:da togliere



    $oField = ffField::factory($cm->oPage);
    $oField->label = "Email";
    $oField->placeholder = "Email";
    $oField->addValidator("email");
    $oField->setWidthComponent(6);
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Password";
    $oField->placeholder = "Password";
    $oField->extended_type = "Password";
    $oField->crypt_method = "mysql_password";
    $oField->setWidthComponent(6);
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Address";
    $oField->placeholder = "1234 Main St";
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Address 2";
    $oField->placeholder = "Apartment, studio, or floor";
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "City";
    $oField->setWidthComponent(6);
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "State";
    $oField->extended_type = "Selection";
    $oField->multi_select_one_label = "Choose";
    $oField->multi_pairs = array(
        array(new ffData("1"), new ffData("Option 1")),
        array(new ffData("2"), new ffData("Option 2")),
        array(new ffData("3"), new ffData("Option 3"))
    );
    $oField->setWidthComponent(4);
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Zip";
    $oField->setWidthComponent(2);
    $oRecord->addContent($oField);

    $oField = ffField::factory($cm->oPage);
    $oField->label = "Check this custom checkbox";
    $oField->base_type = "Number";
    $oField->control_type = "checkbox";
    $oField->checked_value = new ffData("1", "Number");
    $oField->unchecked_value = new ffData("0", "Number");
    $oRecord->addContent($oField);


    $oButton = ffButton::factory($cm->oPage);
    $oButton->label = "Submit";
    $oButton->aspect = "button";
    $oButton->action_type = "submit";
    $oButton->url = "javascript:void(0);";

    $oRecord->addActionButton($oButton); //todo: da sistemare con addcontent


$cm->oPage->addContent($oRecord);