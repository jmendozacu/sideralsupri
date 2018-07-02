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
 
class Magmodules_Snippets_Model_Source_Product_Uniqueid {

	public function toOptionArray() {
		$uniqueid = array();
		$uniqueid[] = array('value'=>'', 'label'=>'');
		$uniqueid[] = array('value'=>'gtin8', 'label'=>'GTIN-8');
		$uniqueid[] = array('value'=>'gtin12', 'label'=>'GTIN-12');
		$uniqueid[] = array('value'=>'gtin13', 'label'=>'GTIN-13');
		$uniqueid[] = array('value'=>'gtin14', 'label'=>'GTIN-14');
		$uniqueid[] = array('value'=>'mpn', 'label'=>'MPN');		
		$uniqueid[] = array('value'=>'isbn', 'label'=>'ISBN');				
		return $uniqueid;
	}
	
}