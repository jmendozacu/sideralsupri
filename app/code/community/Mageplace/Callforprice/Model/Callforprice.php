<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */

class Mageplace_Callforprice_Model_Callforprice extends Mage_Core_Model_Abstract
{

    const FIELD_CATEGORY_ID     = 'id_cat';
    const FIELD_PRODUCT_ID      = 'id_prod';
    const FIELD_IS_CALLFORPRICE = 'callforprice';
    const FIELD_CUSTOMER_GROUPS = 'customer_groups';
    const FIELD_CUSTOMER_IDS    = 'customer_ids';
    const FIELD_STORE_IDS       = 'store_ids';

    protected function _construct() 
	{
        $this->_init("mageplace_callforprice/callforprice");
    }

    /**
     * Load model by category id
     * @param $categoryId
     * @return Mage_Core_Model_Abstract
     */
    public function loadByCategoryId($categoryId)
    {
        return $this->load($categoryId, self::FIELD_CATEGORY_ID);
    }

    /**
     * Load model by product id
     * @param $productId
     * @return Mage_Core_Model_Abstract
     */
    public function loadByProductId($productId)
    {
        return $this->load($productId, self::FIELD_PRODUCT_ID);
    }

    /**
     * Is call for price enabled
     * @return bool|null
     */
    public function isCallforprice()
    {
        return $this->getData(self::FIELD_IS_CALLFORPRICE);
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $isEnabled = $this->getData(self::FIELD_IS_CALLFORPRICE);
        $this->setData(self::FIELD_IS_CALLFORPRICE, self::boolToStr($isEnabled));

        $customerGroups = $this->getData(self::FIELD_CUSTOMER_GROUPS);
        $this->setData(self::FIELD_CUSTOMER_GROUPS, implode(',', $customerGroups));

        $customerIds = $this->getData(self::FIELD_CUSTOMER_IDS);
        $this->setData(self::FIELD_CUSTOMER_IDS, implode(',', $customerIds));

        $storeIds = $this->getData(self::FIELD_STORE_IDS);
        $this->setData(self::FIELD_STORE_IDS, implode(',', $storeIds));

        return parent::_beforeSave();
    }

    /**
     * @return $this|Mage_Core_Model_Abstract
     */
    protected function _afterLoad()
    {
        $isEnabled = $this->getData(self::FIELD_IS_CALLFORPRICE);
        $this->setData(self::FIELD_IS_CALLFORPRICE, self::strToBool($isEnabled));

        $customerGroups = $this->getData(self::FIELD_CUSTOMER_GROUPS);
        if(!is_string($customerGroups) || empty($customerGroups)){
            $this->setData(self::FIELD_CUSTOMER_GROUPS, array());
        } else {
            $this->setData(self::FIELD_CUSTOMER_GROUPS, explode(',', $customerGroups));
        }

        $customerIds = $this->getData(self::FIELD_CUSTOMER_IDS);
        if(!is_string($customerIds) || empty($customerIds)){
            $this->setData(self::FIELD_CUSTOMER_IDS, array());
        } else {
            $this->setData(self::FIELD_CUSTOMER_IDS, explode(',', $customerIds));
        }

        $storeIds = $this->getData(self::FIELD_STORE_IDS);
        if(!is_string($storeIds) || empty($storeIds)){
            $this->setData(self::FIELD_STORE_IDS, array());
        } else {
            $this->setData(self::FIELD_STORE_IDS, explode(',', $storeIds));
        }

        return parent::_afterLoad();
    }

    /**
     * String to bool
     * [For backward compatibility with prev versions]
     * @param $str
     * @deprecated
     * @return bool
     */
    public static function strToBool($str)
    {
        return strpos($str, "on") > -1;
    }

    /**
     * Bool to str
     * [For backward compatibility with prev versions]
     * @param $bool
     * @deprecated
     * @return null|string
     */
    public static function boolToStr($bool)
    {
        return $bool ? 'on' : null;
    }
}