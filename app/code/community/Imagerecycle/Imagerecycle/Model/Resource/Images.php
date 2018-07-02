<?php
Class Imagerecycle_Imagerecycle_Model_Resource_Images extends Mage_Core_Model_Resource_Db_Abstract{
	protected function _construct(){
		$this->_init('imagerecycle/images', 'id');
	}
}