<?php
	require_once($basepath . "FileManager/FileManagerPlugin.php");

	$host_name = $_SERVER["HTTP_HOST"];
    if (strpos(php_uname(), "Windows") !== false)
        $tmp_file = str_replace("\\", "/", __FILE__);
    else
        $tmp_file = __FILE__;
    
	if(strpos($tmp_file, $_SERVER["DOCUMENT_ROOT"]) !== false) {
		$document_root =  $_SERVER["DOCUMENT_ROOT"];
		if (substr($document_root,-1) == "/")
			$document_root = substr($document_root,0,-1);

		$site_path = str_replace($document_root, "", str_replace("/themes/library/tiny_mce/plugins/filemanager/config.php", "", $tmp_file));
		$disk_path = $document_root . $site_path;
	} elseif(strpos($tmp_file, $_SERVER["PHP_DOCUMENT_ROOT"]) !== false) {
		$document_root =  $_SERVER["PHP_DOCUMENT_ROOT"];
		if (substr($document_root,-1) == "/")
			$document_root = substr($document_root,0,-1);

		$site_path = str_replace($_SERVER["DOCUMENT_ROOT"], "", str_replace("/themes/library/tiny_mce/plugins/filemanager/config.php", "", $_SERVER["SCRIPT_FILENAME"]));
		$disk_path = $document_root . str_replace($document_root, "", str_replace("/themes/library/tiny_mce/plugins/filemanager/config.php", "", $tmp_file));
	} else {
		$st_disk_path = str_replace("/themes/library/tiny_mce/plugins/filemanager/config.php", "", $tmp_file);
		$st_site_path = str_replace("/themes/library/tiny_mce/plugins/filemanager/config.php", "", $_SERVER["SCRIPT_NAME"]);
	}

	define("DISABLE_CACHE", true);
	 
	define("SHOWFILES_IS_RUNNING", true);
	//define("FF_SKIP_COMPONENTS", true);
	define("CM_DONT_RUN", true);
	define("FF_ERROR_HANDLER_HIDE", true);

	require_once($disk_path . "/cm/main.php");

	error_reporting(E_ALL ^ E_NOTICE);

	//da cambiare senno esplode l'activecombo
	if(!defined("MOD_SECURITY_SESSION_STARTED")) { 
		mod_security_check_session();  

		//session_name("ckf_" . session_name());
	/*	if (!mod_security_check_session(false)) {
			mod_security_create_session(MOD_SEC_GUEST_USER_NAME, MOD_SEC_GUEST_USER_ID);
		}
	*/
	}
	
	if(!defined("MAX_UPLOAD"))
		define("MAX_UPLOAD", 10000000);
	if(!defined("DISK_UPDIR"))
		define("DISK_UPDIR", $disk_path . "/upload");
	if(!defined("AREA_GALLERY_SHOW_ADDNEW"))
		define("AREA_GALLERY_SHOW_ADDNEW", true);
	if(!defined("THUMB_CACHE_PATH"))
		define("THUMB_CACHE_PATH", "_thumb");
	
	if(!function_exists("stripslash")) {
		function stripslash($temp) {
			if (substr($temp,-1) == "/")
				$temp = substr($temp,0,-1);
			return $temp;
		}
	}

    $mcFileManagerConfig = get_session("mcFileManagerConfig");    

    if($mcFileManagerConfig == "" || strlen($src_page_url)) {
        //FormsTriggerError("E_USER_ERROR", E_USER_ERROR, NULL, get_defined_vars());
         
        if(is_dir(DISK_UPDIR . $user_path)) {
            $actual_path = DISK_UPDIR . $user_path;
        } else {
            $actual_path = DISK_UPDIR;
        }        
        
        $general_tools = "refresh,copy,paste,selectall,unselectall,view,download,addfavorite,removefavorite";
        if(AREA_GALLERY_SHOW_ADDNEW) {
            $general_tools .= ",createdir,createdoc,upload,rename,cut,delete,insert";
        }

	    // * * * * FileManager config
           
	    // General options
	    $mcFileManagerConfig['general.theme'] = "fm";
	    $mcFileManagerConfig['general.user_friendly_paths'] = true;
	    $mcFileManagerConfig['general.tools'] = $general_tools; //"createdir,createdoc,refresh,zip,upload,rename,cut,copy,paste,delete,selectall,unselectall,view,download,insert,addfavorite,removefavorite";
	    $mcFileManagerConfig['general.disabled_tools'] = "";
	    $mcFileManagerConfig['general.error_log'] = "";
	    $mcFileManagerConfig['general.language'] = strtolower(substr(FF_LOCALE, 0, -1));
	    $mcFileManagerConfig['general.plugins'] = "History,Favorites"; // comma seperated
	    $mcFileManagerConfig['general.demo'] = false;
	    $mcFileManagerConfig['general.debug'] = false;
	    $mcFileManagerConfig['general.encrypt_paths'] = true;
	    $mcFileManagerConfig['general.remember_last_path'] = false;
	    $mcFileManagerConfig['general.allow_override'] = "*";
	    $mcFileManagerConfig['general.allow_export'] = "demo,tools,disabled_tools,debug";

	    // Preview options
	    $mcFileManagerConfig['preview.wwwroot'] = ''; // absolute or relative from this script path (c:/Inetpub/wwwroot).
	    $mcFileManagerConfig['preview.urlprefix'] = "{proto}://{host}/"; // domain name
	    $mcFileManagerConfig['preview.urlsuffix'] = "";
	    $mcFileManagerConfig['preview.include_file_pattern'] = '';
	    $mcFileManagerConfig['preview.exclude_file_pattern'] = '';
	    $mcFileManagerConfig['preview.extensions'] = "*";
	    $mcFileManagerConfig['preview.allow_export'] = "urlprefix,urlsuffix";
	    $mcFileManagerConfig['preview.allow_override'] = "*";
                                       
	    // General file system options
	    $mcFileManagerConfig['filesystem'] = "Moxiecode_LocalFileImpl";
	    $mcFileManagerConfig['filesystem.path'] =  stripslash($actual_path); // 'files'; // absolute or relative from this script path.
	    $mcFileManagerConfig['filesystem.rootpath'] = stripslash(DISK_UPDIR . ROOT_PATH); //'files'; // absolute or relative from this script path.
	    $mcFileManagerConfig['filesystem.datefmt'] = "Y-m-d H:i";
	    $mcFileManagerConfig['filesystem.include_directory_pattern'] = '';
	    $mcFileManagerConfig['filesystem.exclude_directory_pattern'] = '/^' . THUMB_CACHE_PATH . '$/i'; //|^' . GALLERY_TPL_PATH . '$
	    $mcFileManagerConfig['filesystem.invalid_directory_name_msg'] = "";
	    $mcFileManagerConfig['filesystem.include_file_pattern'] = '';
	    $mcFileManagerConfig['filesystem.exclude_file_pattern'] = '/^\.|mcic_/i';
	    $mcFileManagerConfig['filesystem.invalid_file_name_msg'] = "";
	    $mcFileManagerConfig['filesystem.extensions'] = "gif,jpg,htm,html,pdf,zip,txt,php,png,swf,dcr,mov,qt,ram,rm,avi,mpg,mpeg,asf,flv,wmv";
	    $mcFileManagerConfig['filesystem.file_templates'] = '${rootpath}/templates/document.htm,${rootpath}/templates/another_document.htm';
	    $mcFileManagerConfig['filesystem.directory_templates'] = '${rootpath}/templates/directory,${rootpath}/templates/another_directory';
	    $mcFileManagerConfig['filesystem.readable'] = true;
	    $mcFileManagerConfig['filesystem.writable'] = true;
	    $mcFileManagerConfig['filesystem.delete_recursive'] = true;
	    $mcFileManagerConfig['filesystem.force_directory_template'] = false;
	    $mcFileManagerConfig['filesystem.allow_export'] = "extensions,readable,writable,file_templates,directory_templates,force_directory_template";
	    $mcFileManagerConfig['filesystem.allow_override'] = "*";

	    // Upload options
	    $mcFileManagerConfig['upload.maxsize'] = floor(MAX_UPLOAD / 1000) . "KB"; //"10MB";
	    $mcFileManagerConfig['upload.overwrite'] = true;
	    $mcFileManagerConfig['upload.include_file_pattern'] = '';
	    $mcFileManagerConfig['upload.exclude_file_pattern'] = '/[^a-zA-Z0-9_\.-]/';
	    $mcFileManagerConfig['upload.invalid_file_name_msg'] = ffTemplate::_get_word_by_code("file_manager_wrong_name");
	    $mcFileManagerConfig['upload.extensions'] = "gif,jpg,png,htm,html,pdf,txt,zip";
	    $mcFileManagerConfig['upload.use_flash'] = false;
	    $mcFileManagerConfig['upload.allow_export'] = "maxsize,use_flash,overwrite,extensions";
	    $mcFileManagerConfig['upload.allow_override'] = "*";

	    // Download options
	    $mcFileManagerConfig['download.include_file_pattern'] = "";
	    $mcFileManagerConfig['download.exclude_file_pattern'] = "";
	    $mcFileManagerConfig['download.extensions'] = "gif,jpg,htm,html,pdf,txt,zip";
	    $mcFileManagerConfig['download.allow_override'] = "*";

	    // Create document options
	    $mcFileManagerConfig['createdoc.fields'] = "Document title=title";
	    $mcFileManagerConfig['createdoc.include_file_pattern'] = '';
	    $mcFileManagerConfig['createdoc.exclude_file_pattern'] = '/[^a-zA-Z0-9_\.-]/';
	    $mcFileManagerConfig['createdoc.invalid_file_name_msg'] = ffTemplate::_get_word_by_code("file_manager_wrong_name");
	    $mcFileManagerConfig['createdoc.allow_export'] = "fields";
	    $mcFileManagerConfig['createdoc.allow_override'] = "*";

	    // Create directory options
	    $mcFileManagerConfig['createdir.include_directory_pattern'] = '';
	    $mcFileManagerConfig['createdir.exclude_directory_pattern'] = '/[^a-zA-Z0-9_\.-]/';
	    $mcFileManagerConfig['createdir.invalid_directory_name_msg'] = ffTemplate::_get_word_by_code("file_manager_wrong_name");
	    $mcFileManagerConfig['createdir.allow_override'] = "*";

	    // Rename options
	    $mcFileManagerConfig['rename.include_file_pattern'] = '';
	    $mcFileManagerConfig['rename.exclude_file_pattern'] = '/[^a-zA-Z0-9_\.-]/';
	    $mcFileManagerConfig['rename.invalid_file_name_msg'] = "";
	    $mcFileManagerConfig['rename.include_directory_pattern'] = '';
	    $mcFileManagerConfig['rename.exclude_directory_pattern'] = '/[^a-zA-Z0-9_\.-]/';
	    $mcFileManagerConfig['rename.invalid_directory_name_msg'] = ffTemplate::_get_word_by_code("file_manager_wrong_name");
	    $mcFileManagerConfig['rename.allow_override'] = "*";

	    // Authenication
	    $mcFileManagerConfig['authenticator'] = "BaseAuthenticator";
	    $mcFileManagerConfig['authenticator.login_page'] = "login_session_auth.php";
	    $mcFileManagerConfig['authenticator.allow_override'] = "*";

	    // SessionAuthenticator
	    $mcFileManagerConfig['SessionAuthenticator.logged_in_key'] = "isLoggedIn";
	    $mcFileManagerConfig['SessionAuthenticator.groups_key'] = "groups";
	    $mcFileManagerConfig['SessionAuthenticator.user_key'] = "user";
	    $mcFileManagerConfig['SessionAuthenticator.path_key'] = "mc_path";
	    $mcFileManagerConfig['SessionAuthenticator.rootpath_key'] = "mc_rootpath";
	    $mcFileManagerConfig['SessionAuthenticator.config_prefix'] = "filemanager";

	    // ExternalAuthenticator config
	    $mcFileManagerConfig['ExternalAuthenticator.external_auth_url'] = "auth_example.jsp";
	    $mcFileManagerConfig['ExternalAuthenticator.secret_key'] = "someSecretKey";

	    // Local filesystem options
	    $mcFileManagerConfig['filesystem.local.access_file_name'] = "mc_access";
	    $mcFileManagerConfig['filesystem.local.allow_override'] = "access_file_name";
	    $mcFileManagerConfig['filesystem.local.file_mask'] = "0777";
	    $mcFileManagerConfig['filesystem.local.directory_mask'] = "0777";
	    $mcFileManagerConfig['filesystem.allow_override'] = "*";

	    // Stream options
	    $mcFileManagerConfig['stream.mimefile'] = "mime.types";
	    $mcFileManagerConfig['stream.include_file_pattern'] = '';
	    $mcFileManagerConfig['stream.exclude_file_pattern'] = '/\.php$|\.shtm$/i';
	    $mcFileManagerConfig['stream.extensions'] = "*";
	    $mcFileManagerConfig['stream.allow_override'] = "*";

	    // Logging options
	    $mcFileManagerConfig['log.enabled'] = false;
	    $mcFileManagerConfig['log.level'] = "error"; // debug, warn, error
	    $mcFileManagerConfig['log.path'] = "logs";
	    $mcFileManagerConfig['log.filename'] = "{level}.log";
	    $mcFileManagerConfig['log.format'] = "[{time}] [{level}] {message}";
	    $mcFileManagerConfig['log.max_size'] = "100k";
	    $mcFileManagerConfig['log.max_files'] = "10";

	    // Image manager options
	    $mcFileManagerConfig['imagemanager.urlprefix'] = "../../../imagemanager/?type=im";  // need to add "imagemanager" button to tools as well.
	    $mcFileManagerConfig['imagemanager.allow_override'] = "*";
	    $mcFileManagerConfig['imagemanager.allow_export'] = "urlprefix";
        
        set_session("mcFileManagerConfig", $mcFileManagerConfig);
    } 
?>