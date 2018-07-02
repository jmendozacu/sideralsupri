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
 
class Magmodules_Snippets_Model_System_Config_Model_Enable extends Mage_Core_Model_Config_Data {

    protected function _beforeSave() {
		
		// Fix for breadcrumbs
		$data = $this->getData();
		if(isset($data['groups']['system']['fields']['breadcrumbs_markup']['value'])) {
			$markup = $data['groups']['system']['fields']['breadcrumbs_markup']['value'];
			$breadcrumbs = $data['groups']['system']['fields']['breadcrumbs']['value'];
					
			if($markup == 'json') {
				Mage::getModel('core/config')->saveConfig('snippets/system/breadcrumbs_overwrite', 0);
			} else {
				if($breadcrumbs) {
					Mage::getModel('core/config')->saveConfig('snippets/system/breadcrumbs_overwrite', 1);		
				}	
			}
		}
		
        Mage::register('snippets_modify_event', true, true);
        parent::_beforeSave();
    }

    public function has_value_for_configuration_changed($observer) {
        if (Mage::registry('snippets_modify_event') == true) {
            Mage::unregister('snippets_modify_event');
            Magmodules_Snippets_Model_System_Config_Model_License::isEnabled();
        }       
    }

    protected function _afterSave() {
        Mage::app()->cleanCache(Magmodules_Snippets_Model_Snippets::CACHE_TAG);
    }
    
}
