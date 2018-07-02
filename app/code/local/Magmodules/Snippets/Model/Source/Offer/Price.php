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
 
class Magmodules_Snippets_Model_Source_Offer_Price {

	public function toOptionArray() {
		$price = array();
		$price[] = array('value'=>'', 'label'=>'Default Pricing');
		$price[] = array('value'=>'notax', 'label'=>'Default Pricing - Remove % Tax');
		$price[] = array('value'=>'custom', 'label'=>'Attribute Pricing');
		return $price;
	}
	
}