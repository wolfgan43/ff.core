<?php

/**
 * Amount
 * Classe di definizione di valore monetario
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */

namespace PayPalIntegration\Entity;

use PayPalIntegration\Entity\Entity;
use PayPalIntegration\Entity\Amount;
use PayPalIntegration\Entity\Item;

/**
 * Amount
 * Implementazione concreta valore monetario
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */
class Amount extends Entity
{
    /**
     * Valuta
     *
     * @var String $_currency Valuta
     */
    private $_currency = 'EUR';

    /**
     * Valore monetario
     *
     * @var Float $_value Valore
     */
    private $_value = 0.00;

    /**
     * Assegnazione valuta
     *
     * @param String $currency Valuta
     *
     * @return Void
     */
    public function setCurrency($currency)
    {
        $this->_currency = $currency;
    }

    /**
     * Assegnazione valore monetario
     *
     * @param Float $value Valore monetario
     *
     * @return Void
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Recupero valuta
     *
     * @return String or Null
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * Recupero valore monetario
     *
     * @return Float or Null
     */
    public function getValue()
    {
        return $this->_value;
    }
}