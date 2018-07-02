<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */


class Mageplace_Callforprice_Block_Adminhtml_Catalog_Category_Tab
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_template = 'callforprice/catalog/category/tab.phtml';
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
            /** @var $currentCategory Mage_Catalog_Model_Category */
            $currentCategory = Mage::registry('current_category');
            /** @var $cfpModel Mageplace_Callforprice_Model_Callforprice */
            $cfpModel = Mage::getModel('mageplace_callforprice/callforprice');

            $categoryId = $currentCategory->getId();
            $cfpModel->loadByCategoryId($categoryId);

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