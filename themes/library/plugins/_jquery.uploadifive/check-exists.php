<?php
/*
UploadiFive
Copyright (c) 2012 Reactive Apps, Ronnie Garcia
*/

$host_name = $_SERVER["HTTP_HOST"];
if (strpos(php_uname(), "Windows") !== false)
    $tmp_file = str_replace("\\", "/", __FILE__);
else
    $tmp_file = __FILE__;

if(strpos($tmp_file, $_SERVER["DOCUMENT_ROOT"]) !== false)
{
    $document_root =  $_SERVER["DOCUMENT_ROOT"];
    if (substr($document_root,-1) == "/")
        $document_root = substr($document_root,0,-1);

    $site_path = str_replace($document_root, "", str_replace("/themes/library/plugins/jquery.uploadifive/check-exists.php", "", $tmp_file));
    $disk_path = $document_root . $site_path;
}
elseif(strpos($tmp_file, $_SERVER["PHP_DOCUMENT_ROOT"]) !== false)
{
    $document_root =  $_SERVER["PHP_DOCUMENT_ROOT"];
    if (substr($document_root,-1) == "/")
        $document_root = substr($document_root,0,-1);

    $site_path = str_replace($_SERVER["DOCUMENT_ROOT"], "", str_replace("/themes/library/plugins/jquery.uploadifive/check-exists.php", "", $_SERVER["SCRIPT_FILENAME"]));
    $disk_path = $document_root . str_replace($document_root, "", str_replace("/themes/library/plugins/jquery.uploadifive/check-exists.php", "", $tmp_file));
} else {
    $st_disk_path = str_replace("/themes/library/plugins/jquery.uploadifive/check-exists.php", "", $tmp_file);
    $st_site_path = str_replace("/themes/library/plugins/jquery.uploadifive/check-exists.php", "", $_SERVER["SCRIPT_NAME"]);
}

define("DISABLE_CACHE", true);

define("SHOWFILES_IS_RUNNING", true);
//define("FF_SKIP_COMPONENTS", true);
define("CM_DONT_RUN", true);
define("FF_ERROR_HANDLER_HIDE", true);

require_once($disk_path . "/cm/main.php");

$referer = str_replace($_SERVER["HTTP_ORIGIN"] . FF_SITE_PATH, "", $_SERVER["HTTP_REFERER"]);
$arrReferer = explode("/", $referer);
if($arrReferer[1] == "domains")
    $domain_path = "/domains/" . $arrReferer[2];

/*
$valid_session = false;
if (isset($_POST[session_name()]))
{
	$valid_session = true;
	session_id($_POST[session_name()]);
}
elseif (isset($_GET[session_name()]))
{
	$valid_session = true;
	session_id($_GET[session_name()]);
}
elseif (isset($_COOKIE[session_name()]))
{
	$valid_session = true;
	session_id($_COOKIE[session_name()]);
}

@session_start();*/

if(0)
{
	$ff = get_session("ff");
	$data_src = basename($_REQUEST['sess']);
	
	if(
		is_array($ff)
		&& array_key_exists("uploadifive", $ff)
		&& is_array($ff["uploadifive"])
		&& array_key_exists($data_src, $ff["uploadifive"])
	)
	{
		$folder = $ff["uploadifive"][$data_src]["folder"];
		$base_path = $ff["uploadifive"][$data_src]["base_path"];
	}
	else
	{
		$folder = $_REQUEST['folder'];
		$base_path = FF_DISK_UPDIR . $domain_path;
	}
}
else
{
	$folder = $_REQUEST['folder'];
	$base_path = FF_DISK_UPDIR . $domain_path;
}

if(strpos($folder, FF_SITE_UPDIR) === 0)
	$base_path = FF_DISK_UPDIR . $domain_path;
elseif(strpos($folder, FF_THEME_DIR) === 0)
	$base_path = FF_DISK_PATH . FF_THEME_DIR . $domain_path;

if(!strlen($base_path))
	$base_path = FF_DISK_UPDIR . $domain_path;

if(!function_exists("ffGetFilename")) {
	function ffGetFilename($path, $return_name = true)
	{
	    $file_ext = pathinfo($path, PATHINFO_EXTENSION); 
	    $file_basename = basename($path);
	    if($file_ext)
	        $res = substr($file_basename, 0, strrpos($file_basename, "." . $file_ext));
	    else
	        $res = $file_basename;
	    
	    if($return_name)    
    		return $res;
		else
			return $file_ext;
	}
}
//ffErrorHandler::raise("as", E_USER_ERROR, null, get_defined_vars());

/*
if(strpos($_REQUEST['folder'], "/uploads") === 0)
    $base_path = "";

if(strpos($_REQUEST['folder'], FF_THEME_DIR) === 0)
    $base_path = "";
*/
$res = array();

$fileNormalize = $_REQUEST['fileNormalize'];

if($folder == "/")
{
	$relativePath = "/";
	$targetPath = $base_path . $folder;
}
else
{
	$relativePath = $folder . "/";
	$targetPath = $base_path . $folder . '/';
}

$targetFile = str_replace('//', '/', $targetPath) . $_REQUEST["filename"];

if($fileNormalize /*|| (function_exists("check_function") && check_function("check_fs") && function_exists("check_fs"))*/)
{
	//check_fs($targetFile, str_replace($base_path, "", $targetFile), false);
	$real_file = ffCommon_url_rewrite(ffGetFilename($targetFile))
			. (pathinfo($targetFile, PATHINFO_EXTENSION)
				? "." . ffCommon_url_rewrite(pathinfo($targetFile, PATHINFO_EXTENSION))
				: ""
			);
}
else
{
	$real_file = basename($targetFile);
}

if (file_exists($targetPath . $real_file))
	echo 1;
else 
	echo 0;
exit;

/*if(is_file($base_path . $relativePath . $real_file))
{
	@chmod($base_path . $relativePath . $real_file, 0777);

	ffMedia::optimize($base_path . $relativePath . $real_file, array("wait" => true));

	$res["name"] = basename($relativePath . $real_file); 
	$res["path"] = ffCommon_dirname($relativePath . $real_file); 
	$res["fullpath"] = $relativePath . $real_file;
	$res["status"] = true;			
}
else
{
	$res["error"] = ffTemplate::_get_word_by_code("upload_permission_denied");
	$res["status"] = false;	
}*/
