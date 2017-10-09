<?php
mod_security_check_session(false);

header("Content-type: application/json");
$data = array();
$db = ffDB_Sql::factory();
$db2 = ffDB_Sql::factory();

if ($_REQUEST["geo_provincia_src"])
{
    $sWhere .= " AND support_province.ID = " . $db->toSql($_REQUEST["geo_provincia_src"]) . " ";
    $sWhere2 .= " AND support_province.ID = " . $db->toSql($_REQUEST["geo_provincia_src"]) . " ";
}

if ($_REQUEST["geo_ragsoc_src"])
{
    $sWhere .= " AND " . CM_TABLE_PREFIX . "mod_clienti_main.ragsoc LIKE('%" . $db->toSql($_REQUEST["geo_ragsoc_src"], "Text", false) . "%') ";
}

 
$sSQL = "
        SELECT DISTINCT
                 'Cliente' AS type,
                 CONCAT('c', " . CM_TABLE_PREFIX . "mod_clienti_main.ID) AS ID,
                 " . CM_TABLE_PREFIX . "mod_clienti_main.ID AS realid,
                 " . CM_TABLE_PREFIX . "mod_clienti_main.coords_lat,
                 " . CM_TABLE_PREFIX . "mod_clienti_main.coords_lng,
                 " . CM_TABLE_PREFIX . "mod_clienti_main.ragsoc,
                 " . CM_TABLE_PREFIX . "mod_clienti_main.indirizzo,
                 " . CM_TABLE_PREFIX . "mod_clienti_main.cap,
                 " . CM_TABLE_PREFIX . "mod_clienti_main.citta,
                 support_province.CarAbbreviation AS supportprovincia,
                 " . CM_TABLE_PREFIX . "mod_clienti_main.telefono1,
                 " . CM_TABLE_PREFIX . "mod_clienti_main.cellulare1,
                 " . CM_TABLE_PREFIX . "mod_clienti_main.fax,
                 " . CM_TABLE_PREFIX . "mod_clienti_main.email1
            FROM
                " . CM_TABLE_PREFIX . "mod_clienti_main
            LEFT JOIN support_province
                ON support_province.ID = " . CM_TABLE_PREFIX . "mod_clienti_main.provincia
            WHERE
                " . CM_TABLE_PREFIX . "mod_clienti_main.coords_lat  <> ''
                AND " . CM_TABLE_PREFIX . "mod_clienti_main.coords_lng <> ''
            " . $sWhere . "
            HAVING
                1
                $sHaving
        
    ";
$db->query($sSQL);

if ($db->nextRecord())
{
    do
    {
        $data[$db->getField("ID")->getValue()] = array(
             "id" => $db->getField("ID")->getValue(),
             "realid" => $db->getField("realid")->getValue(),
             "lat" => $db->getField("coords_lat")->getValue(),
             "lng" => $db->getField("coords_lng")->getValue(),
             "zoom" => $db->getField("coords_zoom", "Number")->getValue(),
             "description" => "",
             "ragsoc" => htmlspecialchars($db->getField("ragsoc")->getValue()),
             "address" => htmlspecialchars($db->getField("indirizzo")->getValue()),
             "cap" => htmlspecialchars($db->getField("cap")->getValue()),
             "citta" => htmlspecialchars($db->getField("citta")->getValue()),
             "prov" => htmlspecialchars($db->getField("supportprovincia")->getValue()),
             "tel" => htmlspecialchars($db->getField("telefono1")->getValue()),
             "cell" => htmlspecialchars($db->getField("cellulare1")->getValue()),
             "fax" => htmlspecialchars($db->getField("fax")->getValue()),
             "email" => htmlspecialchars($db->getField("email1")->getValue())
        );
     } while ($db->nextRecord());
}


die(json_encode($data));
