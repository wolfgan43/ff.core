<?php
mod_security_check_session();

$permission = check_recruitment_permission();
if($permission !== true && !(is_array($permission) && count($permission))) {
    ffRedirect(FF_SITE_PATH . "/login" . "?ret_url=" . urlencode($_SERVER["REQUEST_URI"]) . "&relogin");
}

$db = ffDB_Sql::factory();
$UserNID = get_session("UserNID");

if(!$_REQUEST["keys"]["ID"] > 0 && strlen(basename($cm->real_path_info))) {
    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.*
                , " . CM_TABLE_PREFIX . "mod_recruitment_category.name AS category_name
                ,  " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.name AS subcategory_name
                , " . CM_TABLE_PREFIX . "mod_recruitment_category.smart_url AS category_smart_url
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                    LEFT JOIN " . CM_TABLE_PREFIX . "mod_recruitment_category ON " . CM_TABLE_PREFIX . "mod_recruitment_category.ID = " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID_category
                    LEFT JOIN " . CM_TABLE_PREFIX . "mod_recruitment_subcategory ON " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID = " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID_subcategory
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.smart_url = " . $db->toSql(basename($cm->real_path_info)) . "
                    AND " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID_user = " . $db->toSql($UserNID, "Number", false);
    $db->query($sSQL);
    if($db->nextRecord()) {
        $_REQUEST["keys"]["ID"] = $db->getField("ID", "Number", true);
        $ID_category = $db->getField("ID_category", "Number", true);
        $category_name = $db->getField("category_name", "Text", true);
        $subcategory_name = $db->getField("category_name", "Text", true);
        $category_smart_url = $db->getField("category_smart_url", "Text", true);
        $title = $db->getField("title", "Text", true);
        $nome_azienda = $db->getField("nome_azienda", "Text", true);
    }
}

$ID_job_advertisement = $_REQUEST["keys"]["ID"];

$oRecord = ffRecord::factory($cm->oPage);
$oRecord->id = "JobAdvertisementModify";
$oRecord->resources[] = $oRecord->id;
$oRecord->title = ffTemplate::_get_word_by_code("recruitment_job_advertisement_modify");
$oRecord->src_table = CM_TABLE_PREFIX . "mod_recruitment_job_advertisement";
$oRecord->buttons_options["delete"]["display"] = false;
$oRecord->insert_additional_fields["ID_user"] =  new ffData($UserNID, "Number");
$oRecord->update_additional_fields["last_update"] =  new ffData(time(), "Number");
$oRecord->insert_additional_fields["created"] =  new ffData(time(), "Number");
$oRecord->addEvent("on_done_action", "JobAdvertisementModify_on_done_action");
if(strlen($subcategory_name))
{
   $oRecord->user_vars["subcategory"] = $subcategory_name;
}
if(strlen($title))
{
   $oRecord->user_vars["title"] = $title;
}

$oField = ffField::factory($cm->oPage);
$oField->id = "ID";
$oField->base_type = "Number";
$oRecord->addKeyField($oField);

if($ID_job_advertisement)
{
    $oField = ffField::factory($cm->oPage);
    $oField->id = "city";
    $oField->base_type = "Number";
    $oField->widget = "activecomboex";
    $oField->source_SQL = "SELECT " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".ID, " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".name
                            FROM " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . "
                            WHERE 1
                            ORDER BY name";
    $oField->actex_update_from_db = true;
    $oField->required = true;
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_city");
    $oRecord->addContent($oField);
    
    /*
    $oField = ffField::factory($cm->oPage);
    $oField->id = "region";
    $oField->extended_type = "Selection";
    $oField->widget = "autocompletetoken";
    $oField->autocompletetoken_minLength = 0;
    $oField->autocompletetoken_theme = "";
    $oField->autocompletetoken_not_found_label = ffTemplate::_get_word_by_code("autocompletetoken_not_found");
    $oField->autocompletetoken_init_label = ffTemplate::_get_word_by_code("autocompletetoken_init");
    $oField->autocompletetoken_searching_label = ffTemplate::_get_word_by_code("autocompletetoken_searching");
    $oField->autocompletetoken_label = ffTemplate::_get_word_by_code("autocompletetoken_label");
    $oField->autocompletetoken_combo = true;
    $oField->autocompletetoken_compare_having = "city_name";
        $oField->source_SQL = "SELECT ID, name as region
                            FROM " . MOD_RECRUITMENT_TBL_SUPPORT_REGION . "
                            WHERE 1
                            ORDER BY region";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_region");
    $oRecord->addContent($oField);
/*    
    if($category_smart_url !== "generico")
    {
        $oField = ffField::factory($cm->oPage);
        $oField->id = "ID_subcategory";
        $oField->container_class = "subcategory";
        $oField->base_type = "Number";
        $oField->source_SQL = "SELECT ID, name
                                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_subcategory 
                                    WHERE " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID_category = " . $db->toSql($ID_category, "Number") . "
                                    ORDER BY name";
        $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_subcategory");
        $oField->widget = "activecomboex";
        $oField->actex_update_from_db = true;
        $oField->resources[] = "SubCategoryModify";
        $oField->actex_dialog_url = $cm->oPage->site_path . VG_SITE_RESTRICTED  . "/recruitment/subcategory/modify?category=" . $category_smart_url;
        //$oField->actex_dialog_edit_params = array("keys[anagraph-ID]" => null);
        $oRecord->addContent($oField);
    }
*/   
    $oField = ffField::factory($cm->oPage);
    $oField->id = "ID_question";
    $oField->container_class = "question";
    $oField->base_type = "Number";
    $oField->source_SQL = "SELECT ID, title
                            FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement_question 
                            WHERE ID_user = " . $db->toSql($UserNID, "Number") . "
                            ORDER BY title";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_question");
    $oField->widget = "activecomboex";
    $oField->actex_update_from_db = true;
    $oRecord->addContent($oField);
        
    $oField = ffField::factory($cm->oPage);
    $oField->id = "role";
    $oField->required = true;
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_role");
    $oRecord->addContent($oField);
    
    $oField = ffField::factory($cm->oPage);
    $oField->id = "required_workers";
    $oField->base_type = "Number";
    $oField->required = true;
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_required_workers");
    $oRecord->addContent($oField);
    
    $oField = ffField::factory($cm->oPage);
    $oField->id = "description";
    $oField->container_class = "description";
    $oField->required = true;
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_description");
    $oField->base_type = "Text";
    $oField->extended_type = "Text";
    $oRecord->addContent($oField);
    
    $oField = ffField::factory($cm->oPage);
    $oField->id = "required_studies";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_required_studies");
    $oRecord->addContent($oField);
    
    $oField = ffField::factory($cm->oPage);
    $oField->id = "required_experience";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_required_experience");
    $oRecord->addContent($oField);
    
    $oField = ffField::factory($cm->oPage);
    $oField->id = "contract_type";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_contract_type");
    $oRecord->addContent($oField);
    
    $oField = ffField::factory($cm->oPage);
    $oField->id = "contract_durata";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_contract_durata");
    $oRecord->addContent($oField);
    
    $oField = ffField::factory($cm->oPage);
    $oField->id = "day_type";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_day_type");
    $oRecord->addContent($oField);
    
    $oField = ffField::factory($cm->oPage);
    $oField->id = "day_timing";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_day_timing");
    $oRecord->addContent($oField);
    
    $oField = ffField::factory($cm->oPage);
    $oField->id = "stipendio";
    $oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_stipendio");
    $oRecord->addContent($oField);
    
} else {
        $oField = ffField::factory($cm->oPage);
	$oField->id = "title";
	$oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_title");
        $oField->required = true;
	$oRecord->addContent($oField);
        
    $oField = ffField::factory($cm->oPage);
	$oField->id = "nome_azienda";
	$oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_azienda_name");
    $oField->required = true;
    $oField->default_value = new ffData($_REQUEST["company"]);
	$oRecord->addContent($oField);
        /*
	$oField = ffField::factory($cm->oPage);
	$oField->id = "ID_category";
	$oField->container_class = "category";
	$oField->base_type = "Number";
	$oField->source_SQL = "SELECT ID, name
                            FROM " . CM_TABLE_PREFIX . "mod_recruitment_category 
                            WHERE 1
                            ORDER BY name";
	$oField->label = ffTemplate::_get_word_by_code("recruitment_job_advertisement_category");
        $oField->widget = "activecomboex";
        $oField->actex_update_from_db = true;
        $oField->resources[] = "CategoryModify";
        $oField->required = true;
        $oField->actex_dialog_url = $cm->oPage->site_path . VG_SITE_RESTRICTED  . "/recruitment/category/modify";
        //$oField->actex_dialog_edit_params = array("keys[anagraph-ID]" => null);
         * */
        
        $oField = ffField::factory($cm->oPage);
	$oField->id = "ID_subcategory";
	$oField->container_class = "subcategory";
	$oField->base_type = "Number";
	$oField->source_SQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID
                                        , " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.name
                                        , " . CM_TABLE_PREFIX . "mod_recruitment_category.name AS `group`
                                    FROM " . CM_TABLE_PREFIX . "mod_recruitment_category
                                       INNER JOIN  " . CM_TABLE_PREFIX . "mod_recruitment_subcategory ON " . CM_TABLE_PREFIX . "mod_recruitment_category.ID = " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID_category
                                    WHERE 1
                                    ORDER BY `group`"; 
        $oField->widget = "activecomboex";
        $oField->actex_update_from_db = true;
        $oField->actex_group = "group";
        $oField->resources[] = "SubCategoryModify";
        /*
        $oField->actex_dialog_url = $cm->oPage->site_path . "/restricted/recruitment/category/modify";
        $oField->actex_dialog_edit_url = $cm->oPage->site_path . "/restricted/recruitment/subcategory/modify";
        $oField->actex_dialog_edit_params = array("keys[ID]" => null);
        $oField->actex_dialog_delete_url = actex_dialog_edit_url . "?frmAction=CategoryModify_confirmdelete";
        */
	$oField->label = ffTemplate::_get_word_by_code("recruitment_cv_category");
        $oField->required = true;
	$oRecord->addContent($oField);
}
$cm->oPage->addContent($oRecord);


function JobAdvertisementModify_on_done_action($component, $action)
{
    $db = ffDB_Sql::factory();
    if(strlen($action))
    {
   
        if(isset($component->user_vars["subcategory"])) {
            $subcategory = $component->user_vars["subcategory"];
        } elseif(isset($component->form_fields["ID_subcategory"])) {
            $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.*
                        FROM " . CM_TABLE_PREFIX . "mod_recruitment_subcategory
                        WHERE " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID = " . $db->toSql($component->form_fields["ID_subcategory"]->value, "Number");
            $db->query($sSQL);
            if($db->nextRecord()) {
                $ID_category = $db->getField("ID_category", "Number", true);
                $subcategory = $db->getField("name", "text", true);
            }
        }
        
        if(isset($component->user_vars["title"])) {
            $title = $component->user_vars["title"];
        } elseif(isset($component->form_fields["title"])) {
            $title = $component->form_fields["title"]->getValue();
        }
        
        $smart_url = "advertisement "
                    . $subcategory
                    . " "
                    . $title;
        switch($action) {
            case "insert": 
                $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                            SET smart_url = " . $db->toSql(ffCommon_url_rewrite($smart_url)) . "
                                , ID_category = " . $db->toSql($ID_category, "Number") . "
                                , time_inserted = " . $db->toSql(time(), "Number") . "
                            WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID = " . $db->toSql($component->key_fields["ID"]->value);
                $db->execute($sSQL);
				if (isset($_REQUEST["XHR_DIALOG_ID"])) {
					die(ffCommon_jsonenc(array("url" => $component->parent[0]->site_path . $component->parent[0]->page_path . "/modify/" . ffCommon_url_rewrite($smart_url) . "?ret_url=" . urlencode($_REQUEST["ret_url"]), "close" => true, "refresh" => true, "doredirects" => true), true));
				} else {
					ffRedirect($component->parent[0]->site_path . $component->parent[0]->page_path . "/modify/" . ffCommon_url_rewrite($smart_url) . "?ret_url=" . urlencode($_REQUEST["ret_url"]));
				}
                break;
            case "update":
                $ID_regione = 0;
                $ID_provincia = 0;
                
                $sSQL = "SELECT " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".*
                            FROM " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . "
                            WHERE " . MOD_RECRUITMENT_TBL_SUPPORT_CITY . ".ID = " . $db->toSql($component->form_fields["city"]->getValue());
                $db->query($sSQL);
                if($db->nextRecord()) {
                    if($db->getField("RegionID", "Number", true) > 0)
                        $ID_regione = $db->getField("RegionID", "Number", true);
                    if($db->getField("DisctrictID", "Number", true) > 0)
                        $ID_provincia = $db->getField("DisctrictID", "Number", true);
                }
                
                $sSQL = "UPDATE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                            SET smart_url = " . $db->toSql(ffCommon_url_rewrite($smart_url)) . "
                                , region = " . $db->toSql($ID_regione, "Number") . "
                                , province = " . $db->toSql($ID_provincia, "Number") . "
                            WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID = " . $db->toSql($component->key_fields["ID"]->value);
                $db->execute($sSQL);
                break;
            default: 
        }
    }
}