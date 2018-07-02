<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to suporte.developer@buscape-inc.com so we can send you a copy immediately.
 *
 * @category   Buscape
 * @package    Buscape_PagamentoDigital
 * @copyright  Copyright (c) 2010 Buscapé Company (http://www.buscapecompany.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Buscape_PagamentoDigital_Model_Source_Parcelas
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('adminhtml')->__('01')),
            array('value' => 2, 'label'=>Mage::helper('adminhtml')->__('02')),
            array('value' => 3, 'label'=>Mage::helper('adminhtml')->__('03')),
            array('value' => 4, 'label'=>Mage::helper('adminhtml')->__('04')),
            array('value' => 5, 'label'=>Mage::helper('adminhtml')->__('05')),
            array('value' => 6, 'label'=>Mage::helper('adminhtml')->__('06')),
            array('value' => 7, 'label'=>Mage::helper('adminhtml')->__('07')),
            array('value' => 8, 'label'=>Mage::helper('adminhtml')->__('08')),
            array('value' => 9, 'label'=>Mage::helper('adminhtml')->__('09')),
            array('value' => 10, 'label'=>Mage::helper('adminhtml')->__('10')),
            array('value' => 11, 'label'=>Mage::helper('adminhtml')->__('11')),
            array('value' => 12, 'label'=>Mage::helper('adminhtml')->__('12')),
            array('value' => 13, 'label'=>Mage::helper('adminhtml')->__('13')),
            array('value' => 14, 'label'=>Mage::helper('adminhtml')->__('14')),
            array('value' => 15, 'label'=>Mage::helper('adminhtml')->__('15')),
            array('value' => 16, 'label'=>Mage::helper('adminhtml')->__('16')),
            array('value' => 17, 'label'=>Mage::helper('adminhtml')->__('17')),
            array('value' => 18, 'label'=>Mage::helper('adminhtml')->__('18')),
            array('value' => 19, 'label'=>Mage::helper('adminhtml')->__('19')),
            array('value' => 20, 'label'=>Mage::helper('adminhtml')->__('20')),
            array('value' => 21, 'label'=>Mage::helper('adminhtml')->__('21')),
            array('value' => 22, 'label'=>Mage::helper('adminhtml')->__('22')),
            array('value' => 23, 'label'=>Mage::helper('adminhtml')->__('23')),
            array('value' => 24, 'label'=>Mage::helper('adminhtml')->__('24')),
        );
    }

}