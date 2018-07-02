<?php

require_once(dirname(__FILE__) . "/../Source/Attribute.php");

class Aleap_Integrator_Model_Resource_Product extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * @param $mp Mage_Catalog_Model_Product Magento product
     * @param $attribute_name string attribute name
     * @return mixed attribute value or null if not found
     */
    private static function alt($mp, $attribute_name) {
        return Aleap_Integrator_Model_Source_Attribute::getAttribute($mp, $attribute_name);
    }

    protected function _construct()
    {
        $this->_init('aleap/product', 'entity_id'); // the closest thing
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field = null)
    {
        $mp = Mage::getModel('catalog/product')->load($value);
        $this->assignMagentoProduct($object, $mp);
    }

    public static function assignMagentoProduct($target, $mp, $storeId = null, $storeRootId = null)
    {
        /** @var $mp Mage_Catalog_Model_Product */
        $target->name = self::alt($mp, 'name');
        $target->id = $mp->getId();

        $prices = self::prices($mp);
        $target->price = (float)$prices['price'];
        if ($prices['original_price']) {
            $target->original_price = (float)$prices['original_price'];
        }

        $target->url = $mp->getProductUrl();
        $ht = self::alt($mp, 'handling_time');
        if ($ht) {
            $target->handling_time = $ht;
        }

        $ean = self::alt($mp, 'ean');
        if ($ean) {
            $target->ean = $ean;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $target->description = self::alt($mp, 'description');
        $target->images = self::images($mp);
        if ($mp->getTypeId() == 'simple') {
            $target->sku = $mp->getSku();
            $target->stock = self::stock($mp);
            $target->attributes = self::attributesOf($mp);
        } else {
            $target->variants = self::variantsOf($mp, $target->images);
        }
        $target->brand = self::brand($mp);
        $target->categories = self::categories($mp, $storeId, $storeRootId);
        $target->delivery = self::delivery($mp);

        Mage::log("Assign product: " . $target->getId() . $target->getName() . "\n Brand: " . $target->brand . "\n");
    }

    /**
     * @param $mp Mage_Catalog_Model_Product
     * @return array
     */
    private static function attributesOf($mp)
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

    /**
     * @param $mp Mage_Catalog_Model_Product
     * @param $storeId String
     * @param $storeRootId String
     * @return String[]
     */
    private static function categories($mp, $storeId = null, $storeRootId = null)
    {

        $all_categories = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addIdFilter($mp->getCategoryIds());

        if ($storeId) {
            $isFlatCategories = $all_categories instanceof Mage_Catalog_Model_Resource_Category_Flat_Collection;

            if ($isFlatCategories) {
                $all_categories = array();
                foreach ($mp->getCategoryIds() as $categoryId) {
                    $all_categories[] = Mage::getModel('catalog/category')
                            ->setStoreId($storeId)
                            ->load($categoryId);
                }
            } else {
                $all_categories->addPathFilter("^1/$storeRootId/");
            }
        }

        $categories = array();
        /** @var $category Mage_Catalog_Model_Category */
        foreach ($all_categories as $category) {
            if ($category->getIncludeInMenu() && $category->getIsActive()) { // only visible categories
                $categories[] = $category;
            }
        }

        $result = array();
        foreach ($categories as $category) {
            $isParent = false;
            foreach ($categories as $possibleChild) {
                $isParent = $isParent || ($category->getId() == $possibleChild->getParentId());
            }

            if (!$isParent) {
                $result[] = self::categoryName($category);
            }
        }

        return $result;
    }

    /**
     * @param $category Mage_Catalog_Model_Category
     * @return string
     */
    private static function categoryName($category)
    {
        $result = $category->getName();

        $parent = $category->getParentCategory();
        $parentIsRootCategory = $parent->getParentCategory() && $parent->getParentCategory()->getLevel() == 0;
        if (!$parentIsRootCategory) {
            $result = self::categoryName($parent) . '/' . $result;
        }

        return $result;
    }

    /**
     * @param $mp
     * @return int
     */
    public static function stock($mp)
    {
        /** @var $stock_item Mage_CatalogInventory_Model_Stock_Item */
        $stock_item = Mage::getModel('cataloginventory/stock_item');
        $stock_item->loadByProduct($mp);
        $stock = (int)$stock_item->getQty();
        return $stock;
    }

    private static function brand($mp)
    {
        $possibleNames = array('brand', 'manufacturer', 'marca', 'fabricante');
        $brand = self::findAttributeValue($mp, $possibleNames);

        return $brand;
    }

    /**
     * @param $mp Mage_Catalog_Model_Product
     * @param $possibleNames
     * @return string
     */
    private static function findAttributeValue($mp, $possibleNames)
    {
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
    private static function findAttributeByNameOrLabel($mp, $names)
    {
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


    public static function delivery($mp)
    {
        $result = array();
        $result['weight'] = (float)$mp->getWeight();

        return $result;
    }

    private static function images($mp)
    {
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

    private static function variantsOf($mp, $parentImages)
    {
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

    private static function priceVariations($parent, $attributes) {
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

    private static function variantAttributes($parent, $child, $parentAttributes, $pricesByAttributeValues, $parentImages)
    {
        $result = array(
                'sku' => $child->getSku(),
                'stock' => self::stock($child),
                'attributes' => self::attributesOf($child),
        );

        if ($parent->getName() != $child->getName()) {
            $result['name'] = $child->getName();
        }

        $parent_prices = self::prices($parent);
        $ht = self::alt($child, 'handling_time');
        if ($ht && $parent->getHandlingTime() != $ht) {
            $result['handling_time'] = $ht;
        }

        $ean = self::alt($child, 'ean');
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
            $result['delivery'] = self::delivery($child);
        }

        $childImages = self::images($child);
        if (self::useChildImages($parentImages, $childImages)) {
            $result['images'] = $childImages;
        }

        return $result;
    }

    private static function flatImages($images) {
        $result = Array();
        foreach($images as $image) {
            $result[] = $image['url'];
        }
        sort($result);

        return $result;
    }

    private static function useChildImages($parentImages, $childImages) {
        $parents = self::flatImages($parentImages);
        $childs = self::flatImages($childImages);

        return $parents != $childs && sizeof($childs) > 0;
    }

    private static function prices($mp)
    {
        $today = date('c');
        $useSpecialPrice = $mp->getSpecialPrice()
                && ($mp->getSpecialFromDate() <= $today || !$mp->getSpecialFromDate())
                && ($today <= $mp->getSpecialToDate() || !$mp->getSpecialToDate());

        return array(
                'price' => $useSpecialPrice ? $mp->getSpecialPrice() : $mp->getPrice(),
                'original_price' => $useSpecialPrice ? $mp->getPrice() : null
        );
    }
}