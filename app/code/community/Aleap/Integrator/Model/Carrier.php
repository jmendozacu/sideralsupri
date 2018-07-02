<?php

class Aleap_Integrator_Model_Carrier
        extends Mage_Shipping_Model_Carrier_Abstract
        implements Mage_Shipping_Model_Carrier_Interface {
    protected $_code = 'aleap_shipping';

    /**
     * Collect and get rates
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result|bool|null
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        /** @var Mage_Shipping_Model_Rate_Result $result */
        $result = Mage::getModel('shipping/rate_result');

        $session = Mage::getSingleton('adminhtml/session_quote');
        $aleap_order = $session->getData('aleap_order');
        $freight_charged = $session->getData('freight_charged');
        $shipping_method = $session->getData('shipping_method');

        if ($aleap_order) {
            $result->append($this->getStandardRate($freight_charged, $shipping_method));
        }

        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods() {
        return array(
                'standard' => 'Standard delivery'
        );
    }

    protected function getStandardRate($freight_charged, $shipping_method)
    {
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('custom');
        $rate->setMethodTitle($shipping_method);
        $rate->setPrice($freight_charged);
        $rate->setCost($freight_charged); // 0?

        return $rate;
    }
}