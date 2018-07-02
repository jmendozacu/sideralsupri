<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Emailfilter
 */
$autoloader = Varien_Autoload::instance();
if (Mage::helper('core')->isModuleEnabled('Amasty_Customerattr')) {
    $autoloader->autoload('Amasty_Emailfilter_Model_Onepage_Customerattr');
} else {
    class Amasty_Emailfilter_Model_Onepage_Pure extends Mage_Checkout_Model_Type_Onepage {}
}
