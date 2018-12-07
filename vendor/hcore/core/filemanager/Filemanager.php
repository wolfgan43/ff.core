<?php
/**
 *   VGallery: CMS based on FormsFramework
Copyright (C) 2004-2015 Alessandro Stucchi <wolfgan@gmail.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

 * @package VGallery
 * @subpackage core
 * @author Alessandro Stucchi <wolfgan@gmail.com>
 * @copyright Copyright (c) 2004, Alessandro Stucchi
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link https://github.com/wolfgan43/vgallery
 */

if(!defined("FTP_USERNAME"))                                    define("FTP_USERNAME", null);
if(!defined("FTP_PASSWORD"))                                    define("FTP_PASSWORD", null);

class Filemanager extends vgCommon
{
    private static $singleton                                           = null;
    private static $storage                                             = null;

    const FTP_USERNAME                                                  = FTP_USERNAME;
    const FTP_PASSWORD                                                  = FTP_PASSWORD;

    const SEARCH_IN_KEY                                                 = 1;
    const SEARCH_IN_VALUE                                               = 2;
    const SEARCH_IN_BOTH                                                = 3;
    const SEARCH_DEFAULT                                                = Filemanager::SEARCH_IN_KEY;

    const SCAN_DIR                                                      = 1;
    const SCAN_DIR_RECURSIVE                                            = 2;
    const SCAN_FILE                                                     = 4;
    const SCAN_FILE_RECURSIVE                                           = 8;
    const SCAN_ALL                                                      = 16;
    const SCAN_ALL_RECURSIVE                                            = 32;

    protected $services                                                 = array(                //servizi per la scrittura o lettura della notifica
        "fs"                                                            => null
    );
    protected $controllers                                              = array(
        "fs"                                                            => array(
            "default"                                                   => null
            , "services"                                                => null
        )
    );
    protected $controllers_rev                                          = null;
    protected $path                                                     = null;
    protected $var                                                      = null;
    protected $keys                                                     = null;
    protected $data                                                     = null;
    protected $expires                                                  = null;

    private $result                                                     = array();



    public static function xcopy($source, $destination) {
        $res                                                            = false;
        $ftp                                                            = self::ftp_xconnect();

        if($ftp) {
            $res                                                        = self::ftp_copy($ftp["conn"], $ftp["path"], $source, $destination, FF_DISK_PATH);
            @ftp_close($ftp["conn"]);
        }

        if(!$res) {
            self::full_copy(FF_DISK_PATH . $source, FF_DISK_PATH . $destination);
        }
        return $res;
    }

    public static function xpurge_dir($path) {
        $res                                                            = false;

        $ftp                                                            = self::ftp_xconnect();
        if($ftp) {
            $res                                                        = self::ftp_purge_dir($ftp["conn"], $ftp["path"], $path, FF_DISK_PATH);
            @ftp_close($ftp["conn"]);
        }

        if(!$res) {
            $res                                                        = self::purge_dir(FF_DISK_PATH . $path, $path);
        }

        return $res;
    }

    /**
     * FTP
     */
    private static function ftp_xconnect() {

        if(self::FTP_USERNAME && self::FTP_PASSWORD) {
            $conn_id = @ftp_connect("localhost");
            if($conn_id === false)
                $conn_id = @ftp_connect("127.0.0.1");
            if($conn_id === false)
                $conn_id = @ftp_connect($_SERVER["SERVER_ADDR"]);

            if($conn_id !== false) {
                // login with username and password
                if(@ftp_login($conn_id, self::FTP_USERNAME, self::FTP_PASSWORD)) {
                    $local_path = self::getDiskPath();
                    $part_path = "";
                    $real_ftp_path = NULL;

                    foreach(explode("/", $local_path) AS $curr_path) {
                        if(strlen($curr_path)) {
                            $ftp_path = str_replace($part_path, "", $local_path);
                            if(@ftp_chdir($conn_id, $ftp_path)) {
                                $real_ftp_path = $ftp_path;
                                break;
                            }

                            $part_path .= "/" . $curr_path;
                        }
                    }
                    if($real_ftp_path === NULL) {
                        if(@ftp_chdir($conn_id, "/")) {
                            $real_ftp_path = "";
                        }
                    }

                    if($real_ftp_path) {
                        $res = array(
                            "conn" => $conn_id
                            , "path" => $real_ftp_path
                        );
                    } else {
                        @ftp_close($conn_id);
                    }
                }
            }
        }

        return $res;
    }

    private static function ftp_copy($conn_id, $ftp_disk_path, $source, $dest, $local_disk_path = null) {
        $absolute_path = ffCommon_dirname($ftp_disk_path . $dest);

        $res = true;
        if (!@ftp_chdir($conn_id, $absolute_path)) {
            $parts = explode('/', trim(ffCommon_dirname($dest), "/"));
            @ftp_chdir($conn_id, $ftp_disk_path);
            foreach($parts as $part) {
                if(!@ftp_chdir($conn_id, $part)) {
                    $res = $res && @ftp_mkdir($conn_id, $part);
                    $res = $res && @ftp_chmod($conn_id, 0755, $part);

                    @ftp_chdir($conn_id, $part);
                }
            }

            if(!$res && $local_disk_path && !is_dir(ffCommon_dirname($local_disk_path . $dest))) {
                $res = @mkdir(ffCommon_dirname($local_disk_path . $dest), 0777, true);
            }
        }

        if($res) {

            if(!is_dir(FF_DISK_UPDIR . "/tmp"))
                $res = @mkdir(FF_DISK_UPDIR . "/tmp", 0777);
            elseif(substr(sprintf('%o', fileperms(FF_DISK_UPDIR . "/tmp")), -4) != "0777")
                $res = @chmod(FF_DISK_UPDIR . "/tmp", 0777);

            if($res) {
                $res = ftp_get($conn_id, FF_DISK_UPDIR . "/tmp/" . basename($dest), $ftp_disk_path . $source, FTP_BINARY);
                if($res) {
                    $res = $res && ftp_put($conn_id, $ftp_disk_path . $dest, FF_DISK_UPDIR . "/tmp/" . basename($dest), FTP_BINARY);

                    $res = $res && @ftp_chmod($conn_id, 0644, $ftp_disk_path . $dest);

                    @unlink(FF_DISK_UPDIR . "/tmp/" . basename($dest));
                }
            }
            if(!$res && $local_disk_path && !is_file($local_disk_path . $dest)) {
                $res = @copy($local_disk_path . $source, $local_disk_path . $dest);
                $res = $res && @chmod($local_disk_path . $dest, 0666);
            }
        }

        return $res;
    }

    private static function ftp_purge_dir($conn_id, $ftp_disk_path, $relative_path, $local_disk_path = null) {
        $absolute_path = $ftp_disk_path . $relative_path;

        $res = true;
        if (@ftp_chdir($conn_id, $absolute_path)) {
            $handle = @ftp_nlist($conn_id, "-la " . $absolute_path);
            if (is_array($handle) && count($handle)) {
                foreach($handle AS $file) {
                    if(basename($file) != "." && basename($file) != "..") {
                        if(strlen($ftp_disk_path))
                            $real_file = substr($file, strlen($ftp_disk_path));
                        else
                            $real_file = $file;

                        if (@ftp_chdir($conn_id, $file)) {
                            $res = ($res && self::ftp_purge_dir($conn_id, $ftp_disk_path, $real_file, $local_disk_path));
                            @ftp_rmdir($conn_id, $file);
                            if($local_disk_path !== null)
                                @rmdir($local_disk_path . $real_file);
                        } else {
                            if(!@ftp_delete($conn_id, $file)) {
                                if($local_disk_path === null) {
                                    $res = false;
                                } else {
                                    if(!@unlink($local_disk_path . $real_file)) {
                                        $res = false;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if(!@ftp_rmdir($conn_id, $absolute_path)) {
                if($local_disk_path === null) {
                    $res = false;
                } else {
                    if(!@rmdir($local_disk_path . $relative_path)) {
                        $res = false;
                    }
                }
            }
        } else {
            if(!@ftp_delete($conn_id, $absolute_path)) {
                if($local_disk_path === null) {
                    $res = false;
                } else {
                    if(!@unlink($local_disk_path . $relative_path)) {
                        $res = false;
                    }
                }
            }
        }
        return $res;
    }

    /**
     * FS
     */

    private static function full_copy( $source, $target, $delete_source = false ) {
        if(!$source || !$target || stripslash($source) == FF_DISK_UPDIR || stripslash($target) == FF_DISK_UPDIR || $source == $target)
            return;

        if (file_exists($source) && is_dir( $source ) ) {
            $disable_rmdir = false;

            @mkdir( $target, 0777, true );
            $d = dir( $source );
            while ( FALSE !== ( $entry = $d->read() ) ) {
                if (strpos($entry, ".") === 0 ) {
                    continue;
                }

                if($source . '/' . $entry == $target) {
                    $disable_rmdir = true;
                    continue;
                }
                if ( is_dir( $source . '/' . $entry )) {
                    self::full_copy( $source . '/' . $entry, $target . '/' . $entry, $delete_source );
                    //if($delete_source)
                    //rmdir($source . '/' . $entry);
                    continue;
                }

                @copy( $source . '/' . $entry, $target . '/' . $entry );
                @chmod( $target . '/' . $entry, 0777);
                if($delete_source)
                    @unlink($source . '/' . $entry);
            }

            $d->close();
            if($delete_source && !$disable_rmdir)
                @rmdir($source);
        } elseif(file_exists($source) && is_file($source)) {
            @mkdir( ffcommon_dirname($target), 0777, true );

            @copy( $source, $target );
            @chmod( $target, 0777);
            if($delete_source)
                @unlink($source);
        }
    }
    //Procedura per cancellare i file/cartelle e le correlazioni nel db
    private static function purge_dir($absolute_path, $relative_path, $delete_db = true, $exclude_dir = false) {
        if (file_exists($absolute_path) && is_dir($absolute_path)) {
            if ($handle = opendir($absolute_path)) {
                while (false !== ($file = readdir($handle))) {
                    if ($file != "." && $file != "..") {
                        if (is_dir($absolute_path . "/" . $file)) {
                            self::purge_dir($absolute_path . "/" . $file, $relative_path . "/" . $file);
                        } else {
                            if(is_file($absolute_path . "/" . $file))
                                @unlink($absolute_path . "/" . $file);
                            if($delete_db)
                                self::delete_file_from_db($relative_path, $file);
                        }
                    }
                }
                if(!$exclude_dir)
                    @rmdir ($absolute_path);
                if($delete_db)
                    self::delete_file_from_db(ffCommon_dirname($relative_path), basename($relative_path));
            }
        } else {
            if(file_exists($absolute_path) && is_file($absolute_path))
                @unlink($absolute_path);

            if($delete_db)
                self::delete_file_from_db(ffCommon_dirname($relative_path), basename($relative_path));
        }
    }

    /**
     * DB
     */
    private static function delete_file_from_db($strPath, $strFile, $exclude = null) {
        $db = ffDB_Sql::factory();

        if($exclude !== null && strlen($exclude)) {
            $sSQL_addit = " AND files.ID NOT IN (" . $db->toSql($exclude, "Text", false) . ")";
        } else {
            $sSQL_addit = "";
        }
        $sSQL = "SELECT ID from files WHERE parent = " . $db->toSql($strPath, "Text") . " AND name = " . $db->toSql($strFile, "Text") . $sSQL_addit;
        $db->query($sSQL);
        if ($db->nextRecord()) {
            $ID = $db->getField("ID")->getValue();
            $sSQL = "DELETE FROM files_rel_groups WHERE ID_files = " . $db->toSql($ID, "Number");
            $db->query($sSQL);

            $sSQL = "DELETE FROM files_rel_languages WHERE ID_files = " . $db->toSql($ID, "Number");
            $db->query($sSQL);

            $sSQL = "DELETE FROM files_description WHERE ID_files = " . $db->toSql($ID, "Number");
            $db->query($sSQL);

            $sSQL = "DELETE FROM rel_nodes WHERE contest_src = " . $db->toSql("files", "Text") . " AND ID_node_src = " . $db->toSql($ID, "Number");
            $db->query($sSQL);

            $sSQL = "DELETE FROM rel_nodes WHERE contest_dst = " . $db->toSql("files", "Text") . " AND ID_node_dst = " . $db->toSql($ID, "Number");
            $db->query($sSQL);

            $sSQL = "DELETE FROM files WHERE ID = " . $db->toSql($ID, "Number");
            $db->query($sSQL);
        }
    }

/*
    private static function recursiveiterator($pattern, $filter = null) {
        if(is_array($filter)) {
            $limit = '\.(?:' . implode("|", $filter). ')';
        }

        $directory = new RecursiveDirectoryIterator($pattern, RecursiveDirectoryIterator::SKIP_DOTS);
        $flattened = new RecursiveIteratorIterator($directory);

        // Make sure the path does not contain "/.Trash*" folders and ends eith a .php
        $files = new RegexIterator($flattened, '#^(?:[A-Z]:)?(?:/(?!\.Trash)[^/]+)+/[^/]+' . $limit . '$#Di');

        foreach ($files as $file) {
            self::scanAddItem($file->getPathname());
        }
    }*/
/*
    private static function DirectoryIterator($pattern, $recursive = false) {
        foreach (new DirectoryIterator($pattern) as $fileInfo) {
            if($fileInfo->isDot()) { continue; }
            if($fileInfo->isDir()) {
                self::scanAddItem($fileInfo->getPathname());
                if($recursive) {
                    self::DirectoryIterator($fileInfo->getPathname(), true);
                }
            }
        }
    }
    */
/*
    private static function RecursiveDirectoryIterator($pattern) {
        foreach ($iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($pattern,
                RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST) as $fileInfo) {

            if($fileInfo->isDir()) {
                self::scanAddItem($fileInfo->getPathname());
            }
        }
    }*/
    /*
    private static function readdir($pattern, $recursive = false) {
        if (($handle = opendir($pattern))) {
            while (($file = readdir($handle)) !== false) {
                if (($file == '.') || ($file == '..')) { continue; }

                if (is_dir($pattern . "/" . $file)) {
                    self::scanAddItem($pattern . "/" . $file);
                    if($recursive) {
                        self::readdir($pattern . "/" . $file, true);
                    }
                }
            }
            closedir($handle);
        }
    }*/
    public static function scan($patterns, $what = null, $callback = null) {
        if(is_array($patterns) && !$callback) {
            $callback = $what;
        }

        self::$storage["scan"]   = array(
            "rawdata" => array()
            , "callback" => $callback
        );

        if(is_array($patterns) && count($patterns)) {
            foreach($patterns AS $pattern => $opt) {
                self::scanRun($pattern, $opt);
            }
        } else {
            self::scanRun($patterns, $what);
        }

        return self::$storage;
    }

     private static function scanAddItem($file, $opt = null) {
        if(is_callable(self::$storage["scan"]["callback"])) {
            $file_info = pathinfo($file);
            if($opt["filter"] && !$opt["filter"][$file_info["extension"]]) {
                return;
            }
            if($opt["name"] && !$opt["name"][$file_info["basename"]]) {
                return;
            }

            $callback = self::$storage["scan"]["callback"];
            $callback($file, self::$storage);
        } elseif(!$opt) {
            self::$storage["scan"]["rawdata"][] = $file;
        } else {
            $file_info = pathinfo($file);
            if($opt["filter"] && !$opt["filter"][$file_info["extension"]]) {
                return;
            }
            if($opt["name"] && !$opt["name"][$file_info["basename"]]) {
                return;
            }

            if($opt["rules"] && !self::setStorage($file_info, $opt["rules"])) {
                self::$storage["unknowns"][] = $file;
            }
        }
    }

    private static function scanRun($pattern, $what = null) {
        $pattern = (strpos($pattern, self::$disk_path) === 0
                ? ""
                : self::$disk_path
            )
            . $pattern
            . (strpos($pattern, "*") === false
                ? '/*'
                : ''
            );

        $flag = ($what["flag"]
            ? $what["flag"]
            : $what
        );


        if($what["filter"] && isset($what["filter"][0])) {
            $what["filter"] = array_combine($what["filter"], $what["filter"]);
        }
        if($what["name"] && isset($what["name"][0])) {
            $what["name"] = array_combine($what["name"], $what["name"]);
        }

        switch ($flag) {
            case Filemanager::SCAN_DIR:
                if(self::$storage["scan"]["callback"]) {
                    self::glob_dir_callback($pattern);
                } else {
                    self::glob_dir($pattern);
                }
                break;
            case Filemanager::SCAN_DIR_RECURSIVE:
                self::rglob_dir($pattern);
                break;
            case Filemanager::SCAN_ALL:
                self::glob($pattern, false);
                break;
            case Filemanager::SCAN_ALL_RECURSIVE:
                self::rglobfilter($pattern, false);
                break;
            case Filemanager::SCAN_FILE:
                self::glob($pattern, $what);
                break;
            case Filemanager::SCAN_FILE_RECURSIVE:
                self::rglobfilter($pattern);
                break;
            case null;
                self::rglob($pattern);
                break;
            default:
                self::rglobfilter($pattern, $what);
        }
    }

    private static function glob_dir($pattern) {
        self::$storage["scan"] = glob($pattern, GLOB_ONLYDIR);
    }
    private static function glob_dir_callback($pattern) {
        foreach(glob($pattern, GLOB_ONLYDIR) AS $file) {
            self::scanAddItem($file);
        }
    }
    private static function rglob_dir($pattern) {
        foreach(glob($pattern, GLOB_ONLYDIR) AS $file) {
            self::scanAddItem($file);
            self::rglob_dir($file . '/*');
        }
    }

    private static function glob($pattern, $opt = null) {
        if(is_array($opt["filter"])) {
            $flags = GLOB_BRACE;
            $limit = ".{" . implode(",", $opt["filter"]) . "}";
        }

        foreach(glob($pattern . $limit, $flags) AS $file) {
            if($opt === false || is_file($file)) {
                self::scanAddItem($file, $opt["rules"]);
            }
        }
    }
    private static function rglob($pattern) {
        foreach(glob($pattern) AS $file) {
            if(is_file($file)) {
                self::scanAddItem($file);
            } else {
                self::rglob($file . '/*');
            }
        }
    }

    private static function rglobfilter($pattern, $opt = null) {
        $final_dir = basename(dirname($pattern)); //todo:: da togliere
        if ($final_dir == "node_modules") {
            return;
        }
        foreach(glob($pattern) AS $file) {
            if(is_file($file)) {
                self::scanAddItem($file, $opt);
            } else {
                if($opt === false) {
                    self::scanAddItem($file);
                }
                self::rglobfilter($file . '/*', $opt);
            }
        }
    }

    private static function setStorage($file_info, $rules) {
        if(is_array($rules) && count($rules)) {
            $key = $file_info["filename"];
            $file = $file_info["dirname"] . "/" . $file_info["basename"];

            foreach($rules AS $rule => $type) {
                if(strpos($file, $rule) !== false) {
                    self::$storage[$type][$key] = $file;
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * @param null $service
     * @param null $path
     * @param null $var
     * @return Filemanager|null
     */
    public static function getInstance($services = null, $path = null, $var = null)
    {
        if (self::$singleton === null) {
            self::$singleton                                            = new Filemanager($services, $path, $var);
        } else {
            self::$singleton->setServices(is_array($services)
                ? $services
                : array("fs" => $services)
            );
            self::$singleton->path                                      = $path;
            self::$singleton->var                                       = $var;
        }

        return self::$singleton;
    }

    public static function getAttr($item) {
        return ($item["@attributes"]
            ? $item["@attributes"]
            : $item
        );
    }

    /**
     * Filemanager constructor.
     * @param null $service
     * @param null $path
     * @param null $var
     */
    public function __construct($services = null, $path = null, $var = null)
    {
		$this->loadControllers(__DIR__);

    	$this->setServices(is_array($services)
            ? $services
            : array("fs" => $services)
        );

        $this->path                                                     = $path;
        $this->var                                                      = $var;
    }


    /**
     * @param null $path
     * @return bool
     */
    public function makeDir($path = null)
    {
        $rc = true;
        if(!$path)
			$path = dirname($this->path);

		if(!is_dir($path))
            $rc                                                         = @mkdir($path, 0777, ture);

        return $rc;
    }

    /**
     * @param null $keys
     * @param null $path
     * @param null $flags
     * @return array|bool|mixed|null
     */
    public function read($path = null, $keys = null, $flags = null)
    {
        $this->clearResult();
        if($this->isError())
            return $this->isError();
        else {
            $this->action                                               = "read";
            $this->keys                                                 = $keys;
            $this->controller(realpath($path), $flags);
        }

        return $this->getResult();
    }

    /**
     * @param null $data
     * @param null $var
     * @param null $expires
     * @param null $path
     * @return array|bool|mixed|null
     */
    public function write($data = null, $path = null, $var = null, $expires = null)
    {
        $this->clearResult();
        if($this->isError())
            return $this->isError();
        else {

            $this->action                                               = "write";
            $this->data                                                 = $data;
            $this->expires                                              = $expires;
            $this->controller($path, $var);
        }

        return $this->getResult();
    }

    /**
     * @param null $data
     * @param null $var
     * @param null $expires
     * @param null $path
     * @return array|bool|mixed|null
     */
    public function update($data = null, $path = null, $var = null, $expires = null)
    {
        $this->clearResult();
        if($this->isError())
            return $this->isError();
        else {
            $this->action                                               = "update";
            $this->data                                                 = $data;
            $this->expires                                              = $expires;
            $this->controller($path, $var);
        }

        return $this->getResult();
    }

    /**
     * @param null $keys
     * @param null $path
     * @param null $flags
     * @return array|bool|mixed|null
     */
    public function delete($keys = null, $path = null, $flags = null)
    {
        $this->clearResult();
        if($this->isError())
            return $this->isError();
        else {
			$this->action                                               = "delete";
            $this->keys                                                 = $keys;
            $this->controller($path, $flags);
        }

        return $this->getResult();
    }

    /**
     * @param $buffer
     * @param null $expires
     * @param null $path
     * @return bool|int
     */
    public function save($buffer, $expires = null, $path = null)
    {
		if(!$path)
    		$path = $this->getPath();

		$rc = $this->makeDir(dirname($path));
        if ($rc)
        {
            if ($rc = @file_put_contents($path, $buffer, LOCK_EX))
                @chmod($path, 0777);

            if ($rc && $expires !== null)
            {
                $this->touch($expires, $path);
            }
        }

		return $rc;
    }

    /**
     * @param $expires
     * @param null $path
     * @return bool
     */
    public function touch($expires, $path = null)
    {
    	if(!$path)
			$path = $this->getPath();

		$rc                                                             = @touch($path, $expires);
        //if (!$rc)
        //    @unlink($path);

        return $rc;
    }

    /**
     * @param null $path
     * @return bool
     */
    public function isExpired($path = null)
    {
    	if(!$path)
			$path = $this->getPath();

        return (filemtime($path) >= filectime($path)
            ? false
            : true
        );
    }

    /**
     * @param null $type
     * @return bool
     */
    public function exist($type = null) {
		$path = $this->getPath($type);

		return (is_file($path)
			? true
			: false
		);
	}


    /**
     * @param null $type
     * @param null $path
     * @return string
     */
    public function getPath($type = null, $path = null) {
		if(!$type) {
			$service = reset($this->services);
			$type = $service["default"];
		}
		if(!$path)
			$path = $this->path;

		if(!$path)
			$path = $this->path;

		return dirname($path) . "/" . basename($path, "." . $type) . "." . $type;
	}

    /**
     * @param null $path
     * @param null $flags
     */
    private function controller($path = null, $flags = null)
    {
        if($path)
            $this->path                                                = $path;

        foreach($this->services AS $controller => $services)
        {
            $this->isError("");

			$funcController = "controller_" . $controller;
            $this->$funcController((is_array($services)
                ? $services["service"]
                : $services
            ), $flags);

            if($this->action == "read" && $this->result)
                break;
        }
    }

    /**
     * @param null $service
     * @param null $flags
     */
    private function controller_fs($service = null, $flags = null)
    {
        if(!$service)
            $service                                                    = $this->services["fs"]["default"];

        if($service)
        {
            $type                                                       = "fs";
            $controller                                                 = "filemanager" . ucfirst($service);
            //require_once($this->getAbsPathPHP("/filemanager/services/" . $type . "_" . $service, true));

            $driver                                                     = new $controller($this);
            //$db                                                         = $driver->getDevice();
           // $config                                                     = $driver->getConfig();


            if(!$this->isError()) {
                switch($this->action)
                {
                    case "read":
                        $this->result = $driver->read($this->keys, $flags);
                        break;
                    case "update":
                        $this->result = $driver->update($this->data, $flags);
                        break;
                    case "write":
                        $this->result = $driver->write($this->data, $flags);
                        break;
                    case "delete":
                        $this->result = $driver->delete($this->keys, $flags);
                        break;
                    default:
                }
            }


        }


    }

    /**
     *
     */
    private function clearResult()
    {
        $this->keys                                                     = null;
        $this->data                                                     = null;
        $this->expires                                                  = null;
        $this->result                                                   = array();

        $this->isError("");
    }

    /**
     * @return array|bool|mixed|null
     */
	private function getResult($service = null)
	{
		return ($this->isError()
			? $this->isError()
			: ($service
				? $this->result[$service]
				: $this->result
			)
		);
	}

    /*private function getResult()
    {
        return ($this->isError()
            ? false
            : ($this->result
                ? (is_array($this->keys) || count($this->result) > 1
                    ? $this->result
                    : array_shift($this->result)
                )
                : null
            )
        );
    }*/
}