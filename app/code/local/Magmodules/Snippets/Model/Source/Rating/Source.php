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
 
class Magmodules_Snippets_Model_Source_Rating_Source {

	public function toOptionArray() {
		$type = array();
		$type[] = array('value'=>'', 'label'=> Mage::helper('snippets')->__('Magento Reviews'));
		
		// CHECK FOR YOTPO EXTENSION & VERSION
		if(Mage::helper('core')->isModuleEnabled('Yotpo_Yotpo')) {
			$version = Mage::getConfig()->getNode()->modules->Yotpo_Yotpo->version;
			if(version_compare($version, '1.6.2', '>=')){
				$type[] = array('value'=>'yotpo', 'label'=> Mage::helper('snippets')->__('Yotpo Reviews'));				
			}	
		}	

		return $type;		
	}
	
}