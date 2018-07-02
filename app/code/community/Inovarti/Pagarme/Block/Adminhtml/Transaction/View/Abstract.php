<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */

abstract class Inovarti_Pagarme_Block_Adminhtml_Transaction_View_Abstract extends Mage_Adminhtml_Block_Template
{
	protected $_viewBlockType;
	protected $_transaction;

	public function __construct()
    {
        parent::__construct();
        $this->setTemplate('pagarme/transaction/view/' . $this->_viewBlockType . '.phtml');
    }

    public function getTransaction()
    {
    	return $this->_transaction;
    }

    public function setTransaction($transaction)
    {
        $this->_transaction = $transaction;
    }

    public function getStatusLabel()
    {
        return Mage::getModel('pagarme/source_status')->getOptionLabel($this->getTransaction()->getStatus());
    }

    public function getAmount()
    {
    	return Mage::helper('core')->formatPrice($this->getTransaction()->getAmount()/100);
    }
}