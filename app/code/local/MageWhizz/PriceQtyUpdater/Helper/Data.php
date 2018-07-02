<?php
/**
 * MageWhizz
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://magewhizz.com/magento-extension-LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magewhizz.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *
 * @category   MageWhizz
 * @package    MageWhizz_PriceQtyUpdater
 * @copyright  Copyright (c) 2013 PROTO BALSAS UAB (http://magewhizz.com)
 * @license    http://magewhizz.com/magento-extension-LICENSE.txt
 */
 
class MageWhizz_PriceQtyUpdater_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * Upload product file and import data from it
     *
     * @param Varien_Object $object
     */
	public function uploadAndImport(Varien_Object $object)
	{
		
		if (empty($_FILES['groups']['tmp_name']['priceqtyupdater_config_import']['fields']['import_file']['value'])) {
            return $this;
        }

        $csvFile = $_FILES['groups']['tmp_name']['priceqtyupdater_config_import']['fields']['import_file']['value'];
        
		$io     = new Varien_Io_File();
        $info   = pathinfo($csvFile);
        $io->open(array('path' => $info['dirname']));
        $io->streamOpen($info['basename'], 'r');
		
		// check and skip headers
        $headers = $io->streamReadCsv(";");

        if ($headers === false || count($headers) != 5) {
            $io->streamClose();
            Mage::throwException(Mage::helper('priceqtyupdater')->__('Invalid File Format'));
        }
		
		try {
            $rowNumber  = 1;
            $importData = array();
						
			$count_price = 0;
			$count_qty = 0;
			
			$db = Mage::getSingleton('core/resource')->getConnection('core_read');
						
			while (false !== ($csvLine = $io->streamReadCsv(";"))) {
				
                $rowNumber ++;

                if (empty($csvLine)) {
                    continue;
                }

                if ($csvLine !== false) {
                    					
					$current_price = Mage::getResourceModel('catalog/product')->getAttributeRawValue((int)$csvLine[0], 'price', 0);
					
					if((float)$current_price!=(float)$csvLine[3]){						
						$product = Mage::getModel('catalog/product')
							->load((int)$csvLine[0])
							->setIsMassupdate(true)
						;
						$product->setPrice((float)$csvLine[3])->save();
						$count_price++;
					} 
					
					$stock_table = Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_item');
					
					$current_qty = $db->query("SELECT qty FROM ".$stock_table." WHERE product_id=".(int)$csvLine[0]."")->fetchAll();
					
					if(count($current_qty) && (int)$current_qty[0]['qty']!=(int)$csvLine[4]){
						$qtyObj = Mage::getModel('cataloginventory/stock_item')->loadByProduct((int)$csvLine[0]);
						
						if((int)$csvLine[4] > 0){
							$qtyObj->setQty((int)$csvLine[4])->setData('is_in_stock',1)->save();
						} else{
							$qtyObj->setQty((int)$csvLine[4])->save();
						}
						$count_qty++;
					}
					
				}

            }
			
            $io->streamClose();

			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('%s rows have been processed successfully, %s prices updated, %s products qty updated.',$rowNumber,$count_price,$count_qty)); 
			
        } catch (Mage_Core_Exception $e) {
            $io->streamClose();
            Mage::throwException($e->getMessage());
        } catch (Exception $e) {
            $io->streamClose();
            Mage::logException($e);
            Mage::throwException($this->__('An error occurred while importing.'));
        }
		
	}	
}
