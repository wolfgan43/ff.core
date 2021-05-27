<?php
    
    require '../vendor/autoload.php';

    use PayPalIntegration\Model\PayPal;
    use PayPalIntegration\Settings;

    Settings::setClientId('PAYPAL_CLIENT_ID');
    Settings::setSecret('PAYPAL_SECRET');

    try {
    
        PayPal::makePayment();
    
    } catch (\Exception $e) {
        printf('%s - %s', $e->getCode(), $e->getMessage());
    }