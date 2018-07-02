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
 
class Magmodules_Snippets_Block_Default_Combined extends Mage_Core_Block_Template {
	
    protected function _construct() 
    {
        parent::_construct();	      			
		if($this->getSnippetsEnabled()) {		
			$module_name = $this->getRequest()->getModuleName(); 
			$sitename = $this->helper('snippets')->getSiteNameSnippets();
			$organization = $this->helper('snippets')->getOrganizationSnippets();	
			if($sitename || $organization) {
				$storeId = Mage::app()->getStore()->getStoreId();
				if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'cms_index_index') {
					$cache_key = $storeId . '-snippets-combined-home';				
				} else {
					$cache_key = $storeId . '-snippets-combined';								
				}
				$this->addData(array(
					'cache_lifetime'    => 7200,
					'cache_tags'        => array(Mage_Cms_Model_Block::CACHE_TAG, Magmodules_Snippets_Model_Snippets::CACHE_TAG),
					'cache_key'         => $cache_key,
				));
				$this->setSitenameSnippets($sitename);
				$this->setOrganizationSnippets($organization);
				$this->setTemplate('magmodules/snippets/page/combined.phtml');
			}	
		}
    }

    public function getSnippetsEnabled() 
    {
        return Mage::getStoreConfig('snippets/general/enabled');
    }  
    
}