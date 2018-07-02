<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */

interface Mageplace_Callforprice_Model_Htmlprocessor_Interface
{

    /**
     * Load html to processing
     * @param $html
     * @return mixed
     */
    public function load($html);

    /**
     * Replace finded element to $replacement
     * @param $selector
     * @param $replacement
     * @return mixed
     */
    public function replace($selector, $replacement);

    /**
     * Remove finded element
     * @param $selector
     * @return mixed
     */
    public function remove($selector);

    /**
     * Remove inner elements in finded elem
     * @param $selector
     * @return mixed
     */
    public function removeInner($selector);

    /**
     * Execute custom processor
     * @param string $processorName
     * @param array $params
     * @return mixed
     */
    public function process($processorName, $params);

    /**
     * Custom Query
     * @param $selector
     * @return mixed
     */
    public function query($selector);

    /**
     * Return processed html
     * @return string
     */
    public function getHtml();

}