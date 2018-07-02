<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Emailfilter
 */
class Amasty_Emailfilter_Model_Customer extends Amasty_Emailfilter_Model_Customer_Pure
{
    public function validate()
    {
        $errors = parent::validate();
        if (Mage::getStoreConfig('customer/amemailfilter/forreg')
            && !Mage::helper('amemailfilter')->validateEmail($this->getEmail())
        ) {
            if (!is_array($errors)) {
                $errors = array();
            }
            $errors[] = Mage::helper('amemailfilter')->__('Sorry, your e-mail address is not available at this store.');
        }
        return $errors;
    }
}
