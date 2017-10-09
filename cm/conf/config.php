<?php
/**
 * @package ContentManager
 * @subpackage config
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

if (!defined("CM_MIME_FORCE"))				define("CM_MIME_FORCE", false);
//if (!defined("CM_MULTIDOMAIN_ROUTING"))     define("CM_MULTIDOMAIN_ROUTING", false);
if (!defined("CM_DEFAULT_THEME"))			define("CM_DEFAULT_THEME", "restricted");
if (!defined("CM_TABLE_PREFIX"))			define("CM_TABLE_PREFIX", "cm_");
if (!defined("CM_LOCAL_APP_NAME"))			define("CM_LOCAL_APP_NAME", "FF CMS");
if (!defined("CM_IGNORE_THEME_DEFAULTS"))	define("CM_IGNORE_THEME_DEFAULTS", false);
if (!defined("CM_ENABLE_MEM_CACHING"))		define("CM_ENABLE_MEM_CACHING", false);
if (!defined("CM_ENABLE_PATH_CACHE"))		define("CM_ENABLE_PATH_CACHE", false); // will cache router respons for every url request. avoid if you have many urls
if (!defined("CM_CACHE_ROUTER_MATCH"))		define("CM_CACHE_ROUTER_MATCH", false); // will cache cache routing matches
if (!defined("CM_CACHE_ADAPTER"))			define("CM_CACHE_ADAPTER", FF_CACHE_ADAPTER);

if (!defined("CM_SHOWFILES_ENABLE_DEBUG"))	define("CM_SHOWFILES_ENABLE_DEBUG", true);
if (!defined("CM_SHOWFILES_FORCE_PATH"))	define("CM_SHOWFILES_FORCE_PATH", true);
if (!defined("CM_SHOWFILES_SKIP_DB"))		define("CM_SHOWFILES_SKIP_DB", false);
if (!defined("CM_SHOWFILES_THEME"))			define("CM_SHOWFILES_THEME", "restricted");
if (!defined("CM_SHOWFILES_ICON_PATH"))		define("CM_SHOWFILES_ICON_PATH", "/images/icons");
if (!defined("CM_SHOWFILES_ENABLE_GZIP"))   define("CM_SHOWFILES_ENABLE_GZIP", false);
if (!defined("CM_SHOWFILES_THUMB_PATH"))	define("CM_SHOWFILES_THUMB_PATH", "_thumb");
if (!defined("CM_SHOWFILES_THUMB_IN_CACHE"))define("CM_SHOWFILES_THUMB_IN_CACHE", false);
if (!defined("CM_SHOWFILES_EXTEND"))		define("CM_SHOWFILES_EXTEND", false);
if (!defined("CM_SHOWFILES_MODULES"))		define("CM_SHOWFILES_MODULES", false); // set it to false when u use .htaccess rewriting
if (!defined("CM_SHOWFILES_OPTIMIZE"))		define("CM_SHOWFILES_OPTIMIZE", false);

if (!defined("FF_ENABLE_MEM_SHOWFILES_CACHING"))		define("FF_ENABLE_MEM_SHOWFILES_CACHING", false); // set it to false when u don't use .htaccess rewriting

if (!defined("CM_CACHE_PURGE_JS"))   				define("CM_CACHE_PURGE_JS", false);
if (!defined("CM_CACHE_IMG_SET_DIMENSION"))   		define("CM_CACHE_IMG_SET_DIMENSION", false);
if (!defined("CM_CACHE_IMG_LAZY_LOAD"))   			define("CM_CACHE_IMG_LAZY_LOAD", false);
if (!defined("CM_CACHE_IMG_LAZY_LOAD_CSS"))   		define("CM_CACHE_IMG_LAZY_LOAD_CSS", false);
if (!defined("CM_CACHE_PATH_CONVERT_SHOWFILES"))	define("CM_CACHE_PATH_CONVERT_SHOWFILES", false);
if (!defined("CM_CACHE_CSS_INLINE_TO_STYLE"))   	define("CM_CACHE_CSS_INLINE_TO_STYLE", false);
if (!defined("CM_CACHE_STORAGE_SAVING_MODE"))   	define("CM_CACHE_STORAGE_SAVING_MODE", false);

if (!defined("CM_PAGECACHE"))					define("CM_PAGECACHE", false);
if (!defined("CM_PAGECACHE_DIR"))				define("CM_PAGECACHE_DIR", FF_DISK_PATH . "/cache/contents");
if (!defined("CM_PAGECACHE_GROUPHASH"))			define("CM_PAGECACHE_GROUPHASH", false);
if (!defined("CM_PAGECACHE_GROUPDIRS"))			define("CM_PAGECACHE_GROUPDIRS", false);
if (!defined("CM_PAGECACHE_MAXGROUPDIRS"))		define("CM_PAGECACHE_MAXGROUPDIRS", 10);
if (!defined("CM_PAGECACHE_WRITEALL"))			define("CM_PAGECACHE_WRITEALL", false); // write compressed and uncompressed at once
if (!defined("CM_PAGECACHE_SCALEDOWN"))			define("CM_PAGECACHE_SCALEDOWN", false); // use uncompressed if available when lacking compressed
if (!defined("CM_PAGECACHE_BYDOMAIN"))			define("CM_PAGECACHE_BYDOMAIN", false);
if (!defined("CM_PAGECACHE_BYDOMAIN_STRIPWWW"))	define("CM_PAGECACHE_BYDOMAIN_STRIPWWW", false);
if (!defined("CM_PAGECACHE_DEFAULT_MAXAGE"))	define("CM_PAGECACHE_DEFAULT_MAXAGE", 60 * 60); // in seconds, must be the hypotetic minimum value used by entire site
if (!defined("CM_PAGECACHE_USE_STRONG_CACHE"))	define("CM_PAGECACHE_USE_STRONG_CACHE", false); // when enabled, max-age is ignored ad expire is sent
if (!defined("CM_PAGECACHE_LAST_VALID"))		define("CM_PAGECACHE_LAST_VALID", 0); // UTC timestamp, used to reset cache from this date
if (!defined("CM_PAGECACHE_ASYNC"))				define("CM_PAGECACHE_ASYNC", false); // UTC timestamp, used to reset cache from this date
if (!defined("CM_PAGECACHE_KEEP_ALIVE"))		define("CM_PAGECACHE_KEEP_ALIVE", false);

if (!defined("CM_CSSCACHE_MINIFIER"))			define("CM_CSSCACHE_MINIFIER", "cssmin"); // this setting require files too into /library. can be: minify_css, cssmin, minify, gminify
if (!defined("CM_CSSCACHE_SHOWPATH"))			define("CM_CSSCACHE_SHOWPATH", CM_SHOWFILES);
if (!defined("CM_CSSCACHE_DIR"))				define("CM_CSSCACHE_DIR", FF_DISK_PATH . "/cache/css");
if (!defined("CM_CSSCACHE_GROUPDIRS"))			define("CM_CSSCACHE_GROUPDIRS", false);
if (!defined("CM_CSSCACHE_MAXGROUPDIRS"))		define("CM_CSSCACHE_MAXGROUPDIRS", 10);
if (!defined("CM_CSSCACHE_WRITEALL"))			define("CM_CSSCACHE_WRITEALL", false); // write compressed and uncompressed at once
if (!defined("CM_CSSCACHE_SCALEDOWN"))			define("CM_CSSCACHE_SCALEDOWN", false); // use uncompressed if available when lacking compressed
if (!defined("CM_CSSCACHE_BYDOMAIN"))			define("CM_CSSCACHE_BYDOMAIN", false);
if (!defined("CM_CSSCACHE_BYDOMAIN_STRIPWWW"))	define("CM_CSSCACHE_BYDOMAIN_STRIPWWW", false);
if (!defined("CM_CSSCACHE_DEFAULT_EXPIRES"))	define("CM_CSSCACHE_DEFAULT_EXPIRES", 604800); // in seconds, relative to creation date. null disabled (affect only smart urls)
if (!defined("CM_CSSCACHE_LAST_VALID"))			define("CM_CSSCACHE_LAST_VALID", 0); // UTC timestamp, used to reset cache from this date (affect only smart urls)
if (!defined("CM_CSSCACHE_SMARTURLS"))			define("CM_CSSCACHE_SMARTURLS", false);
if (!defined("CM_CSSCACHE_RENDER_PATH"))		define("CM_CSSCACHE_RENDER_PATH", false);
if (!defined("CM_CSSCACHE_RENDER_THEME_PATH"))	define("CM_CSSCACHE_RENDER_THEME_PATH", false);
if (!defined("CM_CSSCACHE_DEFERLOADING"))		define("CM_CSSCACHE_DEFERLOADING", 0);

if (!defined("CM_JSCACHE_MINIFIER"))			define("CM_JSCACHE_MINIFIER", "jsmin"); // this setting require files too into /library. can be: jsmin, pecl_jsmin (https://github.com/sqmk/pecl-jsmin), minify, gminify
if (!defined("CM_JSCACHE_SHOWPATH"))			define("CM_JSCACHE_SHOWPATH", CM_SHOWFILES);
if (!defined("CM_JSCACHE_DIR"))					define("CM_JSCACHE_DIR", FF_DISK_PATH . "/cache/js");
if (!defined("CM_JSCACHE_GROUPDIRS"))			define("CM_JSCACHE_GROUPDIRS", false);
if (!defined("CM_JSCACHE_MAXGROUPDIRS"))		define("CM_JSCACHE_MAXGROUPDIRS", 10);
if (!defined("CM_JSCACHE_WRITEALL"))			define("CM_JSCACHE_WRITEALL", false); // write compressed and uncompressed at once
if (!defined("CM_JSCACHE_SCALEDOWN"))			define("CM_JSCACHE_SCALEDOWN", false); // use uncompressed if available when lacking compressed
if (!defined("CM_JSCACHE_BYDOMAIN"))			define("CM_JSCACHE_BYDOMAIN", false);
if (!defined("CM_JSCACHE_BYDOMAIN_STRIPWWW"))	define("CM_JSCACHE_BYDOMAIN_STRIPWWW", false);
if (!defined("CM_JSCACHE_DEFAULT_EXPIRES"))		define("CM_JSCACHE_DEFAULT_EXPIRES", 604800); // in seconds, relative to creation date. null disabled (affect only smart urls)
if (!defined("CM_JSCACHE_LAST_VALID"))			define("CM_JSCACHE_LAST_VALID", 0); // UTC timestamp, used to reset cache from this date (affect only smart urls)
if (!defined("CM_JSCACHE_SMARTURLS"))			define("CM_JSCACHE_SMARTURLS", false);
if (!defined("CM_JSCACHE_DEFERLOADING"))		define("CM_JSCACHE_DEFERLOADING", false);
if (!defined("CM_MIME_FINFO"))					define("CM_MIME_FINFO", false);

if (!defined("CM_MEDIACACHE_SHOWPATH"))			define("CM_MEDIACACHE_SHOWPATH", null);
//CM_JQUERY_UI_THEME

$request_uri_parts = explode("?", $_SERVER["REQUEST_URI"]);
$ff_global_setting["ffWidget_actex"]["innerURL"] = rtrim($request_uri_parts[0], '/') . "/actexparse?" . $request_uri_parts[1];
$ff_global_setting["ffWidget_activecomboex"]["innerURL"] = rtrim($request_uri_parts[0], '/') . "/parsedata?" . $request_uri_parts[1];
$ff_global_setting["ffWidget_autocomplete"]["innerURL"] = rtrim($request_uri_parts[0], '/') . "/aparsedata?" . $request_uri_parts[1];
$ff_global_setting["ffWidget_autocompletex"]["innerURL"] = rtrim($request_uri_parts[0], '/') . "/aparsedatax?" . $request_uri_parts[1];
$ff_global_setting["ffWidget_autocompletetoken"]["innerURL"] = rtrim($request_uri_parts[0], '/') . "/atparsedata?" . $request_uri_parts[1];
