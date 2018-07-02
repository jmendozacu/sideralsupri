<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */

class Inovarti_Pagarme_Block_Adminhtml_Transaction_View_Boleto extends Inovarti_Pagarme_Block_Adminhtml_Transaction_View_Abstract
{
   	protected $_viewBlockType = 'boleto';

    public function getBoletoExpirationDate()
    {
        return Mage::helper('core')->formatDate($this->getTransaction()->getBoletoExpirationDate(), 'medium');
    }
}
