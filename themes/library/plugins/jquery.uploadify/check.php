<?php

$host_name = $_SERVER["HTTP_HOST"];
if (strpos(php_uname(), "Windows") !== false)
    $tmp_file = str_replace("\\", "/", __FILE__);
else
    $tmp_file = __FILE__;
    
if(strpos($tmp_file, $_SERVER["DOCUMENT_ROOT"]) !== false) {
	$document_root =  $_SERVER["DOCUMENT_ROOT"];
	if (substr($document_root,-1) == "/")
		$document_root = substr($document_root,0,-1);

	$site_path = str_replace($document_root, "", str_replace("/themes/library/plugins/jquery.uploadify/check.php", "", $tmp_file));
	$disk_path = $document_root . $site_path;
} elseif(strpos($tmp_file, $_SERVER["PHP_DOCUMENT_ROOT"]) !== false) {
	$document_root =  $_SERVER["PHP_DOCUMENT_ROOT"];
	if (substr($document_root,-1) == "/")
		$document_root = substr($document_root,0,-1);

	$site_path = str_replace($_SERVER["DOCUMENT_ROOT"], "", str_replace("/themes/library/plugins/jquery.uploadify/check.php", "", $_SERVER["SCRIPT_FILENAME"]));
	$disk_path = $document_root . str_replace($document_root, "", str_replace("/themes/library/plugins/jquery.uploadify/check.php", "", $tmp_file));
} else {
	$st_disk_path = str_replace("/themes/library/plugins/jquery.uploadify/check.php", "", $tmp_file);
	$st_site_path = str_replace("/themes/library/plugins/jquery.uploadify/check.php", "", $_SERVER["SCRIPT_NAME"]);
}

define("DISABLE_CACHE", true);

define("SHOWFILES_IS_RUNNING", true);
//define("FF_SKIP_COMPONENTS", true);
define("CM_DONT_RUN", true);
define("FF_ERROR_HANDLER_HIDE", true);

require_once($disk_path . "/cm/main.php");
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
$fileArray = array();
foreach ($_POST as $key => $value) {
	if ($key != 'folder') {
		if (file_exists(FF_DISK_PATH . FF_UPDIR . $_POST['folder'] . '/' . $value)) {
			$fileArray[$key] = $value;
		}
	}
}
echo json_encode($fileArray);
exit;
?>