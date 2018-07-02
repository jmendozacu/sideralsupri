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
 * @package     Magmodules_Core
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2015 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Core_Block_Adminhtml_Versions extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

    public function render(Varien_Data_Form_Element_Abstract $element) {
		$html = $this->_getHeaderHtml($element);
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		sort($modules);

        foreach($modules as $moduleName) {
        	
        	if(strstr($moduleName, 'Magmodules_') === false) {
        		continue;
        	}
			
			if($moduleName == 'Magmodules_Core'){
				continue;
			}
			
        	$html.= $this->_getFieldHtml($element, $moduleName);
        }
        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    protected function _getFieldRenderer() {
    	if(empty($this->_fieldRenderer)) {
    		$this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
    	}

    	return $this->_fieldRenderer;
    }

	protected function _getFieldHtml($fieldset, $moduleCode) {
		$currentVer = Mage::getConfig()->getModuleConfig($moduleCode)->version;
		
		if(!$currentVer) {
            return '';
		}
		  
		$moduleName = substr($moduleCode, strpos($moduleCode, '_') + 1); 
	
		$field = $fieldset->addField($moduleCode, 'label', array(
            'name'  => 'dummy',
            'label' => $moduleName,
            'value' => $currentVer,
		))->setRenderer($this->_getFieldRenderer());
			
		return $field->toHtml();
    }
    
}