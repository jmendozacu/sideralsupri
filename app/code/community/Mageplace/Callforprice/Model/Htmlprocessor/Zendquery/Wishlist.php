<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */
class Mageplace_Callforprice_Model_Htmlprocessor_Zendquery_Wishlist
    extends Mageplace_Callforprice_Model_Htmlprocessor_Zendquery_Productlist
    implements Mageplace_Callforprice_Model_Processor_Interface
{

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $products
     * @return bool
     */
    protected function _process($products)
    {
        $helper = $this->_getHelper();

        $processor = $this->_htmlProcessor;

        $items = $processor->query($helper->getCssSelector('wishlist_product_link'));
        if (count($items) === 0) {
            return false;
        }

        foreach ($products as $product) {
            $product->setData('url', $product->getProductUrl());
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

        if($selector = $helper->getCssSelector('wishlist_product_price')) {
            $processor->replace(
                $selector,
                $this->_getHelper()->prepareReplacement(),
                $positions,
                $helper->getCssSelector('wishlist_product_cell')
            );
        }

        if($selector = $helper->getCssSelector('wishlist_product_addtocart')) {
            $processor->remove(
                $selector,
                $positions,
                $helper->getCssSelector('wishlist_product_cell')
            );
        }


        if ($positions) {
            $processor->remove(
                $helper->getCssSelector('wishlist_product_all_addtocart')
            );
        }
    }
}