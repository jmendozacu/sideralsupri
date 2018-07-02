<?php
class MageWhizz_PriceQtyUpdater_Adminhtml_DataController extends Mage_Adminhtml_Controller_Action
{
	public function exportAction()
    {
    	iconv_set_encoding("internal_encoding", "UTF-8");
		    
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"product_price_and_qty_".date('Y-m-d_H:i:s').".csv\";" );
		header("Content-Transfer-Encoding: binary"); 

		$_productCollection = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSelect('id')
			->addAttributeToSelect('sku')
			->addAttributeToSelect('name')
			->addAttributeToSelect('price')
			->addAttributeToSelect('qty')
		;
	
		$output = fopen('php://output', 'w');
	
		fputcsv($output, array('id','sku','name','price','qty'),",");

		foreach ($_productCollection as $product) {

			fputcsv($output, array(
				$product->getId(),
				$product->getSku(),
				$product->getName(),
				$product->getPrice(),
				(int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty()
			),",");
	
		}

		fclose($output);

    }
}
