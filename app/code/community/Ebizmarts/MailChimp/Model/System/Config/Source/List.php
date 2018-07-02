<?php
/**
 * MailChimp For Magento
 *
 * @category Ebizmarts_MailChimp
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 4/29/16 3:55 PM
 * @file: Account.php
 */
class Ebizmarts_MailChimp_Model_System_Config_Source_List
{

    /**
     * Lists for API key will be stored here
     *
     * @access protected
     * @var array Email lists for given API key
     */
    protected $_lists = null;

    /**
     * Load lists and store on class property
     */
    public function __construct()
    {
        if ($this->_lists == null) {
            $apiKey = Mage::helper('mailchimp')->getConfigValue(Ebizmarts_MailChimp_Model_Config::GENERAL_APIKEY);
            if ($apiKey) {
                try {
                    $version = (string)Mage::getConfig()->getNode('modules/Ebizmarts_MailChimp/version');
                    $api = new Ebizmarts_Mailchimp($apiKey, null, 'Mailchimp4Magento' . $version);
                    $this->_lists = $api->lists->getLists(null, 'lists', null, 100);
                    if (isset($this->_lists['lists']) && count($this->_lists['lists']) == 0) {
                        $apiKeyArray = explode('-', $apiKey);
                        $anchorUrl = 'https://' . $apiKeyArray[1] . '.admin.mailchimp.com/lists/new-list/';
                        $htmlAnchor = '<a target="_blank" href="' . $anchorUrl . '">' . $anchorUrl . '</a>';
                        $message = 'Please create a list at '. $htmlAnchor;
                        Mage::getSingleton('adminhtml/session')->addWarning($message);
                    }
                } catch(Mailchimp_Error $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getFriendlyMessage());
                }
            }
        }
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $lists = array();

        if (is_array($this->_lists)) {
            $lists[] = array('value' => '', 'label' => Mage::helper('mailchimp')->__('--- Select a list ---'));
            foreach ($this->_lists['lists'] as $list) {
                $memberCount = $list['stats']['member_count'];
                $memberText = Mage::helper('mailchimp')->__('members');
                $label = $list['name'] . ' (' . $memberCount . ' ' . $memberText . ')';
                $lists [] = array('value' => $list['id'], 'label' => $label);
            }
        } else {
            $lists [] = array('value' => '', 'label' => Mage::helper('mailchimp')->__('--- No data ---'));
        }

        return $lists;
    }

}