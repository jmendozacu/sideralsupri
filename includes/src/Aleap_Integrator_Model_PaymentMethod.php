<?php

/**
 * Our test CC module adapter
 */
class Aleap_Integrator_Model_PaymentMethod extends Mage_Payment_Model_Method_Checkmo
{
    protected $_code = 'aleap';

    /**
     * Can show this payment method as an option on checkout payment page?
     */
    protected $_canUseCheckout          = false;
}
