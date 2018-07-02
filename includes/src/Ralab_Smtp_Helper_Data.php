<?php
/**
 *
 * @package     Ralab_Smtp
 * @author      Kalpesh Balar <kalpeshbalar@gmail.com>
 * @copyright   Ralab (http://ralab.in)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

class Ralab_Smtp_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getSMTPAuth($storeId = null)
    {
        return Mage::getStoreConfig('system/smtp/smtp_auth', $storeId);
    }

    public function getSMTPHost($storeId = null)
    {
        return Mage::getStoreConfig('system/smtp/host', $storeId);
    }

    public function getSMTPPort($storeId = null)
    {
        return Mage::getStoreConfig('system/smtp/port', $storeId);
    }

    public function getSMTPUsername($storeId = null)
    {
        return Mage::getStoreConfig('system/smtp/smtp_username', $storeId);
    }

    public function getSMTPPassword($storeId = null)
    {
        return Mage::getStoreConfig('system/smtp/smtp_password', $storeId);
    }

    public function getSMTPSSL($storeId = null)
    {
        return Mage::getStoreConfig('system/smtp/smtp_ssl', $storeId);
    }

    public function getTransport () {

        $config = array();

        $auth = $this->getSMTPAuth($storeId);
        if ($auth != "none") {
            $config['auth'] = $auth;
            $config['username'] = $this->getSMTPUsername($storeId);
            $config['password'] = $this->getSMTPPassword($storeId);
        }

        $config['port'] = $this->getSMTPPort($storeId);

        $ssl = $this->getSMTPSSL($storeId);
        if ($ssl != "none" ) {
            $config['ssl'] = $ssl;
        }

        $host = $this->getSMTPHost($storeId);
        $transport = new Zend_Mail_Transport_Smtp($host, $config);
        return $transport;
    }
}
