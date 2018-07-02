<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */

interface Mageplace_Callforprice_Model_Processor_Interface
{

    /**
     * @param $params
     * @return mixed
     */
    public function process($params);

    public function setHtmlProcessor($processor);

}