<?php

class Inovarti_Pagarme_Model_Library extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $apiMode = Mage::getStoreConfig('payment/pagarme_settings/mode');
        $apiKey = Mage::getStoreConfig('payment/pagarme_settings/apikey_' . $apiMode);

        if (false === (boolean)class_exists('PagarMe')) {
            require_once(Mage::getBaseDir('lib') . DS . 'pagarme' . DS . 'Pagarme.php');
        }

        PagarMe::setApiKey($apiKey);
    }
}
