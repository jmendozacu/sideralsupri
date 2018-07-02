<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Pagarme_Block_Checkout_Success_Payment_Boleto extends Inovarti_Pagarme_Block_Checkout_Success_Payment_Default
{
	public function getBoletoUrl()
	{
		return $this->getPayment()->getPagarmeBoletoUrl();
	}
}