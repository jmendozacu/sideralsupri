<?php

class Aleap_Integrator_Model_Source_CustomerGroup {
    public function toOptionArray() {
        $result = Array(Array('value' => '', 'label' => '(use default)'));

        $model = Mage::getModel('customer/group');
        foreach ($model->getCollection() as $group) {
            $id = ':' . $group->getId();
            $result[] = Array('value' => $id, 'label' => $group->getCode());
        }

        return $result;
    }

    public function getId() {
        $value = Mage::getStoreConfig('general/aleap_integrator/customer_group');

        $result = null;
        if ($value != '' && $value) {
            $result = (int) substr($value, 1);
        }

        return $result;
    }
}
