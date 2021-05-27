<?php
    
    require '../vendor/autoload.php';

    use PayPalIntegration\Settings;
    use PayPalIntegration\Entity\PurchaseUnit;
    use PayPalIntegration\Entity\Amount;
    use PayPalIntegration\Entity\Item;
    use PayPalIntegration\Model\PayPal;

    Settings::setClientId('PAYPAL_CLIENT_ID');
    Settings::setSecret('PAYPAL_SECRET');
    Settings::setReturnUrl('RichiestaPagamentoAutorizzata.php');
    Settings::setCancelUrl('RichiestaPagamentoNonAutorizzata.php');
    Settings::setBrandName('Nome Venditore');
    Settings::setLocale('it-IT');
    
    try {

        // =============================================================
        // 1) UNITA'/BLOCCO DI ACQUISTO ( ISTANZIAMENTO )
        // =============================================================
        $purchaseunit = new PurchaseUnit();
        // Assegnazione numero ordine ( Numero/id interno )
        $purchaseunit->setCustom("ORDINE_INTERNO_26112020");

        
        // =============================================================
        // 2) VALORE MONETARIO TOTALE ORDINE ( articoli + spediz. + tasse )
        // =============================================================
        $amount = new Amount();
        $amount->setValue(122);
        // Assegnazione valore totale a unità di acquisto
        $purchaseunit->setAmount($amount);


        // =============================================================
        // 3) CREAZIONE ELENCO VOCI SPESA
        // =============================================================
        // Costo unitario al netto di IVA
        $unitamount1 = new Amount();
        $unitamount1->setValue(50);
        // Valore monetario unitario di IVA per singolo prodotto
        // Es. 22% del prezzo netto del prodotto
        $taxamount1 = new Amount();
        $taxamount1->setValue((50*0.22));
        // Dettagli sul prodotto/voce spesa
        $item1 = new Item();
        $item1->setName('Vasca di gelato da 10 Kg');
        $item1->setDescription('Gusto: Cioccolato e Panna');
        $item1->setSku('REF009IS12');
        $item1->setUnitAmount($unitamount1);
        $item1->setTax($taxamount1);
        $item1->setQuantity(2);
        // Inserimento prodotto in unità/blocco di acquisto
        $purchaseunit->addItem($item1);
        

        // =============================================================
        // 4) AVVIO PROCESSO DI AUTORIZZAZIONE DI PAGAMENTO A PAYPAL
        // =============================================================
        PayPal::authorizePayment($purchaseunit);

    } catch (\Exception $e) {
        printf('%s - %s', $e->getCode(), $e->getMessage());
    }