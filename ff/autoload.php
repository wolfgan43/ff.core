<?php
/**
 * framework common functions
 *
 * @package FormsFramework
 * @subpackage common
 * @author Alessandro Stucchi <wolfgan@gmail.com>
 * @copyright Copyright (c) 2012-2020, Alessandro Stucchi
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

//if(defined("COMPOSER_PATH") && COMPOSER_PATH)
    require_once (__TOP_DIR__ . "/vendor/autoload.php");

spl_autoload_register(function ($class) {
    $php_ext = ".php";
    switch ($class) {
        case "ffEvent":
            require("classes/ffEvents/ffEvent" . $php_ext);
            break;
        case "ffEvents":
            require("classes/ffEvents/ffEvents" . $php_ext);
            break;
        case "ffData":
            require("classes/ffData/ffData" . $php_ext);
            break;
        case "ffValidator":
            require("classes/ffValidator/ffValidator" . $php_ext);
            break;
        case "ffErrorHandler":
            require("error_handling" . $php_ext);
            break;
        case "ffDBAdapter":
        case "ffDBConnection":
        case "ffDBField":
        case "ffDBIndex":
        case "ffDBRecord":
        case "ffDBRecordset":
        case "ffDBSource":
        case "ffDBTable":
        case "ffDBQuery":
            require("classes/ffDB/ffDBAdapter" . $php_ext);
            require("classes/ffDB/ffDBConnection" . $php_ext);
            require("classes/ffDB/ffDBField" . $php_ext);
            require("classes/ffDB/ffDBIndex" . $php_ext);
            require("classes/ffDB/ffDBRecord" . $php_ext);
            require("classes/ffDB/ffDBRecordset" . $php_ext);
            require("classes/ffDB/ffDBSource" . $php_ext);
            require("classes/ffDB/sources/ffDBTable" . $php_ext);
            require("classes/ffDB/sources/ffDBQuery" . $php_ext);
            break;
        case "PHPSQLCreator":
        case "PHPSQLParser":
            require("library/PHP-SQL-Parser/src/PHPSQLParser" . $php_ext);
            require("library/PHP-SQL-Parser/src/PHPSQLCreator" . $php_ext);
            break;
        case "ffXmlElement":
        case "ffXmlParser":
            require("lasses/ffXml/ffXmlParser" . $php_ext); // UNDER DEVELOPMENT
            require("classes/ffXml/ffXmlElement" . $php_ext); // UNDER DEVELOPMENT
            break;
        case "ffButton":
            require('classes/ffButton' . $php_ext);
            break;
        case "ffCommon":
            require('classes/ffCommon' . $php_ext);
            break;
        case "ffDetails":
            require('classes/ffDetails' . $php_ext);
            break;
        case "ffField":
            require('classes/ffField' . $php_ext);
            break;
        case "ffGrid":
            require('classes/ffGrid' . $php_ext);
            break;
        case "ffPage":
            require('classes/ffPage' . $php_ext);
            break;
        case "ffPageNavigator":
            require('classes/ffPageNavigator' . $php_ext);
            break;
        case "ffRecord":
            require('classes/ffRecord' . $php_ext);
            break;
        case "ffSerializable":
            require('classes/ffSerializable' . $php_ext);
            break;
        default:
    }
});


function ffRewritePathInfo($env = "_ffq_") {
    // normalize superglobals (to avoid bugs, server differences and others)
    if (isset($_SERVER["HTTP_HOST"]))
    {
        $_SERVER["HTTP_HOST"] = strtolower($_SERVER["HTTP_HOST"]);

        $fftmp_ffq = false;
        if (isset($_SERVER["argv"]))
        {
            foreach($_SERVER["argv"] AS $argv)
            {
                parse_str($argv, $tmp_request);
                if(isset($tmp_request[$env]))
                {
                    $_REQUEST[$env] = $tmp_request[$env];
                    break;
                }
                unset($tmp_request);
            }
        }
        if ($_REQUEST[$env]) // used to manage .htaccess [QSA] option, this overwhelm other options
        {
            $fftmp_ffq = true;
            $_SERVER["PATH_INFO"] = $_REQUEST[$env];
            $_SERVER["ORIG_PATH_INFO"] = $_REQUEST[$env];
        } elseif($_SERVER["PATH_INFO"] == "" && $_SERVER["REQUEST_URI"]) {
            $_SERVER["PATH_INFO"] = rtrim($_SERVER["QUERY_STRING"]
                ? rtrim($_SERVER["REQUEST_URI"],  $_SERVER["QUERY_STRING"])
                : $_SERVER["REQUEST_URI"]
                , "?");
        }
        //else if (isset($_SERVER["ORIG_PATH_INFO"]))
        //    $_SERVER["PATH_INFO"] = $_SERVER["ORIG_PATH_INFO"];

        if (strlen($_SERVER["QUERY_STRING"]))
        {
            $fftmp_new_querystring = "";
            $fftmp_parts = explode("&", rtrim($_SERVER["QUERY_STRING"], "&"));
            foreach ($fftmp_parts as $fftmp_value)
            {
                $fftmp_subparts = explode("=", $fftmp_value);
                if ($fftmp_subparts[0] == $env)
                    continue;
                if (!isset($_REQUEST[$fftmp_subparts[0]]))
                    $_REQUEST[$fftmp_subparts[0]] = (count($fftmp_subparts) == 2 ? rawurldecode($fftmp_subparts[1]) : "");
                $fftmp_new_querystring .= $fftmp_subparts[0] . (count($fftmp_subparts) == 2 ? "=" . $fftmp_subparts[1] : "") . "&";
            }
            if ($fftmp_ffq)
            {
                $_SERVER["QUERY_STRING"] = $fftmp_new_querystring;
                unset($_REQUEST[$env]);
                unset($_GET[$env]);
            }
            unset($fftmp_new_querystring);
            unset($fftmp_parts);
            unset($fftmp_value);
            unset($fftmp_subparts);
        }

        // fix request_uri. can't use code above due to multiple redirects (es.: R=401 and ErrorDocument in .htaccess)
        if (strpos($_SERVER["REQUEST_URI"], "?") !== false)
        {
            $fftmp_requri_parts = explode("?", $_SERVER["REQUEST_URI"]);
            if (strlen($fftmp_requri_parts[1]))
            {
                $fftmp_new_querystring = "";
                $fftmp_parts = explode("&", rtrim($fftmp_requri_parts[1], "&"));
                foreach ($fftmp_parts as $fftmp_value)
                {
                    $fftmp_subparts = explode("=", $fftmp_value);
                    if ($fftmp_subparts[0] == $env)
                        continue;
                    $fftmp_new_querystring .= $fftmp_subparts[0] . (count($fftmp_subparts) == 2 ? "=" . $fftmp_subparts[1] : "") . "&";
                }

                $_SERVER["REQUEST_URI"] = $fftmp_requri_parts[0] . "?" . $fftmp_new_querystring;

                unset($fftmp_new_querystring);
                unset($fftmp_parts);
                unset($fftmp_value);
                unset($fftmp_subparts);
            }
            unset($fftmp_requri_parts);
        }
    }

    // now check presence of server redirect to fix something
    /*if (isset($_SERVER["HTTP_HOST"]))
    {
        if (!$fftmp_ffq && isset($_SERVER["REDIRECT_URL"]))
        {
            if(strpos($_SERVER["REDIRECT_URL"], $_SERVER["SCRIPT_NAME"]) === 0)
                $_SERVER["PATH_INFO"] = substr($_SERVER["REDIRECT_URL"], strlen($_SERVER["SCRIPT_NAME"]));
            else if (FF_SITE_PATH !== "")
                $_SERVER["PATH_INFO"] = substr($_SERVER["REDIRECT_URL"], strlen(FF_SITE_PATH));
            else
                $_SERVER["PATH_INFO"] = $_SERVER["REDIRECT_URL"];
            $_SERVER["ORIG_PATH_INFO"] = $_SERVER["PATH_INFO"];
        }
        unset($fftmp_ffq);
    }*/
}