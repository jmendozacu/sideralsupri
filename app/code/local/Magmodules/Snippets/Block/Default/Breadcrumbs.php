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
 
class Magmodules_Snippets_Block_Default_Breadcrumbs extends Mage_Catalog_Block_Breadcrumbs {
	
    protected function _construct() 
    {
        parent::_construct();	      					
		$enabled = $this->getSnippetsEnabled();
		$breadcrumbs = $this->getBreadcrumbsEnabled();
		if($enabled && $breadcrumbs) {
			$this->setTemplate('magmodules/snippets/page/html/breadcrumbs-json.phtml');
		}	
    }

    protected function _prepareLayout() 
    {
    	if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
    		unset($breadcrumbsBlock);
    	}
    }
    
    public function getSnippetsEnabled() 
    {
        return Mage::getStoreConfig('snippets/general/enabled');
    }	

    public function getBreadcrumbsEnabled() 
    {
        $enabled = Mage::getStoreConfig('snippets/system/breadcrumbs');
        $markup = Mage::getStoreConfig('snippets/system/breadcrumbs_markup');		
		if($enabled && ($markup == 'json')) {
			return true;
		}
    }	
        
}