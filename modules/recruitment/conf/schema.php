<?php
	$schema = array();
    $schema["db"]["table_prefix"] = "cm_mod_recruitment_";
    $schema["db"]["schema"]["data"]["cm_mod_security_groups"][] = array("ID" => null
     														, "name" => "recruitment-admin"
     														, "level" => 10
     													);
    $schema["db"]["schema"]["data"]["cm_mod_security_groups"][] = array("ID" => null
     														, "name" => "recruitment-user"
     														, "level" => 10
     													);
    $schema["db"]["schema"]["data"]["cm_mod_security_groups"][] = array("ID" => null
                                                             , "name" => "recruitment-advertiser"
                                                             , "level" => 10
                                                         );
?>