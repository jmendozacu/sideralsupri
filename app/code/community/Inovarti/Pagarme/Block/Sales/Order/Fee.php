<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Block_Sales_Order_Fee
extends Mage_Core_Block_Template
{

public function getOrder()
{
    return $this->getParentBlock()->getOrder();
}

public function getSource()
{
    return $this->getParentBlock()->getSource();
}

public function initTotals()
{
    if ((float) $this->getOrder()->getBaseFeeAmount())
    {
        $source = $this->getSource();
        $value  = $source->getFeeAmount();
        $this->getParentBlock()->addTotal(new Varien_Object(array(
            'code'   => 'fee',
            'strong' => false,
            'label'  => Mage::helper('pagarme')->__('Fee'),
            'value'  => $value
        )));
    }
    return $this;
}

}

