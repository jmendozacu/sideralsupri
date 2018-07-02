<?php

/**
 *
 * @package     Ralab_Smtp
 * @author      Kalpesh Balar <kalpeshbalar@gmail.com>
 * @copyright   Ralab (http://ralab.in)
 *
 */

class Ralab_Smtp_Model_Email extends Mage_Core_Model_Email
{
    public function send()
    {
        Mage::log(__METHOD__);
        if (Mage::getStoreConfigFlag('system/smtp/disable')) {
            return $this;
        }

        $mail = new Zend_Mail();

        if (strtolower($this->getType()) == 'html') {
            $mail->setBodyHtml($this->getBody());
        }
        else {
            $mail->setBodyText($this->getBody());
        }

        $mail->setFrom($this->getFromEmail(), $this->getFromName())
            ->addTo($this->getToEmail(), $this->getToName())
            ->setSubject($this->getSubject());
        //$mail->send();
        $mail->send(Mage::helper('smtp')->getTransport());

        return $this;
    }
}
