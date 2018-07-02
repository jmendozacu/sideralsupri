<?php

class Aleap_Integrator_Model_Resource_ProductHelper {
    public static function delivery($mp) {
        $result = array();
        $result['weight'] = (float)$mp->getWeight();

        return $result;
    }

    public static function images($mp) {
        $result = array();

        $prod = Mage::helper('catalog/product')->getProduct($mp->getId(), null, null);
        $galleryData = $prod->getData('media_gallery');

        foreach ($galleryData['images'] as &$image) {
            $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $image['file'];
            $image = array('url' => $url);
            $result[] = $image;
        }

        return $result;
    }

    /**
     * @param $mp Mage_Catalog_Model_Product Magento product
     * @param $attribute_name string attribute name
     * @return mixed attribute value or null if not found
     */
    public static function alt($mp, $attribute_name) {
        return Aleap_Integrator_Model_Source_Attribute::getAttribute($mp, $attribute_name);
    }

    /**
     * @param $mp
     * @return int
     */
    public static function stock($mp) {
        /** @var $stock_item Mage_CatalogInventory_Model_Stock_Item */
        $stock_item = Mage::getModel('cataloginventory/stock_item');
        $stock_item->loadByProduct($mp);
        $stock = (int)$stock_item->getQty();
        return $stock;
    }

    public static function brand($mp) {
        $possibleNames = array('brand', 'manufacturer', 'marca', 'fabricante');
        $brand = self::findAttributeValue($mp, $possibleNames);

        return $brand;
    }

    /**
     * @param $mp Mage_Catalog_Model_Product
     * @param $possibleNames
     * @return string
     */
    private static function findAttributeValue($mp, $possibleNames) {
        $result = self::findAttributeByNameOrLabel($mp, $possibleNames);
        if (!$result) {
            $result = self::findByDataName($mp, $possibleNames);
        }

        return $result;
    }

    private static function findByDataName($mp, $possibleNames)
    {
        foreach ($possibleNames as $name) {
            $result = $mp->getData($name);
            if ($result) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param $mp Mage_Catalog_Model_Product
     * @param $names string[]
     * @return string
     */
    private static function findAttributeByNameOrLabel($mp, $names) {
        foreach ($names as $name) {
            foreach ($mp->getAttributes() as $attribute) {
                $attributeName = $attribute->getName();
                $foundByName = strcasecmp($name, $attributeName) == 0;
                $foundByLabel = strcasecmp($name, $attribute->getFrontendLabel()) == 0;
                if ($foundByName || $foundByLabel) {
                    return $mp->getAttributeText($attributeName);
                }
            }
        }

        return null;
    }

    public static function prices($mp) {
        $today = date('c');
        $useSpecialPrice = $mp->getSpecialPrice()
                && ($mp->getSpecialFromDate() <= $today || !$mp->getSpecialFromDate())
                && ($today <= $mp->getSpecialToDate() || !$mp->getSpecialToDate());

        return array(
                'price' => $useSpecialPrice ? $mp->getSpecialPrice() : $mp->getPrice(),
                'original_price' => $useSpecialPrice ? $mp->getPrice() : null
        );
    }

    public static function flatImages($images) {
        $result = Array();
        foreach($images as $image) {
            $result[] = $image['url'];
        }
        sort($result);

        return $result;
    }
}
