<?php
$file = ""; // DA CONTROLLARE
$xml = new SimpleXMLElement("file://" . $file, null, true);
$db = ffDB_Sql::factory();

$legend = array(
    "titolo" , "descrizione", "azienda", "locate", "settore", "data", "categoria", "posti"
);

if (isset($xml->menu) && count($xml->menu->children()))
{
    $i = 0;
    $first_elem = 1;
    $last_elem = count($xml->menu->children());
    if(isset($_REQUEST["p"]) && isset($_REQUEST["elem"]))
    {
        $first_elem += ($_REQUEST["p"]-1) * $_REQUEST["elem"];
        $last_elem = $first_elem + $_REQUEST["elem"];
    }
    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.smart_url
                    , " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID AS ID_subcategory
                    , " . CM_TABLE_PREFIX . "mod_recruitment_subcategory.ID_category AS ID_category
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_subcategory";
    $db->query($sSQL);
    if($db->nextRecord())
    {
        do {
            $array_category[$db->getField("smart_url", "Text", true)] = array(
                "ID" => $db->getField("ID_subcategory", "Number", true)
                , "ID_category" => $db->getField("ID_category", "Number", true)
            );
        } while($db->nextRecord());
    }
    foreach ($xml->menu->children() as $key => $value)
    {
        $i++;
        if($i >= $last_elem)
        {
           break; 
        } elseif($i >= $first_elem)
        {
            $attrs = $value->attributes();
            foreach($legend AS $value)
            {
                if(isset($attrs[$value])) {
                    ${$value} = $value;
                } else {
                    $skip_record = true;
                    break;
                }
            }
            
            if($skip_record)
                continue;
            
            list($dd, $mm, $yyyy) = explode('/', $data);
            $data = mktime(0,0,0,$mm, $dd, $yyyy);
            
            $subcategory_smart_url = ffCommon_url_rewrite($categoria);

            if(array_key_exists($subcategory_smart_url, $array_category))
            {
                $settore = $array_category[$subcategory_smart_url]["ID_category"];
                $categoria = $array_category[$subcategory_smart_url]["ID"];
            } else
            {
                $new_subcategory = true;
            }

            $hash = md5(implode("-", $new_advertisement), true); // Li ho trovati da 16 a 40 cifre, il minore mi è risultato md5 binario,
            
            $new_advertisement[] = array(
               "title" => $titolo
                , "description" => $descrizione
                , "azienda" => $azienda
                , "city" => $locate
                , "category" => $settore
                , "time_insert" => $data
                , "subcategory" => $categoria
                , "required_workers" => $required_workers
                , "hash" => $hash
                , "new_subcategory" => $posti
            );
            
            $selected_hash .= $db->toSql($hash, "Text");
        } else {
            continue;
        }
    }
}

if(is_array($new_advertisement) && count($new_advertisement))
{
    $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.ID
                    , " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.hash
                FROM " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                WHERE " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.is_feed = 1
                    AND " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement.hash IN (" . $selected_hash . ")";
    $db->query($sSQL);
    if($db->nextRecord())
    {
        do {
            $advertisement_hash[$db->getField("hash", "text", true)] = $db->getField("ID", "Number", true);
        } while($db->nextRecord());
    }
    foreach($new_advertisement AS $key => $value)
    {
        if(is_array($advertisement_hash) && array_key_exists($value["hash"], $advertisement_hash))
            continue;
        else
        {
            if($value["new_subcategory"])
            {
                $sSQL = "SELECT " . CM_TABLE_PREFIX . "mod_recruitment_category.*
                            FROM " . CM_TABLE_PREFIX . "mod_recruitment_category
                            WHERE " . CM_TABLE_PREFIX . "mod_recruitment_category.smart_url = " . $db->toSql(ffCommon_url_rewrite($value["category"]));
                if($db->nextRecord()) {
                    $ID_category = $db->getField("ID", "Number", true);
                } else {
                    $sSQL = "INSERT INTO " . CM_TABLE_PREFIX . "mod_recruitment_category
                                (
                                    ID
                                    , name
                                    , smart_url
                                    , is_feed
                                ) VALUES
                                (
                                    null
                                    , " . $db->toSql($value["category"], "Text") . "
                                    , " . $db->toSql(ffCommon_url_rewrite($value["category"]), "Text") . "
                                    , 1
                                )";
                    $db->execute($sSQL);
                    
                    $ID_category = $db->getInsertID(true);
                }
                $value["category"] = $ID_category;
                
                $sSQL = "INSERT INTO " . CM_TABLE_PREFIX . "mod_recruitment_subcategory
                                (
                                    ID
                                    , ID_category
                                    , name
                                    , smart_url
                                    , is_feed
                                ) VALUES
                                (
                                    null
                                    , " . $db->toSql($ID_category, "Number") . "
                                    , " . $db->toSql($value["subcategory"], "Text") . "
                                    , " . $db->toSql(ffCommon_url_rewrite($value["subcategory"]), "Text") . "
                                    , 1
                                )";
                $db->execute($sSQL);
                
                $value["subcategory"] = $db->getInsertID(true);
                
            }
            
            $sSQL = "INSERT INTO " . CM_TABLE_PREFIX . "mod_recruitment_job_advertisement
                        ( 
                            ID
                            , title
                            , nome_azienda
                            , ID_category
                            , ID_subcategory
                            , city
                            , required_workers
                            , description
                            , time_insert
                            , hash
                            , is_feed
                        ) VALUES
                        (
                            null
                            , " . $db->toSql($value["title"], "Text") . "
                            , " . $db->toSql($value["azienda"], "Text") . "
                            , " . $db->toSql($value["category"], "Text") . " 
                            , " . $db->toSql($value["subcategory"], "Number") . "
                            , " . $db->toSql($value["azienda"], "Text") . "
                            , " . $db->toSql($value["required_workers"], "Number") . "
                            , " . $db->toSql($value["description"], "Text") . "
                            , " . $db->toSql($value["time_insert"], "Number") . "
                            , " . $db->toSql($value["hash"], "Text") . "
                            , 1
                        )";
                    $db->execute($sSQL);
        }
    }
}
/*
$rss = new DOMDocument();
$rss->load(); //INSERIRE LA RISORSA
$feed = array();
foreach ($rss->getElementsByTagName('item') as $node) {
        $item = array ( 
                'title' => $node->getElementsByTagName('titolo')->item(0)->nodeValue,
                'description' => strip_tags($node->getElementsByTagName('descrizione')->item(0)->nodeValue),
                'nome_azienda' => $node->getElementsByTagName('azienda')->item(0)->nodeValue,
                'city' => $node->getElementsByTagName('locate')->item(0)->nodeValue,
                'category' => $node->getElementsByTagName('settore')->item(0)->nodeValue,
                'time_inserted' => $node->getElementsByTagName('data')->item(0)->nodeValue,
                'subcategory' => $node->getElementsByTagName('categoria')->item(0)->nodeValue,
                'required_workers' => $node->getElementsByTagName('posti')->item(0)->nodeValue,
                );
        array_push($feed, $item);


} */