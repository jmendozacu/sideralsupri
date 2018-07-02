<?php

/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 *
 * UPDATED:
 *
 * @copyright  Copyright (C) 2016 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */
$installer = new Mage_Sales_Model_Resource_Setup('pagarme_setup');
$installer->startSetup();

// Quote Payment
$entity = 'quote_payment';
$attributes = array(
    'pagarme_card_hash' => array('type' => Varien_Db_Ddl_Table::TYPE_TEXT),
    'installments' => array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT),
    'installment_description' => array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR),
);

foreach ($attributes as $attribute => $options) {
    $installer->addAttribute($entity, $attribute, $options);
}

// Order Payment
$entity = 'order_payment';
$attributes = array(
    'installments' => array('type' => Varien_Db_Ddl_Table::TYPE_SMALLINT),
    'installment_description' => array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR),
    'pagarme_transaction_id' => array('type' => Varien_Db_Ddl_Table::TYPE_INTEGER),
    'pagarme_boleto_url' => array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR),
    'pagarme_boleto_barcode' => array('type' => Varien_Db_Ddl_Table::TYPE_VARCHAR),
    'pagarme_boleto_expiration_date' => array('type' => Varien_Db_Ddl_Table::TYPE_DATETIME),
    'pagarme_antifraud_score' => array('type' => Varien_Db_Ddl_Table::TYPE_DECIMAL)
);

foreach ($attributes as $attribute => $options) {
    $installer->addAttribute($entity, $attribute, $options);
}

$table = $installer->getTable('sales/quote_address');

$installer->getConnection()
    ->addColumn($table, 'fee_amount', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Fee Amount',
    ));

$installer->getConnection()
    ->addColumn($table, 'base_fee_amount', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Base Fee Amount',
    ));

$table = $installer->getTable('sales/order');

$installer->getConnection()
    ->addColumn($table, 'fee_amount', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Fee Amount',
    ));

$installer->getConnection()
    ->addColumn($table, 'base_fee_amount', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Base Fee Amount',
    ));

$installer->getConnection()
    ->addColumn($table, 'fee_amount_invoiced', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Fee Amount Invoiced',
    ));

$installer->getConnection()
    ->addColumn($table, 'base_fee_amount_invoiced', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Base Fee Amount Invoiced',
    ));

$installer->getConnection()
    ->addColumn($table, 'fee_amount_refunded', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Fee Amount Refunded',
    ));

$installer->getConnection()
    ->addColumn($table, 'base_fee_amount_refunded', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Base Fee Amount Refunded',
    ));

$table = $installer->getTable('sales/invoice');

$installer->getConnection()
    ->addColumn($table, 'fee_amount', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Fee Amount',
    ));

$installer->getConnection()
    ->addColumn($table, 'base_fee_amount', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Base Fee Amount',
    ));

$table = $installer->getTable('sales/creditmemo');

$installer->getConnection()
    ->addColumn($table, 'fee_amount', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Fee Amount',
    ));

$installer->getConnection()
    ->addColumn($table, 'base_fee_amount', array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'unsigned' => true,
        'nullable' => false,
        'comment' => 'Base Fee Amount',
    ));

$installer->endSetup();
