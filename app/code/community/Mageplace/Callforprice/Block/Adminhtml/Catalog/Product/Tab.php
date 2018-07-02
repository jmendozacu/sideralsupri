<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */


class Mageplace_Callforprice_Block_Adminhtml_Catalog_Product_Tab
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_template = 'callforprice/catalog/product/tab.phtml';
    protected $_cfpModel = null;

    /**
     * Tab label
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Call for Price');
    }

    /**
     * Tab title
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Call for price configuration');
    }

    /**
     * Can show tab
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is tab hidden
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    public function getCallForPriceModel()
    {
        if(is_null($this->_cfpModel)){
            /** @var $currentProduct Mage_Catalog_Model_Product */
            $currentProduct = Mage::registry('current_product');
            /** @var $cfpModel Mageplace_Callforprice_Model_Callforprice */
            $cfpModel = Mage::getModel('mageplace_callforprice/callforprice');

            $productId = $currentProduct->getId();
            $cfpModel->loadByProductId($productId);

            $this->_cfpModel = $cfpModel;
        }

        return $this->_cfpModel;
    }


    public function getCustomerGroups()
    {
        $result = array();
        $groups = Mage::getModel('customer/group')->getCollection()->getData();
        foreach ($groups as $group){
            $result[] = array('value' => $group["customer_group_id"], 'label' => $group['customer_group_code']);
        }
        return $result;
    }
}