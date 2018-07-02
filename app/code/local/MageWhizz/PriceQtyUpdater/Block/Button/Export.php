<?php
/**
 * MageWhizz
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://magewhizz.com/magento-extension-LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magewhizz.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *
 * @category   MageWhizz
 * @package    MageWhizz_PriceQtyUpdater
 * @copyright  Copyright (c) 2013 PROTO BALSAS UAB (http://magewhizz.com)
 * @license    http://magewhizz.com/magento-extension-LICENSE.txt
 */

class MageWhizz_PriceQtyUpdater_Block_Button_Export extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $url = $this->getUrl('priceqtyupdater/adminhtml_data/export'); 

        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setLabel(Mage::helper('priceqtyupdater')->__('Export'))
                    ->setOnClick("setLocation('$url')")
                    ->toHtml();
					
        return $html;
    }
}