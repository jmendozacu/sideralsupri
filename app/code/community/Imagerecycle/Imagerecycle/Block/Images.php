<?php

Class Imagerecycle_Imagerecycle_Block_Images extends Mage_Page_Block_Html {

    private $allowed_ext = array('jpg', 'jpeg', 'png', 'gif','pdf');
    private $ignoredPath = array('app', 'var', 'cache', 'adminhtml', '.', '..');
    protected $totalImages = 0;
    protected $totalFile = 0;
    protected $time_elapsed_secs = 0;
    protected $totalOptimizedImages = 0;
    protected $limit = 30;
    protected $page_show = 10;
    protected $order_by = '';
    protected $order_dir = 'asc';

    public function __construct() {
        parent::__construct();
        //Include ioaphp class once
        include_once(Mage::getModuleDir('', 'Imagerecycle_Imagerecycle') . '/classes/ioa.class.php');
        //Get settings
        $this->settings = array(            
            'api_key' => Mage::getStoreConfig('mageio_api_key'),
            'api_secret' => Mage::getStoreConfig('mageio_api_secret'),            
            'installed_time'  => Mage::getStoreConfig('mageio_installed_time'),
            'exclude_folders'  => Mage::getStoreConfig('mageio_exclude_folders'),                        
            'resize_auto'  => Mage::getStoreConfig('mageio_resize_auto'),    
            'resize_image'  => Mage::getStoreConfig('mageio_resize_image'),    
            'min_size'  => Mage::getStoreConfig('mageio_min_size'),    
            'max_size'  => Mage::getStoreConfig('mageio_max_size'),    
            'compression_type_pdf'  => Mage::getStoreConfig('mageio_compression_type_pdf'),    
            'compression_type_png'  => Mage::getStoreConfig('mageio_compression_type_png'),    
            'compression_type_jpg'  => Mage::getStoreConfig('mageio_compression_type_jpg'),    
            'compression_type_gif'  => Mage::getStoreConfig('mageio_compression_type_gif'),    
            'compression_type'  => Mage::getStoreConfig('mageio_compression_type'),
        );
        
        if(empty($this->settings['compression_type']) ) { $this->settings['compression_type'] = 'lossy'; }        
        if (!empty($this->settings['exclude_folders'])) {
            $this->ignoredPath = explode(',',$this->settings['exclude_folders']);          
            array_unshift($this->ignoredPath, '.');
            array_unshift($this->ignoredPath, './');
        }else {            
            $this->settings['exclude_folders'] = "app,var,cache,adminhtml";
        }
        
        for($i=0;$i<count($this->allowed_ext); $i++) {
            $compression_type = $this->settings['compression_type_'.$this->allowed_ext[$i]];
            if($compression_type=="none") {
                unset($this->allowed_ext[$i]);
            }
        }
        $this->allowed_ext = array_values($this->allowed_ext);
        
        //Get current page
        $requestParams = $this->getRequest()->getParams();
        $page = !empty($requestParams['page']) ? abs((int) $requestParams['page']) : 1;
        $this->page = $page;
    }

    public function getImages() {

        $images = $this->_getLocalImages();
        $numberOfPage = $this->totalImages / $this->limit;
        $totalPages = is_int($numberOfPage) ? $numberOfPage : (floor($numberOfPage) + 1);
        if ($this->page > $totalPages)
            $this->page = $totalPages;
        $this->pagination = $this->buildPager($this->totalImages);

        $images = $this->prepareLocalImages($images);

        return $images;
    }

    public function prepareLocalImages($images) {
        //process data before display                   
	$requestParams = $this->getRequest()->getParams();
        $filter_fields = array('filename','size', 'status');     
        if( !empty($requestParams['order_by']) && in_array($requestParams['order_by'], $filter_fields)) {
            $order_by = $requestParams['order_by'];
            if(isset($requestParams['dir']) && $requestParams['dir']=='desc') { 
                $this->order_dir = 'desc';
            }else {
                $this->order_dir = 'asc';
            }
        } else {
            $order_by = '' ;//default - image path
        }
        $this->order_by = $order_by;
      
        if($order_by == 'size') {           
            usort($images, array("Imagerecycle_Imagerecycle_Block_Images","cmpSize") );            
        }else if($order_by == 'status') {
            usort($images, array("Imagerecycle_Imagerecycle_Block_Images","cmpStatus"));            
        }else if($order_by == 'filename' && $this->order_dir == 'desc') {
            usort($images, array("Imagerecycle_Imagerecycle_Block_Images","cmpNameDesc"));   
        }   
     
        $start = ($this->page - 1) * $this->limit;
        $result = array_slice($images, $start, $this->limit);
        
        return $result;
    }
    
    public function _getLocalImages() {

        $optimizedFiles = array();
        $resourceR = $this->getCoreRead('imagerecycle/images');
        $sql = "SELECT file,size_before FROM `{$resourceR->tableName}`";
        $optimizedFiles = $resourceR->fetchAssoc($sql);       

        $this->totalOptimizedImages = count($optimizedFiles);
        $min_size = isset($this->settings['min_size']) ? (int)$this->settings['min_size']*1024 : 1* 1024;
        $max_size =  !empty($this->settings['max_size']) ? (int)$this->settings['max_size'] * 1024 : 10000 * 1024; 
        $images = array();
        
        $base_dir = Mage::getBaseDir();
        // Set a flag to prevent scanning over and over again by request
        Mage::getSingleton('core/session')->setData('scanning_local_images', true);
        $start = microtime(true);
        clearstatcache(); $counter = 0;        
        foreach (new RecursiveIteratorIterator(new IgnorantRecursiveDirectoryIterator($base_dir)) as $filename) {
            $continue = false;
            $this->totalFile++;
           // if( $counter > 100 ) break;
            foreach ($this->ignoredPath as $ignore_path) {
                $ignore_path = DIRECTORY_SEPARATOR . $ignore_path . DIRECTORY_SEPARATOR;
                if (strpos(substr($filename, strlen($base_dir)), $ignore_path) === 0 || strpos(substr($filename, strlen($base_dir)), DIRECTORY_SEPARATOR . 'adminhtml' . DIRECTORY_SEPARATOR) !== FALSE) {
                    $continue = true;
                    continue;
                }
            }
            if ($continue === true) {
                continue;
            }
             
            if (!in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), $this->allowed_ext)) {
                continue;
            }
          
            if(filesize($filename) < $min_size  || filesize($filename) > $max_size ) {
                  continue;
            }
            $data = array();
            $data['filename'] = str_replace('\\', '/', substr($filename, strlen($base_dir) + 1));          
            $data['size'] = filesize($filename);
            $data['filetype'] = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (isset($optimizedFiles[$data['filename']]) ) {
                $data['optimized'] = true;
                $data['optimized_datas'] = $optimizedFiles[$data['filename']];
            } else {
                $data['optimized'] = false;
            }
            $this->totalImages++;

            $images[] = $data; $counter++;
        }
        $this->time_elapsed_secs = microtime(true) - $start;
        //Unset image scanning in local flag
        Mage::getSingleton('core/session')->unsetData('scanning_local_images');

        return $images;
    }
    
    private function cmpStatus($a, $b)
    {
        if ($a['optimized'] == $b['optimized']) {
            return strcmp($a['filename'], $b['filename']);
        }
        
         if( $this->order_dir=='asc') {            
              return strcmp($a['optimized'], $b['optimized']);
         }else {
              return strcmp($b['optimized'], $a['optimized']);
         }              
    }
    
   private  function cmpSize($a, $b)
    {        
        if ($a['size'] == $b['size']) {
            return strcmp($a['filename'], $b['filename']);
        }
        if( $this->order_dir=='asc') {
            return ($a['size'] < $b['size']) ? -1 : 1;
        }else {
            return ($a['size'] < $b['size']) ? 1 : -1;
        }        
    }
     private  function cmpNameDesc($a, $b)
    {               
        return strcmp($b['filename'], $a['filename']);      
    }

    public function getTotalImages() {
        return $this->totalImages;
    }

    public function getTotalOptimizedImages() {
        return $this->totalOptimizedImages;
    }

    protected function buildPager($total) {
        $pagination = new stdClass();

        $numb = $total / $this->limit;
        if ($numb > 1) {
            if (!is_float($numb)) {
                $total_page = $numb;
            } else {
                $total_page = ceil($numb);
            }
        } else {
            $total_page = 1;
        }

        if ($total_page > $this->page_show) {
            $t_floor = floor($total_page / $this->page_show) + 1;
            $p_floor = floor($this->page / $this->page_show) + ($this->page % $this->page_show == 0 ? 0 : 1);

            if ($p_floor < $t_floor) {
                $pagination->range = range($p_floor * $this->page_show - ($this->page_show - 1), $p_floor * $this->page_show);
            } elseif ($p_floor = $t_floor) {
                $pagination->range = range(($p_floor - 1) * $this->page_show, $total_page);
            }
        } else {
            $pagination->range = range(1, $total_page);
        }

        $pagination->start = 1;
        $pagination->prev = $this->page > 1 ? $this->page - 1 : 1;
        $pagination->next = $this->page < $total_page ? $this->page + 1 : $total_page;
        $pagination->end = $total_page;
        $pagination->current = $this->page;

        #debug($pagination);
        return (int) $total_page > 1 ? $pagination : 0;
    }
   
    public function getCoreRead($entityId) {
        $resource = Mage::getSingleton('core/resource');
        $resourceR = $resource->getConnection('core_read');
        $tableName = $resource->getTableName($entityId);
        $resourceR->tableName = $tableName;
        return $resourceR;
    }

    public function getCoreWrite($entityId) {
        $resource = Mage::getSingleton('core/resource');
        $resourceW = $resource->getConnection('core_write');
        $tableName = $resource->getTableName($entityId);
        $resourceW->tableName = $tableName;
        return $resourceW;
    }

    public function getDataIgnore($key, $default = null) {
        return isset($this->{$key}) ? $this->{$key} : $default;
    }

    protected function trimToLower(&$string) {
        return strtolower(trim($string));
    }

}

class IgnorantRecursiveDirectoryIterator extends RecursiveDirectoryIterator {

    function getChildren() {
        try {
            return new IgnorantRecursiveDirectoryIterator($this->getPathname());
        } catch (UnexpectedValueException $e) {
            return new RecursiveArrayIterator(array());
        }
    }

}
