<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */

class Mageplace_Callforprice_Model_Htmlprocessor_Factory
{

    const MODEL_CLASS_PROCESSOR = 'mageplace_callforprice/htmlprocessor';


    /**
     * @return false|Mageplace_Callforprice_Model_Htmlprocessor_Interface
     * @throws Exception
     */
    public function createProcessor()
    {
        $processorName = $this->_getHelper()->getProcessorName();
        $modelClass = self::MODEL_CLASS_PROCESSOR . '_' . $processorName;
        $model = Mage::getModel($modelClass);
        if($model === false){
            throw new Exception('Undefined processor model');
        }
        return $model;
    }

    /**
     * @return Mageplace_Callforprice_Helper_Abstract
     */
    private function _getHelper()
    {
        return Mage::helper('mageplace_callforprice');
    }

}