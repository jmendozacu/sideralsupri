<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

class Inovarti_Pagarme_Model_Source_Invoice_Email
{

public function toOptionArray ()
{
    $options = array(
        array(
            'value' => '0',
            'label' => Mage::helper('pagarme')->__("Don't Send Invoice Email"),
        ),
        array(
            'value' => '1',
            'label' => Mage::helper('pagarme')->__('Send Invoice Email'),
        ),
        array(
            'value' => '2',
            'label' => Mage::helper('pagarme')->__('Send Invoice Update Email'),
        ),
    );

    return $options;
}

}

