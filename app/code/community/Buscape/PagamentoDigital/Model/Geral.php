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

class Buscape_PagamentoDigital_Model_Geral extends Buscape_PagamentoDigital_Model_Standard
{
    protected $_code  = 'pagamentodigital_geral';
    
    protected $_formBlockType = 'pagamentodigital/form_geral';
    
    protected $_blockType = 'pagamentodigital/geral';
    
    protected $_infoBlockType = 'pagamentodigital/info_geral';
    
    protected $_standardType = 'geral';
    
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('pagamentodigital/standard/payment', array('_secure' => true, 'type' => 'geral'));
    }
}