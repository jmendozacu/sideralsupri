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
 
class Magmodules_Snippets_Model_Source_Breadcrumbs_Markup {

	public function toOptionArray() {
		$type = array();
		$type[] = array('value'=>'json', 'label'=> Mage::helper('snippets')->__('JSON-LD'));
		$type[] = array('value'=>'rdf', 'label'=> Mage::helper('snippets')->__('RDF - Data-vocabulary'));
		$type[] = array('value'=>'schema', 'label'=> Mage::helper('snippets')->__('Schema.org'));				
		return $type;		
	}
	
}