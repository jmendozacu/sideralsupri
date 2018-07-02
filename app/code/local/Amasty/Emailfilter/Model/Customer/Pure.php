<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Emailfilter
 */
$autoloader = Varien_Autoload::instance();
if (Mage::helper('core')->isModuleEnabled('Amasty_Customerattr')) {
    $autoloader->autoload('Amasty_Emailfilter_Model_Customer_Customerattr');
} else {
    class Amasty_Emailfilter_Model_Customer_Pure extends Mage_Customer_Model_Customer {}
}
