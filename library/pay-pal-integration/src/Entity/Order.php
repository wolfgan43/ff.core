<?php

/**
 * Order
 * Classe di definizione di un entità Ordine PayPal
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */

namespace PayPalIntegration\Entity;

use PayPalIntegration\Entity\Entity;
use PayPalIntegration\Entity\PurchaseUnit;

/**
 * Order
 * Implementazione concreta entità ordine
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */
class Order extends Entity
{
    /**
     * Intento
     *
     * @var String $_intent intento
     */
    private $_intent = "CAPTURE";

    /**
     * Unità di acquisto
     *
     * @var PurchaseUnit $_purchaseunit Unità di acquisto
     */
    private $_purchaseunit = null;

    /**
     * Assegnazione intento
     *
     * @param String $intent Intento
     * 
     * @return Void
     */
    public function setIntent($intent)
    {
        $this->_intent = $intent;
    }

    /**
     * Assegnazione unità di acquisto
     *
     * @param PurchaseUnit $purchaseunit Istanza entità unità d'acquisto
     * 
     * @return Void
     */
    public function setPurchaseUnit($purchaseunit)
    {
        $this->_purchaseunit = $purchaseunit;
    }

    /**
     * Recupero intento
     *
     * @return String or Null
     */
    public function getIntent()
    {
        return $this->_intent;
    }

    /**
     * Recupero unità di acquisto
     *
     * @return PurchaseUnit or Null
     */
    public function getPurchaseUnit()
    {
        return $this->_purchaseunit;
    }

}