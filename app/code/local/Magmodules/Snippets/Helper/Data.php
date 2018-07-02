<?php
/**
 * Magmodules.eu - http://www.magmodules.eu - info@magmodules.eu
 * =============================================================
 * NOTICE OF LICENSE [Single domain license]
 * This source file is subject to the EULA that is
 * available through the world-wide-web at:
 * http://www.magmodules.eu/license-agreement/
 * =============================================================
 * @category    Magmodules
 * @package     Magmodules_Snippets
 * @author      Magmodules <info@magmodules.eu>
 * @copyright   Copyright (c) 2015 (http://www.magmodules.eu)
 * @license     http://www.magmodules.eu/license-agreement/  
 * =============================================================
 */
 
class Magmodules_Snippets_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getProduct() {
        $product = Mage::registry('current_product');
        return ($product && $product->getEntityId()) ? $product : false;
    }

	public function getCategory() {
        $category = Mage::registry('current_category');
        return ($category && $category->getEntityId() && !Mage::registry('current_product')) ? $category : false;
    }

	public function getSnippetsEnabled($type = 'product') {
		$extension = Mage::getStoreConfig('snippets/general/enabled');
		
		if($type == 'product') {
			$enabled = Mage::getStoreConfig('snippets/products/enabled');
		} else {
			$enabled = Mage::getStoreConfig('snippets/category/enabled');		
		}
		
		if($extension && $enabled) {	
			return true;
		} else {
			return false;
		}	
	}

	// PRODUCT SNIPPETS	        
    public function getProductSnippets() {
		if($product = $this->getProduct()) {		
			$snippets = array();
			$snippets['name'] = $product->getName();

			if($description = $this->_getProductDescription()) 
			$snippets['description'] = $description;
						
			if($thumbnail = $this->_getProductThumbnail())
			$snippets['thumbnail'] = $thumbnail;
					
			$snippets['offers'] = $this->_getProductOffers();
			$snippets['availability'] = $this->_getAvailability();
			$snippets['condition'] = $this->_getCondition();

			$snippets['rating'] = $this->_getProductRatings();
			$snippets['extra'] = $this->_getProductExtraFields();
			
			if(($snippets['offers']['price_only'] < 0.01) && (Mage::getStoreConfig('snippets/products/hide_noprice'))) {		
				return false;
			} else {				
				return $snippets; 
			}	
		} 		
    }
    
    public function getJsonProductSnippets() {			
		if($product = $this->getProductSnippets()) {	
			$snippets = array();
			if(Mage::getStoreConfig('snippets/products/type') == 'json') {					
			
				$snippets['@context'] = 'http://schema.org';
				$snippets['@type'] = 'Product';
				$snippets['name'] = $product['name'];
				
				if(isset($product['description']))
					$snippets['description'] = $product['description'];

				$snippets['image'] = $this->_getProductImage();
				$snippets['offers'] = '';				
				$snippets['offers']['@type'] =  $product['offers']['type'];

				if(isset($product['availability'])) {
					$snippets['offers']['availability'] = $product['availability']['url'];
				}
				
				if(isset($product['offers']['price_low'])) {
					$snippets['offers']['lowprice'] = $product['offers']['clean_low'];
				} else {
					$snippets['offers']['price'] = $product['offers']['clean'];
				}
				
				$snippets['offers']['priceCurrency'] = $product['offers']['currency'];

				if(isset($product['condition']))
					$snippets['offers']['itemCondition'] = $product['condition']['url'];						

				if(isset($product['offers']['extra_offer'])) {
					$offers = array();
					$offers[] = $snippets['offers'];
					foreach($product['offers']['extra_offer'] as $extra_offer) {
						if($extra_offer['currency'] != $snippets['offers']['priceCurrency']) {
							$offers_extra['@type'] =  $product['offers']['type'];
							$offers_extra['availability'] = $product['availability']['url'];
							$offers_extra['price'] = $extra_offer['price'];
							$offers_extra['priceCurrency'] = $extra_offer['currency'];
							$offers[] = $offers_extra;				
						}
					}
					$snippets['offers'] = $offers;
				}
											
				if((isset($product['rating']['count'])) && ($product['rating']['percentage'] > 0)) {
					$snippets['aggregateRating'] = '';
					$snippets['aggregateRating']['@type'] = 'AggregateRating';
					$snippets['aggregateRating']['ratingValue'] = $product['rating']['avg'];
					$snippets['aggregateRating']['bestRating'] = $product['rating']['best'];
					$snippets['aggregateRating'][$product['rating']['type']] = $product['rating']['count'];
				}	
				
				if($extrafields = $product['extra']) {
					foreach($extrafields as $field) { 
						$snippets[$field['itemprop']] = $field['clean'];					
					}
				}				
				return $snippets;
			} 
		}					
	}
	
	// CATEGORY SNIPPETS	        
    public function getCategorySnippets() {			

		if($category = $this->getCategory()) {		

			$snippets = array();
			$snippets['name'] = $category->getName();

			if($description = $this->_getCategoryDescription())
			$snippets['description'] = $description;
				
			if($thumbnail = $this->_getCategoryThumbnail())
			$snippets['thumbnail'] = $thumbnail;
		
			$snippets['offers'] = $this->_getCategoryOffers();
			$snippets['availability']['url'] = 'http://schema.org/InStock';
			$snippets['availability']['text'] = Mage::helper('snippets')->__('In stock');
			$snippets['rating'] = $this->_getCategoryRatings();
			
			if(($snippets['offers']['clean_low'] < 0.01) && (Mage::getStoreConfig('snippets/category/noprice'))) {		
				return false;
			} else {
				return $snippets; 			
			}
		} 		
		return false;
    }

    public function getJsonCategorySnippets() {			
		if($category = $this->getCategorySnippets()) {	
			$snippets = array();
			if(Mage::getStoreConfig('snippets/category/type') == 'json') {					
				$snippets['@context'] = 'http://schema.org';
				$snippets['@type'] = 'Product';
				$snippets['name'] = $category['name'];
				
				if(isset($category['description']))
					$snippets['description'] = $category['description'];

				if(isset($category['thumbnail']))
					$snippets['image'] = $category['thumbnail'];

				if(isset($category['offers']['price_low'])) {
					$snippets['offers'] = '';
					$snippets['offers']['@type'] = 'AggregateOffer';

					if(isset($category['availability'])) {
						$snippets['offers']['availability'] = 'http://schema.org/InStock';
					}
					
					if(isset($category['offers']['price_high'])) {
						$snippets['offers']['lowprice'] = $category['offers']['clean_low'];
						$snippets['offers']['highprice'] = $category['offers']['clean_high'];						
					} else {
						$snippets['offers']['lowprice'] = $category['offers']['clean_low'];
					}
					
					$snippets['offers']['priceCurrency'] = $category['offers']['currency'];
				}	

				if((isset($category['rating']['count'])) && ($category['rating']['percentage'] > 0)) {
					$snippets['aggregateRating'] = '';
					$snippets['aggregateRating']['@type'] = 'AggregateRating';
					$snippets['aggregateRating']['ratingValue'] = $category['rating']['avg'];
					$snippets['aggregateRating']['bestRating'] = $category['rating']['best'];
					$snippets['aggregateRating'][$category['rating']['type']] = $category['rating']['count'];
				}	
				
    			if(($category['offers']['qty'] < 1) && (Mage::getStoreConfig('snippets/category/noprice'))) {		
					return false;
				} else {
					return $snippets;
				}
			} 
		}					
		return false;
    }
    
	// ORGANIZATION SNIPPETS	        
    public function getOrganizationSnippets() {			
		$snippets = array();
		if(Mage::getStoreConfig('snippets/system/organization')) {		
			$snippets['@context'] = 'http://schema.org';
			$snippets['@type'] = 'Organization';
			$snippets['address']['@type'] = 'PostalAddress';
			$snippets['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);			
											
			if($locality = Mage::getStoreConfig('snippets/system/locality'))
				$snippets['address']['addressLocality'] = $locality; 				

			if($postalcode = Mage::getStoreConfig('snippets/system/postalcode'))
				$snippets['address']['postalCode'] = $postalcode; 				
				
			if($street = Mage::getStoreConfig('snippets/system/street'))
				$snippets['address']['streetAddress'] = $street; 				

			if($email = Mage::getStoreConfig('snippets/system/email'))
				$snippets['email'] = $email; 									

			if($name = Mage::getStoreConfig('snippets/system/name'))
				$snippets['name'] = $name; 				

			if($logo_url = Mage::getStoreConfig('snippets/system/logo_url'))
				$snippets['logo'] = $logo_url; 				

			if($telephone = Mage::getStoreConfig('snippets/system/telephone'))
				$snippets['telephone'] = $telephone;
			
			// SAME AS, SOCIAL LINKS
			$social_links = array();
			
			// Twitter Link
			$twitter = Mage::getStoreConfig('snippets/system/twitter');
			$twitter_username = Mage::getStoreConfig('snippets/system/twitter_username');
			if($twitter && $twitter_username) {
				$twitter_url = 'https://twitter.com/' . $twitter_username;
				$social_links[] = $twitter_url;
			}

			// Facebook Link			
			$facebook = Mage::getStoreConfig('snippets/system/facebook');
			$facebook_username = Mage::getStoreConfig('snippets/system/facebook_username');
			if($facebook && $facebook_username) {
				$facebook_url = 'https://www.facebook.com/' . $facebook_username;
				$social_links[] = $facebook_url;
			}

			// Linkedin Link			
			$linkedin = Mage::getStoreConfig('snippets/system/linkedin');
			$linkedin_username = Mage::getStoreConfig('snippets/system/linkedin_username');
			if($linkedin && $linkedin_username) {
				$linkedin_url = 'https://www.linkedin.com/company/' . $linkedin_username;
				$social_links[] = $linkedin_url;
			}

			// Google+ Link			
			$googleplus = Mage::getStoreConfig('snippets/system/googleplus');
			$googleplus_username = Mage::getStoreConfig('snippets/system/googleplus_username');
			if($googleplus && $googleplus_username) {
				$googleplus_url = 'https://plus.google.com/' . $googleplus_username;
				$social_links[] = $googleplus_url;
			}

			// Pinterest Link			
			$pinterest = Mage::getStoreConfig('snippets/system/pinterest');
			$pinterest_username = Mage::getStoreConfig('snippets/system/pinterest_username');
			if($pinterest && $pinterest_username) {
				$pinterest_url = 'https://www.pinterest.com/' . $pinterest_username;
				$social_links[] = $pinterest_url;
			}

			// Instragram Link			
			$instagram = Mage::getStoreConfig('snippets/system/instagram');
			$instagram_username = Mage::getStoreConfig('snippets/system/instagram_username');
			if($instagram && $instagram_username) {
				$instagram_url = 'https://instagram.com/' . $instagram_username;
				$social_links[] = $instagram_url;
			}			
									
			if($social_links) {
				$snippets['sameAs'] = '';
				$snippets['sameAs'][] = $social_links;
			}

			if(Mage::getStoreConfig('snippets/system/rating')) {				
				if($rating = $this->_getRatingOrganization()) {
					$snippets['aggregateRating'] = $rating;
				}					
			}

			return $snippets;
		} 		
		return false;
    }    

    protected function _getRatingOrganization() {			
		
		$type = Mage::getStoreConfig('snippets/system/rating'); 
		
		$rating_value = '';
		$rating_count = '';
		$rating_best = '';
		
		// MAGMODULES: SHOPREVIEW
		if($type == 'shopreview') {
			if(Mage::helper('core')->isModuleEnabled('Magmodules_Shopreview')) { 
				$total = Mage::helper('shopreview')->getTotalScore();
				if(isset($total['total'])) {
					$rating_value = $total['total'];		
					$rating_count = Mage::helper('shopreview')->getReviewCount();
					$rating_best = '100';
				}
			}
		}

		// MAGMODULES: FEEDBACKCOMPANY
		if($type == 'feedbackcompany') {
			if(Mage::helper('core')->isModuleEnabled('Magmodules_Feedbackcompany')) { 
				$total = Mage::helper('feedbackcompany')->getTotalScore();				
				if(isset($total['percentage'])) {
					$rating_value = $total['percentage'];		
					$rating_count = $total['votes'];
					$rating_best = '100';
				}
			}
		}

		// MAGMODULES: WEBWINKELKEUR
		if($type == 'webwinkelkeur') {
			if(Mage::helper('core')->isModuleEnabled('Magmodules_Webwinkelconnect')) { 
				$total = Mage::helper('webwinkelconnect')->getTotalScore();				
				if(isset($total['average'])) {
					$rating_value = $total['average'];		
					$rating_count = $total['votes'];
					$rating_best = '100';
				}
			}
		}

		// MAGMODULES: TRUSTPILOT
		if($type == 'trustpilot') {
			if(Mage::helper('core')->isModuleEnabled('Magmodules_Trustpilot')) { 
				$total = Mage::helper('trustpilot')->getTotalScore();				
				if(isset($total['score'])) {
					$rating_value = $total['score'];		
					$rating_count = $total['votes'];
					$rating_best = '100';
				}
			}
		}

		// MAGMODULES: TRUSTPILOT
		if($type == 'kiyoh') {
			if(Mage::helper('core')->isModuleEnabled('Magmodules_Kiyoh')) { 
				$total = Mage::helper('kiyoh')->getTotalScore();				
				if(isset($total['score'])) {
					$rating_value = $total['score'];		
					$rating_count = $total['votes'];
					$rating_best = '100';
				}
			}
		}
		
		// RETURN RATING
		if(($rating_value > 0) && ($rating_count > 0)) {		
			$rating = array();
			$rating['@type'] = 'AggregateRating';
			$rating['ratingValue'] = $rating_value;		
			$rating['reviewCount'] = $rating_count; 
			$rating['bestRating'] = $rating_best;
			return $rating;
		}			
	}
	
	public function getJsonBreadcrumbs($breadcrumbs) {		
		if($breadcrumbs) {
			$cacheKeyInfo = $breadcrumbs->getCacheKeyInfo();
			if(!empty($cacheKeyInfo['crumbs'])) {
				$crumbs = unserialize(base64_decode($cacheKeyInfo['crumbs']));
				$listitems = array(); $i = 1;
	
				if($crumbs) {
					$snippets['@context'] = 'http://schema.org';
					$snippets['@type'] = 'BreadcrumbList';

					foreach($crumbs as $crumb) {
						if($crumb['link']) {
							$list['@type'] = 'ListItem';
							$list['position'] = $i;
							$list['item']['@id'] = $crumb['link'];

							if($i == 1) {
								$list['item']['name'] = $this->getFirstBreadcrumbTitle($crumb['label']);

							} else{
								$list['item']['name'] = $crumb['label'];
							}	
							$listitems[] = $list;
							$i++;
						}
					}

					$snippets['itemListElement'] = $listitems;							
					return $snippets;		
				}
			}
		}
	}
		
    public function getSiteNameSnippets() {			
		$snippets = array();
		if(Mage::getStoreConfig('snippets/system/sitelinkssearch')) {
			if(Mage::app()->getFrontController()->getAction()->getFullActionName() == 'cms_index_index') {
				$snippets['potentialAction'] = '';
				$snippets['potentialAction']['@type'] = 'SearchAction';
				$snippets['potentialAction']['target'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'catalogsearch/result/?q={q}';
				$snippets['potentialAction']['query-input'] = '';
				$snippets['potentialAction']['query-input']['@type'] = 'PropertyValueSpecification';						
				$snippets['potentialAction']['query-input']['valueRequired'] = true;
				$snippets['potentialAction']['query-input']['valueMaxlength'] = 100;						
				$snippets['potentialAction']['query-input']['valueName'] = 'q';						
			}
		}
		if(Mage::getStoreConfig('snippets/system/sitename')) {					
			if($sitename = Mage::getStoreConfig('snippets/system/sitename_name')) {
				$snippets['@context'] = 'http://schema.org';
				$snippets['@type'] = 'WebSite';
				$snippets['name'] = $sitename;
				$snippets['alternateName'] = Mage::getStoreConfig('snippets/system/sitename_alternate');
				$snippets['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);				
			}	
		} 		
		if(!empty($snippets)) {
			return $snippets;
		}
    }    
    

    public function getProductMetatags() {
		if($product = $this->getProduct()) {			
			$meta = array();			
			$pin_sitename = Mage::getStoreConfig('snippets/system/pinterest_username');
			$pin_enabled = Mage::getStoreConfig('snippets/products/pinterest');
			$twitter_user = Mage::getStoreConfig('snippets/system/twitter_username');
			$twitter_enabled = Mage::getStoreConfig('snippets/products/twitter');
			$product_markup = Mage::getStoreConfig('snippets/products/type');
			$product_enabled = Mage::getStoreConfig('snippets/products/enabled');
			
			if(($twitter_user && $twitter_enabled) || ($pin_enabled)) {			
				$offers = $this->_getProductOffers();
				$avilabilty = $this->_getAvailability();
				$rating = $this->_getProductRatings(); 				
			} else {
				$offers = '';
				$avilabilty = '';
				$rating = '';
			}

			if($pin_enabled) {
				$meta['og:site_name'] = Mage::getStoreConfig('snippets/system/sitename_name');
				if((!$product_enabled) ||($product_markup == 'json')) {
					$meta['og:type'] = 'product';
					$meta['og:title'] = htmlspecialchars($product->getName());
					$meta['og:url'] = $this->_getCurrentUrl();
					$meta['og:image'] = $this->_getProductImage();
					$description = str_replace(array("\r\n", "\r", "\n"), '', $this->_getProductDescription());
					$meta['og:description'] = htmlspecialchars($description);

					if(isset($offers['clean'])) {
						$meta['og:price:amount'] = $offers['clean'];
						$meta['og:price:currency'] = $offers['currency'];				
					}	

					if(isset($avilabilty['url'])) {
						if($avilabilty['url'] == 'http://schema.org/InStock') {
							$meta['og:availability'] = 'In Stock';					
						} else {
							$meta['og:availability'] = 'Out of Stock';											
						}	
					}
					
					if(isset($rating['count'])) {					
						$meta['og:rating'] = $rating['avg'];
						$meta['og:rating_scale'] = $rating['best'];
						$meta['og:rating_count'] = $rating['count'];
					}	
				}
			}
			
			if($twitter_user && $twitter_enabled) {			
				$prices = $offers;
				$meta['twitter:card'] = 'product';
				$meta['twitter:url'] = $this->_getCurrentUrl();
				$meta['twitter:title'] = htmlspecialchars($product->getName());

				if($description = $this->_getProductDescription()) {
					$description = str_replace(array("\r\n", "\r", "\n"), '', $description);
					$meta['twitter:description'] = htmlspecialchars($description);
				} else {
					$meta['twitter:description'] = htmlspecialchars($product->getName()) . ' - ' . $prices['price'];
				}	

				$meta['twitter:image:src'] = $this->_getProductImage();
				$meta['twitter:site'] = $twitter_user;
				$meta['twitter:creator'] = $twitter_user;			
				$meta['twitter:data1'] = $prices['price'];			
				$meta['twitter:label1'] = 'PRICE';			

				if(isset($avilabilty['url'])) {
					if($avilabilty['url'] == 'http://schema.org/InStock') {
						$meta['twitter:data2'] = 'In Stock';			
						$meta['twitter:label2'] = 'AVAILABILITY';				
					}	
				}

			}							
			return $meta;
		}
		return false;
	}

    
    public function getCategoryMetatags() {
		if($category = $this->getCategory()) {			
			$meta = array();			

			$pin_sitename = Mage::getStoreConfig('snippets/system/pinterest_username');
			$pin_enabled = Mage::getStoreConfig('snippets/category/pinterest');
			$twitter_user = Mage::getStoreConfig('snippets/system/twitter_username');
			$twitter_enabled = Mage::getStoreConfig('snippets/category/twitter');
			$category_markup = Mage::getStoreConfig('snippets/category/type');
			$category_enabled = Mage::getStoreConfig('snippets/category/enabled');
			
			if(($twitter_user && $twitter_enabled) || ($pin_enabled)) {			
				$offers = $this->_getCategoryOffers();
				$rating = $this->_getCategoryRatings(); 				
			} else {
				$offers = '';
				$rating = '';
			}
			
			if($pin_enabled) {
				$meta['og:site_name'] = Mage::getStoreConfig('snippets/system/sitename_name');
				$meta['og:availability'] = 'In Stock';					

				if((!$category_enabled) ||($category_markup == 'json')) {
					$meta['og:type'] = 'product';
					$meta['og:title'] = htmlspecialchars($category->getName());
					$meta['og:url'] = $this->_getCurrentUrl();
					$meta['og:image'] = $this->_getCategoryImage();
					$meta['og:description'] = htmlspecialchars($this->_getCategoryDescription());

					if(isset($offers['clean_low'])) {
						$meta['og:price:amount'] = $offers['clean_low'];
						$meta['og:price:currency'] = $offers['currency'];				
					}	
					
					if(isset($rating['count'])) {					
						$meta['og:rating'] = $rating['avg'];
						$meta['og:rating_scale'] = $rating['best'];
						$meta['og:rating_count'] = $rating['count'];
					}	
				}
			}
			
			if($twitter_user && $twitter_enabled) {			
				$prices = $offers;
				$meta['twitter:card'] = 'product';
				$meta['twitter:url'] = $this->_getCurrentUrl();
				$meta['twitter:title'] = htmlspecialchars($category->getName());

				if($description = $this->_getCategoryDescription()) {
					$meta['twitter:description'] = htmlspecialchars($description);
				} else {
					$meta['twitter:description'] = htmlspecialchars($category->getName()) . ' - ' . $prices['price_low'];
				}	

				$meta['twitter:image:src'] = $this->_getCategoryImage();
				$meta['twitter:site'] = $twitter_user;
				$meta['twitter:creator'] = $twitter_user;			
				$meta['twitter:data1'] = $prices['price_low'];			
				$meta['twitter:label1'] = 'PRICE';			
				$meta['twitter:data2'] = 'In Stock';			
				$meta['twitter:label2'] = 'AVAILABILITY';				

			}									
			return $meta;
		}
		return false;
	}


    public function getCmsMetatags() {
		$og_enabled = Mage::getStoreConfig('snippets/cms/og');
		if($og_enabled) {
			$meta = array();			
			$og_title = Mage::getStoreConfig('snippets/cms/og_title');
			$og_description = Mage::getStoreConfig('snippets/cms/og_description');
			$og_logo = Mage::getStoreConfig('snippets/cms/og_logo');
			$og_logo_url = Mage::getStoreConfig('snippets/cms/og_logo_url');
			$twitter = Mage::getStoreConfig('snippets/cms/twitter');
			$twitter_username = Mage::getStoreConfig('snippets/system/twitter_username');
			$twitter_logo_url = Mage::getStoreConfig('snippets/cms/twitter_logo_url');
			if($og_enabled && ($og_title || $og_description || $og_logo)) {			
				if($og_title) {
					$meta['og:title'] = htmlspecialchars($this->getLayout()->getBlock('head')->getTitle());
				}	
				if($og_description) {
				$meta['og:description'] = htmlspecialchars($this->getLayout()->getBlock('head')->getDescription());
				}
				if($og_logo && $og_logo_url) {
					$meta['og:image'] = $og_logo_url;
				}
				if(Mage::getSingleton('cms/page')->getIdentifier() == 'home') {
					$meta['og:type'] = 'website';
				} else {
					$meta['og:type'] = 'article';
				}	
				$meta['og:url'] = $this->_getCurrentUrl();
			}
			if($twitter && $twitter_username) {
				$meta['twitter:card'] = 'summary';
				$meta['twitter:site'] = '@' . $twitter_username;
				$meta['twitter:title'] = htmlspecialchars($this->getLayout()->getBlock('head')->getTitle());
				$meta['twitter:description'] = htmlspecialchars($this->getLayout()->getBlock('head')->getDescription());
				if($twitter_logo_url) {
					$meta['twitter:image'] = $twitter_logo_url;
				}
			}		
			return $meta;
		}
	}
			
	protected function _getAvailability() {
		if(Mage::getStoreConfig('snippets/products/stock')) {
			$product = $this->getProduct();
			$availability = array();
			$availability['url'] = ($product->isAvailable() ? 'http://schema.org/InStock' : 'http://schema.org/OutOfStock');		
			$availability['text'] = ($product->isAvailable() ? Mage::helper('snippets')->__('In stock') : Mage::helper('snippets')->__('Out of Stock'));
			return $availability;		
		}
		return false;		
	}

	protected function _getCondition() {
		if($_condition = Mage::getStoreConfig('snippets/products/condition')) {
			$product = $this->getProduct();
			$condition = array();
			
			if($_condition == 1) {
				$_condition = ucfirst(Mage::getStoreConfig('snippets/products/condition_default'));
				if($_condition) {
					$condition['url'] = 'http://schema.org/' . $_condition . 'Condition';				
					$condition['text'] = Mage::helper('snippets')->__($_condition);
				}			
			}
			if($_condition == 2) {
				$_condition = ucfirst($this->_getProductCondition());
				if($_condition) {
					$condition['url'] = 'http://schema.org/' . $_condition . 'Condition';				
					$condition['text'] = Mage::helper('snippets')->__($_condition);
				}			
			}			
			return $condition;		
		}
		return false;		
	}
	
	protected function _getProductThumbnail() {
		$product = $this->getProduct();
		return Mage::helper('catalog/image')->init($product, 'small_image')->resize(75);
	}
	
	protected function _getProductImage() {
		$product = $this->getProduct();
		return Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getImage());
	}

	protected function _getCategoryImage() {
		$category = $this->getCategory();
		if($image_url = $category->getImageUrl()) {
			return $image_url; 
		}
		return false;	
	}

	protected function _getCategoryThumbnail() {
		$category = $this->getCategory();
		if($image_url = $category->getThumbnail()) {
			return Mage::getBaseUrl('media') . 'catalog/category/' . $image_url; 
		}
		return false;		
	}		
				
	protected function _getProductOffers() {
		$product = $this->getProduct();			
		$offers = array();
		$price = '';
		if(Mage::getStoreConfig('snippets/products/prices') == 'custom') {
			$attribute = Mage::getStoreConfig('snippets/products/price_attribute');
			$price = $product[$attribute];			
		} else {	
			if($product->getTypeId() == 'grouped') {
				if($price = $this->_getPriceGrouped()) {				
					$offers['price_low'] = Mage::helper('core')->currency($price, true, false);	
					$offers['clean_low'] = Mage::helper('core')->currency($price, false, false);
				}			
			}
			if($product->getTypeId() == 'bundle') {
				$price = $this->_getPriceBundle(); 
			}
			if(!$price) {
				$price = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);
			}	
		}

		if(Mage::getStoreConfig('snippets/products/prices') == 'notax') {
			$tax = Mage::getStoreConfig('snippets/products/taxperc');
			if($tax > 0) {
				$price = (($price / (100 + $tax)) * 100);
			}	
		}

		$offers['price_only'] = number_format(Mage::helper('core')->currency($price, false, false), 2, '.', '');	
		$offers['clean'] = number_format(Mage::helper('core')->currency($price, false, false), 2, '.', '');	
		$offers['price'] = Mage::helper('core')->currency($price, true, false);	
		$offers['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();

		if(isset($offers['price_low'])) {
			$offers['type'] = 'http://schema.org/AggregateOffer';
		} else {
			$offers['type'] = 'http://schema.org/Offer';		
		}				
		
		// Currencies
		if(Mage::getStoreConfig('snippets/products/mulitple_currencies')) {
			if($config_currencies = Mage::getStoreConfig('snippets/products/currencies')) {
				$currencyModel = Mage::getModel('directory/currency');
				$currencies = $currencyModel->getConfigAllowCurrencies();
                $rates = $currencyModel->getCurrencyRates($offers['currency'], $currencies);
				if(is_array($rates)) {				
					$cur_array = explode(',', $config_currencies);
					foreach($cur_array as $currency) {
						if(isset($rates[$currency])) {
							$price = number_format(($rates[$currency] * $offers['clean']), 2, '.', '');
							$offers['extra_offer'][$currency]['price'] = $price;
							$offers['extra_offer'][$currency]['currency'] = $currency;
						}
					}
				}	
			}
		}
		
		return $offers;
	}

	protected function _getPriceGrouped() {
		$price = '';
		$product = $this->getProduct();		
		$_associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
		foreach ($_associatedProducts as $_item):
			$price_associated = Mage::helper('tax')->getPrice($_item, $_item->getFinalPrice(), true);
			if(($price_associated < $price) || ($price == '')):
				$price = $price_associated;
			endif;
		endforeach;		
		
		if($price > 0) {
			return $price; 
		}	
	}

	protected function _getPriceBundle() {		
		$price = '';
		$product = $this->getProduct();				
		if(($product->getPriceType() == '1') && ($product->getFinalPrice() > 0)) {
			$price = $product->getFinalPrice();				
		} else {
			$priceModel = $product->getPriceModel();
			$block = Mage::getSingleton('core/layout')->createBlock('bundle/catalog_product_view_type_bundle');
			$options = $block->setProduct($product)->getOptions();
			$price = 0;
		
			foreach ($options as $option) {
			  $selection = $option->getDefaultSelection();
			  if($selection === null) { continue; }
				$prod_option = Mage::getModel('catalog/product')->load($selection->getProductId()); 
				$price += (Mage::helper('tax')->getPrice($prod_option, $prod_option->getFinalPrice(), true) * $selection->getSelectionQty()); 
			}				
		}

		if($price < 0.01):
			$price = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);			
		endif;		
		
		return $price; 				
	}	

	protected function _getCategoryOffers() {
		$category = $this->getCategory();		
		$offers = array();
		$price = '';
		$qty = '';
		
		if(Mage::getStoreConfig('snippets/products/prices') == 'custom') {
			$price_attribute = Mage::getStoreConfig('snippets/products/price_attribute');
			$cat_products = Mage::getModel('catalog/product')->getCollection()->addCategoryFilter(Mage::registry('current_category'))->addAttributeToSelect($price_attribute)->addAttributeToFilter($price_attribute, array('gt' => 0))->load();
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($cat_products); 
			Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($cat_products);	
			$qty = $cat_products;
			if(count($cat_products)) {
				$prices = array();			
				foreach($cat_products as $catproduct) {	
					$prices[] = $catproduct[$price_attribute];
				}
				if($prices) {
					$price_low = min($prices);	
					$price_high = max($prices);	
				} 					
			}						
		} else {		
			$magentoVersion = Mage::getVersion();
			if(version_compare($magentoVersion, '1.7.2', '>=')){
				$price_low = Mage::getSingleton('catalog/layer')->getProductCollection()->getMinPrice();
				$price_high = Mage::getSingleton('catalog/layer')->getProductCollection()->getMaxPrice(); 				
				$qty = Mage::getSingleton('catalog/layer')->getProductCollection()->getSize();
				if($price_low < 0.01) {
					$price_low = '0.0001';
				}	
			} else {
				$price_attribute = 'price';
				$category = Mage::getModel('catalog/category')->load(Mage::registry('current_category')->getId());	
				$productColl = Mage::getModel('catalog/product')->getCollection()->addCategoryFilter($category)
																->addAttributeToFilter('visibility', array('eq' => 4))
																->addAttributeToFilter('status', array('eq' => 1))
																->addAttributeToFilter('price', array('gt' => 0))
																->addAttributeToSort('price', 'asc')
																->setPageSize(1)->load();
				$qty = count($productColl);
				$lowestProductPrice = $productColl->getFirstItem()->getPrice();	
				$price_low = $lowestProductPrice;
			}	
		}

		if(Mage::getStoreConfig('snippets/products/prices') == 'notax') {
			$tax = Mage::getStoreConfig('snippets/products/taxperc');
			if($tax > 0) {
				if($price_low) {
					$price_low = (($price_low / (100 + $tax)) * 100);
				}	
				if($price_high) {
					$price_high = (($price_high / (100 + $tax)) * 100);
				}	
			}	
		}

		if(isset($price_low)) {
			$offers['price_low'] = Mage::helper('core')->formatPrice($price_low, false);
			$offers['clean_low'] = number_format($price_low, 2, '.', '');	
		} 
		
		if((isset($price_high)) && (Mage::getStoreConfig('snippets/category/prices') == 'range')) {		
			$offers['price_high'] = Mage::helper('core')->formatPrice($price_high, false);
			$offers['clean_high'] = number_format($price_high, 2, '.', '');	
		}
		
		$offers['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();		
		
		// Extra check for missing price
		if(!isset($offers['price_low'])) {
			$offers['price_low'] = Mage::helper('core')->currency('0.00', true, false);		
			$offers['clean_low'] =  '0.00';
		}			
		
		$offers['qty'] = $qty;		
		return $offers;
	}	
		
	protected function _getProductDescription() {	
		if(Mage::getStoreConfig('snippets/products/description')) {		
			$product = $this->getProduct();
			$attribute = Mage::getStoreConfig('snippets/products/description_attribute');
			if($attribute) {
				$description = trim(strip_tags($product[$attribute]));
			} else {
				$description = trim(strip_tags($product->getShortDescription()));		
			}	
			
			$description_lenght = Mage::getStoreConfig('snippets/products/description_lenght');
			
			if($description_lenght > 0) {
				$description = Mage::helper('core/string')->truncate($description, $description_lenght, '...', $remainder, false);
			}
			return $description;
		}
		return false;		
	}    

	protected function _getCategoryDescription() {	
		if(Mage::getStoreConfig('snippets/category/description')) {		
			$category = $this->getCategory();
			$description = strip_tags($category->getDescription());		
			if($description) {
				$description_lenght = Mage::getStoreConfig('snippets/category/description_lenght');			
				if($description_lenght > 0) {
					$description = Mage::helper('core/string')->truncate($description, $description_lenght, '...', $remainder, false);
				}			
				return $description; 
			}	
		}
		return false;		
	} 
	
	protected function _getProductRatings() {			
		if(Mage::getStoreConfig('snippets/products/reviews')) {		

			if((Mage::getStoreConfig('snippets/products/reviews_source') == 'yotpo') && Mage::helper('core')->isModuleEnabled('Yotpo_Yotpo')) { 			
				return $this->_getYotpoReviews();
			} else { 	
				$product = $this->getProduct();	
				$summaryData = Mage::getModel('review/review_summary')->setStoreId(Mage::app()->getStore()->getStoreId())->load($product->getId());

				$rating = array();
				$rating['count'] = $summaryData->getReviewsCount();  
				$rating['percentage'] = $summaryData->getRatingSummary();
		
				if(Mage::getStoreConfig('snippets/products/reviews_metric') == '5') {		
					$rating['avg'] = round(($summaryData->getRatingSummary() / 20), 1);
					$rating['best'] = '5';
				} else {
					$rating['avg'] = round($summaryData->getRatingSummary());
					$rating['best'] = '100';		
				}

				if(Mage::getStoreConfig('snippets/products/reviews_type') == 'votes') {		
					$rating['type'] = 'ratingCount';
				} else {
					$rating['type'] = 'reviewCount';		
				}	
		
				if($summaryData->getReviewsCount() > 0) {
					return $rating;
				}			
			}
		}
		return false;		
	}  

	protected function _getYotpoReviews() {	
		$rating = array();
		$yotpo_snippets = Mage::helper('yotpo/richSnippets')->getRichSnippet(); 			
		if(isset($yotpo_snippets["average_score"])) {
			if($yotpo_snippets["average_score"] > 0) {
				$ratingSummary = $yotpo_snippets["average_score"];
				$rating['percentage'] = ($ratingSummary * 20);
				$rating['avg'] = $ratingSummary;
				$rating['count'] = $yotpo_snippets["reviews_count"];	
				$rating['best'] = '5';										
				$rating['type'] = 'ratingCount';
				if(Mage::getStoreConfig('snippets/products/reviews_metric') != '5') {		
					$rating['avg'] = ($ratingSummary * 20);
					$rating['best'] = '100';
				}
				return $rating;
			}
		}
		return $rating;		
    }
    
	protected function _getCategoryRatings() {			
		if(Mage::getStoreConfig('snippets/category/reviews')) {		
			$category = $this->getCategory();	

			$productIds = $category->getProductCollection()->addAttributeToFilter('visibility', array('neq' => 1))->getAllIds();
	
			$product_ratings = array();
			foreach($productIds as $productId){			
				$product_ratings[] = Mage::getModel('review/review_summary')->setStoreId(Mage::app()->getStore()->getId())->load($productId);	
			}
	
			$totals = array(); $count = 0;

			foreach($product_ratings as $rating) {
				if(($rating['reviews_count'] > 0) && ($rating['rating_summary'] > 0)) {
					$totals[] = $rating['rating_summary'];	
					$count = ($count + $rating['reviews_count']);
				}
			}
			
			if(count($totals) > 0) {
				$ratingSummary = (array_sum($totals) / count($totals));
			} else {
				$ratingSummary = '';
			}	

			$rating = array();
			$rating['count'] = $count;
			$rating['percentage'] = $ratingSummary;
									
			if(Mage::getStoreConfig('snippets/category/reviews_metric') == '5') {		
				$rating['avg'] = round(($ratingSummary / 20), 1);
				$rating['best'] = '5';
			} else {
				$rating['avg'] = round($ratingSummary);
				$rating['best'] = '100';		
			}
		
			if(Mage::getStoreConfig('snippets/category/reviews_type') == 'votes') {		
				$rating['type'] = 'ratingCount';
			} else {
				$rating['type'] = 'reviewCount';		
			}	

			return $rating;
		}
		return false;
	}
		
	protected function _getCurrentUrl() {	
		$url = Mage::helper('core/url')->getCurrentUrl(); 
		$url = preg_replace('/\?.*/', '', $url);		
        return $url;
    }	
	
	protected function _getProductExtraFields() {
		$product = $this->getProduct();	
		$fields = array();
			
		// Brand
		if($brand = $this->_getProductBrand()): 
			$data = '<span itemprop="brand" itemscope itemtype="http://schema.org/Brand"><span itemprop="name">' . $this->escapeHtml($brand) . '</span></span>';
			$fields[] = array('value'=> $data, 'label'=> 'Brand', 'clean'=> $this->escapeHtml($brand), 'itemprop'=> 'brand');			
		endif;

		// Color
		if($color = $this->_getProductColor()): 
			$data = '<span itemprop="color">' . $this->escapeHtml($color) . '</span>';
			$fields[] = array('value'=> $data, 'label'=> 'Color', 'clean'=> $this->escapeHtml($color), 'itemprop'=> 'color');			
		endif;

		// Model
		if($model = $this->_getProductModel()): 
			$data = '<span itemprop="model">' . $this->escapeHtml($model) . '</span>';
			$fields[] = array('value'=> $data, 'label'=> 'Model', 'clean'=> $this->escapeHtml($model), 'itemprop'=> 'model');			
		endif;		

		// EAN
		if($ean = $this->_getProductEan()): 
			$type = Mage::getStoreConfig('snippets/products/ean_type');	
			$data = '<span itemprop="' . $type. '">' . $this->escapeHtml($ean) . '</span>';
			$fields[] = array('value'=> $data, 'label'=> 'Product ID', 'clean'=> $this->escapeHtml($ean), 'itemprop'=> $type);			
		endif;	
							
		return $fields;
	}

	protected function _getProductBrand() {		
		if(Mage::getStoreConfig('snippets/products/brand')) {							
			$attribute = Mage::getStoreConfig('snippets/products/brand_attribute');		
			$product = $this->getProduct();	
			if($brand = $product->getAttributeText($attribute)) {
				return $brand;
			} else {
				if($brand = $product[$attribute]) {
					return $brand;				
				}	
			}
		}
		return false;
	}

	protected function _getProductCondition() {		
		if(Mage::getStoreConfig('snippets/products/condition')) {							
			$attribute = Mage::getStoreConfig('snippets/products/condition_attribute');		
			$product = $this->getProduct();	
			if($condition = $product->getAttributeText($attribute)) {
				return $condition;
			} else {
				if($condition = $product[$attribute]) {
					return $condition;				
				}	
			}
		}
		return false;
	}
	
	protected function _getProductColor() {		
		if(Mage::getStoreConfig('snippets/products/color')) {							
			$attribute = Mage::getStoreConfig('snippets/products/color_attribute');		
			$product = $this->getProduct();	
			if($color = $product->getAttributeText($attribute)) {
				return $color;
			} else {
				if($color = $product[$attribute]) {
					return $color;				
				}			
			}
		}	
		return false;		
	}

	protected function _getProductModel() {		
		if(Mage::getStoreConfig('snippets/products/model')) {							
			$attribute = Mage::getStoreConfig('snippets/products/model_attribute');		
			$product = $this->getProduct();	
			if($model = $product->getAttributeText($attribute)) {
				return $model;
			} else {
				if($model = $product[$attribute]) {
					return $model;				
				}				
			}
		}	
		return false;		
	}	

	protected function _getProductEan() {		
		if(Mage::getStoreConfig('snippets/products/ean')) {							
			$attribute = Mage::getStoreConfig('snippets/products/ean_attribute');		
			$type = Mage::getStoreConfig('snippets/products/ean_type');		
			$product = $this->getProduct();	
			$ean = trim($product[$attribute]);	
			if($ean) {
				$value = $ean; 				
				if($type == 'gtin8') {
					$value = str_pad($ean, 8, "0", STR_PAD_LEFT);		
				}
				if($type == 'gtin12') {
					$value = str_pad($ean, 12, "0", STR_PAD_LEFT);		
					$type_text = $type;				
				}
				if($type == 'gtin13') {
					$value = str_pad($ean, 13, "0", STR_PAD_LEFT);		
					$type_text = $type;				
				}
				if($type == 'gtin14') {
					$value = str_pad($ean, 14, "0", STR_PAD_LEFT);				
				}
				return $value;
			}		
		}	
		return false;			
	} 

	// FOR OBSERVER
	public function getMarkup() {
		if(Mage::registry('product')) {
			return Mage::getStoreConfig('snippets/products/type');
		} elseif(Mage::registry('current_category') && !Mage::registry('product')) {
			return Mage::getStoreConfig('snippets/category/type');		
		}			
	}	
	
	public function getContent()  {
		if(Mage::registry('current_product')) {
			$type = Mage::getStoreConfig('snippets/products/type');
			if($type == 'visible') {
				if(Mage::getStoreConfig('snippets/products/location') == 'advanced') {
					return Mage::getStoreConfig('snippets/products/location_custom');			
				} else {
					return Mage::getStoreConfig('snippets/products/location');		
				}
			}
			if($type == 'footer') {
				return Mage::getStoreConfig('snippets/products/location_ft');
			}
		} elseif(Mage::registry('current_category') && !Mage::registry('product')) {
			$type = Mage::getStoreConfig('snippets/category/type');
			if($type == 'visible') {
				return Mage::getStoreConfig('snippets/category/location');		
			}
			if($type == 'footer') {
				return Mage::getStoreConfig('snippets/category/location_ft');
			}
		}
	}	

	public function getPosition() {
		if(Mage::registry('current_product')) {
			$type = Mage::getStoreConfig('snippets/products/type');
			if($type == 'visible') {
				return Mage::getStoreConfig('snippets/products/position');
			}	
			if($type == 'footer') {
				return Mage::getStoreConfig('snippets/products/position_ft');
			}	
		} elseif(Mage::registry('current_category') && !Mage::registry('product')) {
			$type = Mage::getStoreConfig('snippets/category/type');
			if($type == 'visible') {
				return Mage::getStoreConfig('snippets/category/position');
			}	
			if($type == 'footer') {
				return Mage::getStoreConfig('snippets/category/position_ft');
			}	
		}
	}	
	
	// FOR OBSERVER
	public function getEnabled() {
		$enabled = Mage::getStoreConfig('snippets/general/enabled');
		$block = ''; $enabled_ent = '';	 $type = '';			
		
		if(Mage::registry('current_product')) {
			$enabled_ent 	= Mage::getStoreConfig('snippets/products/enabled');
			$type 			= Mage::getStoreConfig('snippets/products/type');
			if($type == 'visible') {
				$block 	= Mage::getStoreConfig('snippets/products/location');
			}
			if($type == 'footer') {
				$block 	= Mage::getStoreConfig('snippets/products/location_ft');
			}
		} elseif(Mage::registry('current_category')) {

			$enabled_ent = Mage::getStoreConfig('snippets/category/enabled');			
			$type = Mage::getStoreConfig('snippets/category/type');	
			if($type == 'visible') {
				$block 	= Mage::getStoreConfig('snippets/category/location');
			}
			if($type == 'footer') {
				$block 	= Mage::getStoreConfig('snippets/category/location_ft');
			}	
		}
				
		if(($block == '') || ($enabled == '') || ($enabled_ent == '') || ($type == 'hidden')) {
			return false;
		} else {
			return true;
		}
			
	}	

	public function getFirstBreadcrumbTitle($title) {
		$custom = Mage::getStoreConfig('snippets/system/breadcrumbs_custom'); 
		$enabled = Mage::getStoreConfig('snippets/system/breadcrumbs');			
		$customname = Mage::getStoreConfig('snippets/system/breadcrumbs_customname'); 
		if($custom && $enabled && $customname) {
			return $customname;
		} 		
		return $title;		
	}

	public function getFilterHash() {
		if($category = $this->getCategory()) {		
			$url = str_replace('?___SID=U', '', $category->getUrl());
			$diff = str_replace(Mage::getBaseUrl(), '', $url);
			return $diff;					
		}	
	}
		   
}