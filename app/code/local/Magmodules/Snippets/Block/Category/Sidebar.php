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
 
class Magmodules_Snippets_Block_Category_Sidebar extends Mage_Core_Block_Template {
	
    protected function _construct() 
    {
        parent::_construct();	      			
		if($this->getSnippetsEnabled()) {
			$type = Mage::getStoreConfig('snippets/category/type');	
			$category = Mage::registry('current_category');	
			$displayMode = $category->getDisplayMode();
			if(($type == 'sidebar') && ($displayMode != 'PAGE')) {
				$storeId = Mage::app()->getStore()->getStoreId();
				if($category->getIsAnchor()) {
					$cache_key = $storeId . '-snippets-sidebar-c-' . $this->helper('snippets')->getFilterHash();
				} else {
					$cache_key = $storeId . '-snippets-sidebar-c-' . $category->getId();
				}		
				$this->addData(array(
					'cache_lifetime'    => 7200,
					'cache_tags'        => array(Mage_Catalog_Model_Category::CACHE_TAG, Magmodules_Snippets_Model_Snippets::CACHE_TAG),
					'cache_key'         => $cache_key,       
				));
				$this->setTemplate('magmodules/snippets/catalog/category/sidebar.phtml');
			}
		}
    }
        
    public function getCategorySnippets() 
    {
        return $this->helper('snippets')->getCategorySnippets();
    }	

    public function getSnippetsEnabled() 
    {
        return $this->helper('snippets')->getSnippetsEnabled('category');
    }	    
		
}