<?php

// ---------------------
// call export function
// ---------------------
exportMysqlToCsv('export_users.csv');

// ---------------------
// export csv
// ---------------------
function exportMysqlToCsv($filename = 'export_users.csv')
{
    $db = ffDB_Sql::factory();
    $sql_query = "SELECT cm_mod_security_users.ID
                            , cm_mod_security_users.username
                            , cm_mod_security_users.email
                            ,(SELECT value FROM cm_mod_security_users_fields WHERE field = 'firstname' AND ID_users = cm_mod_security_users.ID) AS nome
                            ,(SELECT value FROM cm_mod_security_users_fields WHERE field = 'lastname' AND ID_users = cm_mod_security_users.ID) AS cognome
                            ,(SELECT value FROM cm_mod_security_users_fields WHERE field = 'company' AND ID_users = cm_mod_security_users.ID) AS azienda
                            ,(SELECT value FROM cm_mod_security_users_fields WHERE field = 'tel' AND ID_users = cm_mod_security_users.ID) AS tel
                            ,(SELECT value FROM cm_mod_security_users_fields WHERE field = 'cell' AND ID_users = cm_mod_security_users.ID) AS cell
                            ,(SELECT value FROM cm_mod_security_users_fields WHERE field = 'settore' AND ID_users = cm_mod_security_users.ID) AS settore
                            ,(SELECT value FROM cm_mod_security_users_fields WHERE field = 'newsletter' AND ID_users = cm_mod_security_users.ID) AS newsletter
                            , cm_mod_security_users.created
                            , cm_mod_security_users.modified
                            , cm_mod_security_users.lastlogin
                    FROM cm_mod_security_users
                    LEFT JOIN cm_mod_security_users_fields
                        ON cm_mod_security_users.ID = cm_mod_security_users_fields.ID_users
                    WHERE 1
                    GROUP BY cm_mod_security_users.ID";
    // Gets the data from the database
    $db->query($sql_query);

    $f = fopen('php://temp', 'wt');
    $first = true;
    if($db->nextRecord()){
        do{
            $ID = $db->getField("ID")->getValue();
            $username = $db->getField("username")->getValue();
            $email = $db->getField("email")->getValue();
            $nome = $db->getField("nome")->getValue();
            $cognome = $db->getField("cognome")->getValue();
            $azienda = $db->getField("azienda")->getValue();
            $tel = $db->getField("tel")->getValue();
            $cell = $db->getField("cell")->getValue();
            $settore = $db->getField("settore")->getValue();
            if($db->getField("newsletter")->getValue()){
                $newsletter = "Si";
            }else{
                $newsletter = "No";
            }
            $created = $db->getField("created")->getValue();
            $modified = $db->getField("modified")->getValue();
            $lastlogin = $db->getField("lastlogin")->getValue();
            $row = array(
                "id" => $ID,
                "Username" => $username,
                "Email" => $email,
                "Nome" => $nome,
                "Cognome" => $cognome,
                "Azienda" => $azienda,
                "Telefono" => $tel,
                "Cellulare" => $cell,
                "Settore" => $settore,
                "Newsletter" => $newsletter,
                "DataCreazione" => $created,
                "DataUltimaModifica" => $modified,
                "UltimoLogin" => $lastlogin,
            );

            if ($first) {
                fputcsv($f, array_keys(str_replace('"', "", $row)), ';', '/n');
                $first = false;
            }
            fputcsv($f, str_replace('"', "", $row), ';');
        }while($db->nextRecord());
    } // end while

    $size = ftell($f);
    rewind($f);

    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Length: $size");
    // Output to browser with appropriate mime type, you choose ;)
    header("Content-type: text/x-csv; charset=utf-8");
    header("Content-type: text/csv; charset=utf-8");
    header("Content-type: application/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=$filename");
    fpassthru($f);
    //header('Location: /restricted/security/users/?'.$_SERVER['QUERY_STRING']);

    exit;


}

?>