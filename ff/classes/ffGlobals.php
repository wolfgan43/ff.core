<?php
/**
 * namespace emulation
 * 
 * @package FormsFramework
 * @subpackage base
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

/**
 * namespace emulation
 *
 * Questa classe simula l'utilizzo dei namespace.
 * Non può essere istanziata direttamente, è necessario usare il metodo getInstance()
 *
 * @package FormsFramework
 * @subpackage base
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */
class ffGlobals
{
	private static $instances = null;

    public function __call($method, $args)
    {
        if (isset($this->$method)) {
            $func = $this->$method;
            return call_user_func_array($func, $args);
        }
    }

    private function __construct()
	{
	}

	/**
	 * Questa funzione restituisce un "finto" namespace sotto forma di oggetto attraverso il quale è possibile definire
	 * variabili ed oggetti in modo implicito (magic).
	 * 
	 * @param string $namespace il nome del namespace desiderato. Se omesso è "default"
	 * @return ffGlobals
	 */
	public static function getInstance($namespace = null)
	{
		if ($namespace == null)
			$namespace = "default";
		
		if (ffGlobals::$instances === null)
			ffGlobals::$instances = array();
		
		if (!isset(ffGlobals::$instances[$namespace]))
			ffGlobals::$instances[$namespace] = new ffGlobals();
			
		return ffGlobals::$instances[$namespace];
	}
}