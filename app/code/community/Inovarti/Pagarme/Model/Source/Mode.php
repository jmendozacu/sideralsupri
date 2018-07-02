<?php
/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */
class Inovarti_Pagarme_Model_Source_Mode
{
    const MODE_TEST = 'test';
    const MODE_LIVE = 'live';

    public function toOptionArray()
    {
        return array(
            array(
                'value' => self::MODE_TEST,
                'label' => Mage::helper('pagarme')->__('Test')
            ),
            array(
                'value' => self::MODE_LIVE,
                'label' => Mage::helper('pagarme')->__('Live')
            ),
        );
    }
}
