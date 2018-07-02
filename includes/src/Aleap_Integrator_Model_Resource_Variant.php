<?php

class Aleap_Integrator_Model_Resource_Variant {
    protected static function priceVariations($parent, $attributes) {
        $pricesByAttributeValues = array();
        $basePrice = $parent->getFinalPrice();
        foreach ($attributes as $attribute){
            $prices = $attribute->getPrices();
            foreach ($prices as $price){
                if ($price['is_percent']){ //if the price is specified in percents
                    $pricesByAttributeValues[$price['value_index']] = (float)$price['pricing_value'] * $basePrice / 100;
                } else { //if the price is absolute value
                    $pricesByAttributeValues[$price['value_index']] = (float)$price['pricing_value'];
                }
            }
        }

        return $pricesByAttributeValues;
    }

    protected static function variantAttributes($parent, $child, $parentAttributes, $pricesByAttributeValues, $parentImages)
    {
        $result = array(
                'sku' => $child->getSku(),
                'stock' => Aleap_Integrator_Model_Resource_ProductHelper::stock($child),
                'attributes' => self::attributesOf($child),
        );

        if ($parent->getName() != $child->getName()) {
            $result['name'] = $child->getName();
        }

        $parent_prices = Aleap_Integrator_Model_Resource_ProductHelper::prices($parent);
        $ht = Aleap_Integrator_Model_Resource_ProductHelper::alt($child, 'handling_time');
        if ($ht && $parent->getHandlingTime() != $ht) {
            $result['handling_time'] = $ht;
        }

        $ean = Aleap_Integrator_Model_Resource_ProductHelper::alt($child, 'ean');
        if ($ean && $parent->getEan() != $ean) {
            $result['ean'] = $ean;
        }

        $deltaPrice = 0;
        foreach ($parentAttributes as $attribute){
            // get the value for a specific attribute for a simple product
            $value = $child->getData($attribute->getProductAttribute()->getAttributeCode());
            // add the price adjustment to the total price of the simple product
            if (isset($pricesByAttributeValues[$value])){
                $deltaPrice += $pricesByAttributeValues[$value];
            }
        }

        if ($deltaPrice != 0) {
            $result['price'] = (float) $parent_prices['price'] + $deltaPrice;

            if ($parent_prices['original_price']) {
                $result['original_price'] = (float) $parent_prices['original_price'] + $deltaPrice;
            }
        }

        if ($child->getWeight() != $parent->getWeight()) {
            $result['delivery'] = Aleap_Integrator_Model_Resource_ProductHelper::delivery($child);
        }

        $childImages = Aleap_Integrator_Model_Resource_ProductHelper::images($child);
        if (self::useChildImages($parentImages, $childImages)) {
            $result['images'] = $childImages;
        }

        return $result;
    }

    private static function useChildImages($parentImages, $childImages) {
        $parents = Aleap_Integrator_Model_Resource_ProductHelper::flatImages($parentImages);
        $childs = Aleap_Integrator_Model_Resource_ProductHelper::flatImages($childImages);

        return $parents != $childs && sizeof($childs) > 0;
    }

    /**
     * @param $mp Mage_Catalog_Model_Product
     * @return array
     */
    public static function attributesOf($mp)
    {
        $ignoreAttributes = array('name', 'price', 'description', 'sku', 'short_description', 'in_depth',
                'activation_information', 'tax_class_id', 'status', 'meta_keyword');
        $result = array();
        $attributes = $mp->getAttributes();
        foreach ($attributes as $attribute) {
            $looksGood = $attribute->getIsVisible() &&
                    ($attribute->getIsVisibleInAdvancedSearch() || $attribute->getIsSearchable() || $attribute->getIsVisibleOnFront()) &&
                    !in_array($attribute->getName(), $ignoreAttributes);
            if ($looksGood) {
                $name = $attribute->getFrontendLabel();
                $value = $attribute->getFrontend()->getValue($mp);
                $result[$name] = $value;
            }
        }

        return $result;
    }
}
