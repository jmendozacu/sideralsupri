<?php
class EvoPin_AutoRelatedProducts_Block_Adminhtml_Catalog_Product_Edit_Tab_Related extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Related
{
	/**
	 * Retrieve related products
	 *
	 * @return array
	 */
	public function getSelectedRelatedProducts()
	{
		$_enabled = Mage::getStoreConfig('autorelatedproducts/general/enabled');
		if(!$_enabled)
			return parent::getSelectedRelatedProducts();
		
		$_backend_enabled = Mage::getStoreConfig('autorelatedproducts/general/backend_enabled');
		if(!$_backend_enabled)
			return parent::getSelectedRelatedProducts();
		
		$products = array();
		foreach (Mage::registry('current_product')->getRelatedProducts() as $product) {
			$products[$product->getId()] = array('position' => $product->getPosition());
		}
		
		$product = Mage::registry('current_product');
		
		if ($category = Mage::registry('current_category')) {
		
		} elseif ($product) {
			$ids = $product->getCategoryIds();
		
			if (!empty($ids)) {
				$category = Mage::getModel('catalog/category')->load($ids[0]);
			}
		}
		
		if ($category) {
		
			$related_products = Mage::getResourceModel('reports/product_collection')
			->addAttributeToFilter('visibility', array(
					Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
					Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
			))
			->addAttributeToFilter('status', 1)
			->addCategoryFilter($category)
			->addAttributeToSelect('*');
			
			if ($product) {
				$related_products->addAttributeToFilter('entity_id', array(
						'neq' => Mage::registry('current_product')->getId())
				);
			}
		
			Mage::getModel('cataloginventory/stock')->addInStockFilterToCollection($related_products);
			
			$related_products_ids = array();
			foreach ($related_products as $product) {
				$related_products_ids[$product->getId()] = array('position' => $product->getPosition());
			}
			
			return $products + $related_products_ids;
		
		} else {
			return $products;
		}

	}
	
}