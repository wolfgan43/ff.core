<?php
define("CM_LOCAL_APP_NAME", "DEV");
if(CM_LOCAL_APP_NAME == "not_set"){
    die("Inserisci CM_LOCAL_APP_NAME in /conf/cm/config.php");
}

switch (FF_ENV)
{
    case FF_ENV_DEVELOPMENT:
        define("CM_ENABLE_MEM_CACHING", false);
        define("CM_FILECACHE", false);
        define("FF_ERROR_HANDLER_HIDE", false);
        break;

    case FF_ENV_STAGING:
        define("CM_ENABLE_MEM_CACHING", false);
        define("CM_FILECACHE", false);
        define("FF_ERROR_HANDLER_HIDE", false);
        break;

    default:
        define("CM_SHOWFILES_ENABLE_DEBUG", false);
        define("FF_ERROR_HANDLER_HIDE", true);
}
