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

class Buscape_PagamentoDigital_Model_Config extends Varien_Object
{
    const XML_PATH = 'payment/pagamentodigital/';
    
    const XML_PATH_CONFIG = 'pagamentodigital/settings/';
    
    protected $_config = array();
    
    protected $_config_settings = array();
    
    public function getConfigData($key, $storeId = null)
    {
        if (!isset($this->_config[$key][$storeId])) {
            $value = Mage::getStoreConfig(self::XML_PATH . $key, $storeId);
            $this->_config[$key][$storeId] = $value;
        }
        return $this->_config[$key][$storeId];
    }

    public function getConfigDataSettings($key, $storeId = null)
    {
        if (!isset($this->_config_settings[$key][$storeId])) {
            $value = Mage::getStoreConfig(self::XML_PATH_CONFIG . $key, $storeId);
            $this->_config_settings[$key][$storeId] = $value;
        }
        return $this->_config_settings[$key][$storeId];
    }    
    
    public function getAccount($store = null)
    {
        if (!$this->hasData('pagamentodigital_account')) {
            $this->setData('pagamentodigital_account', $this->getConfigData('account', $storeId));
        }
        
        return $this->getData('pagamentodigital_account');
    }
    
    public function getToken($store = null)
    {
        if (!$this->hasData('pagamentodigital_token')) {
            $this->setData('pagamentodigital_token', $this->getConfigData('token', $storeId));
        }
        
        return $this->getData('pagamentodigital_token');
    }
    
    public function getUrl($store = null)
    {
        if (!$this->hasData('pagamentodigital_url')) {
            $this->setData('pagamentodigital_url', $this->getConfigData('url', $storeId));
        }
        
        return $this->getData('pagamentodigital_url');
    }
    
    public function getExibitionWebCheckout($storeId = null)
    {        
        if (!$this->hasData('exibitionmodal')) {
            $this->setData('exibitionmodal', $this->getConfigDataSettings('exibitionmodal', $storeId));
        }
        
        return $this->getData('exibitionmodal');
    }
    
}