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

class Buscape_PagamentoDigital_Model_Observer extends Varien_Object
{
    public function sendEmailFrontend() 
    {
        $session = Mage::getSingleton('checkout/session');
        $lastSuccessOrderId = $session->getData('last_real_order_id');
       
        $order = Mage::getModel('sales/order')->loadByAttribute('increment_id',$lastSuccessOrderId);

        $sendNewOrderEmail = Mage::getStoreConfig('sales_email/order/enabled');
        if ($sendNewOrderEmail && !$order->getData('email_sent')) {
            $order->sendNewOrderEmail();
            $order->setEmailSent(true);
            $order->save();
        }
    }

}