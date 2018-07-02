<?php

require_once(dirname(__FILE__) . "/../Source/Attribute.php");
require_once(dirname(__FILE__) . "/OptionVariant.php");

class Aleap_Integrator_Model_Resource_Product extends Mage_Core_Model_Resource_Db_Abstract
{
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
        $target->name = Aleap_Integrator_Model_Resource_ProductHelper::alt($mp, 'name');
        $target->id = $mp->getId();

        $prices = Aleap_Integrator_Model_Resource_ProductHelper::prices($mp);
        $target->price = (float)$prices['price'];
        if ($prices['original_price']) {
            $target->original_price = (float)$prices['original_price'];
        }

        $target->url = $mp->getProductUrl();
        $ht = Aleap_Integrator_Model_Resource_ProductHelper::alt($mp, 'handling_time');
        if ($ht) {
            $target->handling_time = $ht;
        }

        $ean = Aleap_Integrator_Model_Resource_ProductHelper::alt($mp, 'ean');
        if ($ean) {
            $target->ean = $ean;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $target->description = Aleap_Integrator_Model_Resource_ProductHelper::alt($mp, 'description');
        $target->images = Aleap_Integrator_Model_Resource_ProductHelper::images($mp);
        $hasCustomOptions = $mp->getHasOptions();
        if ($mp->getTypeId() == 'simple' && !$hasCustomOptions) {
            $target->sku = $mp->getSku();
            $target->stock = Aleap_Integrator_Model_Resource_ProductHelper::stock($mp);
            $target->attributes = Aleap_Integrator_Model_Resource_Variant::attributesOf($mp);
        } else {
            $target->variants = self::variantsOf($mp, $target->images);
        }
        $target->brand = Aleap_Integrator_Model_Resource_ProductHelper::brand($mp);
        $target->categories = self::categories($mp, $storeId, $storeRootId);
        $target->delivery = Aleap_Integrator_Model_Resource_ProductHelper::delivery($mp);

        Mage::log("Assign product: " . $target->getId() . $target->getName() . "\n Brand: " . $target->brand . "\n");
    }

    /**
     * @param $mp Mage_Catalog_Model_Product
     * @param int $storeId
     * @param int $storeRootId
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

    private static function variantsOf($mp, $parentImages) {
        if ($mp->getTypeId() == 'simple') {
            return Aleap_Integrator_Model_Resource_OptionVariant::variantsOf($mp, $parentImages);
        } else {
            return Aleap_Integrator_Model_Resource_ConfigurableVariant::variantsOf($mp, $parentImages);
        }
    }
}