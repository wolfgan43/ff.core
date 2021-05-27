<?php

/**
 * Entity
 * Classe astratta dedicata alle entità / oggetti
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */

namespace PayPalIntegration\Entity;

/**
 * Entity
 * Implementazione astratta entità generica
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */
abstract class Entity
{
    /**
     * Recupero proprietà in formato array associativo
     *
     * @return Array
     */
    public function asArray()
    {
        return [];
    }
}