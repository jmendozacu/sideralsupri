<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */
class Mageplace_Callforprice_Model_Observer_Category
{
    public function addTab($observer)
    {
        $tab = $observer->getEvent()->getTabs();

        $tab->addTab('callforprice', array(
            'label'   => Mage::helper('catalog')->__('Call for price'),
            'content' => $tab->getLayout()->createBlock('mageplace_callforprice/adminhtml_catalog_category_tab')->toHtml()
        ));
    }

    public function saveTabData()
    {
        /** @var $currentCategory Mage_Catalog_Model_Category */
        $currentCategory = Mage::registry('current_category');
        if (!$currentCategory || !($categoryId = (int)$currentCategory->getId())) {
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
            $cfpModel = Mage::getModel('mageplace_callforprice/callforprice');

            $cfpModel->loadByCategoryId($categoryId);

            $cfpModel->setIdCat($categoryId)
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