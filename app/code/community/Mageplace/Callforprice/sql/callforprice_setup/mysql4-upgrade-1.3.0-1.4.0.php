<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */

$installer = $this;
$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('mageplace_callforprice/callforprice'), 'store_ids', 'varchar(100) NOT NULL');

$installer->endSetup();