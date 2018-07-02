<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Pagarme_Block_Info_Cc extends Mage_Payment_Block_Info_Cc
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('pagarme/info/cc.phtml');
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
    public function getCcOwner()
    {
        return $this->getInfo()->getCcOwner();
    }

    /**
     * @return string
     */
    public function getCcNumber()
    {
        return sprintf('xxxx-%s', $this->getInfo()->getCcLast4());
    }

    /**
     * @return string
     */
    public function getCcExpirationDate()
    {
        return $this->_formatCardDate($this->getInfo()->getCcExpYear(), $this->getInfo()->getCcExpMonth());
    }

   /**
     * @return string
     */
    public function getInstallments()
    {
        return $this->getInfo()->getInstallments();
    }

    /**
     * @return string
     */
    public function getInstallmentDescription()
    {
        return $this->getInfo()->getInstallmentDescription();
    }

    /**
     * @return string
     */
    public function getAntifraudScore()
    {
        return sprintf ("%.2f", $this->getInfo()->getPagarmeAntifraudScore());
    }
}
