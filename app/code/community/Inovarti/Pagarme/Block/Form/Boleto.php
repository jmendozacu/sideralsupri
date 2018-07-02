<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Pagarme_Block_Form_Boleto extends Mage_Payment_Block_Form
{
    protected $_instructions;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('pagarme/form/boleto.phtml');
    }

    public function getInstructions()
    {
        if (is_null($this->_instructions)) {
            $this->_instructions = $this->getMethod()->getConfigData('instructions');
        }
        return $this->_instructions;
    }
}
