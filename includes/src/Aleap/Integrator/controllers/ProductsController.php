<?php
require_once(dirname(__FILE__) . "/../Model/Product.php");

class Aleap_Integrator_ProductsController extends Mage_Core_Controller_Front_Action {
  public function indexAction() {
    /** @var $product Aleap_Integrator_Model_Product */
    $product  = new Aleap_Integrator_Model_Product();
    $page     = (int) $this->getRequest()->getParam('page', '1');
    $per_page = (int) $this->getRequest()->getParam('per_page', '200');
    $storeId = (int) $this->getRequest()->getParam('storeId');
    $products = $product->fetchAll($page, $per_page, $storeId);
    $totalCount = $product->totalCount($storeId);
    $result   = array();

    foreach ($products as $product) {
      $result[] = $product->getData();
    }

    $json = json_encode($result);
    $this->getResponse()->setHeader('Content-type', 'application/json');
    $this->getResponse()->setHeader('X-Total-Count', $totalCount);
    $this->getResponse()->setBody($json);
  }

  public function showAction() {
      $id = (int) $this->getRequest()->getParam('id');
      $product  = Mage::getModel('aleap/product')->load($id);
      $json = json_encode($product->getData());
      $this->getResponse()->setHeader('Content-type', 'application/json');
      $this->getResponse()->setBody($json);
  }
}
