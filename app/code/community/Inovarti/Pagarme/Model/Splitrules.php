<?php

class Inovarti_Pagarme_Model_Splitrules extends Mage_Core_Model_Abstract
{
    /**
     * Inovarti_Pagarme_Model_Splitrules constructor.
     */
    protected function _construct()
    {
        return $this->_init('pagarme/splitrules');
    }

    public function validate() {
        $errors = array();

        $amount = $this->getAmount();

        if(!is_numeric($amount)
            || $amount > 100
            || $amount < 0) {
            $errors[] = 'Invalid value for \'amount\'';
        }

        $recipientHasAssociatedProduct = Mage::getModel('pagarme/marketplacemenu')
            ->getCollection()
            ->addFieldToFilter('recipient_id', $this->getRecipientId())
            ->count() > 0;

        if($recipientHasAssociatedProduct) {
            if(!Mage::getStoreConfig('payment/pagarme_settings/charge_processing_fee')
                && !$this->getChargeProcessingFee()) {
                $errors[] = 'At least one recipient must be responsible for the charge processing fee';
            }

            if(!Mage::getStoreConfig('payment/pagarme_settings/liable')
                && !$this->getLiable()) {
                $errors[] = 'At least one recipient must be responsible for the chargeback';
            }
        }

        return $errors;
    }
}
