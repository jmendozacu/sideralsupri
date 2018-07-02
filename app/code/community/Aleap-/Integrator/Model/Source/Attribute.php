<?php

class Aleap_Integrator_Model_Source_Attribute
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = Array(Array('value' => '', 'label' => '(use default)'));

        $attributes = Mage::getResourceModel('catalog/product_attribute_collection');
        foreach ($attributes as $attribute) {
            $label = $code = $attribute->getData('attribute_code');
            $front_name = $attribute->getData('frontend_label');
            if (!empty($front_name)) {
                $label .= ' (' . $front_name . ')';
            }

            $result[] = Array('value' => $code, 'label' => $label);
        }

        return $result;
    }

    public static function getAttribute($product, $name) {
        $result = self::getAttr($product, $name);

        $alt_code = Mage::getStoreConfig('general/aleap_integrator/alt_' . $name);
        if (!empty($alt_code)) {
            $alt_value = self::getAttr($product, $alt_code, $name);
            if (!empty($alt_value)) {
                $result = $alt_value;
            }
        }

        return $result;
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param $name
     * @param $standard_name
     * @return string
     */
    private static function getAttr($product, $name, $standard_name = null) {
        if (!$standard_name) {
            $standard_name = $name;
        }
        $directAttrs = Array('name', 'description', 'handling_time', 'ean');
        $result = $product->getData($name);

        if (!in_array($standard_name, $directAttrs)) {
            $attributeText = $product->getAttributeText($name);

            if (!empty($attributeText)) {
                $result = $attributeText;
            }
        }

        return $result;
    }
}
