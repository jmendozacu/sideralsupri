<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */


$installer = $this;
$installer->startSetup();

$installer->run("
     ALTER TABLE `{$this->getTable('mageplace_callforprice/callforprice')}` CHANGE `customer_groups` `customer_groups` text COLLATE 'utf8_general_ci' NOT NULL AFTER `callforprice`;
");

$installer->endSetup();
