<?php

/**
 * Order
 * Ordine PayPal
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */

namespace PayPalIntegration;

use PayPalIntegration\Settings;
use PayPalIntegration\Client;
use PayPalIntegration\Entity\Order as OrderEntity;
use PayPalIntegration\Entity\Amount as AmountEntity;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

/**
 * Order
 * Implementazione classe dedicata alla generazione di ordini finalizzati
 * al pagamento
 *
 * PHP version 5.6
 *
 * @category  Class
 * @package   PayPalIntegration
 * @author    Federico Maffucci <federico.maffucci@gmail.com>
 */
class Order
{
    /**
     * Avvio processo di autorizzazione di pagamento ordine
     *
     * Questo processo ritorna un oggetto di risposta con tutti i parametri
     * necessari per procedere all'inoltro dell'utente alla pagina di autorizzazione
     * del pagamento.
     *
     * @param OrderEntity $order Entità ordine
     *
     * @return PayPalHttp\HttpResponse
     */
    public static function auth($order)
    {
        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = self::buildRequestBody($order);
        $client = Client::getInstance();
        return $client->execute($request);
    }

    /**
     * Avvio processo di pagamento ordine dato identificativo PayPal
     *
     * Questo processo effettua una chiamata server-side a paypal con 
     * l'identificativo d'ordine impostato. Quando l'ordine è stato
     * precedentemente autorizzato il pagamento viene effettuato
     * istantanemante, al contrario, se l'ordine è privo di autorizzazione
     * dell'utente il pagamento non viene erogato. ( Tutto descritto dal response )
     *
     * @param String $id Identificativo Ordine PayPal
     *
     * @return PayPalHttp\HttpResponse
     */
    public static function pay($id)
    {
        $request = new OrdersCaptureRequest($id);
        $request->prefer('return=representation');
        $client = Client::getInstance();
        try {
            return $client->execute($request);
        } catch (\Exception $ex) {
            $message = "Impossibile finalizzare il pagamento, prova più tardi";
            throw new \Exception($message);
        }
    }

    /**
     * Parsificazione entità ordine
     *
     * @param OrderEntity $order Istanza entità ordine
     *
     * @return Array
     */
    public static function buildRequestBody($order)
    {
        if (!$order instanceof OrderEntity) {
            $message = "PayPalIntegration\Order::buildRequestBody order non è un istanza di PayPalIntegration\Entity\Order";
            $code = 5010;
            throw new \Exception($message, $code);
        }

        $body = [];
        $body['intent'] = null;
        $body['application_context'] = [];
        $body['application_context']['return_url'] = null;
        $body['application_context']['cancel_url'] = null;
        $body['application_context']['brand_name'] = null;
        $body['application_context']['locale'] = null;
        $body['purchase_units'] = [];

        $tmp = [];
        $tmp['amount']['currency_code'] = null;
        $tmp['amount']['value'] = null;
        $tmp['items'] = [];

        $returnurl = Settings::getReturnUrl();
        if (is_null($returnurl)) {
            $message = "PayPalIntegration\Order::buildRequestBody la proprietà setting->returnurl non può essere nulla";
            $code = 5011;
            throw new \Exception($message, $code);
        }
        $body['application_context']['return_url'] = $returnurl;

        $cancelurl = Settings::getCancelUrl();
        if (is_null($cancelurl)) {
            $message = "PayPalIntegration\Order::buildRequestBody la proprietà setting->cancelurl non può essere nulla";
            $code = 5012;
            throw new \Exception($message, $code);
        }
        $body['application_context']['cancel_url'] = $cancelurl;

        $brand = Settings::getBrandName();
        if (is_null($brand)) {
            $message = "PayPalIntegration\Order::buildRequestBody la proprietà setting->brand non può essere nulla";
            $code = 5013;
            throw new \Exception($message, $code);
        }
        $body['application_context']['brand_name'] = $brand;

        $locale = Settings::getLocale();
        if (is_null($locale)) {
            $message = "PayPalIntegration\Order::buildRequestBody la proprietà setting->locale non può essere nulla";
            $code = 5014;
            throw new \Exception($message, $code);
        }
        $body['application_context']['locale'] = $locale;

        $intent = $order->getIntent();
        if (is_null($intent)) {
            $message = "PayPalIntegration\Order::buildRequestBody la proprietà order->intent non può essere nulla";
            $code = 5015;
            throw new \Exception($message, $code);
        }
        $body['intent'] = $intent;

        $purchaseunit = $order->getPurchaseUnit();
        if (is_null($purchaseunit)) {
            $message = "PayPalIntegration\Order::buildRequestBody la proprietà order->purchaseunit non può essere nulla";
            $code = 5016;
            throw new \Exception($message, $code);
        }

        $custom = $purchaseunit->getCustom();
        if (!is_null($custom)) {
            $tmp['custom_id'] = $custom;
        }

        $shipping = $purchaseunit->getShipping();
        if ($shipping instanceof AmountEntity) {
            $shippingcurrency = $shipping->getCurrency();
            $shippingvalue = $shipping->getValue();
            if (!is_null($shippingcurrency) && !is_null($shippingvalue)) {
                $tmp['amount']['breakdown']['shipping']['currency_code'] = $shippingcurrency;
                $tmp['amount']['breakdown']['shipping']['value'] = self::_formatPrice($shippingvalue);
            }
        }

        $amount = $purchaseunit->getAmount();
        if (is_null($amount)) {
            $message = "PayPalIntegration\Order::buildRequestBody la proprietà order->purchaseunit->amount non può essere nulla";
            $code = 5017;
            throw new \Exception($message, $code);
        }

        $currency = $amount->getCurrency();
        if (is_null($currency)) {
            $message = "PayPalIntegration\Order::buildRequestBody la proprietà order->purchaseunit->amount->currency non può essere nulla";
            $code = 5018;
            throw new \Exception($message, $code);
        }
        $tmp['amount']['currency_code'] = $currency;

        $value = $amount->getValue();
        if (is_null($value)) {
            $message = "PayPalIntegration\Order::buildRequestBody la proprietà order->purchaseunit->amount->value non può essere nulla";
            $code = 5019;
            throw new \Exception($message, $code);
        }
        $tmp['amount']['value'] = self::_formatPrice($value);

        $items = $purchaseunit->getItems();
        foreach ($items as $item) {
            
            $product = [];
            $product['name'] = null;
            $product['sku'] = null;
            $product['unit_amount']['currency_code'] = null;
            $product['unit_amount']['value'] = null;
            $product['tax']['currency_code'] = null;
            $product['tax']['value'] = null;
            $product['quantity'] = null;
            
            $name = $item->getName();
            if (is_null($name)) {
                $message = "PayPalIntegration\Order::buildRequestBody la proprietà order->purchaseunit->items->item->name non può essere nulla";
                $code = 5020;
                throw new \Exception($message, $code);
            }
            $product['name'] = $name;

            $unitamount = $item->getUnitAmount();
            if (is_null($unitamount)) {
                $message = "PayPalIntegration\Order::buildRequestBody la proprietà order->purchaseunit->items->item->unitamount non può essere nulla";
                $code = 5021;
                throw new \Exception($message, $code);
            }

            $currency = $unitamount->getCurrency();
            if (is_null($currency)) {
                $message = "PayPalIntegration\Order::buildRequestBody la proprietà order->purchaseunit->items->item->unitamount->currency non può essere nulla";
                $code = 5022;
                throw new \Exception($message, $code);
            }
            $product['unit_amount']['currency_code'] = $currency;

            $value = $unitamount->getValue();
            if (is_null($value)) {
                $message = "PayPalIntegration\Order::buildRequestBody la proprietà order->purchaseunit->items->item->unitamount->value non può essere nulla";
                $code = 5023;
                throw new \Exception($message, $code);
            }
            $product['unit_amount']['value'] = self::_formatPrice($value);
            
            $tax = $item->getTax();
            if (is_null($tax)) {
                $message = "PayPalIntegration\Order::buildRequestBody la proprietà order->purchaseunit->items->item->tax non può essere nulla";
                $code = 5024;
                throw new \Exception($message, $code);
            }

            $currency = $tax->getCurrency();
            if (is_null($currency)) {
                $message = "PayPalIntegration\Order::buildRequestBody la proprietà order->purchaseunit->items->item->tax->currency non può essere nulla";
                $code = 5025;
                throw new \Exception($message, $code);
            }
            $product['tax']['currency_code'] = $currency;

            $value = $tax->getValue();
            if (is_null($value)) {
                $message = "PayPalIntegration\Order::buildRequestBody la proprietà order->purchaseunit->items->item->tax->value non può essere nulla";
                $code = 5026;
                throw new \Exception($message, $code);
            }
            $product['tax']['value'] = self::_formatPrice($value);

            $quantity = $item->getQuantity();
            if (is_null($quantity)) {
                $message = "PayPalIntegration\Order::buildRequestBody la proprietà order->purchaseunit->items->item->quantity non può essere nulla";
                $code = 5027;
                throw new \Exception($message, $code);
            }
            $product['quantity'] = $quantity;

            $description = $item->getDescription();
            if (!is_null($description)) {
                $product['description'] = $description;
            }
            $sku = $item->getSku();
            if (!is_null($sku)) {
                $product['sku'] = $sku;
            }
            $tmp['items'][] = $product;
            unset($product);
        }
        if (empty($items)) {
            unset($tmp['items']);
        } else {

            $itemtotal = [];
            $itemtotal['currency_code'] = null;
            $itemtotal['value'] = 0;
            $taxtotal = [];
            $taxtotal['currency_code'] = null;
            $taxtotal['value'] = 0;
            foreach ($tmp['items'] as $item) {
                $itemtotal['currency_code'] = $item['unit_amount']['currency_code'];
                $itemtotal['value'] = $itemtotal['value'] + ((Float)$item['unit_amount']['value'] * (Int)$item['quantity']);
                $taxtotal['currency_code'] = $item['unit_amount']['currency_code'];
                $taxtotal['value'] = $taxtotal['value'] + ((Float)$item['tax']['value'] * (Int)$item['quantity']);
            }
            $itemtotal['value'] = self::_formatPrice($itemtotal['value']);
            $taxtotal['value'] = self::_formatPrice($taxtotal['value']);
            $tmp['amount']['breakdown']['item_total'] = $itemtotal;
            $tmp['amount']['breakdown']['tax_total'] = $taxtotal;


        }

        $body['purchase_units'][] = $tmp;
        // TEST MIRKO
        //$body['purchase_units']['discount'] = '10';
        return $body;
    }

    private static function _formatPrice($value)
    {
        return number_format($value, 2, '.', '');
    }
}