<?php

/**
 * PayPal
 * Interfaccia semplificata a Package PayPalIntegration
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */

namespace PayPalIntegration\Model;

use PayPalIntegration\Entity\PurchaseUnit as PurchaseUnitEntity;
use PayPalIntegration\Entity\Order as OrderEntity;
use PayPalIntegration\Order;

/**
 * PayPal
 * Implementazione modello di interazione
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */
class PayPal
{
    /**
     * Avvio processo di richiesta di autorizzazione di pagamento ordine
     * e redirezionamento a client PayPal
     *
     * @param PurchaseUnitEntity $purchaseunit Unità di Acquisto
     *
     * @return Void
     */
    public static function authorizePayment($purchaseunit)
    {
        if (!$purchaseunit instanceof PurchaseUnitEntity) {
            $message = "PayPalIntegration\Model\PayPal::authorizePayment La proprietà purchaseunit non è un istanza dell'entità PurchaseUnit";
            $code = 5028;
            throw new \Exception($message, $code);
        }
        $order = new OrderEntity();
        $order->setPurchaseUnit($purchaseunit);
        $response = Order::auth($order);
        $token = isset($response->result->id) ? $response->result->id : null;
        $links = isset($response->result->links) ? $response->result->links : [];
        $link = null;
        foreach ($links as $key => $value) {
            if (!isset($value->rel) || $value->rel != 'approve') {
                continue;
            }
            $link = $value->href;
        }
        if (is_null($token)) {
            $message = "PayPalIntegration\Model\PayPal::authorizePayment Errore di generazione numero ordine";
            $code = 5029;
            throw new \Exception($message, $code);
        }
        if (is_null($link)) {
            $message = "PayPalIntegration\Model\PayPal::authorizePayment Errore di generazione link di autorizzazione";
            $code = 5030;
            throw new \Exception($message, $code);
        }
        $custom = $purchaseunit->getCustom();
        
        static::beforeAuthorizing($custom, $token);

        header(sprintf("Location: %s", $link));
        exit;
    }

    /**
     * Avvio pagamento automatico se autorizzato
     *
     * Questo metodo recupera i parametri _GET inoltrati da paypal
     * a seguito di una richiesta di autorizzazione di pagamento e
     * avvia il processo di trasferimento del credito dal conto del
     * compratore a quello del venditore.
     *
     * E' importante a seguito dell'azione di recupero credito andare
     * a impostare come pagato un eventuale ordine di riferimento.
     *
     * @return Void
     */
    public static function makePayment()
    {
        try {

            $token = isset($_REQUEST['token']) ? (String)$_REQUEST['token'] : null;
            $response = Order::pay($token);
            $status = isset($response->result->status) ? $response->result->status : null;
            $custom = isset($response->result->purchase_units) ? reset($response->result->purchase_units)->custom_id : '';
            if ($status!='COMPLETED') {
                static::afterPayment($custom, $token, false);
                return;
            }
            static::afterPayment($custom, $token, true);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Hook precedente al ridirezionamento a PayPal per autorizzazion
     *
     * @param String $custom Valore custom ( Es. numero ordine interno )
     * @param String $token  Identificativo ordine PayPal
     *
     * @return Void
     */
    protected static function beforeAuthorizing($custom, $token)
    {
        $custom = is_null($custom) ? '--' : (String)$custom;
        printf("L'ordine interno %s è stato associato al token %s e viene inviato a paypal per l'autorizzazione", $custom, $token);
    }

    /**
     * Hook successivo al pagamento PayPal di un ordine
     *
     * @param String $custom Valore custom ( Es. numero ordine interno )
     * @param String $token  Identificativo ordine PayPal
     * @param Bool   $status Esito recupero credito
     *
     * @return Void
     */
    protected static function afterPayment($custom, $token, $status)
    {
        $custom = is_null($custom) ? '--' : (String)$custom;
        $response = $status ? 'è stato' : 'non è stato';
        printf("L'ordine interno %s con token %s %s pagato", $custom, $token, $response);
    }
}