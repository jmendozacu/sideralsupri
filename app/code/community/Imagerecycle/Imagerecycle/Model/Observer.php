<?php
class Imagerecycle_Imagerecycle_Model_Observer {

    public function imageuploaded($observer) {
        //$observer contains data passed from when the event was triggered.
        //You can use this data to manipulate the order data before it's saved.
        //Uncomment the line below to log what is contained here:        
        $data = $observer->getEvent()->getData();
        $picture = $data['result'];
        Mage::log($picture);
        if(!$picture['error']) {
            include_once(__DIR__.'/../Helper/Data.php');
            $helper = new Imagerecycle_Imagerecycle_Helper_Data();
            $baseMedia = 'media';
            $return = $helper->optimize( $baseMedia . '/tmp/catalog/product' .$picture['file'], $baseMedia . '/catalog/product' .$picture['file']);
            Mage::log($return);
        }        
    }
    
    public function productsaved($observer) {
         Mage::log('product after saved!');
         $productId = $observer->getProduct()->getId();
        $model = Mage::getModel('catalog/product');
        $_product = $model->load($productId);
 
        include_once(__DIR__.'/../Helper/Data.php');
        $helper = new Imagerecycle_Imagerecycle_Helper_Data();
        $baseMedia = 'media';
         foreach ($_product->getMediaGalleryImages() as $image) {
             $file = $baseMedia . '/catalog/product'. $image['file'];                
             if(!$helper->checkOptimize($file)) {
                  $return = $helper->optimize($file);
                  Mage::log($return);          
             }                         
        }         
         
    }
    
     public function categorySaved($observer) {
         Mage::log('category after saved!');
         
         $categoryId = $observer->getEvent()->getCategory()->getId();
         $model = Mage::getModel('catalog/category');        
         $category = $model->load($categoryId);
         $image = $category->getImage();     
         $thumbnail = $category->getThumbnail();
        
         include_once(__DIR__.'/../Helper/Data.php');
         $helper = new Imagerecycle_Imagerecycle_Helper_Data();                 
         $baseMedia = 'media';
         if($image!='') {
            $file = $baseMedia .'/catalog/category/'. $image;               
            if(!$helper->checkOptimize($file)) {
                     $return = $helper->optimize($file);
                     Mage::log($return);          
            }               
         }
         
         if($thumbnail!='') {
            $file = $baseMedia .'/catalog/category/'. $thumbnail;                
            if(!$helper->checkOptimize($file)) {
                     $return = $helper->optimize($file);
                     Mage::log($return);          
            }               
         }
         
    }
 
}
?>