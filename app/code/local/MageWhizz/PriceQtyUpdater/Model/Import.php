<?php
/**
 * MageWhizz
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://magewhizz.com/magento-extension-LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magewhizz.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *
 * @category   MageWhizz
 * @package    MageWhizz_PriceQtyUpdater
 * @copyright  Copyright (c) 2013 PROTO BALSAS UAB (http://magewhizz.com)
 * @license    http://magewhizz.com/magento-extension-LICENSE.txt
 */

class MageWhizz_PriceQtyUpdater_Model_Import extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
        Mage::helper('priceqtyupdater')->uploadAndImport($this);
    }
}
