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
 
class Magmodules_Snippets_Model_Source_Offer_Currencies {

	public function toOptionArray(){
        $currency = array();
		$currency_code = Mage::getModel('core/config_data')->getCollection()->addFieldToFilter('path','currency/options/allow')->getData();       
		$currencies_array = explode(',', $currency_code[0]['value']);
		
		foreach($currencies_array as $_currency) {
			$currency[] = array('value'=> $_currency, 'label'=> $_currency);				
		}
        
        return $currency;
    }

}