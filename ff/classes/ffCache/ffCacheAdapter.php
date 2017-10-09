<?php
/**
 * @ignore
 * @package FormsFramework
 */

/**
 * @ignore
 * @package FormsFramework
 */
abstract class ffCacheAdapter extends ffClassChecks
{
	var $relation_table = null;

	public abstract function set($name, $ttl, $value);
	public abstract function get($name, &$success);
	public abstract function clear();
}
