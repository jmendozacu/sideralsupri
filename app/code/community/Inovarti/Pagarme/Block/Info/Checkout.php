<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Block_Info_Checkout
extends Mage_Payment_Block_Info
{

protected function _construct()
{
    parent::_construct();

    $this->setTemplate('pagarme/info/checkout.phtml');
}

public function getTransactionId()
{
    return $this->getInfo()->getPagarmeTransactionId();
}

public function getTransactionUrl()
{
    return Mage::getUrl('adminhtml/pagarme_transaction/view', array('id' => $this->getTransactionId()));
}

public function getCcOwner()
{
    return $this->getInfo()->getCcOwner();
}

public function getCcNumber()
{
    return sprintf('xxxx-%s', $this->getInfo()->getCcLast4());
}

public function getCcExpirationDate()
{
    return $this->_formatCardDate($this->getInfo()->getCcExpYear(), $this->getInfo()->getCcExpMonth());
}

public function getInstallments()
{
    return $this->getInfo()->getInstallments();
}
/*
public function getInstallmentDescription()
{
    return $this->getInfo()->getInstallmentDescription();
}
*/
public function getAntifraudScore()
{
    return sprintf ("%.2f", $this->getInfo()->getPagarmeAntifraudScore());
}

public function getAdditionalInfo ($key)
{
    $transactions = Mage::getResourceModel('sales/order_payment_transaction_collection');
    $transactions->addOrderIdFilter($this->getInfo()->getOrder()->getId());

    $add_info = $transactions->getFirstItem()->getAdditionalInformation();

    return $add_info ['raw_details_info'][$key];
}

public function getCcTypeName()
{
    $types = Mage::getSingleton('payment/config')->getCcTypes();

    $ccType = $this->getInfo()->getCcType();
    if (isset($types[$ccType]))
    {
        return $types[$ccType];
    }

    return (empty($ccType)) ? Mage::helper('payment')->__('N/A') : $ccType;
}

}

