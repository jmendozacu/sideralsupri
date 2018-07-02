<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */
class Mageplace_Callforprice_Model_Htmlprocessor_Zendquery_Productlist
    implements Mageplace_Callforprice_Model_Processor_Interface
{

    /** @var  $_htmlProcessor Mageplace_Callforprice_Model_Htmlprocessor_Interface */
    protected $_htmlProcessor;

    public function setHtmlProcessor($processor)
    {
        $this->_htmlProcessor = $processor;
    }

    public function process($params)
    {
        /** @var $products Mage_Catalog_Model_Resource_Product_Collection */
        $products = $params['products'];
        return $this->_process($products);
    }

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $products
     * @return bool
     */
    protected function _process($products)
    {
        $helper = $this->_getHelper();

        foreach ($products as $product) {
            $product->setData('url', $product->getProductUrl());
        }

        $processor = $this->_htmlProcessor;

        $items = $processor->query($helper->getCssSelector('product_link'));
        if (count($items) === 0) {
            return false;
        }

        $positions = array();
        foreach ($items as $i => $item) {
            /** @var $item DOMElement */
            $url     = $item->getAttribute('href');
            $product = $products->getItemByColumnValue('url', $url);
            if (!$product) {
                continue;
            }

            if ($helper->isEnabledForProduct($product)) {
                $positions[] = $i;
            }
        }

        $processor->replace(
            $helper->getCssSelector('product_list_price'),
            $this->_getHelper()->prepareReplacement(),
            $positions,
            $helper->getCssSelector('product_list_cell')
        );

        $processor->remove(
            $helper->getCssSelector('product_list_addtocart'),
            $positions,
            $helper->getCssSelector('product_list_cell')
        );

    }

    /**
     * @return Mageplace_Callforprice_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('mageplace_callforprice');
    }


}