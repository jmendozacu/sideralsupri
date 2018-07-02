<?php
class Magestore_BannerSlider_Block_BannerSlider extends Mage_Core_Block_Template
{
	private $_display = '0';
	
	public function _prepareLayout()	{
		return parent::_prepareLayout();
	}
    
	public function getBannerSlider() { 
		if (!$this->hasData('bannerslider')) {
			$this->setData('bannerslider', Mage::registry('bannerslider'));
		}
		return $this->getData('bannerslider');			
	}
	
	public function setDisplay($display){
		$this->_display = $display;
	}
	
	public function getBannerCollection() {
		$collection = Mage::getModel('bannerslider/bannerslider')->getCollection()
			->addFieldToFilter('status',1)
			->addFieldToFilter('is_home',$this->_display);
		if ($this->_display == Magestore_Bannerslider_Helper_Data::DISP_CATEGORY){
			$current_category = Mage::registry('current_category')->getId();
			$collection->addFieldToFilter('categories',array('finset' => $current_category));
		}
		
		$current_store = Mage::app()->getStore()->getId();
		$banners = array();
		foreach ($collection as $banner) {
			$stores = explode(',',$banner->getStores());
			if (in_array(0,$stores) || in_array($current_store,$stores))
			//if ($banner->getStatus())
				$banners[] = $banner;
		}
		rsort($banners);//inverte a ordem dos banner para descrescente - ADAPTIVE SHOP
		return $banners;
	}
	
	public function getDelayTime() {
		$delay = (int) Mage::getStoreConfig('bannerslider/settings/time_delay');
		$delay = $delay * 1000;
		return $delay;
	}
	
	public function isShowDescription(){
		return (int)Mage::getStoreConfig('bannerslider/settings/show_description');
	}
	
	public function getListStyle(){
		return (int)Mage::getStoreConfig('bannerslider/settings/list_style');
	}
	
	public function getImageWidth() {
		return (int)Mage::getStoreConfig('bannerslider/settings/image_width');
	}
	
	public function getImageHeight() {
		return (int)Mage::getStoreConfig('bannerslider/settings/image_height');
	}
}