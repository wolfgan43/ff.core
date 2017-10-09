<?php
/**
 * @ignore
 * @package FormsFramework
 */

/**
 * @ignore
 * @package FormsFramework
 */
class ffCache // apc | memcached
{
	private static $singletons = null;
	
	/**
	 *
	 * @param type $eType
	 * @return ffCacheAdapter
	 */
	static public function getInstance($eType, $bNoTblRel = FF_CACHE_DEFAULT_TBLREL)
    {
		if (!strlen($eType))
			ffErrorHandler::raise("cache adpater type required", E_USER_ERROR, NULL, get_defined_vars());
		
		if (!isset(self::$singletons[$eType]))
		{
			require_once("adapters/" . $eType . "." . FF_PHP_EXT);
			$classname = "ffCache_" . $eType;
			self::$singletons[$eType] = new $classname($bNoTblRel);
		}
		
		return self::$singletons[$eType];
    }

	private function __construct()
	{
	}
}
