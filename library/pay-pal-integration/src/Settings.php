<?php

/**
 * Settings
 * Classe di configurazione
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */

namespace PayPalIntegration;

/**
 * Settings
 * Collection di parametri di configurazione
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */
class Settings
{
    /**
     * Client ID PayPal
     *
     * @var String $_clientid Client ID
     */
    private static $_clientid = null;

    /**
     * Secret PayPal
     *
     * @var String $_secret Secret
     */
    private static $_secret = null;

    /**
     * Url di ritorno a seguito del pagamento
     *
     * @var String $_returnurl Indirizzo URL
     */
    private static $_returnurl = null;

    /**
     * Url di ritorno a seguito di un pagamento fallito
     *
     * @var String $_cancelurl Indirizzo URL
     */
    private static $_cancelurl = null;

    /**
     * Nome / Ragione sociale venditore
     *
     * @var String $_brandname Venditore
     */
    private static $_brandname = null;

    /**
     * Localitò / Lingua
     *
     * @var String $_locale Locale
     */
    private static $_locale = null;

    /**
     * Assegnazione Client ID
     *
     * @param String $clientid Client ID
     *
     * @return Void
     */
    public static function setClientId($clientid)
    {
        if ((is_string($clientid) && !empty($clientid)) || is_null($clientid)) {
            self::$_clientid = $clientid;
        }
        return;
    }

    /**
     * Assegnazione Secret
     *
     * @param String $secret Secret
     *
     * @return Void
     */
    public static function setSecret($secret)
    {
        if ((is_string($secret) && !empty($secret)) || is_null($secret)) {
            self::$_secret = $secret;
        }
        return;
    }

    /**
     * Assegnazione URL di ritorno
     *
     * @param String $returnurl Indirizzo URL
     *
     * @return Void
     */
    public static function setReturnUrl($returnurl)
    {
        if ((is_string($returnurl) && filter_var($returnurl, FILTER_VALIDATE_URL)) || is_null($returnurl)) {
            self::$_returnurl = $returnurl;
        }
        return;
    }

    /**
     * Assegnazione URL di cancellazione processo pagamento
     *
     * @param String $cancelurl Indirizzo URL
     *
     * @return Void
     */
    public static function setCancelUrl($cancelurl)
    {
        if ((is_string($cancelurl) && filter_var($cancelurl, FILTER_VALIDATE_URL)) || is_null($cancelurl)) {
            self::$_cancelurl = $cancelurl;
        }
        return;
    }

    /**
     * Assegnazione nome / ragione sociale venditore
     *
     * @param String $brandname Indirizzo URL
     *
     * @return Void
     */
    public static function setBrandName($brandname)
    {
        if ((is_string($brandname) && !empty($brandname)) || is_null($brandname)) {
            self::$_brandname = $brandname;
        }
        return;
    }

    /**
     * Assegnazione località venditore ( Lingua )
     *
     * Per evitare validazione corposa e onerosa in termini di righe si consiglia
     * di valorizzare questa proprietà in relazione a quanto indicato dalla tabella
     * pubblicata nella documentazione PayPal ovvero al link sotto riportato.
     * La colonna di riferimento è: "BCP-47 code for REST APIs"
     *
     * @link  https://developer.paypal.com/docs/api/reference/locale-codes/
     * @param String $locale Locale
     *
     * @return Void
     */
    public static function setLocale($locale)
    {
        if ((is_string($locale) && strlen($locale)===5) || is_null($locale)) {
            self::$_locale = $locale;
        }
        return;
    }

    /**
     * Recupero Client ID
     *
     * @return String or Null
     */
    public static function getClientId()
    {
        return self::$_clientid;
    }

    /**
     * Recupero Secret
     *
     * @return String or Null
     */
    public static function getSecret()
    {
        return self::$_secret;
    }

    /**
     * Recupero URL di ritorno 
     *
     * @return String or Null
     */
    public static function getReturnUrl()
    {
        return self::$_returnurl;
    }

    /**
     * Recupero URL di cancellazione processo pagamento
     *
     * @return String or Null
     */
    public static function getCancelUrl()
    {
        return self::$_cancelurl;
    }

    /**
     * Recupero nome / ragione sociale venditore
     *
     * @return String or Null
     */
    public static function getBrandName()
    {
        return self::$_brandname;
    }

    /**
     * Recupero località venditore
     *
     * @return String or Null
     */
    public static function getLocale()
    {
        return self::$_locale;
    }
}