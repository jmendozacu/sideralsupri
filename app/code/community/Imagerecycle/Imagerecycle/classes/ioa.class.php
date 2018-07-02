<?php
class ioaphp {
    /**
     * Authentication values array
     * @var array
     */
    protected $auth = array();
    
    /**
     * Image Optimize API Url
     * @var string api url
     */
    protected $apiUrl = 'https://api.imagerecycle.com/v1/';

    /**
     * Last Error message
     * @var string 
     */
    protected $lastError = null;


    /**
     * 
     * @param string $key
     * @param string $secret
     */
    public function __construct($key,$secret){
	$this->auth = array('key'=>$key, 'secret'=>$secret);	
    }
    
    /**
     * Change the API URL
     * @param string $url
     */
    public function setAPIUrl($url){
	$this->apiUrl = $url;
    }
    
    /**
     * Upload a file sent through an html post form
     * @param $_FILES $file posted file
     */
    public function uploadFile($file,$params=array()){
	if(class_exists('CURLFile')){
	    $curlFile = new CURLFile($file);
	}else{
	    $curlFile = '@'.$file;
	}	
     
	$params = array(
	    'auth' => json_encode($this->auth),
	    'file' => $curlFile,
	    'params' => json_encode( $params)			    
	);
        
	try {
	    $result = $this->callAPI($this->apiUrl.'images/','POST',$params);
           
	} catch (Exception $exc) {
	    $this->lastError = $exc->getMessage();
	    return  $this->lastError;// false;
	}
           
	return $result;
    }
    
    /**
     * Upload a file from an url
     * @param string $url
     * @return Object
     */
    public function uploadUrl($url,$params=array()){		
	$params = array(
	    'auth' => json_encode($this->auth),
            'url' => $url,
	    'params' => json_encode($params)		
	);
	try {
	    $result = $this->callAPI($this->apiUrl.'images/','POST',$params);
	} catch (Exception $exc) {
	    $this->lastError = $exc->getMessage();
	    return false;
	}
	return $result;
    }
    
    /**
     * Call the API with curl
     * @param string $url
     * @param string $type HTTP method
     * @param array $datas 
     * @return type
     */
    protected function callAPI($url,$type,$datas){
	$curl = curl_init();	
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 100);
	curl_setopt($curl, CURLOPT_TIMEOUT, 100);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $type);
        //fix windows localhost ssl problem
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
	if($type==='POST'){            
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);            
	}else{
	    $url .= '?'.http_build_query($datas);
	}
             
	curl_setopt($curl, CURLOPT_URL, $url);
	$content = curl_exec($curl);
	$infos = curl_getinfo($curl);
	$infos['http_code'] = (String)$infos['http_code'];
      
	if($infos['http_code'][0]!=='2'){
	    $error = json_decode($content);
	    if(isset($error->errCode)){
		$errCode = $error->errCode;
	    }else{
		$errCode = 0;
	    }
	    if(isset($error->errMessage)){
		$errMessage = $error->errMessage;
	    }else{
		$errMessage = 'An error occurs';
	    }
	    throw new Exception($errMessage,$errCode);
	}
	curl_close($curl);
	return json_decode($content);
    }
    
    /**
     * Get all the images
     * @return type
     */
    public function getImagesList($offset=0, $limit=30){
	$params = array(	  
	    'auth' => json_encode($this->auth),
	    'params' => '',
	    'offset' => $offset,
	    'limit' => $limit
	);
	
	try {
	    $result = $this->callAPI($this->apiUrl.'images/','GET',$params);
	} catch (Exception $exc) {
	    $this->lastError = $exc->getMessage();
	    return false;
	}
	return $result;
    }
    
    /**
     * Get one image
     * @param int $id
     * @return type
     */
    public function getImage($id){
	$params = array(
	    'auth' => json_encode($this->auth),
	    'params' => ''
	);
	
	try {
	    $result = $this->callAPI($this->apiUrl.'images/'.(int)$id,'GET',$params);
	} catch (Exception $exc) {
	    $this->lastError = $exc->getMessage();
	    return false;
	}
	return $result;
    }
    
    /**
     * Delete an image 
     * @param int $id
     * @return type
     */
    public function deleteImage($id){
	$params = array(	
	    'auth' => json_encode($this->auth),
	    'params' => ''
	);
	
	try {
	    $result = $this->callAPI($this->apiUrl.'images/'.(int)$id,'DELETE',$params);
	} catch (Exception $exc) {
	    $this->lastError = $exc->getMessage();
	    return false;
	}
	return $result;
    }
    
    /**
     * Get account information
     * @return type
     */
    public function getAccountInfos(){
	$params = array(
	    'auth' => json_encode($this->auth),
	    'params' => ''
	);
	
	try {
	    $result = $this->callAPI($this->apiUrl.'accounts/mine','GET',$params);
	} catch (Exception $exc) {
	    $this->lastError = $exc->getMessage();
	    return false;
	}
	return $result;
    }
    
    /**
     * Get last error message
     * @return string
     */
    public function getLastError(){
	return $this->lastError;
    }
}
?>