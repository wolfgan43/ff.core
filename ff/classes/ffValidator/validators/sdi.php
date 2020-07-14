<?php
/**
 * validator: sdi
 *
 * @package FormsFramework
 * @subpackage utils
 * @author Romolo Scarpato
 * @copyright Copyright (c) 2004-2020, Romolo Scarpato
 * @license https://opensource.org/licenses/LGPL-3.0
 * @link http://www.formsphpframework.com
 */

class ffValidator_sdi extends ffValidator_base
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
     * @param ffData valore immesso
     * @param String label del campo
     * @param <type> $options
     * @return boolean valodità del valore immesso
     */

    public function checkValue(ffData $value, $label, $options)
    {
        $sdi = $value->getValue();

        //controllo sulla lunghezza
        if(!strlen($sdi) || strlen($sdi) != 7)
            return "La lunghezza del codice \"$label\" inserito nel campo non è corretto: il codice sdi deve essere lungo esattamente 7 caratteri.";

        //controllo sul formato
        $sdi = strtoupper($sdi);
        if(!preg_match('/^[A-Za-z0-9]{7}$/', $sdi))
            return "Il formato del valore inserito per il campo \"$label\" non risulta corretto.";

        return false;
    }
}
