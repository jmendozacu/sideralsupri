<?php

Class Imagerecycle_Imagerecycle_IndexController extends Mage_Adminhtml_Controller_Action {

    public function indexAction() {
        $this->_title($this->__('Image Recycle'));
        $this->_initAction()->renderLayout();
        return $this;
    }

    /**
     * Initialize action
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction() {

        $this->loadLayout()->_setActiveMenu('imagerecycle');

        return $this;
    }

    public function saveConfigAction() {

        $response = new stdClass();
        $response->success = false;
        $configKey = array('api_key', 'api_secret','exclude_folders','resize_auto',
            'resize_image','min_size','max_size','compression_type_pdf','compression_type_png','compression_type_jpg','compression_type_gif');
        $coreConfig = Mage::getConfig();
        $post = $this->getRequest()->getPost();
       // var_dump($post);
        foreach ($configKey as $key) {            
            if (isset($post[$key])) {                
                $coreConfig->saveConfig("mageio_".$key, Mage::helper('core')->escapeHtml($post[$key]));
                $response->success = true;
            }
        }
		$installed_time =  Mage::getStoreConfig('mageio_installed_time');               
        if(empty($installed_time)) {
            $installed_time = time();
            $coreConfig = Mage::getConfig();
            $coreConfig->saveConfig('mageio_installed_time', $installed_time);                 
        }
		
		$cache = Mage::getSingleton('core/cache');
        $cache->flush();
        $response->msg = "All modifications were saved!";
        exit(json_encode($response));
    }

    public function revertAction() {
        $requestParams = $this->getRequest()->getParams();
        $image = isset($requestParams['image']) ? $requestParams['image'] : "";
        $return_params = array();
        if (isset($requestParams['page'])) {
            $return_params['page'] = $requestParams['page'];
        }

        //Include ioaphp class once
        include_once(Mage::getModuleDir('', 'Imagerecycle_Imagerecycle') . '/classes/ioa.class.php');

        $this->blockImages = $this->getLayout()->createBlock('imagerecycle/images');
        $returned = $this->_revert($image);
        $this->ajaxReponse($returned->status, $returned);        
        
    }

    protected function _revert($image) {

        $response = new stdClass();
        $response->status = false;
        $response->msg = Mage::helper('imagerecycle')->__('Not be reverted yet');

        $resourceR = $this->blockImages->getCoreRead('imagerecycle/images');
        $resourceW = $this->blockImages->getCoreWrite('imagerecycle/images');

        $api_id = $resourceR->fetchOne("SELECT api_id FROM {$resourceR->tableName}  WHERE `file` = " . $resourceR->quote($image));
        if ($api_id) {

            $ioa = new ioaphp($this->blockImages->settings['api_key'], $this->blockImages->settings['api_secret']);            
            $return = $ioa->getImage($api_id);

            if (!isset($return->id)) {
                $response->msg = Mage::helper('imagerecycle')->__('api id is not correct');
                return $response;
            }

            $fileContent = file_get_contents($return->origin_url);
            if ($fileContent === false) {
                $response->msg = Mage::helper('imagerecycle')->__('Image not found');
                return $response;
            }

            $file = realpath($image);
            if (file_put_contents($file, $fileContent) === false) {
                $response->msg = Mage::helper('imagerecycle')->__("Can't write file");
                return $response;
            }
            clearstatcache();
            $size_after = filesize($file);

            $where = "api_id = " . $api_id;
            $result = $resourceW->delete($resourceW->tableName, $where);
            if ($result === false) {
                $response->msg = Mage::helper('imagerecycle')->__("Can't delete db record");
                return $response;
            }

            $response->newSize = number_format($size_after/1000, 2, '.', '') ;
            $response->status = true;
            $response->msg = Mage::helper('imagerecycle')->__('Reverted');
        }
        return $response;
    }

    public function optimizeAction() {
        $requestParams = $this->getRequest()->getParams();
        $image = isset($requestParams['image']) ? $requestParams['image'] : '';
        $return_params = array();
        if (isset($requestParams['page'])) {
            $return_params['page'] = $requestParams['page'];
        }

        //Include ioaphp class once
        include_once(Mage::getModuleDir('', 'Imagerecycle_Imagerecycle') . '/classes/ioa.class.php');
        $this->blockImages = $this->getLayout()->createBlock('imagerecycle/images');
        
        include_once(__DIR__.'/../Helper/Data.php');
        $helper = new Imagerecycle_Imagerecycle_Helper_Data();
        $returned = $helper->optimize($image);
        $this->ajaxReponse($returned->status, $returned);
    }

    /**
     * Be careful of this action if site has a very big amount of images
     */
    protected function optimizeAllAction() {
        $steps = 2;
        $this->blockImages = $this->getLayout()->createBlock('imagerecycle/images');
        $images = $this->blockImages->_getLocalImages();  
        include_once(__DIR__.'/../Helper/Data.php');
        $helper = new Imagerecycle_Imagerecycle_Helper_Data();
        
        foreach ($images as $image) {
            if ($image['optimized'] === false) {
                if ($steps === 0) {
                    $this->ajaxReponse(true, array('continue' => true, 'totalImages' => $this->blockImages->getTotalImages(), 'totalOptimizedImages' => $this->blockImages->getTotalOptimizedImages()));
                }
                
                $returned = $helper->optimize($image['filename']);                
                if ($returned === false) {
                    $this->ajaxReponse(false);
                }
                $steps--;
            }
        }
    }      

    protected function ajaxReponse($status, $datas = null) {
        $response = array('status' => $status, 'datas' => $datas);
        echo json_encode($response);
        die();
    }
   

}
