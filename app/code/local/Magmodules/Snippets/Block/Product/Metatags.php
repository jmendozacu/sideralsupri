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
 * @copyright   Copyright (c) 2014 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Snippets_Block_Product_Metatags extends Mage_Core_Block_Template {
	
    protected function _construct() 
    {
        parent::_construct();	      			
		if($this->getSnippetsEnabled()) {
			$twitter = Mage::getStoreConfig('snippets/products/twitter');	
			$pinterest = Mage::getStoreConfig('snippets/products/pinterest');			
			if($twitter || $pinterest) {
				if($_snippets = $this->getProductMetatags()) {
					$storeId = Mage::app()->getStore()->getStoreId();
					if(is_object(Mage::registry('current_product'))) {
						$this->addData(array(
							'cache_lifetime'    => 7200,
							'cache_tags'        => array(Mage_Catalog_Model_Category::CACHE_TAG, Magmodules_Snippets_Model_Snippets::CACHE_TAG),
							'cache_key'         => $storeId . 'snippets-meta-p-' . Mage::registry('current_product')->getId(),       
						));
					}
					$this->setSnippets($_snippets);
					$this->setTemplate('magmodules/snippets/catalog/product/metatags.phtml');	    			
				}	
			}
		}
    }
    
    public function getProductMetatags() 
    {
        return $this->helper('snippets')->getProductMetatags();
    }	

    public function getSnippetsEnabled() 
    {
        return Mage::getStoreConfig('snippets/general/enabled');
    }	
		
}