<?php
/*
Uploadify v2.1.0
Release Date: August 24, 2009

Copyright (c) 2009 Ronnie Garcia, Travis Nickels

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
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

	$site_path = str_replace($document_root, "", str_replace("/themes/library/plugins/jquery.uploadify/uploadify.php", "", $tmp_file));
	$disk_path = $document_root . $site_path;
}
elseif(strpos($tmp_file, $_SERVER["PHP_DOCUMENT_ROOT"]) !== false)
{
	$document_root =  $_SERVER["PHP_DOCUMENT_ROOT"];
	if (substr($document_root,-1) == "/")
		$document_root = substr($document_root,0,-1);

	$site_path = str_replace($_SERVER["DOCUMENT_ROOT"], "", str_replace("/themes/library/plugins/jquery.uploadify/uploadify.php", "", $_SERVER["SCRIPT_FILENAME"]));
	$disk_path = $document_root . str_replace($document_root, "", str_replace("/themes/library/plugins/jquery.uploadify/uploadify.php", "", $tmp_file));
} else {
	$st_disk_path = str_replace("/themes/library/plugins/jquery.uploadify/uploadify.php", "", $tmp_file);
	$st_site_path = str_replace("/themes/library/plugins/jquery.uploadify/uploadify.php", "", $_SERVER["SCRIPT_NAME"]);
}

define("DISABLE_CACHE", true);

define("SHOWFILES_IS_RUNNING", true);
//define("FF_SKIP_COMPONENTS", true);
define("CM_DONT_RUN", true);
define("FF_ERROR_HANDLER_HIDE", true);

require_once($disk_path . "/cm/main.php");

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
	session_id($_COOKIE[session_name()]);
}

@session_start();

$data_src = basename($_REQUEST['folder']);

if($valid_session)
{
	$ff = get_session("ff");

	if(
		is_array($ff)
		&& array_key_exists("uploadify", $ff)
		&& is_array($ff["uploadify"])
		&& array_key_exists($data_src, $ff["uploadify"])
	)
	{
		$folder = $ff["uploadify"][$data_src]["folder"];
		$base_path = $ff["uploadify"][$data_src]["base_path"];
	}
	else
	{
		$res = array(
			"status" => false
			, "error" => "Autentication: Access Denied"
		);

		//per motivi di sicurezza e stata dismessa l'opzione
		//Se si vuole usare l'upload senza sessione usare uploadifive
		$folder = "/";
		$base_path = FF_DISK_UPDIR;
	}
}
else
{
	$res = array(
		"status" => false
		, "error" => "Autentication: Required"
	);
	

	//per motivi di sicurezza e stata dismessa l'opzione
	//Se si vuole usare l'upload senza sessione usare uploadifive
	$folder = $_REQUEST['folder'];
	$base_path = FF_DISK_UPDIR;
}

if(is_array($res)) {
	echo ffCommon_jsonenc($res, true);
	exit;

}

if(strpos($folder, FF_SITE_UPDIR) === 0)
	$base_path = FF_DISK_UPDIR;
elseif(strpos($folder, FF_THEME_DIR) === 0)
	$base_path = FF_DISK_PATH . FF_THEME_DIR;

if(!strlen($base_path))
	$base_path = FF_DISK_UPDIR;


//ffErrorHandler::raise("as", E_USER_ERROR, null, get_defined_vars());

/*
if(strpos($_REQUEST['folder'], "/uploads") === 0)
    $base_path = "";

if(strpos($_REQUEST['folder'], FF_THEME_DIR) === 0)
    $base_path = "";
*/
$res = array();

if(!empty($_FILES))
{
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $base_path . $folder . '/';
	if($folder == "/")
	{
		$relativePath = "";
	}
	else
	{
		$relativePath = $folder . "/";
	}
	//$relativePath = "";
	$targetFile = strtolower(str_replace('//','/',$targetPath) . $_FILES['Filedata']['name']);

	// $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
	// $fileTypes  = str_replace(';','|',$fileTypes);
	// $typesArray = split('\|',$fileTypes);
	// $fileParts  = pathinfo($_FILES['Filedata']['name']);
	
	// if (in_array($fileParts['extension'],$typesArray)) {
		// Uncomment the following line if you want to make the directory if it doesn't exist
		// mkdir(str_replace('//','/',$targetPath), 0755, true);
		@mkdir(ffCommon_dirname($targetFile), 0777, true);
	    if(function_exists("check_function") && check_function("check_fs") && function_exists("check_fs"))
		{
        	//check_fs($targetFile, str_replace($base_path, "", $targetFile), false);
			$real_file = $relativePath . ffCommon_url_rewrite(ffGetFilename($targetFile))
			        . (pathinfo($targetFile, PATHINFO_EXTENSION)
			            ? "." . ffCommon_url_rewrite(pathinfo($targetFile, PATHINFO_EXTENSION))
			            : ""
			        );

			move_uploaded_file($tempFile, $targetPath . basename($real_file));
		}
		else
		{
			move_uploaded_file($tempFile, $targetFile);
			$real_file =  $relativePath . basename($targetFile);
		}
		
	 	
		if(is_file($base_path . $real_file))
		{
			@chmod($base_path . $real_file, 0777);
            ffMedia::optimize($base_path . $real_file);

			$res["name"] = basename($real_file);
			$res["path"] = ffCommon_dirname($real_file); 
			$res["fullpath"] = $real_file;
			$res["status"] = true;			
		}
		else
		{
			$res["error"] = ffTemplate::_get_word_by_code("upload_failed");
			$res["status"] = false;	
		}
}
elseif(isset($_REQUEST["delaction"]))
{
	$targetPath = $base_path . $folder . '/';
	if(file_exists($targetPath . $_REQUEST["delaction"]))
	{
		@unlink($targetPath . $_REQUEST["delaction"]);
		$res["status"] = true;
	}
}

echo ffCommon_jsonenc($res, true);
exit;