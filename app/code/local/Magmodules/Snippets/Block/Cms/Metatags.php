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
 
class Magmodules_Snippets_Block_Cms_Metatags extends Mage_Core_Block_Template {
	
    protected function _construct() 
    {
        parent::_construct();	      			
		if($this->getSnippetsEnabled()) {
			$og_enabled = Mage::getStoreConfig('snippets/cms/og');	
			if($og_enabled) {
				$storeId = Mage::app()->getStore()->getStoreId();
				$cms_identifier = Mage::getSingleton('cms/page')->getIdentifier();
				$this->addData(array(
					'cache_lifetime'    => 7200,
					'cache_tags'        => array(Mage_Cms_Model_Page::CACHE_TAG, Magmodules_Snippets_Model_Snippets::CACHE_TAG),
					'cache_key'         => $storeId . '-snippets-meta-cms-' . $cms_identifier,       
				));
				$this->setTemplate('magmodules/snippets/cms/metatags.phtml');	    			
			}
		}
    }
        
    public function getCmsMetatags() 
    {
        return $this->helper('snippets')->getCmsMetatags();
    }	

    public function getSnippetsEnabled() 
    {
        return Mage::getStoreConfig('snippets/general/enabled');
    }	
		
}