<?php

class Aleap_Integrator_Model_Resource_ConfigurableVariant extends Aleap_Integrator_Model_Resource_Variant {
    public static function variantsOf($mp, $parentImages) {
        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_read');
        $select = $conn->select()
                ->from($coreResource->getTableName('catalog/product_relation'), array('child_id'))
                ->where('parent_id = ?', $mp->getId());

        $result = array();
        $childrenIds = $conn->fetchCol($select);
        $parentAttributes = $mp->getTypeInstance(true)->getConfigurableAttributes($mp);
        $priceVariations = self::priceVariations($mp, $parentAttributes);
        foreach ($childrenIds as $childId) {
            $child = Mage::getModel('catalog/product');
            $child->load($childId);
            if ($child->getStatus() == 1) {
                $result[] = self::variantAttributes($mp, $child, $parentAttributes, $priceVariations, $parentImages);
            }
        }

        return $result;
    }
}
