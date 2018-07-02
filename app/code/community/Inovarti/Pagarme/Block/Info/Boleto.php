<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Pagarme_Block_Info_Boleto extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('pagarme/info/boleto.phtml');
    }

   /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getInfo()->getPagarmeTransactionId();
    }

    /**
     * @return string
     */
    public function getTransactionUrl()
    {
        return Mage::getUrl('adminhtml/pagarme_transaction/view', array('id' => $this->getTransactionId()));
    }

    /**
     * @return string
     */
	public function getBoletoExpirationDate()
    {
        return Mage::helper('core')->formatDate($this->getInfo()->getPagarmeBoletoExpirationDate(), 'medium');
    }

    /**
     * @return string
     */
    public function getBoletoBarcode()
    {
        return $this->getInfo()->getPagarmeBoletoBarcode();
    }

    /**
     * @return string
     */
    public function getBoletoUrl()
    {
        return $this->getInfo()->getPagarmeBoletoUrl();
    }
}