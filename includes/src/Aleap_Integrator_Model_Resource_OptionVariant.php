<?php

class Aleap_Integrator_Model_Resource_OptionVariant extends Aleap_Integrator_Model_Resource_Variant {
    public static function variantsOf($mp, $parentImages) {
        $ops = $mp->getOptions();
        $optionsConfigurations = self::explode(array_values($ops), Array(), 0);

        $result = array();
        foreach ($optionsConfigurations as $options) {
            $result[] = Aleap_Integrator_Model_Resource_OptionVariant::mergeOptions($options, $mp);
        }

        return $result;
    }

    private static function mergeOptions($options, $mp) {
        $attributes = array();
        $sku = $mp->getSku();
        $deltaPrice = 0;
        $suffix_ids = array();
        foreach($options as $option) {
            $attr_name = $option->getOption()->getTitle();
            $attributes[$attr_name] = $option->getTitle();
            $suffix_ids[] = $option->getId();

            if ($option->getPriceType() == 'percent') {
                $percent = $option->getPrice() / 100.0;
                $deltaPrice += $mp->getPrice() * $percent;
            } else {
                $deltaPrice += $option->getPrice();
            }
        }
        $sku = $sku . '__' . implode('-', $suffix_ids);

        $result = array();

        if ($deltaPrice != 0) {
            $result['price'] = $mp->getPrice() + $deltaPrice;
        }
        if ($mp->getSku() != $sku) {
            $result['sku'] = $sku;
        }
        if (!empty($attributes)) {
            $result['attributes'] = $attributes;
        }

        return $result;
    }

    /**
     * @param Mage_Catalog_Model_Product_Option[] $ops
     * @param Mage_Catalog_Model_Product_Option_Value[] $path
     * @param int $index
     * @return Mage_Catalog_Model_Product_Option_Value[]
     */
    private static function explode($ops, $path, $index) {
        $result = Array();
        if (sizeof($ops) > $index) {
            $optionsGroup = $ops[$index];
            $options = $optionsGroup->getValues();
            foreach($options as $option) {
                $thisPath = $path;
                $thisPath[] = $option;
                $variations = self::explode($ops, $thisPath, $index + 1);
                if ($variations != null) {
                    $result = array_merge($result, $variations);
                } else {
                    $result[] = $thisPath;
                }
            }
        } else {
            $result = null;
        }

        return $result;
    }
}
