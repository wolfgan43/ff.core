<?php

/**
 * PurchaseUnit
 * Classe di definizione di unità d'acquisto
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
 * PurchaseUnit
 * Implementazione concreta unità d'acquisto
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */
class PurchaseUnit extends Entity
{
    /**
     * Stringa personalizzabile
     *
     * @var String $_custom Stringa personalizzabile
     */
    private $_custom = null;

    /**
     * Elenco di elementi
     *
     * @var Array $_items elenco di elementi
     */
    private $_items = [];

    /**
     * Valore monetario costo di spedizione tasse incluse
     *
     * @var Amount $_shipping istanza valore monetario
     */
    private $_shipping = null;

    /**
     * Valore monetario complessivo
     *
     * @var Amount $_amount istanza valore monetario
     */
    private $_amount = null;

    /**
     * Assegnazione campo personalizzato
     *
     * @param String $custom Campo personalizzato
     *
     * @return Void
     */
    public function setCustom($custom)
    {
        $this->_custom = $custom;
    }

    /**
     * Inserimento elemento in elenco elementi
     *
     * @param Item $item Istanza elemento
     *
     * @return Void
     */
    public function addItem($item)
    {
      $this->_items[] = $item;
    }

    /**
     * Assegnazione valore monetario tasse incluse della spedizione
     *
     * @param Amount $shipping Istanza valore monetario spedizione
     *
     * @return Void
     */
    public function setShipping($shipping)
    {
      $this->_shipping = $shipping;
    }

    /**
     * Assegnazione valore monetario complessivo
     *
     * @param Amount $amount Istanza valore monetario complessivo
     *
     * @return Void
     */
    public function setAmount($amount)
    {
      $this->_amount = $amount;
    }

    /**
     * Recupero campo personalizzato
     *
     * @return String or Null
     */
    public function getCustom()
    {
        return $this->_custom;
    }

    /**
     * Recupero elementi in elenco elementi
     *
     * @return Array
     */
    public function getItems()
    {
      return $this->_items;
    }

    /**
     * Recupero valore monetario spedizione tasse incluse
     *
     * @return Amount or Null
     */
    public function getShipping()
    {
      return $this->_shipping;
    }

    /**
     * Recupero valore monetario complessivo
     *
     * @return Amount or Null
     */
    public function getAmount()
    {
      return $this->_amount;
    }
}