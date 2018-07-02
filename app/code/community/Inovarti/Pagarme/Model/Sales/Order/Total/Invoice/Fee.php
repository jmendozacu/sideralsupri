<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Model_Sales_Order_Total_Invoice_Fee extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();

        $feeAmountLeft = $order->getFeeAmount() - $order->getFeeAmountInvoiced();
        $baseFeeAmountLeft = $order->getBaseFeeAmount() - $order->getBaseFeeAmountInvoiced();
        if (abs($baseFeeAmountLeft) < $invoice->getBaseGrandTotal()) {
            $invoice->setGrandTotal($invoice->getGrandTotal() + $feeAmountLeft);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseFeeAmountLeft);
        } else {
            $feeAmountLeft = $invoice->getGrandTotal() * -1;
            $baseFeeAmountLeft = $invoice->getBaseGrandTotal() * -1;
            $invoice->setGrandTotal(0);
            $invoice->setBaseGrandTotal(0);
        }
        $invoice->setFeeAmount($feeAmountLeft);
        $invoice->setBaseFeeAmount($baseFeeAmountLeft);

        return $this;
    }
}
