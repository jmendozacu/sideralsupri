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
 
class Magmodules_Snippets_Model_Observer {
	
	public function setSnippetsData(Varien_Event_Observer $observer) {			

		if(Mage::app()->getFrontController()->getRequest()->getRouteName() == 'catalog') {
			$helper			= Mage::helper('snippets');	
			$position 		= $helper->getPosition(); 
			$enabled 		= $helper->getEnabled(); 	
			$markup 		= $helper->getMarkup(); 			
			
			if($enabled) {				
				$block			= $observer->getBlock();
				$fileName		= $block->getTemplateFile();
				$thisClass		= get_class($block);			
				$content 		= $helper->getContent(); 
				$normalOutput 	= $observer->getTransport()->getHtml();
				$argBefore		= null;
				$argAfter		= null;
						
				if($content == $thisClass) {								
					if($markup == 'footer') {
						if(Mage::registry('product')) {
							$snipblock = $block->getLayout()->createBlock('snippets/product_footer')->toHtml();    		
						} else {
							$snipblock = $block->getLayout()->createBlock('snippets/category_footer')->toHtml();    								
						}	
					}
					if($markup == 'visible') {
						if(Mage::registry('product')) {
							$snipblock = $block->getLayout()->createBlock('snippets/product_schema')->toHtml();    	
						} else {
							$snipblock = $block->getLayout()->createBlock('snippets/category_schema')->toHtml();    								
						}								
					}
					if($position == 'after') {
						$argAfter = $snipblock; 
					} else {
						$argBefore = $snipblock; 
					}			
				}
				$observer->getTransport()->setHtml($argBefore . $normalOutput . $argAfter);		
			}
		}
	}

	public function addFullBreadcrumb(Varien_Event_Observer $observer) {
		if(!Mage::registry('current_category')) {
			$detailed = Mage::getStoreConfig('snippets/system/breadcrumbs_detailed'); 
			$breadcrumbs = Mage::getStoreConfig('snippets/system/breadcrumbs');			
			$enabled = Mage::getStoreConfig('snippets/general/enabled');			
			if($detailed && $enabled && $breadcrumbs) {
				$product = $observer->getProduct();
				if($categoryIds = $product->getCategoryIds()) {
					$root_category = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId());
					$all_categories = Mage::getResourceModel('catalog/category_collection')
						->addIdFilter($product->getCategoryIds())
						->addAttributeToFilter('is_active', 1)
						->addFieldToFilter('path', array('like' => $root_category->getPath() . '/%'))
						->addAttributeToSelect('level','id')
						->getItems();
					
					$all_cats = array();					
					foreach($all_categories as $cat) {
						$all_cats[$cat->getLevel()] = $cat->getId();
					}	
					krsort($all_cats);
					$categoryId = reset($all_cats);
					$category = Mage::getModel('catalog/category')->load($categoryId);			
					if($category->getId()) {
						$product->setCategory($category);
						Mage::register('current_category', $category);
					}
				}
			}
		}
		return $this;
	}

}