<?php

/**
 *
 * @package     Ralab_Smtp
 * @author      Kalpesh Balar <kalpeshbalar@gmail.com>
 * @copyright   Ralab (http://ralab.in)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */

class Ralab_Smtp_Model_System_Config_Source_Smtp_Authentication
{
    public function toOptionArray()
    {
        //http://framework.zend.com/manual/current/en/modules/zend.mail.smtp.options.html
        return array(
            "smtp" => Mage::helper('adminhtml')->__('None'),
            "login" => Mage::helper('adminhtml')->__('Login'),
            "plain" => Mage::helper('adminhtml')->__('Plain'),
            "crammd5" => Mage::helper('adminhtml')->__('CRAM MD5')
        );
    }
}
