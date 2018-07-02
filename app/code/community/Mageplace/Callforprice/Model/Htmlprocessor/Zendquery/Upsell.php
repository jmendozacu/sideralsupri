<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */

class Mageplace_Callforprice_Model_Htmlprocessor_Zendquery_Upsell
    extends Mageplace_Callforprice_Model_Htmlprocessor_Zendquery_Productlist
    implements Mageplace_Callforprice_Model_Processor_Interface
{

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $products
     * @return bool
     */
    protected function _process($products)
    {
        $processor = $this->_htmlProcessor;
        $helper = $this->_getHelper();

        foreach($products as $product){
            $product->setData('url', $product->getProductUrl());
        }

        $items = $processor->query($helper->getCssSelector('upsell_product_link'));

        if(count($items) === 0){
            return false;
        }

        $positions = array();
        foreach($items as $i => $item){
            /** @var $item DOMElement */
            $url = $item->getAttribute('href');
            $product = $products->getItemByColumnValue('url', $url);
            if(!$product){
                continue;
            }
            if($helper->isEnabledForProduct($product)){
                $positions[] = $i;
            }
        }

        $processor->replace(
            $helper->getCssSelector('upsell_product_price'),
            $this->_getHelper()->prepareReplacement(),
            $positions,
            $helper->getCssSelector('upsell_product_cell')
       );

    }

}