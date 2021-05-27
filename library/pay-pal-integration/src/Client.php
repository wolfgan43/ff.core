<?php

/**
 * Client
 * Client connessione PayPal
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */

namespace PayPalIntegration;

use PayPalIntegration\Settings;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\PayPalEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;

/**
 * Client
 * Implementazione client per connessione a SDK PayPal
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */
class Client
{
    /**
     * Generazione istanza client
     *
     * @return PayPalHttpClient
     */
    public static function getInstance()
    {
        return new PayPalHttpClient(self::_environment());
    }

    /**
     * Creazione ambinete PayPal SDK con credenziali applicate
     *
     * @return PayPalEnvironment
     */
    private static function _environment()
    {
        $clientid = Settings::getClientId();
        if (is_null($clientid)) {
            $message = "PayPalIntegration\Client::_environment Identificativo PayPal Client ID non impostato";
            $code = 5001;
            throw new \Exception($message, $code);
        }
        $secret = Settings::getSecret();
        if (is_null($clientid)) {
            $message = "PayPalIntegration\Client::_environment Identificativo PayPal Secret non impostato";
            $code = 5002;
            throw new \Exception($message, $code);
        }
        return new ProductionEnvironment($clientid, $secret);
    }
}