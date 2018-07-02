<?php

/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */
class Inovarti_Pagarme_Model_Sales_Order_Total_Creditmemo_Fee extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        $feeAmount = $order->getFeeAmount();
        $baseFeeAmount = $order->getBaseFeeAmount();
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $feeAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseFeeAmount);
        $creditmemo->setFeeAmount($feeAmount);
        $creditmemo->setBaseFeeAmount($baseFeeAmount);

        return $this;
    }
}
