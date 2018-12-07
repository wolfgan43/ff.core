<?php
class Uploader extends vgCommon
{

    public static function getInstance($service = null)
    {
        if(!self::$singleton[$service]) {
            $auth                                                   = (self::$singleton
                ? self::$singleton
                : new Uploader($service)
            );
        }
        return self::$singleton[$service];
    }

    public function __construct($service = null)
    {
        $this->loadControllers(__DIR__);
        $this->service                                              = $service;

        require_once($this->getAbsPathPHP("/config"));

        //$this->setConfig($this->connectors, $this->services);
        //$this->loadSession();
    }


    public function push() {


    }


    private function getTemp($opt = null) {
        if(!empty($_FILES))
        {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $mimetype = ($_FILES['Filedata']['type']
                ? $_FILES['Filedata']['type']
                : ffMedia::getMimeTypeByFilename($_FILES['Filedata']['name'])
            );
            $fileExt = $_REQUEST['fileExt'];
            if(strtolower($fileExt) == "null")
                $fileExt = "";

            if(strlen($fileExt))
            {
                $arrFileExt = explode("|", $fileExt);
                if(is_array($arrFileExt) && count($arrFileExt))
                {
                    foreach($arrFileExt AS $arrFileExt_value)
                    {
                        if(strlen($arrFileExt_value) && strpos($mimetype, trim($arrFileExt_value, "*")) !== false)
                        {
                            $check_ext = true;
                            break;
                        }
                    }
                    if(!$check_ext) {
                        $res["error"] = ffTranslator::get_word_by_code("upload_invalid_file_type");
                        $res["status"] = false;
                    }
                }
            }

            $fileNormalize = $_REQUEST['fileNormalize'];

            if(!array_key_exists("status", $res))
            {
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

                //$relativePath = "";
                $targetFile = str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];
                // $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
                // $fileTypes  = str_replace(';','|',$fileTypes);
                // $typesArray = split('\|',$fileTypes);
                // $fileParts  = pathinfo($_FILES['Filedata']['name']);

                // if (in_array($fileParts['extension'],$typesArray)) {
                // Uncomment the following line if you want to make the directory if it doesn't exist
                // mkdir(str_replace('//','/',$targetPath), 0755, true);
                if(!is_dir(ffCommon_dirname($targetFile)))
                    @mkdir(ffCommon_dirname($targetFile), 0777, true);

                if($fileNormalize || (function_exists("check_function") && check_function("check_fs") && function_exists("check_fs")))
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

                @move_uploaded_file($tempFile, $targetPath . $real_file);

                if(is_file($base_path . $relativePath . $real_file))
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
                    $res["error"] = ffTranslator::get_word_by_code("upload_permission_denied");
                    $res["status"] = false;
                }
            }
        }
    }
}