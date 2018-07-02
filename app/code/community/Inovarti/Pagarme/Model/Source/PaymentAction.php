<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Pagarme_Model_Source_PaymentAction
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Inovarti_Pagarme_Model_Cc::ACTION_AUTHORIZE,
                'label' => Mage::helper('pagarme')->__('Authorize Only')
            ),
            array(
                'value' => Inovarti_Pagarme_Model_Cc::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('pagarme')->__('Authorize and Capture')
            ),
        );
    }
}
