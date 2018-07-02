<?php

require_once(dirname(__FILE__) . '/Resource/Product.php');

/**
 * @property string name
 * @property string description
 * @property string sku
 * @property float price
 * @property mixed[] attributes
 */
class Aleap_Integrator_Model_Product extends Mage_Core_Model_Abstract
{
    /**
     * @param $page int
     * @param $perPage int
     * @param $storeId int
     * @return Aleap_Integrator_Model_Product[]
     */
    public static function fetchAll($page, $perPage, $storeId = null)
    {
        $result = array();
        $productIds = self::productsCollection($storeId)
            ->setPageSize($perPage)
            ->setCurPage($page)
            ->getColumnValues('entity_id');

        $storeRootId = $storeId ? Mage::app()->getStore($storeId)->getRootCategoryId() : null;
        /** @var $mp Mage_Catalog_Model_Product */
        foreach ($productIds as $id) {
            $mp = Mage::getModel('catalog/product')->setStoreId($storeId);
            $mp->load($id);
            $product = new Aleap_Integrator_Model_Product();
            Aleap_Integrator_Model_Resource_Product::assignMagentoProduct($product, $mp, $storeId, $storeRootId);
            $result[] = $product;
        }

        return $result;
    }

    public static function totalCount($storeId = null) {
        $collection = self::productsCollection($storeId);
        return $collection->getSize();
    }

    private static function productsCollection($storeId) {
        $mgProducts = Mage::getModel('catalog/product')->getCollection();
        if ($storeId) {
            $mgProducts->setStoreId($storeId)
                    ->addStoreFilter($storeId);
        }

        $okIds = self::simpleOrSuperProductIds();
        return $mgProducts
                ->addAttributeToFilter('entity_id', array('in', $okIds))
                ->addAttributeToFilter('status', 1);
    }

    public function __construct() {
        $this->_init('aleap/product');
    }

    private static function simpleOrSuperProductIds() {
        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_read');
        $productsTable = $coreResource->getTableName('catalog/product');
        $relationTable = $coreResource->getTableName('catalog/product_relation');
        $select = $conn->select()
                ->from($productsTable, array('entity_id'))
                ->where("
                    (
                        type_id = 'simple'
                        and entity_id not in (
                            select child_id from " . $relationTable .  " pr,
                              " . $productsTable . " p
                            where pr.parent_id = p.entity_id
                              and p.type_id = 'configurable'
                        )
                    )
                    or type_id = 'configurable'");

        return $conn->fetchCol($select);
    }
}