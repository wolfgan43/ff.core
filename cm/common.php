<?php
/**
 * @package ContentManager
 * @subpackage common
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

function cm_getModulesExternalPath()
{
	if (CM_SHOWFILES_MODULES)
		return CM_SHOWFILES . CM_MODULES_PATH;
	else
		return CM_MODULES_PATH;
}

function cm_getAppName()
{
	if (defined("CM_USER_LOCAL_APP_NAME"))
		return CM_USER_LOCAL_APP_NAME;
	else
		return CM_LOCAL_APP_NAME;
}

function cm_confCascadeFind($base_path, $subpath, $file)
{
	$tmp = FF_DISK_PATH . "/conf" . $subpath . "/" . $file;

	if (is_file($tmp))
		return $tmp;
	elseif (is_file($base_path . "/" . $file))
		return $base_path . "/" . $file;
	else
		return null;
}

function cm_showfiles_guessfromurl($url, $default)
{
	if (strpos($url, "?") !== false)
		$url = substr($url, 0, strpos($url, "?") - 1);
	if (strrpos($url, "/") === false)
		$url = $default;
	else
		$url = substr($url, strrpos($url, "/") + 1);

	if (!strlen($url) || strpos($url, "/") !== false)
		$url = $default;

	return $url;
}

function cm_showfiles_get_abs_url($path = null) {
	static $res = null;

	if(!$res) 
	{
		if(substr(strtolower(CM_SHOWFILES), 0, 7) == "http://" || substr(strtolower(CM_SHOWFILES), 0, 8) == "https://" || substr(CM_SHOWFILES, 0, 2) == "//")
			$res = CM_SHOWFILES;
		else 
			$res = "http" . ($_SERVER["HTTPS"] ? "s" : "") . "://" . $_SERVER["HTTP_HOST"] . FF_SITE_PATH . CM_SHOWFILES;
	}
	
	return $res . $path;
}

function cm_getMainTheme()
{
	/*if (defined("CM_LOADED_THEME"))
		return CM_LOADED_THEME;
	else if (defined("CM_DEFAULT_THEME"))
		return CM_DEFAULT_THEME;
	else*/ if (defined("FF_LOADED_THEME"))
		return FF_LOADED_THEME;
	/*else if (defined("FF_DEFAULT_THEME"))
		return FF_DEFAULT_THEME;*/
	else
		ffErrorHandler::raise("CM API: can't guess Main Theme", E_USER_ERROR, null, get_defined_vars());
}

/**
 * Extract URLs from CSS text.
 * @author Alessandro Stucchi
 */
function cm_extract_css_urls($text)
{
    $urls = array();
    $url_pattern     = '(([^\'"\)]*)+)';
    $urlfunc_pattern = 'url\(\s*(?!data:)\s*[\'"]?' . $url_pattern . '[\'"]?\s*\)';
    $pattern         = '/(' .
         '(@import\s*[\'"]' . $url_pattern     . '[\'"])' .
        '|(@import\s*'      . $urlfunc_pattern . ')'      .
        '|('                . $urlfunc_pattern . ')'      .  ')/iu';

    if (!preg_match_all($pattern, $text, $matches)) {
        return preg_last_error();
	}


 	foreach ($matches[9] as $match)
        if (!empty($match))
            $urls[] = preg_replace( '/\\\\(.)/u', '\\1', $match );

    return $urls;
} 

/*
function cm_extract_css_urls($text) //original (if the url have cinna (,) the regexp failed with PREG_BACKTRACK_LIMIT_ERROR )
{
    $urls = array();
    $url_pattern     = '(([^\\\\\'", \(\)]*(\\\\.)?)+)';
    $urlfunc_pattern = 'url\(\s*(?!data:)\s*[\'"]?' . $url_pattern . '[\'"]?\s*\)';
    $pattern         = '/(' .
         '(@import\s*[\'"]' . $url_pattern     . '[\'"])' .
        '|(@import\s*'      . $urlfunc_pattern . ')'      .
        '|('                . $urlfunc_pattern . ')'      .  ')/iu';

    if (!preg_match_all($pattern, $text, $matches)) {
        return preg_last_error();
	}
	
    // @import '...'
    // @import "..."
    foreach ($matches[3] as $match)
        if (!empty($match))
            $urls[] = preg_replace( '/\\\\(.)/u', '\\1', $match );
 
    // @import url(...)
    // @import url('...')
    // @import url("...")
    foreach ($matches[7] as $match)
        if (!empty($match))
            $urls[] = preg_replace( '/\\\\(.)/u', '\\1', $match );
 
    // url(...)
    // url('...')
    // url("...")
    foreach ($matches[11] as $match)
        if (!empty($match))
            $urls[] = preg_replace( '/\\\\(.)/u', '\\1', $match );

            
    return $urls;
}
*/ 
function cm_canonicalize($address)
{
    $address = explode('/', $address);
    $keys = array_keys($address, '..');

    foreach($keys AS $keypos => $key)
    {
        array_splice($address, $key - ($keypos * 2 + 1), 2);
    }

    $address = implode('/', $address);
    $address = str_replace('./', '', $address);
    
    return $address;
}

/**
 * @author Alessandro Stucchi
 * 3/12/12 - Samuele Diella - Fixed encoding detect and management
 * @deprecated since 3/12/12
 */
function cm_parse_gzip($contents)
{
	if (ffHTTP_encoding_isset("gzip"))
		return gzencode($contents, 9, FORCE_GZIP);
	else
		return $contents;
}

// find valid cahce files and purge old ones
function cm_filecache_find($cache_dir, $path_dir, $group_subdir, $use_strong_cache, $last_valid, $scaledown, $now = null, $encodings = null, $purge_old = true)
{
	// ***********************************
	// init here to save a bit calculation
	if ($now === null)
		$now = time();
	
	// define cache indexes based on settings
	if (!$use_strong_cache)
		$cache_compress_idx = 3;
	else
		$cache_compress_idx = 2;

	// ***********************************

	$cache_file = null;
	if ($group_subdir)
	{
		$itGroup = new DirectoryIterator($cache_dir);
		foreach($itGroup as $fiGroup)
		{
			if($fiGroup->isDot())
				continue;

			$cache_file = cm_filecache_cycle_subgroup($fiGroup->getPathname(), $path_dir, $use_strong_cache, $last_valid, $scaledown, $now, $encodings, $purge_old, $cache_compress_idx);
			if ($cache_file !== null)
				return $cache_file;
		}
		return null;
	}
	else
	{
		return cm_filecache_cycle_subgroup($cache_dir, $path_dir, $use_strong_cache, $last_valid, $scaledown, $now, $encodings, $purge_old, $cache_compress_idx);
	}
}

// true = non expired, false = expired
function cm_filecache_check_expiration($mtime, $ctime, $now, $last_valid)
{
		if ($mtime > $now && (
				!$last_valid
				|| $now < $last_valid
				|| $ctime >= $last_valid
		))
			return true;
	else
		return false;
}

function cm_filecache_cycle_subgroup($subpath, $path_dir, $use_strong_cache, $last_valid, $scaledown, $now, $encodings, $purge_old, $cache_compress_idx)
{
	$cache_uncompressed = null;
	$cache_compressed = null;
	$cache_file = null;
	
	$find_dir = $subpath . $path_dir . "/";
	if (!file_exists($find_dir))
		return null;

	$itFiles = new DirectoryIterator($find_dir);
	foreach($itFiles as $fiFile)
	{
		if($fiFile->isDot())
			continue;

		$filematch = $fiFile->getPathname();

		$fmtime = $fiFile->getMTime();
		$fctime = $fiFile->getCTime();
		
		$fexp = cm_filecache_check_expiration($fmtime, $fctime, $now, $last_valid);
		
		if (CM_PAGECACHE_ASYNC || $fexp)
		{
			$filename = $fiFile->getFilename();
			$file_parts = explode(".", $filename);
			if (count($file_parts) > 1)
			{
				if (!$use_strong_cache)
				{
					if (!isset($file_parts[2]) || !is_numeric($file_parts[2]))
					{
						if ($purge_old)
							@unlink($filematch);
						continue;
					}
				}

				if (isset($file_parts[$cache_compress_idx]))
				{
					if (is_numeric($file_parts[$cache_compress_idx]))
					{
						if ($purge_old)
							@unlink($filematch);
					}
					else if (ffHTTP_encoding_isset($file_parts[$cache_compress_idx], $encodings))
					{
						if ($cache_compressed === null)
						{
							if (!$fexp)
							{
								if (
										!$last_valid
										|| $now < $last_valid
										|| $fctime >= $last_valid
									)  // fix values on async cache when in not over last valid
								{
									$diff = $fmtime - $fctime;
									$fctime = $now;
									$fmtime = $fctime + $diff;
								}
								else
								{
									if ($purge_old)
										@unlink($filematch);
									
									continue;
								}
							}
							
							$cache_compressed = array(
									"file" => $filematch
									, "filename" => $filename
									, "file_parts" => $file_parts
									, "fmtime" => $fmtime
									, "fctime" => $fctime
									, "compressed" => $file_parts[$cache_compress_idx]
								);
						}
						else if ($purge_old)
							@unlink($filematch);
					}
				}
				else
				{
					if ($cache_uncompressed === null)
					{
						if (!$fexp)
						{
							if (
									!$last_valid
									|| $now < $last_valid
									|| $fctime >= $last_valid
								)  // fix values on async cache when in not over last valid
							{
								$diff = $fmtime - $fctime;
								$fctime = $now;
								$fmtime = $fctime + $diff;
							}
							else
							{
								if ($purge_old)
									@unlink($filematch);

								continue;
							}
						}

						$cache_uncompressed = array(
								"file" => $filematch
								, "filename" => $filename
								, "file_parts" => $file_parts
								, "fmtime" => $fmtime
								, "fctime" => $fctime
								, "compressed" => false
							);
					}
					else if ($purge_old)
						@unlink($filematch);
				}
			}
			else if ($purge_old)
				@unlink($filematch);
		}
		else if ($purge_old)
			@unlink($filematch);
	}

	if ($cache_compressed || $cache_uncompressed)
		$cache_file = array(
			"compressed" => $cache_compressed
			, "uncompressed" => $cache_uncompressed
		);
	
	return $cache_file;
}

function cm_filecache_groupwrite($top_cache_dir, $cache_dir, $path_dir, $file, $buffer, $expires, $max_group_dirs, &$cache_group_dir, &$cache_disk_fail)
{
	$rc_cache = true;
	$cache_disk_fail = false;

	if ($cache_group_dir === null)
		$cache_group_dir = 0;
	else if ($cache_group_dir)
		$cache_group_dir--;
	
	do
	{
		$cache_group_dir++;

		if (!is_dir($cache_dir . "/" . $cache_group_dir))
			$cache_new_groupdir = true;
		else
			$cache_new_groupdir = false;

		if ($cache_new_groupdir || !is_dir($cache_dir . "/" . $cache_group_dir . $path_dir))
			$rc_cache = @mkdir($cache_dir . "/" . $cache_group_dir . $path_dir, 0777, true);
		if ($rc_cache)
		{
			if ($rc_cache = @file_put_contents($cache_dir . "/" . $cache_group_dir . $path_dir . "/" . $file, $buffer, LOCK_EX))
				@chmod($cache_dir . "/" . $cache_group_dir . $path_dir . "/" . $file, 0777);
			if (!$rc_cache && $cache_new_groupdir)
				$cache_disk_fail = true;
		}
		else if ($cache_new_groupdir)
			$cache_disk_fail = true;

	} while (!$rc_cache && !$cache_disk_fail && $cache_group_dir < $max_group_dirs);

	if ($rc_cache && $expires !== null)
	{
		$rc_cache = @touch($cache_dir . "/" . $cache_group_dir . $path_dir . "/" . $file, $expires);
		if (!$rc_cache)
			@unlink($cache_dir . "/" . $cache_group_dir . $path_dir . "/" . $file);
	} 
	else if ($cache_group_dir == $max_group_dirs)
	{
		@touch($cache_dir . "/maxgroup_limit_reached");
		if ($cache_dir != $top_cache_dir)
			@touch($top_cache_dir . "/maxgroup_limit_reached");
	}
	
	if ($cache_disk_fail)
	{
		@touch($cache_dir . "/disk_fail");
		if ($cache_dir != $top_cache_dir)
			@touch($top_cache_dir . "/disk_fail");
	}
	
	return $rc_cache;
}

function cm_filecache_write($path, $file, $buffer, $expires)
{
	$rc_cache = true;
	
	if (!is_dir($path))
		$rc_cache = @mkdir($path, 0777, true);	
	if ($rc_cache)
	{
		if ($rc_cache = @file_put_contents($path . "/" . $file, $buffer, LOCK_EX))
			@chmod($path . "/" . $file, 0777);
		if ($rc_cache && $expires !== null)
		{
			$rc_cache = @touch($path . "/" . $file, $expires);
			if (!$rc_cache)
				@unlink($path . "/" . $file);
		}
	}
	
	return $rc_cache;
}

function cm_filecache_empty_dir($path) 
{
    if(!file_exists($path) || !is_dir($path))
		return true;

	$directoryIterator = new DirectoryIterator($path);
    foreach($directoryIterator as $fileInfo)
	{
        $filePath = $fileInfo->getPathname();
        if(!$fileInfo->isDot())
		{
            if($fileInfo->isFile())
			{
                @unlink($filePath);
            } 
			elseif($fileInfo->isDir())
			{
				cm_filecache_empty_dir($filePath);
            }
        }
    }
	
	@rmdir($filePath);
}