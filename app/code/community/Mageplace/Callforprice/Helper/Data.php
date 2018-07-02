<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */
class Mageplace_Callforprice_Helper_Data extends Mage_Core_Helper_Abstract
{
    const IS_DEBUG_ENABLE = false;

    const XML_PATH_ADVANCED_PROCESSOR  = 'advanced/processor';
    const XML_PATH_GLOBAL_ENABLED      = 'options/global';
    const XML_PATH_PREFIX_CSS_SELECTOR = 'selectors/';

    const XML_PATH_HREF    = 'options/href';
    const XML_PATH_MESSAGE = 'options/text_message';

    protected $_xmlPathModulePrefix = 'callforprice/';
    protected $_products            = array();
    protected $_categories          = array();

    /**
     * Is global call for price enabled
     * @return mixed
     */
    public function isGlobalEnabled()
    {
        return (bool)$this->getStoreConfig(self::XML_PATH_GLOBAL_ENABLED);
    }

    /**
     * Return css selector by name in config
     * @param string $name
     * @return mixed
     */
    public function getCssSelector($name)
    {
        $xmlPath = self::XML_PATH_PREFIX_CSS_SELECTOR . $name;
        return $this->getStoreConfig($xmlPath);
    }

    /**
     * Return name of installed processor
     * @return string
     */
    public function getProcessorName()
    {
        return $this->getStoreConfig(self::XML_PATH_ADVANCED_PROCESSOR);
    }

    /**
     * Return module config
     * @param string $path without module prefix
     * @return mixed
     */
    public function getStoreConfig($path)
    {
        $xmlPath = $this->_xmlPathModulePrefix . $path;
        return Mage::getStoreConfig($xmlPath);
    }

    /**
     * Return prepared message
     * @return string
     */
    public function prepareReplacement()
    {
        $href    = $this->getStoreConfig(self::XML_PATH_HREF);
        $message = $this->getStoreConfig(self::XML_PATH_MESSAGE);

        if (strlen($href)) {
            $href = "href='" . $href . "'";
        }

        $replacement = "<div><a class='callforprice' " . $href
            . ">" . $message
            . "</a></div>";

        return $replacement;
    }

    /**
     * Is enabled for product
     * @param $product
     * @return bool|mixed
     */
    public function isEnabledForProduct($product)
    {
        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        } else {
            $productId = $product->getProductId();
        }

        if (!$productId) {
            $productId = $product->getId();
        }

        if (isset($this->_products[$productId])) {
            return $this->_products[$productId];
        }

        /** @var $cfpModel Mageplace_Callforprice_Model_Callforprice */
        $cfpModel = Mage::getModel('mageplace_callforprice/callforprice');
        $cfpModel->loadByProductId($productId);

        if ($this->_isEnabledForCustom($cfpModel)) {
            $this->_products[$productId] = true;
            return true;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (($categoriesIds = $product->getCategoryIds()) && is_array($categoriesIds)) {
            foreach ($categoriesIds as $categoriesId) {
                if ($this->isEnabledForCategory($categoriesId)) {
                    return true;
                }
                if ($this->isEnabledForCategory($categoriesId)) {
                    $this->_products[$productId] = true;
                    return true;
                }
            }
        }

        $this->_products[$productId] = false;
        return false;
    }

    /**
     * Is enabled for category
     * @param $category
     * @return mixed
     */
    public function isEnabledForCategory($category)
    {
        if (is_object($category)) {
            $category = $category->getId();
        }

        if (isset($this->_categories[$category])) {
            return $this->_categories[$category];
        }

        /** @var $cfpModel Mageplace_Callforprice_Model_Callforprice */
        $cfpModel = Mage::getModel('mageplace_callforprice/callforprice');
        $cfpModel->loadByCategoryId((int)$category);
        $this->_categories[$category] = $this->_isEnabledForCustom($cfpModel);
        return $this->_categories[$category];
    }


    private function _isEnabledForCustom($cfpModel)
    {
        if (!$cfpModel->getId()) {
            return false;
        }

        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('customer/session');
        $isEnabled       = $cfpModel->isCallforprice()
            && !in_array($customerSession->getCustomerGroupId(), $cfpModel->getCustomerGroups())
            && ((count($cfpModel->getCustomerIds()) === 0) || in_array($customerSession->getCustomerId(),
                    $cfpModel->getCustomerIds()))
            && !in_array(Mage::app()->getStore()->getGroup()->getId(), $cfpModel->getStoreIds());

        return $isEnabled;
    }


    /**
     * Dump model state
     *
     * @param $cfpModel
     * @param bool $isReturn
     * @return string
     */
    private function _dump($cfpModel, $isReturn = false)
    {
        if (!self::IS_DEBUG_ENABLE) {
            return;
        }

        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('customer/session');
        $storeId         = Mage::app()->getStore()->getId();

        ob_start();
        echo 'is_cfp - ';
        var_dump($cfpModel->isCallforprice());
        echo 'customer_group - ';
        var_dump(!in_array($customerSession->getCustomerGroupId(), $cfpModel->getCustomerGroups()),
            $cfpModel->getCustomerGroups());
        echo 'customer_ids - ';
        var_dump(((count($cfpModel->getCustomerIds()) === 0) || in_array($customerSession->getCustomerId(),
                $cfpModel->getCustomerIds())), $cfpModel->getCustomerIds());
        echo 'stores_ids - ';
        var_dump(!in_array($storeId, $cfpModel->getStoreIds()), $cfpModel->getStoreIds());
        $out = ob_get_clean();

        if ($isReturn) {
            return $out;
        } else {
            echo $out;
        }
    }
}