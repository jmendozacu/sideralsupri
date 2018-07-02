<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */
class Mageplace_Callforprice_Model_Observer_Product
{
    public function saveTabData()
    {
        /** @var $currentProduct Mage_Catalog_Model_Product */
        $currentProduct = Mage::registry('current_product');
        if (!$currentProduct || !($productId = (int)$currentProduct->getId())) {
            return;
        }

        try {
            $checkBox      = $this->_getRequest()->getPost("checkbox_field_CFP");
            $groupIdsArray = $this->_getRequest()->getPost("group_ids", array());
            $customerIds   = $this->_getRequest()->getPost("customer_ids");
            $storeIdsArray = $this->_getRequest()->getPost("stores_id", array());

            if (is_string($customerIds)) {
                $customerIds = explode(',', $customerIds);
            } else {
                $customerIds = array();
            }

            /** @var $cfpModel Mageplace_Callforprice_Model_Callforprice */
            $cfpModel = Mage::getModel('mageplace_callforprice/callforprice')
                ->loadByProductId($productId);

            $cfpModel->setIdProd($productId)
                ->setCallforprice($checkBox)
                ->setCustomerGroups($groupIdsArray)
                ->setCustomerIds($customerIds)
                ->setStoreIds($storeIdsArray);

            $cfpModel->save();

        } catch (Exception $e) {
            Mage::logException($e);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }

    protected function _getRequest()
    {
        return Mage::app()->getRequest();
    }

}