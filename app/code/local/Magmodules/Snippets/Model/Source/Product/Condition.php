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
 
class Magmodules_Snippets_Model_Source_Product_Condition {

	public function toOptionArray() {
		$type = array();
		$type[] = array('value'=>'', 'label'=> Mage::helper('snippets')->__('No'));
		$type[] = array('value'=>'1', 'label'=> Mage::helper('snippets')->__('Yes, same for all'));				
		$type[] = array('value'=>'2', 'label'=> Mage::helper('snippets')->__('Yes, use attribute'));				
		return $type;		
	}
	
}