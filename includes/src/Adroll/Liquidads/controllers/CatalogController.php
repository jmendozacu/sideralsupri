<?php
class Adroll_Liquidads_CatalogController extends Mage_Core_Controller_Front_Action {
    var $PREFIX = "Adroll_Liquidads_CatalogController::";

    public function permissionAction() {
        // Requests the nonce and salt to be used in the next request.
        $response = $this->getResponse();
        $username = @$_REQUEST['username'];
        $api_user = Mage::getModel('api/user')->loadByUsername($username);
        $hashed_key = $api_user->getApiKey();
        if(!$hashed_key) {
            $response->setHeader('HTTP/1.1', '403 Forbidden')->sendResponse();
            return;
            }
        $parts = explode(':', $hashed_key);
        $salt = $parts[1];

        $nonce = rand(0,4000000000);

        // Store in session.
        Mage::getSingleton('core/session')->setNonce($nonce);
        $message = "$nonce:$salt";
        $response->appendBody($message);
        Mage::log($this->PREFIX . "permissionAction: $username requested nonce and salt for request.");
        return;
        }

    public function pageAction() {
        $start_time = microtime(TRUE);
        $response = $this->getResponse();

        // Get params from request.
        $page_size = @$_REQUEST['page_size'];
        $page = @$_REQUEST['page'];
        if(!$page_size) $page_size = 1000;
        if(!$page) $page = 1;

        $session = Mage::getSingleton('core/session');
        $token = @$_REQUEST['token'];
        $username = @$_REQUEST['username'];
        $nonce = $session->getNonce();

        # Clear the session every time the nonce is used.
        $session->clear();
        
        $api_user = Mage::getModel('api/user')->loadByUsername($username);

        // The token is sha1(nonce . stored_key).
        // The stored key is the string in storage, minus the salt and delimeter (if any).
        $stored_key = $api_user->getApiKey();
        $stored_key_parts = explode(':', $stored_key);
        $stored_key = $stored_key_parts[0];

        // Bounce bad tokens.
        $local_hash = sha1($nonce . $stored_key);
        if(!$stored_key || $token != $local_hash) {
            $response->setHeader('HTTP/1.1','403 Forbidden')->sendResponse();
            return;
            }

        // Valid credentials. Show catalog.
        // Only consider products that are visible on the site.
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
            );

        // Magento installs can have multiple stores. Only consider the active.
        $store_id = Mage::app()->getStore()->getId();
         
        $products = Mage::getModel('catalog/product')
            ->setStoreId($store_id)
            ->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('url_path')
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('image_url')
            ->addAttributeToSelect('updated_at')
            ->addAttributeToSelect('price')
            ->addAttributeToFilter('visibility', $visibility)
            ->addStoreFilter($store_id)
            ->setPageSize($page_size)
            ->setCurPage($page);

        $total = $products->getSize();
        $actual_page = $products->getCurPage();

        // Magento keeps pulling the last page when we ask for any page greater than the last one.
        // Fortunately, it tracks the actual last page as the curPage.
        // Don't repeat ourselves, just throw a 416.
        if($actual_page < $page) {
            $response->setHeader('HTTP/1.1','416 Requested Range Not Satisfiable')->sendResponse();
            return;
            }

        // Send data.
        // Include JSON metadata header (X-Meta).
        $version = Mage::getVersion();
        $response->setHeader('X-Meta', "{\"total\":$total,\"page\":$page,\"page_size\":$page_size,\"store_id\":$store_id,\"magento_version\":\"$version\"}\n");
        // Send data as CSV
        $num_products = 0;
        $response->appendBody("url_key,url,title,category,price,updated_date,image_url\n");
        foreach($products as $product) {

            // Get paths for categories, with some light caching.
            $categories_string = category_path($product->getCategoryIds());

            $url_key      = csv_escape($product->getUrlKey());
            $url          = csv_escape($product->getProductUrl());
            $title        = csv_escape($product->getName());
            $category     = csv_escape($categories_string);
            $price        = $product->getPrice();
            $updated_date = $product->getUpdatedAt();
            $image_url    = csv_escape($product->getImageURL());

            $response->appendBody(implode(",", array($url_key,$url,$title,$category,$price,$updated_date,$image_url)) . "\n");
            $num_products++;
            }
        $time_taken = microtime(TRUE) - $start_time;
        Mage::log($this->PREFIX . "pageAction: Exported $num_products product rows in $time_taken seconds.");
        }
    }

function category_path($category_ids) {
    # Convert category_id list into a string of some kind.
    static $CACHE = array();
    $categories = Mage::getModel('catalog/category');
    $category_strings = array();
    foreach($category_ids as $category_id) {
        if(!in_array($category_id, array_keys($CACHE))) {
            $category = $categories->load($category_id);
            $path = array();
            while($category->name && $category->name != 'Root Catalog') {
                $path[] = $category->name;
                $category = $category->getParentCategory();
                }
            $path = array_reverse($path);
            $category_string = implode('|', $path);
            $CACHE[$category_id] = $category_string;
            }
        $category_strings[] = $CACHE[$category_id];
        }
    return implode(',', $category_strings);
    }

function csv_escape($s) {
    $s = str_replace('"', '""', $s);
    if(strpos($s, ",") !== FALSE || strpos($s, '"') !== FALSE || strpos($s, "\n") !== FALSE) {
        $s = "\"$s\"";
        }
    return $s;
    }
