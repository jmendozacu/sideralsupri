<?php
define('EMAIL_SUPPORT', 'delight@adroll.com');
define('SALT', 'adr0ll_4__m4g3nt0');

//
// Controllers
//
class Adroll_Liquidads_Adminhtml_LiquidadsController extends Mage_Adminhtml_Controller_Action {
    // Success Page
    public function successAction() {
        $session = Mage::getSingleton('core/session');
        $advertisable_eid = $session->getAdvertisable_eid();

        $layout = $this->loadLayout();
        $block = $this->getLayout()->getBlock('success');
        if($advertisable_eid != "NEW") {
            $block->advertisable_eid = $advertisable_eid;
            }
        else {
            $block->advertisable_eid = FALSE;
            }

        $layout->renderLayout();
        }
    // Index Page
	public function indexAction() {
        // If this store has set up an AdRoll account before, adroll/liquidads/account_created will be set to the time of
        // creation (in es). In this case, go to the success page.
        $adroll_setup = new AdrollSetup($this);
        if($adroll_setup->get_config('adroll/liquidads/account_created')) {
            $this->_redirect("*/*/success");
            return;
            }
		$this->loadLayout()->renderLayout();
        }
    // New User form
    public function newAction() {
        $this->loadLayout()->renderLayout();
	    }
    // Existing User form
    public function existingAction() {
        $this->loadLayout()->renderLayout();
	    }
    // Advertisable Picker
    public function advertisablesAction() {
        $session = Mage::getSingleton('core/session');
        $adroll_setup = new AdrollSetup($this);
        $post = $this->getRequest()->getPost();

        $email = $post['email'];
        $password = $post['password'];
        $form = $post['form'];
        $flush_cache = $post['flush_cache'];

        // Store this form input for later.
        $session->setEmail($email);
        $session->setPassword($password);
        $session->setForm($form);
        $session->setFlush_cache($flush_cache);

        try {
            $advertisables = $adroll_setup->list_advertisables($email, $password);
            }
        catch(Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $adroll_setup->log($e->getMessage());
            $this->_redirect("*/*/$form");
            return;
            }

        $adroll_setup->log("User $email has ".count($advertisables)." active advertisables.");

        $layout = $this->loadLayout();
        $block = $this->getLayout()->getBlock('liquidads');
        $block->advertisables = $advertisables;
        $layout->renderLayout();
        }
    // Process either form
    public function postAction() {
        $ROLE_NAME = 'adrole';
        $USER_NAME = 'adroll_liquidads_user';
        $RULE_RESOURCES = array(
            'allow' => array(
                'catalog',
                'catalog/category',
                'catalog/category/tree',
                'catalog/product',
                'catalog/product/info',
                'catalog/product/media',
                ),
            'deny' => array(
                'catalog/category/create',
                'catalog/category/update',
                'catalog/category/move',
                'catalog/category/delete',
                'catalog/category/product',
                'catalog/product/create',
                'catalog/product/update',
                'catalog/product/delete',
                'catalog/product/update_tier_price',
                'catalog/product/attributes',
                'catalog/product/attribute',
                'catalog/product/link',
                'catalog/product/media/create',
                'catalog/product/media/update',
                'catalog/product/media/remove',
                'catalog/product/option',
                'catalog/product/tag',
                'catalog/product/downloadable_link',
                )
            );

        $post = $this->getRequest()->getPost();
        try {
            // Get variables from the post, or, failing that, the session. Gross.
            $session = Mage::getSingleton('core/session');
            $email = !empty($post['email']) ? $post['email'] : $session->getEmail();
            $password = !empty($post['password']) ? $post['password'] : $session->getPassword();
            $password_confirm = !empty($post['password_confirm']) ? $post['password_confirm'] : $session->getPassword_confirm();
            $form = !empty($post['form']) ? $post['form'] : $session->getForm();
            $flush_cache = $form == 'new' ? $post['flush_cache'] : $session->getFlush_cache();
            $advertisable_eid = @$post['advertisable_eid'];

            $session->setAdvertisable_eid($advertisable_eid);

            // A little sanity check:
            // 1. Email address must look vaguely plausible;
            if(!preg_match("/^.+@.+\..+$/", $email)) Mage::throwException('Please provide a valid email address.');
            // 2. Password must not be absurdly short;
            if(strlen($password) < 4) Mage::throwException("Password should be at least 4 characters long.");
            // 3. Password and confirmation password must match... that's the point of it.
            if($form == 'new' && $password != $password_confirm) Mage::throwException("Password and confirmation didn't match.");

            // -- MAIN --
            $adroll_setup = new AdrollSetup($this);

            // Create API user for AdRoll
            $api_user = Mage::getModel('api/user');
            $api_user->loadByUsername($USER_NAME);
            $api_key = $adroll_setup->generate_api_key();
            if(!$api_user->getId()) {
                // Create API user / permissions
                $api_user->setFirstname('Adroll');
                $api_user->setLastname('Liquidads');
                $api_user->setEmail(EMAIL_SUPPORT);
                $api_user->setUsername($USER_NAME);
                $api_user->setApiKey($api_key);
                $adroll_setup->log("Created local webservice/API user for AdRoll with username: $USER_NAME");
                }
            else {
                $api_user->setApiKey($api_key);
                $adroll_setup->log("Webservice/API user $USER_NAME already exists. Resetting API key.");
                }
            $api_user->save();

            // Create API Group role
            $api_role = Mage::getModel('api/role');
            $api_role = Mage::getModel('api/role')->getCollection()->addFilter('role_name', $ROLE_NAME)->getFirstItem();
            if(!$api_role->getId()) {
                $api_role->setRoleType('G');
                $api_role->setRoleName($ROLE_NAME);
                $api_role->setUserId(0);
                $api_role->setTreeLevel(1);
                $api_role->save();
                }

            // Create API User role
            $api_user_role = Mage::getModel('api/role')->getCollection()->addFilter('role_name', $api_user->getName())->getFirstItem();
            if(!$api_user_role->getId()) {
                $api_user_role->setRoleType('U');
                $api_user_role->setRoleName($api_user->getName());
                $api_user_role->setUserId($api_user->getId());
                $api_user_role->setTreeLevel(1);
                $api_user_role->setParentId($api_role->getId());
                $api_user_role->save();
                }

            // Create API role rules
            foreach($RULE_RESOURCES as $permission => $resources) {
                foreach($resources as $resource_id) {
                    $api_rule = Mage::getModel('api/rules')->getCollection()
                                    ->addFilter('role_id', $api_role->getId())
                                    ->addFilter('resource_id', $resource_id)
                                    ->getFirstItem();
                    if(!$api_rule->getId()) {
                        $api_rule = Mage::getModel('api/rules');
                        $api_rule->setRoleId($api_role->getId());
                        $api_rule->setResourceId($resource_id);
                        $api_rule->setRoleType('G');
                        $api_rule->setApiPermission($permission);
                        $api_rule->setPermission($permission);
                        $api_rule->save();
                        }
                    }
                }

            // Site URL
            $url = $adroll_setup->get_config('web/secure/base_url');

            // The tail end of the logo src, so we can fetch/cache it later.
            $logo_src = Mage::getStoreConfig('design/header/logo_src');

            // Create AdRoll account
            $pixel_code = $adroll_setup->create_adroll_account($email, $password, $advertisable_eid, $url, $api_user->getUsername(), $api_key, $logo_src);

            // Inject pixel code
            if($pixel_code) {
                $existing = $adroll_setup->get_config('design/footer/absolute_footer');
                if(strpos($existing, $pixel_code) === false) {
                    $new_footer = $existing . $pixel_code;
                    $adroll_setup->set_config('design/footer/absolute_footer', $new_footer);
                    $adroll_setup->log("Added AdRoll pixel code to frontend page footer.");
                    }
                else {
                    $adroll_setup->log("Pixel code already in frontend page footer. Doing nothing.");
                    $flush_cache = false;
                    }
                }
            else {
                $adroll_setup->log("No pixel code returned. Pixel was probably installed manually.");
                $flush_cache = false;
                }
            // Done. Record completion time in the store config.
            $adroll_setup->set_config('adroll/liquidads/account_created', time());
            Mage::getSingleton('adminhtml/session')->addSuccess("Adroll Account Created");
            
            // Flush config cache if requested and we actually added a pixel.
            if($flush_cache) {
                $adroll_setup->log("Clearing out config cache so pixel shows up in store footer.");
                Mage::app()->getCacheInstance()->cleanType('config');
                }

            $this->_redirect('*/*/success');
            }
        // If anything's gone pear-shaped in the above logic, drop the user back on the appropriate form with and error message.
        catch(Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $error_page = "*/*/$form";
            $this->_redirect($error_page);
            }
        }
    }



class AdrollSetup {
    public function __construct($controller) {
        // Keep track of the Magento controller instance.
        $this->controller = $controller;
        }

    public function log($message) {
        return Mage::log("AdrollSetup: " . $message);
        }

    public function sign($data) {
        // "Sign" request:
        // 1. Sort parameters by name;
        // 2. Concatenate values together;
        // 3. Concatenate salt to that;
        // 4. Hash w/ SHA 256.
        $sig = '';
        ksort($data);
        foreach($data as $k => $v) $sig .= $v;
        return hash("sha256", $sig . SALT, false);
        }

    public function list_advertisables($email, $password) {
        $endpoint = "https://www.adroll.com/about/advertisables";
        $response = $this->call($endpoint, array('username'=>$email, 'password'=>$password));
        $advertisables = json_decode($response);
        return $advertisables;
        }

    public function call($url, $data, $sign_request=true) {
        if($sign_request) $data['sig'] = $this->sign($data);

        $c = curl_init($url);
        curl_setopt($c, CURLOPT_POST, true);
        curl_setopt($c, CURLOPT_POSTFIELDS, $data);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        $response = trim(curl_exec($c));
        if(curl_getinfo($c, CURLINFO_HTTP_CODE) != "200") {
            Mage::throwException("Invalid response received from AdRoll. Please contact ".EMAIL_SUPPORT." for help or try again later.");
            }

        // Primitive error handling. Errors will start with E:.
        if(preg_match('/^E:/', $response)) {
            $parts = explode(':', $response, 2);
            Mage::throwException($parts[1]);
            }
        return $response;
        }

    public function create_adroll_account($email, $password, $advertisable_eid, $url, $api_username, $api_key, $logo_src) {
        $endpoint = "https://www.adroll.com/about/magento";
        $data = array(
            'api_username' => $api_username,
            'logo_src'     => $logo_src,
            'api_key'      => $api_key,
            'version'      => Mage::getVersion(),
            'email'        => $email,
            'password'     => $password,
            'url'          => $url,
            'advertisable_eid' => $advertisable_eid,
            );

        $response = $this->call($endpoint, $data);

        if($response && !preg_match('/\S*^<script/i', $response)) {
            Mage::throwException("Invalid pixel code received from AdRoll. Please contact ".EMAIL_SUPPORT." for help.");
            }

        return $response;
        }

    public function generate_api_key($n=32) {
        $random_string = "";
        for($i=0;$i<$n;$i++) $random_string .= chr(rand(0,255));
        return substr(base64_encode($random_string), 0, $n);
        }

    public function get_config($path) {
        return Mage::getStoreConfig($path);
        }

    public function set_config($path, $value) {
        return Mage::getModel('core/config')->saveConfig($path, $value);
        }
    }

