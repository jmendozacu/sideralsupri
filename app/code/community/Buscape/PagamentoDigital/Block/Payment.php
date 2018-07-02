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

class Buscape_PagamentoDigital_Block_Payment extends Mage_Core_Block_Template
{
    
    protected function getPayment()
    {
        $standard = Mage::getModel('pagamentodigital/'.$this->getRequest()->getParam("type"));
         
        $form = new Varien_Data_Form();

        $form->setAction($standard->getPagamentoDigitalUrl())
            ->setId('pd_form')
            ->setName('pd_form')
            ->setMethod('POST')
            ->setUseContainer(true);

        foreach ($standard->getCheckoutFormFields() as $field => $value)
        {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }
        
        $html = $form->toHtml();

        echo $html;
    }
}