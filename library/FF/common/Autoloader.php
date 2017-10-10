<?php
namespace FF\Libs;

class Autoloader
{
    private $dir;

    public function __construct($dir = null)
    {
        if (is_null($dir))
            $dir = dirname(__DIR__);
		
        $this->dir = $dir;
    }
	
    /**
     * Registers as an SPL autoloader.
     */
    public static function register($dir = null)
    {
        ini_set("unserialize_callback_func", "spl_autoload_call");
        spl_autoload_register(array(new self($dir), "autoload"));
    }

    /**
     * Handles autoloading of classes.
     *
     * @param string $class A class name.
     *
     * @return boolean Returns true if the class has been loaded
     */
    public function autoload($class)
    {
        if (0 !== strpos($class, "FF\\Libs\\"))
            return;
		
        if (file_exists($file = $this->dir . "/" . str_replace("\\", "/", str_replace("FF\Libs\\", "", $class)) . "." . FF_PHP_EXT))
            require $file;
    }
}
