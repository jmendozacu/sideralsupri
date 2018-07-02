<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */
class Mageplace_Callforprice_Model_Htmlprocessor_Zendquery_Compare
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
        $helper    = $this->_getHelper();

        $positions = array();
        $count     = count($products);
        $i         = 0;
        foreach ($products as $product) {
            if ($helper->isEnabledForProduct($product)) {
                $positions[] = $i;
                $positions[] = $i + $count;
            }
            $i++;
        }

        $processor->replace(
            $helper->getCssSelector('compare_product_price'),
            $this->_getHelper()->prepareReplacement(),
            $positions,
            $helper->getCssSelector('compare_product_cell_top')
        );

        $processor->replace(
            $helper->getCssSelector('compare_product_price'),
            $this->_getHelper()->prepareReplacement(),
            $positions,
            $helper->getCssSelector('compare_product_cell_bottom')
        );

        $processor->remove(
            $helper->getCssSelector('compare_product_addtocart'),
            $positions,
            $helper->getCssSelector('compare_product_cell_top')
        );

        $processor->remove(
            $helper->getCssSelector('compare_product_addtocart'),
            $positions,
            $helper->getCssSelector('compare_product_cell_bottom')
        );
    }
}