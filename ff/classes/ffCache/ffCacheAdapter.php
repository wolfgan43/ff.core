<?php
/**
 * @ignore
 * @package FormsFramework
 */

/**
 * @ignore
 * @package FormsFramework
 */
define("FF_DISABLE_CACHE", defined("DEBUG_MODE") && isset($_REQUEST["__nocache__"]));

abstract class ffCacheAdapter
{
    const DISABLE_CACHE                                             = FF_DISABLE_CACHE;

    private $ttl            = ffCache::TTL;

	public abstract function set($name, $value = null, $bucket = ffCache::APPID);
	public abstract function get($name, $bucket = ffCache::APPID);
    public abstract function del($name, $bucket = ffCache::APPID);
	public abstract function clear($bucket = ffCache::APPID);


	protected function setTTL($val) {
	    $this->ttl = $val;

    }
    protected function getTTL() {
        return $this->ttl;
    }

    protected function getBucket($name = null) {
	    return ($name
            ? (substr($name, 0, 1) == "/"
                ? ffCache::APPID
                : ""
            ) . $name
            : ""
        );
    }
    protected function getKey($name, $bucket = null) {
        return ($bucket
            ? $this->getBucket($bucket) . "/" . ltrim($name, "/")
            : $name
        );
    }

    protected function setValue($value) {
        if(is_array($value)) {
            switch (ffCache::SERIALIZER) {
                case "PHP":
                    $value = serialize($value);
                    break;
                case "JSON":
                    $value = json_encode($value);
                    break;
                case "IGBINARY":
                    break;
                default:
            }
        }
	    return $value;
    }
    protected function getValue($value) {
        switch (ffCache::SERIALIZER) {
            case "PHP":
                $data = unserialize($value);
                break;
            case "JSON":
                $data = json_decode($value);
                break;
            case "IGBINARY":
                break;
            default:
        }
        return ($data === false
            ? $value
            : $data
        );
    }

}
