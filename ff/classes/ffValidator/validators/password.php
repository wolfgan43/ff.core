<?php
/**
 * validator: piva
 *
 * @package FormsFramework
 * @subpackage utils
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */

/**
 * validator: piva
 *
 * @package FormsFramework
 * @subpackage utils
 * @author Samuele Diella <samuele.diella@gmail.com>
 * @copyright Copyright (c) 2004-2010, Samuele Diella
 * @license http://opensource.org/licenses/gpl-3.0.html
 * @link http://www.formsphpframework.com
 */
class ffValidator_password extends ffValidator_base
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
	 * @param ffData Valore inserito nel campo piva
	 * @param String label del campo
	 * @param <type> $options
	 * @return boolean Validità del valore inserito
	 */

	public function checkValue(ffData $value, $label, $options)
	{
		$password = $value->getValue();

		//verifica formale dell'password
        if(strlen($password) < 8)
            return "Il valore inserito nel campo \"$label\" non soddisfa i criteri minimi di sicurezza: la lunghezza deve essere compresa tra gli 8 e i 30 caratteri";
        elseif(preg_match('/[a-z]+/', $password) < 1 )
            return "Il valore inserito nel campo \"$label\" non soddisfa i criteri minimi di sicurezza: deve essere presente almeno una lettera minuscola";
        elseif(preg_match('/[A-Z]+/', $password) < 1 )
            return "Il valore inserito nel campo \"$label\" non soddisfa i criteri minimi di sicurezza: deve essere presente almeno una lettera maiuscola";
        elseif(preg_match('/[0-9]+/', $password) < 1 )
            return "Il valore inserito nel campo \"$label\" non soddisfa i criteri minimi di sicurezza: deve essere presente almeno un numero";        

            

		return false;
	}
}
