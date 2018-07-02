<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */
class Mageplace_Callforprice_Model_Observer
{

    /**
     * Types of blocks, enabled for processing
     * @var array
     */
    protected $_enableBlockType = array(
        'catalog/product_view',
        'catalog/product_list',
        'catalog/product_list/random',
        'catalog/product_list_related',
        'catalog/product_compare_list',
        'catalog/product_list_upsell',
        'catalog/product_popular',
        'reports/product_viewed',
        'wishlist/customer_wishlist',
        'wishlist/customer_sidebar',
        'catalog/product_new',
        'review/product_view'
    );

    private $_replacedJs;

    public function processCoreBlockAbstractToHtmlAfter($observer)
    {
        $html = $observer->getTransport()->getHtml();
        if (empty($html)) {
            return;
        }

        /** @var $block Mage_Core_Block_Abstract */
        $block     = $observer->getEvent()->getBlock();
        $blockType = $block->getType();

        #echo '<!-- xxx ' . $blockType . ' X ' . get_class($block) . ' X ' . $block->getTemplateFile() . ' xxx -->';

        if (!$this->_isEnabled($blockType)) {
            return;
        }

        $this->_replacedJs = array();

        if (strpos($html, '<script') !== false) {
            $html = preg_replace_callback('#(\<script[^\>]*\>)(.*?)(\<\/script\>)#ims', array($this, '_replaceJS'), $html);
        }


        switch ($blockType) {
            case 'catalog/product_list/random':
            case 'catalog/product_list':
                /** @var $block Mage_Catalog_Block_Product_List */
                $html = $this->_processCatalogProductList($html, $block);
                break;

            case 'catalog/product_view':
            case 'review/product_view':
                /** @var $block Mage_Catalog_Block_Product_View */
                $html = $this->_processCatalogProductView($html, $block);
                break;

            case 'catalog/product_list_upsell':
                /** @var $block Mage_Catalog_Block_Product_List_Upsell */
                $html = $this->_processUpsellProducts($html, $block);
                break;

            case 'catalog/product_list_related':
                /** @var $block Mage_Catalog_Block_Product_List_Related */
                $html = $this->_processRelatedProducts($html, $block);
                break;

            case 'catalog/product_compare_list':
                /** @var $block Mage_Catalog_Block_Product_Compare_List */
                $html = $this->_processCompareList($html, $block);
                break;

            case 'reports/product_viewed':
                /** @var $block Mage_Catalog_Block_Product_List_Upsell */
                $html = $this->_processViewedProducts($html, $block);
                break;

            case 'wishlist/customer_wishlist':
                /** @var $block Mage_Wishlist_Block_Customer_Wishlist */
                $html = $this->_processWishList($html, $block);
                break;

            case 'wishlist/customer_sidebar':
                /** @var $block Mage_Wishlist_Block_Customer_Sidebar */
                $html = $this->_processWishListSidebar($html, $block);
                break;

            case 'catalog/product_new':
                /** @var $block Mage_Catalog_Block_Product_New */
                $html = $this->_processNewProducts($html, $block);
                break;
        }

        foreach ($this->_replacedJs as $key => $js) {
            $html = str_replace('{{CALLFORPRICE_' . $key . '}}', $js, $html);
        }

        $observer->getTransport()->setHtml($html);

        $this->_replacedJs = array();
    }

    /**
     * @param string $html
     * @param Mage_Catalog_Block_Product_List $block
     * @return string
     */
    private function _processCatalogProductList($html, $block)
    {
        $helper = $this->_getHelper();

        /** @var $processor Mageplace_Callforprice_Model_Htmlprocessor_Interface */
        $processor = Mage::getModel('mageplace_callforprice/htmlprocessor_factory')->createProcessor();
        $processor->load($html);

        if ($helper->isGlobalEnabled()) {
            $processor->replace($helper->getCssSelector('product_list_price'), $helper->prepareReplacement());
            $processor->remove($helper->getCssSelector('product_list_addtocart'));
            return $processor->getHtml();
        }

        /** @var $parent Mage_Catalog_Block_Category_View */
        $parent = $block->getParentBlock();
        if ($parent instanceof Mage_Catalog_Block_Category_View && ($category = $parent->getCurrentCategory())) {
            if ($helper->isEnabledForCategory($category)) {
                $processor->replace($helper->getCssSelector('product_list_price'), $helper->prepareReplacement());
                return $processor->getHtml();
            }
        }

        $processor->process('productlist', array(
            'products' => $block->getLoadedProductCollection()
        ));

        return $processor->getHtml();
    }

    /**
     * @param string $html
     * @param Mage_Catalog_Block_Product_View $block
     * @return string
     */
    private function _processCatalogProductView($html, $block)
    {
        $helper = $this->_getHelper();

        $product = $block->getProduct();

        /** @var $processor Mageplace_Callforprice_Model_Htmlprocessor_Interface */
        $processor = Mage::getModel('mageplace_callforprice/htmlprocessor_factory')->createProcessor();
        $processor->load($html);

        #echo '<!-- xxx ' .  get_class($block) . ' X ' . $block->getTemplateFile() . ' xxx -->';

        if ($helper->isGlobalEnabled() || $helper->isEnabledForProduct($product)) {
            $processor->replace($helper->getCssSelector('product_view_price'), $helper->prepareReplacement());

            $removeBlockSelectors = array(
                'product_view_qty', 'product_view_qtylabel', 'product_view_addtocart',
                'product_view_tier_price', 'product_view_price_notice', 'product_view_price_bundle');
            foreach ($removeBlockSelectors as $blockSelector) {
                $processor->remove($helper->getCssSelector($blockSelector));
            }

            $html = $processor->getHtml();
        }


        return $html;
    }

    /**
     * @param string $html
     * @param Mage_Catalog_Block_Product_List_Upsell $block
     * @return string
     */
    private function _processUpsellProducts($html, $block)
    {
        $helper = $this->_getHelper();

        /** @var $processor Mageplace_Callforprice_Model_Htmlprocessor_Interface */
        $processor = Mage::getModel('mageplace_callforprice/htmlprocessor_factory')->createProcessor();
        $processor->load($html);

        if ($helper->isGlobalEnabled()) {
            $processor->replace($helper->getCssSelector('upsell_product_price'), $helper->prepareReplacement());
            return $processor->getHtml();
        }

        $processor->process('upsell', array(
            'products' => $block->getItemCollection()
        ));

        return $processor->getHtml();
    }

    /**
     * @param string $html
     * @param Mage_Catalog_Block_Product_List_Related $block
     * @return string
     */
    private function _processRelatedProducts($html, $block)
    {
        $helper = $this->_getHelper();

        /** @var $processor Mageplace_Callforprice_Model_Htmlprocessor_Interface */
        $processor = Mage::getModel('mageplace_callforprice/htmlprocessor_factory')->createProcessor();
        $processor->load($html);

        if ($helper->isGlobalEnabled()) {
            $processor->replace($helper->getCssSelector('upsell_product_price'), $helper->prepareReplacement());
            return $processor->getHtml();
        }

        $processor->process('related', array(
            'products' => $block->getItems()
        ));

        return $processor->getHtml();
    }

    /**
     * @param string $html
     * @param Mage_Catalog_Block_Product_Compare_List $block
     * @return string
     */
    private function _processCompareList($html, $block)
    {
        $helper = $this->_getHelper();

        /** @var $processor Mageplace_Callforprice_Model_Htmlprocessor_Interface */
        $processor = Mage::getModel('mageplace_callforprice/htmlprocessor_factory')->createProcessor();
        $processor->load($html);

        if ($helper->isGlobalEnabled()) {
            $processor->replace($helper->getCssSelector('compare_product_price'), $helper->prepareReplacement());
            $processor->remove($helper->getCssSelector('compare_product_addtocart'));
            return $processor->getHtml();
        }

        $processor->process('compare', array(
            'products' => $block->getItems()
        ));

        return $processor->getHtml();
    }

    /**
     * @param string $html
     * @param Mage_Catalog_Block_Product_List_Upsell $block
     * @return string
     */
    private function _processViewedProducts($html, $block)
    {
        $helper = $this->_getHelper();

        /** @var $processor Mageplace_Callforprice_Model_Htmlprocessor_Interface */
        $processor = Mage::getModel('mageplace_callforprice/htmlprocessor_factory')->createProcessor();
        $processor->load($html);

        if ($helper->isGlobalEnabled()) {
            $processor->replace($helper->getCssSelector('viewed_product_price'), $helper->prepareReplacement());
            return $processor->getHtml();
        }

        $processor->process('viewed', array(
            'products' => $block->getItemsCollection()
        ));

        return $processor->getHtml();
    }

    /**
     * @param string $html
     * @param Mage_Wishlist_Block_Customer_Wishlist $block
     * @return string
     */
    private function _processWishList($html, $block)
    {
        $helper = $this->_getHelper();

        /** @var $processor Mageplace_Callforprice_Model_Htmlprocessor_Interface */
        $processor = Mage::getModel('mageplace_callforprice/htmlprocessor_factory')->createProcessor();
        $processor->load($html);

        if ($helper->isGlobalEnabled()) {
            $processor->replace($helper->getCssSelector('wishlist_product_price'), $helper->prepareReplacement());
            $processor->remove($helper->getCssSelector('wishlist_product_addtocart'));
            $processor->remove($helper->getCssSelector('wishlist_product_all_addtocart'));
            return $processor->getHtml();
        }

        $processor->process('wishlist', array(
            'products' => $block->getWishlistItems()
        ));

        return $processor->getHtml();
    }

    /**
     * @param string $html
     * @param Mage_Wishlist_Block_Customer_Sidebar $block
     * @return string
     */
    private function _processWishListSidebar($html, $block)
    {
        $helper = $this->_getHelper();

        /** @var $processor Mageplace_Callforprice_Model_Htmlprocessor_Interface */
        $processor = Mage::getModel('mageplace_callforprice/htmlprocessor_factory')->createProcessor();
        $processor->load($html);

        if ($helper->isGlobalEnabled()) {
            $processor->replace($helper->getCssSelector('my_wishlist_product_price'), $helper->prepareReplacement());
            $processor->remove($helper->getCssSelector('my_wishlist_product_addtocart'));
            return $processor->getHtml();
        }

        $processor->process('mywishlist', array(
            'products' => $block->getWishlistItems()
        ));

        return $processor->getHtml();
    }

    /**
     * @param string $html
     * @param Mage_Catalog_Block_Product_New $block
     * @return string
     */
    private function _processNewProducts($html, $block)
    {
        $helper = $this->_getHelper();

        /** @var $processor Mageplace_Callforprice_Model_Htmlprocessor_Interface */
        $processor = Mage::getModel('mageplace_callforprice/htmlprocessor_factory')->createProcessor();
        $processor->load($html);

        if ($helper->isGlobalEnabled()) {
            $processor->replace($helper->getCssSelector('new_product_price'), $helper->prepareReplacement());
            $processor->remove($helper->getCssSelector('new_product_addtocart'));
            return $processor->getHtml();
        }

        $processor->process('new', array(
            'products' => $block->getProductCollection()
        ));

        return $processor->getHtml();
    }

    private function _isEnabled($blockType)
    {
        return in_array($blockType, $this->_enableBlockType);
    }

    /**
     * @return Mageplace_Callforprice_Helper_Abstract
     */
    private function _getHelper()
    {
        return Mage::helper('mageplace_callforprice');
    }

    protected function _replaceJS($matches)
    {
        $this->_replacedJs[] = $matches[2];

        return $matches[1] . '{{CALLFORPRICE_' . (count($this->_replacedJs) - 1) . '}}' . $matches[3];
    }
}