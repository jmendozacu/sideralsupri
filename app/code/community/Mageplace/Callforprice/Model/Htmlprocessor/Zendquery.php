<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Mageplace_Callforprice
 */
class Mageplace_Callforprice_Model_Htmlprocessor_Zendquery
    extends Zend_Dom_Query
    implements Mageplace_Callforprice_Model_Htmlprocessor_Interface
{
    protected $_processorName = 'mageplace_callforprice/htmlprocessor_zendquery';

    protected $_curDomNode;
    protected $_domDocument;

    public function __construct($document = null)
    {
        if (!empty($document)) {
            $this->setDocument($document);
        }
    }

    /**
     * Retrieve DOM document
     *
     * @return DOMDocument
     */
    public function getDomDocument()
    {
        return $this->_domDocument;
    }

    /**
     * Initialize DOM document
     */
    public function initDomDocument()
    {
        if (null === ($document = $this->getDocument())) {
            #require_once 'Zend/Dom/Exception.php';
            throw new Zend_Dom_Exception('Cannot query; no document registered');
        }

        libxml_use_internal_errors(true);

        $this->_domDocument = new DOMDocument;
        switch ($this->getDocumentType()) {
            case self::DOC_XML:
                $success = $this->_domDocument->loadXML($document);
                break;
            case self::DOC_HTML:
            case self::DOC_XHTML:
            default:
                $success = $this->_domDocument->loadHTML($document);
                break;
        }

        $errors = libxml_get_errors();
        if (!empty($errors)) {
            $this->_documentErrors = $errors;
            libxml_clear_errors();
        }

        libxml_use_internal_errors(false);

        if (!$success) {
            #require_once 'Zend/Dom/Exception.php';
            throw new Zend_Dom_Exception(sprintf('Error parsing document (type == %s)', $this->getDocumentType()));
        }

        return $this;
    }

    /**
     * @return DOMNode|null
     */
    public function getCurDomNode()
    {
        return $this->_curDomNode;
    }

    /**
     * @param DOMNode $curDomNode
     * @return $this
     */
    public function setCurDomNode($curDomNode)
    {
        $this->_curDomNode = $curDomNode;
        return $this;
    }

    public function load($html)
    {
        $this->setDocumentHtml(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $this->initDomDocument();
    }


    public function replace($selector, $replacement, $positions = null, $parentSelector = null)
    {
        //$this->_reloadDocument();

        $result = $this->query($parentSelector ? $parentSelector : $selector);
        if (count($result) === 0) {
            return false;
        }

        /**
         * @var int $i
         * @var DOMElement $element
         */
        foreach ($result as $i => $element) {
            if (is_array($positions) && !in_array($i, $positions)) {
                continue;
            }

            $replacementElement = $element->ownerDocument->createDocumentFragment();
            $replacementElement->appendXML($replacement);

            if ($parentSelector) {
                $this->setCurDomNode($element);

                foreach ($this->query($selector) as $oldNode) {
                    $oldNode->parentNode->replaceChild($replacementElement, $oldNode);
                }

                $this->setCurDomNode(null);
            } else {
                $element->parentNode->replaceChild($replacementElement, $element);
            }
        }

        return true;
    }

    public function remove($selector, $positions = null, $parentSelector = null)
    {
        //$this->_reloadDocument();

        $result = $this->query($parentSelector ? $parentSelector : $selector);
        if (count($result) === 0) {
            return false;
        }

        foreach ($result as $i => $element) {
            if (is_array($positions) && !in_array($i, $positions)) {
                continue;
            }

            if ($parentSelector) {
                $this->setCurDomNode($element);

                foreach ($this->query($selector) as $oldNode) {
                    $oldNode->parentNode->removeChild($oldNode);
                }

                $this->setCurDomNode(null);
            } else {
                $element->parentNode->removeChild($element);
            }
        }

        return true;
    }

    public function removeInner($selector)
    {
        return false;
    }


    public function process($processorName, $params)
    {
        $modelClass = $this->_processorName . '_' . $processorName;
        /** @var $model Mageplace_Hideprice_Model_Processor_Interface */
        $model = Mage::getModel($modelClass);
        if ($model === false) {
            throw new Exception('Undefined processor model');
        }

        $model->setHtmlProcessor($this);
        return $model->process($params);
    }

    public function getHtml()
    {
        $fragment = preg_replace('/^<!DOCTYPE.+?>/', '',
            str_replace(array('<html>', '</html>', '<body>', '</body>', '<head>', '</head>'), '', $this->getDomDocument()->saveHTML())
        );

        return $fragment;
    }

    public function queryXpath($xpathQuery, $query = null)
    {
        $domDoc = $this->getDomDocument();

        $nodeList = $this->_getNodeList($domDoc, $xpathQuery);

        return new Zend_Dom_Query_Result($query, $xpathQuery, $domDoc, $nodeList);
    }

    protected function _getNodeList($document, $xpathQuery)
    {
        $xpath = new DOMXPath($document);

        $xpathQuery = (string)$xpathQuery;
        if (preg_match_all('|\[contains\((@[a-z0-9_-]+),\s?\' |i', $xpathQuery, $matches)) {
            foreach ($matches[1] as $attribute) {
                $queryString   = '//*[' . $attribute . ']';
                $attributeName = substr($attribute, 1);
                if (null !== $this->getCurDomNode()) {
                    $nodes = $xpath->query('.' . $queryString, $this->getCurDomNode());
                } else {
                    $nodes = $xpath->query($xpathQuery);
                }
                $nodes = $xpath->query((null !== $this->getCurDomNode() ? '.' : '') . $queryString, $this->getCurDomNode());
                foreach ($nodes as $node) {
                    $attr        = $node->attributes->getNamedItem($attributeName);
                    $attr->value = ' ' . $attr->value . ' ';
                }
            }
        }

        if (null !== $this->getCurDomNode()) {
            return $xpath->query('.' . $xpathQuery, $this->getCurDomNode());
        }
        // if($_SERVER['REMOTE_ADDR'] == '46.216.53.0') {var_dump($xpath->query($xpathQuery); die;}
        return $xpath->query($xpathQuery);
    }

    protected function _reloadDocument()
    {
        $this->initDomDocument();
        $this->load($this->getHtml());

        return $this;
    }
}
