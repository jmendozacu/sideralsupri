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
 
class Magmodules_Snippets_Model_Source_Organization_Reviews {

	public function toOptionArray() {
		$reviewtype = array();
		$reviewtype[] = array('value'=>'', 'label'=> Mage::helper('snippets')->__('-- none'));
		$reviewtype[] = array('value'=>'shopreview', 'label'=> Mage::helper('snippets')->__('Magmodules: Shopreview'));				
		$reviewtype[] = array('value'=>'feedbackcompany', 'label'=> Mage::helper('snippets')->__('Magmodules: The Feedback Company'));				
		$reviewtype[] = array('value'=>'webwinkelkeur', 'label'=> Mage::helper('snippets')->__('Magmodules: Webwinkelkeur'));				
		$reviewtype[] = array('value'=>'trustpilot', 'label'=> Mage::helper('snippets')->__('Magmodules: Trustpilot'));				
		$reviewtype[] = array('value'=>'kiyoh', 'label'=> Mage::helper('snippets')->__('Magmodules: KiyOh'));				
		return $reviewtype;		
	}
	
}