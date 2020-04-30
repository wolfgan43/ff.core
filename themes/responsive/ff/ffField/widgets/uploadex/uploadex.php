<?php
header('Access-Control-Allow-Origin: *');

//define("FF_SKIP_COMPONENTS", true);
define("CM_DONT_RUN", true);
//define("FF_ERROR_HANDLER_HIDE", true);

require "../../../../../../cm/main.php";

$res = array(); // output data

function uploadex_session_valid()
{
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
	return $valid_session;
}

$data_src = $_REQUEST['data_src'];
if (!strlen($data_src))
	exitWithError("data_src_missing");

$ff_data = array();
$options = null;

if (uploadex_session_valid())
{
	@session_start();

	$ff_data = get_session("ff");
	if (ffArrIsset($ff_data, "uploadex", $data_src))
		$options = $ff_data["uploadex"][$data_src];
}

if ($options === null)
{
	if (file_exists(FF_DISK_PATH . "/cache/uploadex/" . $data_src))
	{
		require FF_DISK_PATH . "/cache/uploadex/" . $data_src;
		if (ffArrIsset($ff_data, "uploadex", $data_src))
			$options = $ff_data["uploadex"][$data_src];
	}
}

if ($options === null)
	exitWithError("settings_not_found");

if (ffArrIsset($_REQUEST, "delaction"))
{
	$target_file = rtrim($options["storing_paths"]["temp"], "/") . "/" . $_REQUEST["delaction"];
	if (file_exists($target_file) && !$options["keep_old_one"])
	{
		@unlink($target_file);
		$res["status"] = true; // TODO: this value became useless here because overwritten
	}
}

if (ffArrIsset($_REQUEST, "filename"))
{
	$res["status"] = false;
	
	$name = $_REQUEST["filename"];
	if (!strlen($name))
		exitWithError("name_header_not_found");

	if ($options["avoid_temporary"])
		$tmp_name = $name;
	else
		$tmp_name = "tmp_" . date("YmdHms") . "_" . uniqid(rand(), true) . "_" . $name;

	$target_file = rtrim($options["storing_paths"]["temp"], "/") . "/" . $tmp_name;

	if (!is_dir(ffCommon_dirname($target_file)))
		@mkdir(ffCommon_dirname($target_file), 0777, true);

	if (is_file($target_file))
		@unlink($target_file);

	// process file's data and put to temporary
	$tot_bytes = 0;
	if (false === ($f = @fopen($target_file, "w")))
		exitWithError("upload_file_or_directory_error");
	if (false === ($s = @fopen("php://input", "r")))
		exitWithError("stream_error");

	while($kb = fread($s, 1024))
	{ 
		$tot_bytes += fwrite($f, $kb, 1024);
		if ($options["max_size"] && $totkb > $options["max_size"]) {
			fclose($f);
			fclose($s);
			@unlink($target_file);
			exitWithError("invalid_file_size");
		}
	}

	fclose($f);
	fclose($s);

	@chmod($target_file, 0777);
	if (!file_exists($target_file))
		exitWithError("upload_unknown_error");

	$file_mime = ffMimeType($target_file, $name);

	if(is_array($options["allowed_mime"]) && count($options["allowed_mime"])) 
	{
		foreach($options["allowed_mime"] AS $mimetype) 
		{
			if($file_mime === $mimetype) 
			{
				$check_ext = true;
				break;
			}
		}
		if(!$check_ext) 
		{
			@unlink($target_file);
			exitWithError("invalid_file_type");
		}
	}
	
	ffMedia::optimize($target_file, array("wait" => true));

	$res["name"] = $name; 
	$res["mime"] = $file_mime;
	$res["size"] = $tot_bytes;
	$res["status"] = true;
	
	if ($options["avoid_temporary"])
	{
		$type = "saved";
		$filename = $name;
	}
	else
	{
		$type = "temp";
		$res["tmpname"] = $tmp_name; 
		$filename = $tmp_name;
	}
	
	$tmp_fileinfo = pathinfo($filename);
	
	$tmp_view = $options["display_paths"][$type]["view"];
	$tmp_view = str_replace("[_FILENAME_]", $filename, $tmp_view);
	$tmp_view = str_replace("[_FILEONLYNAME_]", $tmp_fileinfo["filename"], $tmp_view);
	$tmp_view = str_replace("[_FILEONLYEXT_]", strlen($tmp_fileinfo["extension"]) ? "." . $tmp_fileinfo["extension"] : "", $tmp_view);
	
	$tmp_preview = $options["display_paths"][$type]["preview"];
	$tmp_preview = str_replace("[_FILENAME_]", $filename, $tmp_preview);
	$tmp_preview = str_replace("[_FILEONLYNAME_]", $tmp_fileinfo["filename"], $tmp_preview);
	$tmp_preview = str_replace("[_FILEONLYEXT_]", strlen($tmp_fileinfo["extension"]) ? "." . $tmp_fileinfo["extension"] : "", $tmp_preview);
	
	$res["view"] = $tmp_view;
	$res["preview"] = $tmp_preview;
}
//http_response_code(403);
echo ffCommon_jsonenc($res, true);
exit;

function exitWithError($error_code)
{
	$res["error"] = ffTemplate::_get_word_by_code("uploadex_" . $error_code);
	$res["status"] = false;
	
	echo ffCommon_jsonenc($res, true);
	exit;
}
