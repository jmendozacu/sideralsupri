<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Snippets
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2015 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Snippets_Block_Category_Metatags extends Mage_Core_Block_Template {
	
    protected function _construct() 
    {
        parent::_construct();	      			
		if($this->getSnippetsEnabled()) {
			$twitter = Mage::getStoreConfig('snippets/category/twitter');	
			$pinterest = Mage::getStoreConfig('snippets/category/pinterest');			
			if($twitter || $pinterest) {
				$storeId = Mage::app()->getStore()->getStoreId();
				$category = Mage::registry('current_category');	
				if($category->getIsAnchor()) {
					$cache_key = $storeId . '-snippets-metatags-c-' . $this->helper('snippets')->getFilterHash();
				} else {
					$cache_key = $storeId . '-snippets-metatags-c-' . $category->getId();
				}
				$this->addData(array(
					'cache_lifetime'    => 7200,
					'cache_tags'        => array(Mage_Catalog_Model_Category::CACHE_TAG, Magmodules_Snippets_Model_Snippets::CACHE_TAG),
					'cache_key'         => $cache_key,       
				));
				$this->setTemplate('magmodules/snippets/catalog/category/metatags.phtml');	    			
			}
		}
    }
        
    public function getCategoryMetatags() 
    {
        return $this->helper('snippets')->getCategoryMetatags();
    }	

    public function getSnippetsEnabled() 
    {
        return Mage::getStoreConfig('snippets/general/enabled');
    }	
		
}