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
 
class Magmodules_Snippets_Block_Products extends Mage_Core_Block_Template {

 	protected function _construct() {
        
        parent::_construct();	      			
		
		$id = '';
		
		if(Mage::registry('product')) {
			$id = 'p-' . Mage::registry('current_product')->getId() . '-' . Mage::app()->getStore()->getStoreId(); 
		} 	
		if(Mage::registry('current_category') && !Mage::registry('product')) {
			$id = 'c-' . Mage::registry('current_category')->getId() . '-' . Mage::app()->getStore()->getStoreId(); 
		} 	
		
		if($id) {
			
			$this->addData(array(
				'cache_lifetime'    => 7200,
				'cache_tags'        => array(Mage_Catalog_Model_Category::CACHE_TAG, Magmodules_Snippets_Model_Snippets::CACHE_TAG),
				'cache_key'         => 'snippets-schema-' . $id,       
			));

			// Set template
			if(Mage::registry('product')) {
				if(Mage::getStoreConfig('snippets/products/type') == 'footer') {
					if(!Mage::getStoreConfig('snippets/products/location_ft')) {
						$this->setTemplate('magmodules/snippets/catalog/product/footer.phtml');
					}
				} 
			
				if(Mage::getStoreConfig('snippets/products/type') == 'visible') {
					if(!Mage::getStoreConfig('snippets/products/location')) {
						$this->setTemplate('magmodules/snippets/catalog/product/schema.phtml');
					}
				} 
			} 

			if(Mage::registry('current_category') && !Mage::registry('product')) {
				if(Mage::getStoreConfig('snippets/category/type') == 'footer') {
					if(!Mage::getStoreConfig('snippets/category/location_ft')) {
						$this->setTemplate('magmodules/snippets/catalog/category/footer.phtml');
					}	
				} 
			
				if(Mage::getStoreConfig('snippets/category/type') == 'visible') {
					if(!Mage::getStoreConfig('snippets/category/location')) {
						$this->setTemplate('magmodules/snippets/catalog/category/schema.phtml');
					}
				} 
			} 
			
		}		
    }

    public function getSnippets() {
        if(Mage::registry('product')) {
	        return $this->helper('snippets')->getProductSnippets();
	    }     
		if(Mage::registry('current_category') && !Mage::registry('product')) {
	        return $this->helper('snippets')->getCategorySnippets();
	    }     
    }	

    public function getSnippetsEnabled() {
		$enabled = Mage::getStoreConfig('snippets/general/enabled');
		return $enabled;		
	}	
	    
}