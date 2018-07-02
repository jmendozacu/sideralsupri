<?php

class Inovarti_Pagarme_Model_Marketplacemenu extends Mage_Core_Model_Abstract
{
    /**
     * Inovarti_Pagarme_Model_Splitrules constructor.
     */
    protected function _construct()
    {
        return $this->_init('pagarme/marketplacemenu');
    }

    /**
     * @param string $productSku
     * @return boolean
     */
    private function productIsAssociatedWithASplitRule($recipientId, $productSku) {
        return Mage::getModel('pagarme/marketplacemenu')
            ->getCollection()
            ->addFieldToFilter('recipient_id', array('neq' => $recipientId))
            ->addFieldToFilter('sku', $productSku)
            ->count() > 0;
    }

    /**
     * @param Inovarti_Pagarme_Model_Splitrules
     * @return bool
     */
    private function atLeastOneRecipientIsResponsibleForChargeProcessingFee($splitRule) {
        $marketplaceIsResponsibleForChargeProcessingFee = Mage::getStoreConfig('payment/pagarme_settings/charge_processing_fee');

        if($splitRule == null && !$marketplaceIsResponsibleForChargeProcessingFee)
            return false;

        $sellerIsResponsibleForChargeProcessingFee = $splitRule->getChargeProcessingFee();

        return ($marketplaceIsResponsibleForChargeProcessingFee
            || $sellerIsResponsibleForChargeProcessingFee);
    }

    /**
     * @param Inovarti_Pagarme_Model_Splitrules
     * @return bool
     */
    private function atLeastOneRecipientIsResponsibleForChargeback($splitRule) {
        $marketplaceIsLiable = Mage::getStoreConfig('payment/pagarme_settings/liable');

        if($splitRule == null && !$marketplaceIsLiable)
            return false;

        $sellerIsLiable = $splitRule->getLiable();

        return ($marketplaceIsLiable
            || $sellerIsLiable);
    }


    /**
     * @return bool
     */
    public function validate()
    {
        $splitRule = Mage::getModel('pagarme/splitrules')
            ->load($this->getRecipientId(), 'recipient_id');

        $errors = array();

        if($this->productIsAssociatedWithASplitRule($this->getRecipientId(), $this->getSku())) {
            $errors[] = 'This product already has an associated split rule.';
        }

        if(!$this->atLeastOneRecipientIsResponsibleForChargeProcessingFee($splitRule)) {
            $errors[] = 'At least one recipient must be responsible for the charge processing fee';
        }

        if(!$this->atLeastOneRecipientIsResponsibleForChargeback($splitRule)) {
            $errors[] = 'At least one recipient must be liable';
        }

        return $errors;
    }
}
