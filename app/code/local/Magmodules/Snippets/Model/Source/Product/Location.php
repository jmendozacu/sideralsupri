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
 
class Magmodules_Snippets_Model_Source_Product_Location {

	public function toOptionArray() {
		$location = array();
		$location[] = array('value'=>'', 'label'=> Mage::helper('snippets')->__('-- Manual'));				
		$location[] = array('value'=>'Mage_Catalog_Block_Product_View_Description', 'label'=> Mage::helper('snippets')->__('Product Description Block'));
		$location[] = array('value'=>'Mage_Catalog_Block_Product_View_Attributes', 'label'=> Mage::helper('snippets')->__('Additional Information Block'));
		$location[] = array('value'=>'Mage_Review_Block_Product_View_List', 'label'=> Mage::helper('snippets')->__('Product Review Block (Modern Theme)'));		
		$location[] = array('value'=>'advanced', 'label'=> Mage::helper('snippets')->__('Advanced: Custom Layout Update Handle'));				
		return $location;
	}
	
}