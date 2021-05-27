<?php

/**
 * Item
 * Classe di definizione prodotto
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

/**
 * Item
 * Implementazione concreta prodotto
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */
class Item extends Entity
{
    /**
     * Nome
     *
     * @var String $_name Nome
     */
    private $_name = null;

    /**
     * Descrizione
     * Opzionale
     *
     * @var String $_description Descrizione
     */
    private $_description = null;

    /**
     * Identificativo SKU
     * Opzionale
     *
     * @var String $_sku Identificativo
     */
    private $_sku = null;

    /**
     * Valore unitario
     *
     * @var Amount $_unitamount Valore monetario unitario
     */
    private $_unitamount = null;

    /**
     * Tasse
     *
     * @var Amount $_tax Valore monetario tasse
     */
    private $_tax = null;

    /**
     * Quantity
     *
     * @var Int $_quantity Quantità
     */
    private $_quantity = 1;

    /**
     * Assegnazione Nome
     *
     * @param String $name Nome
     *
     * @return Void
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Assegnazione Descrizione
     *
     * @param String $description Descrizione
     *
     * @return Void
     */
    public function setDescription($description)
    {
        $this->_description = $description;
    }

    /**
     * Assegnazione Identificativo Sku
     *
     * @param String $sku Identificativo Sku
     *
     * @return Void
     */
    public function setSku($sku)
    {
        $this->_sku = $sku;
    }

    /**
     * Assegnazione Valore unitario
     *
     * @param Amount $unitamount Valore monetario unitario
     *
     * @return Void
     */
    public function setUnitAmount($unitamount)
    {
        if (!$unitamount instanceof Amount) {
            return;
        }
        $this->_unitamount = $unitamount;
    }

    /**
     * Assegnazione Tasse
     *
     * @param Amount $tax Valore monetario Tasse
     *
     * @return Void
     */
    public function setTax($tax)
    {
        if (!$tax instanceof Amount) {
            return;
        }
        $this->_tax = $tax;
    }

    /**
     * Assegnazione quantità
     *
     * @param Int $quantity Quantità
     *
     * @return Void
     */
    public function setQuantity($quantity)
    {
        if (!is_int($quantity)) {
            return;
        }
        $this->_quantity = $quantity;
    }

    /**
     * Recupero Nome
     *
     * @return String or Null
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Recupero Descrizione
     *
     * @return String or Null
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Recupero Sku
     *
     * @return String or Null
     */
    public function getSku()
    {
        return $this->_sku;
    }

    /**
     * Recupero Valore unitario
     *
     * @return Amount or Null
     */
    public function getUnitAmount()
    {
        return $this->_unitamount;
    }

    /**
     * Recupero Tasse
     *
     * @return Amount or Null
     */
    public function getTax()
    {
        return $this->_tax;
    }

    /**
     * Recupero Quantità
     *
     * @return Int
     */
    public function getQuantity()
    {
        return $this->_quantity;
    }
}