<?php
/**
 * validator: tel
 *
 * @package FormsFramework
 * @subpackage utils
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

/**
 * validator: tel
 *
 * @package FormsFramework
 * @subpackage utils
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2017, Samuele Diella
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */
class ffValidator_tel extends ffValidator_base
{
	static $_singleton = null;

	static function getInstance()
	{
		if (self::$_singleton === null)
			self::$_singleton = new self;

		return self::$_singleton;
	}

	/**
	 *
	 * @param ffData valore inserito
	 * @param string label del campo
	 * @param <type> $options
	 * @return boolean validità del valore inserito
	 */

     public function checkValue(ffData $value, $label, $options)
    {
        $tel = $value->getValue();
        //options
        //0 true
        //1 valore minimo ammesso(default null)
        //2 valore massimo ammesso(default null)
        //3 true=val minimo compreso nel controllo(di default false)
        //4 true=val massimo compreso nel controllo(di default false)
        //controllo formale
        if(preg_match('/^[0-9\+\s\-]*$/', $tel) < 1)
            return "Il valore inserito nel campo \"$label\" non è un telefono";

          return false;
    }
}
