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
 
class Magmodules_Snippets_Model_Source_Product_Conditionvalues {

	public function toOptionArray() {
		$type = array();
		$type[] = array('value'=>'new', 'label'=> Mage::helper('snippets')->__('New'));
		$type[] = array('value'=>'refurbished', 'label'=> Mage::helper('snippets')->__('Refurbished'));				
		$type[] = array('value'=>'used', 'label'=> Mage::helper('snippets')->__('Used'));				
		$type[] = array('value'=>'damaged', 'label'=> Mage::helper('snippets')->__('Damaged'));				
		return $type;		
	}
	
}