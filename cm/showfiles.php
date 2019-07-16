<?php
/**
 * @ignore
 * @package ContentManager
 * @subpackage showfiles
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

define("SHOWFILES_IS_RUNNING", true);
//define("FF_SKIP_COMPONENTS", true);
//define("FF_ONLY_INIT", true);
define("CM_ONLY_INIT", true);
//define("CM_DONT_RUN", true);
//define("FF_DB_MYSQLI_AVOID_REAL_CONNECT", true);

require(__DIR__ . "/main.php");


if(!defined("CM_CACHE_PATH"))                       define("CM_CACHE_PATH", "/cache");
if(!defined("CM_CACHE_DISK_PATH"))                  define("CM_CACHE_DISK_PATH", FF_DISK_PATH . CM_CACHE_PATH);
if (!defined("CM_SHOWFILES_ENABLE_DEBUG"))          define("CM_SHOWFILES_ENABLE_DEBUG", true);

if (!defined("CM_JSCACHE_BYDOMAIN"))                define("CM_JSCACHE_BYDOMAIN", false);
if (!defined("CM_JSCACHE_BYDOMAIN_STRIPWWW"))       define("CM_JSCACHE_BYDOMAIN_STRIPWWW", false);
if (!defined("CM_JSCACHE_GROUPHASH"))               define("CM_JSCACHE_GROUPHASH", false);
if (!defined("CM_JSCACHE_HASHSPLIT"))               define("CM_JSCACHE_HASHSPLIT", 10);

if (!defined("CM_CSSCACHE_BYDOMAIN"))                define("CM_CSSCACHE_BYDOMAIN", false);
if (!defined("CM_CSSCACHE_BYDOMAIN_STRIPWWW"))       define("CM_CSSCACHE_BYDOMAIN_STRIPWWW", false);
if (!defined("CM_CSSCACHE_GROUPHASH"))               define("CM_CSSCACHE_GROUPHASH", false);
if (!defined("CM_CSSCACHE_HASHSPLIT"))               define("CM_CSSCACHE_HASHSPLIT", 10);


if (!defined("CM_JSCACHE_DIR"))					  define("CM_JSCACHE_DIR", CM_CACHE_DISK_PATH . "/js");
if (!defined("CM_CSSCACHE_DIR"))					  define("CM_CSSCACHE_DIR", CM_CACHE_DISK_PATH . "/css");





$path_info = $_SERVER["PATH_INFO"];
//if(strpos($path_info, $_SERVER["SCRIPT_NAME"]) === 0) // ??? DA VERIFICARE,DA CORREGGERE ALTROVE
//    $path_info = substr($path_info, strlen($_SERVER["SCRIPT_NAME"]));

$path_parts = explode("/", trim($path_info, "/"));
$expires = null;
$cached = false;

// CSS / JS STATIC CACHE
$tmp_filename = end($path_parts);
$tmp_mime = ffMedia::getMimeTypeByFilename($tmp_filename);
if ($tmp_mime == "application/x-javascript")
{
	$cache_dir = CM_JSCACHE_DIR;
	if (CM_JSCACHE_BYDOMAIN)
	{
		$cache_domain_prefix = $_SERVER["HTTP_HOST"];
		if (CM_JSCACHE_BYDOMAIN_STRIPWWW && strpos($cache_domain_prefix, "www.") === 0)
			$cache_domain_prefix = substr($cache_domain_prefix, 4);
		$cache_dir .= "/" . $cache_domain_prefix;
	}
	
	if (ffHTTP_encoding_isset("gzip") && file_exists($cache_dir . $path_info . ".gz"))
	{
		$cached = true;
		$filepath = $cache_dir . $path_info . ".gz";
		$filename = end($path_parts);
	}
	else if (file_exists($cache_dir . $path_info))
	{
		$cached = true;
		$filepath = $cache_dir . $path_info;
		$filename = end($path_parts);
	} 
	else if (CM_JSCACHE_GROUPHASH)
	{
		$hash_parts = explode("_", trim($path_info, "/"));
		$cache_dir .= "/" . implode("/", str_split($hash_parts[0], CM_JSCACHE_HASHSPLIT));

		if (ffHTTP_encoding_isset("gzip") && file_exists($cache_dir . $path_info . ".gz"))
		{
			$cached = true;
			$filepath = $cache_dir . $path_info . ".gz";
			$filename = end($path_parts);
		}
		else if (file_exists($cache_dir . $path_info))
		{
			$cached = true;
			$filepath = $cache_dir . $path_info;
			$filename = end($path_parts);
		}
	}
	
	if (strlen($filepath))
		$expires = 365;
}
else if ($tmp_mime == "text/css")
{
	$cache_dir = CM_CSSCACHE_DIR;
	if (CM_CSSCACHE_BYDOMAIN)
	{
		$cache_domain_prefix = $_SERVER["HTTP_HOST"];
		if (CM_CSSCACHE_BYDOMAIN_STRIPWWW && strpos($cache_domain_prefix, "www.") === 0)
			$cache_domain_prefix = substr($cache_domain_prefix, 4);
		$cache_dir .= "/" . $cache_domain_prefix;
	}

	if (CM_CSSCACHE_GROUPHASH)
	{
		$hash_parts = explode("_", trim($path_info, "/"));
		$cache_dir .= "/" . implode("/", str_split($hash_parts[0], CM_CSSCACHE_HASHSPLIT));
	}

	if (ffHTTP_encoding_isset("gzip") && file_exists($cache_dir . $path_info . ".gz"))
	{
		$cached = true;
		$filepath = $cache_dir . $path_info . ".gz";
		$filename = $tmp_filename;
	}
	else if (file_exists($cache_dir . $path_info))
	{
		$cached = true;
		$filepath = $cache_dir . $path_info;
		$filename = $tmp_filename;
	} 
	else if (CM_CSSCACHE_GROUPHASH)
	{
		$hash_parts = explode("_", trim($path_info, "/"));
		$cache_dir .= "/" . implode("/", str_split($hash_parts[0], CM_CSSCACHE_HASHSPLIT));

		if (ffHTTP_encoding_isset("gzip") && file_exists($cache_dir . $path_info . ".gz"))
		{
			$cached = true;
			$filepath = $cache_dir . $path_info . ".gz";
			$filename = $tmp_filename;
		}
		else if (file_exists($cache_dir . $path_info))
		{
			$cached = true;
			$filepath = $cache_dir . $path_info;
			$filename = $tmp_filename;
		}
	} 
	
	if (strlen($filepath))
		$expires = 365;
}

/*// MODULES PROPAGATIONS
if (strpos($path_info, CM_MODULES_PATH) === 0)
{
	$parts = explode("/", trim(str_replace(CM_MODULES_PATH, "", $path_info), "/"));
	if (count($parts) >= 2)
	{
		$filepath = CM_MODULES_ROOT . "/". $parts[0] . "/themes";
		$parts = array_slice($parts, 1);
		$filepath .= "/" . implode("/", $parts);
		$filename = end($parts);
		
		$subparts = explode(".", $filename);
		$ext = end($subparts);
		if ($ext == "css" || $ext == "js")
			if (ffHTTP_encoding_isset("gzip") && is_file($filepath . ".gz"))
				$filepath .= ".gz";
	}
}*/



$base_path = FF_DISK_UPDIR;

$db = ffDB_Sql::factory();
$db2 = ffDB_Sql::factory();

$db->query("SELECT * FROM " . CM_TABLE_PREFIX . "showfiles");
if ($db->nextRecord())
{
    if(!is_array($imgParams))
        $imgParams = ffMedia::getModes();
    if(is_array($imgParams) && count($imgParams))
    {

        foreach($imgParams AS $imgParams_value)
        {
            if(strlen($imgParams_value["name"]))
            {
                if (strlen($rule_modes))
                    $rule_modes .= '|';

                $rule_modes .= $imgParams_value["name"];
            }
        }
        $rule_modes = '('. $rule_modes . ')';
    }

    do
    {
        $name		= $db->getField("name", "Text", true);
        $source		= $db->getField("source", "Text", true);
        $field		= $db->getField("field_file", "Text", true);

        $path_saved	= $db->getField("path_full", "Text", true);
        $path_temp	= $db->getField("path_temp", "Text", true);

        if (is_callable("mod_auth_get_domain"))
        {
            $ID_domain = mod_security_get_domain();
        }
        $path_saved = str_replace("[ID_DOMAINS]", $ID_domain, $path_saved);
        $path_temp = str_replace("[ID_DOMAINS]", $ID_domain, $path_temp);

        $tmp_expires	= $db->getField("expires")->getValue();

        $ID_showfiles	= $db->getField("ID");

        // BUILD RULES

        $params = array();
        $params_dbskip = array();
        $rule		= '/^\/' . $name;
        $rule_saved	= $rule . '\/saved';

        $db2->query("SELECT * FROM " . CM_TABLE_PREFIX . "showfiles_where WHERE ID_showfiles = " . $db2->toSql($db->getField("ID")) . " ORDER BY ID");
        if ($db2->nextRecord())
        {
            do
            {
                $rule_saved .= '\/([^\/]+)';
                $params[$db2->getField("name")->getValue()] = null;
                $params_dbskip[$db2->getField("name")->getValue()] = $db2->getField("dbskip")->getValue();
            } while ($db2->nextRecord());
        }

        $rule_saved_with_mode = $rule_saved;
        $rule_saved				.= '\/(.+)$/';
        $rule_saved_with_mode	.= '\/' . $rule_modes . '\/(.+)$/';

        $rule_temp				= $rule . '\/temp\/(.+)$/';
        $rule_temp_with_mode	= $rule . '\/temp\/' . $rule_modes . '\/(.+)$/';

        // CHECK RULES
        $saved = null;
        $mode = null;
        $filename = null;

        $matches = array();
        if (preg_match($rule_saved_with_mode, $path_info, $matches))
        {
            $saved = true;

            $i = 0;
            foreach ($params as $key => $value)
            {
                $i++;
                $params[$key] = $matches[$i];
            }
            reset($params);

            $i++;
            $mode = $matches[$i];

            $i++;
            $filename = $matches[$i];
        }
        elseif (preg_match($rule_saved, $path_info, $matches))
        {
            $saved = true;
            $i = 0;
            foreach ($params as $key => $value)
            {
                $i++;
                $params[$key] = $matches[$i];
            }
            reset($params);

            $i++;
            $filename = $matches[$i];
        }
        elseif (preg_match($rule_temp_with_mode, $path_info, $matches))
        {
            $saved = false;

            $mode = $matches[1];
            $filename = $matches[2];
        }
        elseif (preg_match($rule_temp, $path_info, $matches))
        {
            $saved = false;

            $filename = $matches[1];
        }

        if ($saved !== null && $filename !== null)
        {
            if ($tmp_expires)
                $expires = $tmp_expires;
            break;
        }
    } while ($db->nextRecord());

    if ($saved === null || $filename === null)
    {
        ffMedia::getInstance($path_info)->render();

        if(CM_SHOWFILES_ENABLE_DEBUG)
        {
            ffErrorHandler::raise("NO MATCHED RULES", E_USER_ERROR, null, get_defined_vars());
        }
        else
        {
            //$res = cm::_doEvent("showfiles_on_error", array("NO MATCHED RULES"));
            $res = $ff->showfiles_events->doEvent("on_error", array("NO MATCHED RULES"));
            $last_res = end($res);
            if ($last_res === null)
            {
                http_response_code(404);
                die("NO MATCHED RULES");
            }
        }
    }
}
else
{
    if(CM_SHOWFILES_ENABLE_DEBUG)
    {
        ffErrorHandler::raise("NO RULES DEFINED", E_USER_ERROR, null, get_defined_vars());
    }
    else
    {
        //$res = cm::_doEvent("showfiles_on_error", array("NO RULES DEFINED"));
        $res = $ff->showfiles_events->doEvent("on_error", array("NO RULES DEFINED"));
        $last_res = end($res);
        if ($last_res === null)
        {
            http_response_code(404);
            die("NO RULES DEFINED");
        }
    }
}

//cm::_doEvent("showfiles_before_parsing_path", array(&$path_temp, &$path_saved, &$params, &$db));
$ff->showfiles_events->doEvent("before_parsing_path", array(&$path_temp, &$path_saved, &$params, &$db, $name));

if(!is_file($filepath))
{
    $filepath = $base_path;
    if (!$saved)
    {
        $filepath .= str_replace("[_FILENAME_]", $filename, $path_temp);
    }
    else
    {
        $sWHERE = "";

        $filepath .= str_replace("[_FILENAME_]", $filename, $path_saved);
        foreach ($params as $key => $value)
        {
            $filepath = str_replace("[" . $key . "]", $value, $filepath);
            if (!$params_dbskip[$key])
            {
                if (strlen($sWHERE)) $sWHERE .= " AND ";
                $sWHERE .= $key . " = " . $db->toSql($value);
            }
        }
        reset($params);

        if (substr($source, 0, strlen("SELECT")) == "SELECT")
            $sSQL = $source;
        else
            $sSQL = "SELECT * FROM " . $source . " WHERE [WHERE] ";

        if (strlen($sWHERE))
        {
            $sSQL = str_replace("[AND]", " AND ", $sSQL);
            $sSQL = str_replace("[WHERE]", $sWHERE, $sSQL);
        } else {
            $sSQL = str_replace("[AND]", "", $sSQL);
            $sSQL = str_replace("[WHERE]", "", $sSQL);
        }

        $db->query($sSQL);
        if (!$db->nextRecord())
        {
            if(file_exists($filepath))
            {
                $db_file = $filename;
            }
            else
            {
                if(CM_SHOWFILES_ENABLE_DEBUG)
                {
                    ffErrorHandler::raise("NO MATCHED RECORD", E_USER_ERROR, null, get_defined_vars());
                }
                else
                {
                    //$res = cm::_doEvent("showfiles_on_error", array("NO MATCHED RECORD"));
                    $res = $ff->showfiles_events->doEvent("on_error", array("NO MATCHED RECORD"));
                    $last_res = end($res);
                    if ($last_res === null)
                    {
                        http_response_code(404);
                        die("NO MATCHED RECORD");
                    }
                }
            }
        }

        $db_file = $db->getField($field)->getValue();
        foreach ($db->fields_names as $key => $value)
        {
            $filepath = str_replace("[" . $value . "]", $db->getField($value, "Text", true), $filepath);
        }
        reset($db->fields_names);

        //cm::_doEvent("showfiles_get_file_from_db", array($name, $source, $sWHERE));
        $ff->showfiles_events->doEvent("get_file_from_db", array($name, $source, $sWHERE));

        if (!is_file($filepath))
        {
            if (
                substr(strtolower($db_file), 0, 7) == "http://"
                || substr(strtolower($db_file), 0, 8) == "https://"
                || substr($db_file, 0, 2) == "//"
            )
            {
                // try to get from remote
                @mkdir(dirname($filepath), 0777, true);

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL			, $db_file);
                curl_setopt($ch, CURLOPT_FILETIME		, true);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER	, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER	, true);

                $remote_file	= curl_exec($ch);
                $remote_mtime	= curl_getinfo($ch, CURLINFO_FILETIME);
                curl_close($ch);

                file_put_contents($filepath, $remote_file, LOCK_EX);

                if (!is_file($filepath))
                {
                    if(CM_SHOWFILES_ENABLE_DEBUG)
                    {
                        ffErrorHandler::raise("CANNOT DOWNLOAD IMAGE", E_USER_ERROR, null, get_defined_vars());
                    }
                    else
                    {
                        //$res = cm::_doEvent("showfiles_on_error", array("CANNOT DOWNLOAD IMAGE"));
                        $res = $ff->showfiles_events->doEvent("on_error", array("CANNOT DOWNLOAD IMAGE"));
                        $last_res = end($res);
                        if ($last_res === null)
                        {
                            http_response_code(404);
                            die("CANNOT DOWNLOAD IMAGE");
                        }
                    }
                }
                touch($filepath, $remote_mtime);
            }
        }
        else
        {
            if((substr($db_file, 0, strlen("http://")) == "http://")
                || (substr($db_file, 0, strlen("https://")) == "https://")
                || (substr($db_file, 0, 2) == "//")
            ) {
                // verify remote file for updates

                $local_mtime = filemtime($filepath);

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $db_file);
                curl_setopt($ch, CURLOPT_FILETIME, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);

                curl_exec($ch);
                $remote_mtime =  curl_getinfo($ch, CURLINFO_FILETIME);

                if ($remote_mtime !== $local_mtime || defined("FF_URLPARAM_NOCACHE"))
                {
                    curl_setopt($ch, CURLOPT_NOBODY, false);
                    curl_setopt($ch, CURLOPT_BINARYTRANSFER	, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER	, true);

                    $remote_file	= curl_exec($ch);
                    $remote_mtime	= curl_getinfo($ch, CURLINFO_FILETIME);
                    curl_close($ch);

                    @unlink($filepath);
                    file_put_contents($filepath, $remote_file, LOCK_EX);

                    if (!is_file($filepath))
                    {
                        if(CM_SHOWFILES_ENABLE_DEBUG)
                        {
                            ffErrorHandler::raise("CANNOT DOWNLOAD IMAGE", E_USER_ERROR, null, get_defined_vars());
                        }
                        else
                        {
                            //$res = cm::_doEvent("showfiles_on_error", array("CANNOT DOWNLOAD IMAGE"));
                            $res = $ff->showfiles_events->doEvent("on_error", array("CANNOT DOWNLOAD IMAGE"));
                            $last_res = end($res);
                            if ($last_res === null)
                            {
                                http_response_code(404);
                                die("CANNOT DOWNLOAD IMAGE");
                            }
                        }
                    }

                    touch($filepath, $remote_mtime);
                }
                else
                    curl_close($ch);
            }
        }
    }
}

if (!is_file($filepath))
{
    if(CM_SHOWFILES_ENABLE_DEBUG)
    {
        ffErrorHandler::raise("MISSING SOURCE FILE", E_USER_ERROR, null, get_defined_vars());
    }
    else
    {
        //$res = cm::_doEvent("showfiles_on_error", array("MISSING SOURCE FILE"));
        $res = $ff->showfiles_events->doEvent("on_error", array("MISSING SOURCE FILE"));
        $last_res = end($res);
        if ($last_res === null)
        {
            http_response_code(404);
            die("MISSING SOURCE FILE");
        }
    }
}

if (!$filepath)
{
    $res = $ff->showfiles_events->doEvent("on_warning", array($path_info, $_SERVER["HTTP_REFERER"], $mode));
    $last_res = end($res);
    if ($last_res)
    {
    	if(isset($last_res["base_path"]))
        	$base_path = $last_res["base_path"];
    	if(isset($last_res["filepath"]))
        	$filepath = $last_res["filepath"];
    	if(isset($last_res["mode"]))
        	$mode = $last_res["mode"];
    	if(isset($last_res["wizard"]))
        	$wizard = $last_res["wizard"];
    }
}

if($filepath && $mode)
{
    $final_ext = ffGetFilename($filepath, false);

    if(!is_array($imgParams))
        $imgParams = ffMedia::getModes();

    if(!$imgParams[$mode]) {
        if(!$wizard["mode"]) {
            if(stripos($mode, "x") !== false) {
                $wizard["mode"] 				= explode("x", strtolower($mode));
                $wizard["method"] 				= "proportional";
                $wizard["resize"] 				= false;
            } elseif(strpos($mode, "-") !== false) {
                $wizard["mode"] 				= explode("-", $mode);
                $wizard["method"] 				= "crop";
                $wizard["resize"] 				= false;
            }
        }

        if(count($wizard["mode"]) == 2 && is_numeric($wizard["mode"][0]) && is_numeric($wizard["mode"][1])) {
            $imgParams[$mode] = array(
                                "dim_x"     => $wizard["mode"][0]
                                , "dim_y"   => $wizard["mode"][1]
                                , "format"  => $final_ext
                                , "mode"    => $wizard["method"]
                                , "resize"  => $wizard["resize"]
                            );
        }
    }

    if(is_array($imgParams[$mode])) {
        $final_file = ffMedia::STORING_BASE_PATH . substr(dirname($filepath), strlen($base_path)) . "/" . ffGetFilename($filepath) // ffCommon_url_rewrite(ffGetFilename($filepath))
                    . "-" . $mode
                    . "." . $final_ext;

        if(!defined("FF_URLPARAM_NOCACHE") && is_file($final_file))
        {
            if(filectime($final_file) > $imgParams[$mode]["last_update"]
                && filectime($final_file) > filectime($filepath)
            ) {
                $is_valid_thumb = true;
            }
        }

        if (!is_file($final_file) || !$is_valid_thumb)
        {
            make_image($filepath, $final_file, $imgParams[$mode]);
        }
    }
} else {
    $final_file = $filepath;
}

if (!is_file($final_file))
{
	if(CM_SHOWFILES_ENABLE_DEBUG)
	{
		ffErrorHandler::raise("MISSING FINAL FILE", E_USER_ERROR, null, get_defined_vars());
	}
	else
	{
		//$res = cm::_doEvent("showfiles_on_error", array("MISSING FINAL FILE"));
		$res = $ff->showfiles_events->doEvent("on_error", array("MISSING FINAL FILE"));
		$last_res = end($res);
		if ($last_res === null)
		{
			http_response_code(404);
			die("MISSING FINAL FILE");
		}
	}
}

if ($expires)
{
	// $expires = (60 * 60 * 24 * $expires) * -1; // il -1 dipende da come viene gestito il file in ffMedia:sendHeaders (un if su < 0). Da verificare!!!
	$expires = (60 * 60 * 24 * $expires);
}

if (defined("FF_URLPARAM_GENCACHE"))
	@touch($final_file);

$etag = md5($final_file . filemtime($final_file));

$tmp_content = null;
$size = null;
if (!$cached && ($tmp_mime === "application/x-javascript" || $tmp_mime === "text/css"))
{
	$tmp_content = file_get_contents($final_file);
	$tmp_content = str_replace("{site_path}", FF_SITE_PATH, $tmp_content);
	$tmp_content = str_replace("{showfiles}", CM_SHOWFILES, $tmp_content);
	$size = mb_strlen($tmp_content, "8bit");
}

if(defined("FF_URLPARAM_NOCACHE"))
    ffMedia::optimize($final_file);


ffMedia::sendHeaders($final_file, array(
    "filename" => $filename
    , "expires" => $expires
    , "size" => $size
    , "etag" => $etag
));

// implementare If-Modified-Since

if (!defined("FF_URLPARAM_NOCACHE") && strlen($_SERVER["HTTP_IF_NONE_MATCH"]) && substr($_SERVER["HTTP_IF_NONE_MATCH"], 0, strlen($etag)) == $etag)
{
	http_response_code(304);
	exit;
}

if ($tmp_content === null)
	readfile($final_file);
else
	echo $tmp_content;
exit; // useless, but make me sleep

///////////////////////////////////////////////////////////////////////////////////////////////////////
// FUCNTIONS
///////////////////////////////////////////////////////////////////////////////////////////////////////


function make_image($source_file, $final_file, $params) {
	// NEED PROCESSING
	$default_params = array(
		"dim_x" => 1
		, "dim_y" => 1
		, "resize" => false
		, "alignment" => "center"
		, "mode" => "proportional"
		, "transparent" => true
		, "bgcolor" => "FFFFFF"
		, "alpha" =>  "0"
		, "format" => "jpg"
	);
	$params = array_replace_recursive($default_params, $params);

	if(0)
	{
		if(!$params["dim_x"] > 0)
			$params["dim_x"] = 1;

		if(!$params["dim_y"] > 0)
			$params["dim_y"] = 1;
			
		if($params["resize"] && $params["mode"] != "crop") {
		    $params["max_x"] = $params["dim_x"];
		    $params["max_y"] = $params["dim_y"];

		    $params["dim_x"] = null;
		    $params["dim_y"] = null;
		} else {
		    $params["max_x"]  = null;
		    $params["max_y"]  = null;
		}
		    
		if($params["format"] == "png" && $params["transparent"]) {
		    $params["bgcolor_csv"] = $params["bgcolor"];
		    $params["alpha_csv"] = 127;

		    $params["bgcolor_new"] = $params["bgcolor"];
		    $params["alpha_new"] = 127;
		} else {
		    $params["bgcolor_csv"] = null;
		    $params["alpha_csv"] = 0;

		    $params["bgcolor_new"] = $params["bgcolor"];
		    $params["alpha_new"] =  $params["alpha"];
		}
		if($params["force_icon"])
    		$params["filesource"] = ff_getAbsDir($params["force_icon"]) . $params["force_icon"];
		else
    		$params["filesource"] = $source_file;

		$params["wmk_word_enable"] = (is_dir($source_file)
	        ? $params["enable_thumb_word_dir"]
	        : $params["enable_thumb_word_file"]
	    );
	} else {
		if ($params["dim_x"] == 0)
			$params["dim_x"] = null;

		if ($params["dim_y"] == 0)
			$params["dim_y"] = null;

		if ($params["dim_x"] || $params["max_x"] == 0)
			$params["max_x"] = null;

		if ($params["dim_y"] || $params["max_y"] == 0)
			$params["max_y"] = null;

		$params["bgcolor_csv"] = $params["bgcolor"];
		$params["alpha_csv"] = $params["alpha"];

		$params["bgcolor_new"] = $params["bgcolor"];
		$params["alpha_new"] = $params["alpha"];
		
		$params["filesource"] = $source_file;
		$params["frame_color"] = null;
		$params["frame_size"] = 0;
		$params["wmk_method"] = "proportional";
		
		$params["wmk_word_enable"] = false;

	}
	if(!strlen($params["format"]))
		$params["format"] = "jpg";

	$cCanvas = new ffCanvas();

	$cCanvas->cvs_res_background_color_hex 			= $params["bgcolor_csv"];
	$cCanvas->cvs_res_background_color_alpha 		= $params["alpha_new"];
	$cCanvas->format 								= $params["format"];

	$cThumb = new ffThumb($params["dim_x"], $params["dim_y"]);
	$cThumb->new_res_max_x 							= $params["max_x"];
	$cThumb->new_res_max_y 							= $params["max_y"];
	$cThumb->src_res_path 							= $params["filesource"];

	$cThumb->new_res_background_color_hex 			= $params["bgcolor_new"];
	$cThumb->new_res_background_color_alpha			= $params["alpha_new"];

	$cThumb->new_res_frame_size 					= $params["frame_size"];
	$cThumb->new_res_frame_color_hex 				= $params["frame_color"];

	$cThumb->new_res_method 						= $params["mode"];
	$cThumb->new_res_resize_when 					= $params["when"];
	$cThumb->new_res_align 							= $params["alignment"];
	
	//Default Watermark Image
	if ($params["wmk_enable"])
	{
		$cThumb_wmk = new ffThumb($params["dim_x"], $params["dim_y"]);
		$cThumb_wmk->new_res_max_x 					= $params["max_x"];
		$cThumb_wmk->new_res_max_y 					= $params["max_y"];
		$cThumb_wmk->src_res_path 					= $params["wmk_file"];

		//$cThumb->new_res_background_color_hex = $params["bgcolor"];
		$cThumb_wmk->new_res_background_color_alpha	= "127";

		$cThumb_wmk->new_res_method 				= $params["mode"];
		$cThumb_wmk->new_res_resize_when 			= $params["when"];
		$cThumb_wmk->new_res_align 					= $params["wmk_alignment"];
		$cThumb_wmk->new_res_method 				= $params["wmk_method"];
		
		$cThumb->watermark 							= $cThumb_wmk;
		
		//$cCanvas->addChild($cThumb_wmk);
	}
	

	//Watermark Text
	if($params["wmk_word_enable"]) {
	    $cThumb->new_res_font["caption"] = $params["shortdesc"];
	    if(preg_match('/^[A-F0-9]{1,}$/is', strtoupper($params["word_color"])))
	        $cThumb->new_res_font["color"] = $params["word_color"];
	    if(is_numeric($params["word_size"]) && $params["word_size"] > 0)
	        $cThumb->new_res_font["size"] = $params["word_size"];
	    if(strlen($params["word_type"]))
	        $cThumb->new_res_font["type"] = $params["word_type"]; 
	    if(strlen($params["word_align"]))
	        $cThumb->new_res_font["align"] = $params["word_align"]; 
	}
	
	$cCanvas->addChild($cThumb);

	@mkdir(dirname($final_file), 0777, true);
	$cCanvas->process($final_file);
	
	$ff = ffGlobals::getInstance("ff");
	$res = $ff->showfiles_events->doEvent("make_file", array($source_file, $final_file, $params));
	/*$last_res = end($res);
	if ($last_res === null)
	{				
	}*/
}
