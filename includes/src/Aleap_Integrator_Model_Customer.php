<?php

require_once(dirname(__FILE__) . "/Source/CustomerGroup.php");

class Aleap_Integrator_Model_Customer {
    private $customer;
    private $customerAddress;

    function __construct($order, $storeId)
    {
        $this->order = $order;
        $this->storeId = $storeId;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return mixed
     */
    public function getCustomerAddress()
    {
        return $this->customerAddress;
    }

    /**
     * @param $aleapOrder - Aleap JSON order
     * @param $storeId - Aleap JSON order
     * @return Aleap_Integrator_Model_Customer
     */
    public static function createFromOrder($aleapOrder, $storeId)
    {
        $customer = new Aleap_Integrator_Model_Customer($aleapOrder, $storeId);
        $customer->create();

        return $customer;
    }

    private function create()
    {
        $this->customer = Mage::getModel('customer/customer');
        $this->customer->setWebsiteId($this->getStoreId());
        $names = empty($this->order->customer->last_name) ?
                $this->splitName($this->order->customer->first_name) :
                Array($this->order->customer->first_name, $this->order->customer->last_name);
        $email = $this->order->customer->email;
        $this->customer->loadByEmail($email);
        if (!$this->customer->getId()) {
            $this->setCustomerGroup();
            $this->customer->setEmail($email);
            $this->customer->setFirstname($names[0]);
            $this->customer->setLastname($names[1]);
            $this->customer->setPassword('achieve_leap'); // todo review
            $this->customer->setTaxvat($this->order->customer->document);

            $this->customer->save();
            $this->customer->setConfirmation(null);
            $this->customer->save();
        }

        $this->registerAddress();
    }

    private function setCustomerGroup() {
        $scg = new Aleap_Integrator_Model_Source_CustomerGroup();
        $gid = $scg->getId();
        if ($gid) {
            $this->customer->setGroupId($gid);
        }
    }

    private function getStoreId() {
        return Mage::app()->getStore($this->storeId)->getWebsiteId();
    }

    private function registerAddress()
    {
        $address = $this->order->customer->address;
        $names = $this->splitReceiverName($address->receiver_name, $this->order->customer);
        $addressTexts = $this->buildAddressText($address);
        $addressData = array(
                'firstname' => $names[0],
                'lastname' => $names[1],
                'street' => $addressTexts,
                'city' => $address->city,
                'region' => $this->normalizeState($address->state), // region id?!
                'postcode' => $address->postal_code,
                'country_id' => 'BR',
                'telephone' => $this->order->customer->phone
        );
        $region = Mage::getModel('directory/region')->loadByCode($address->state, 'BR');
        if ($region) {
            $addressData['region_id'] = $region->getRegionId();
        }

        $this->customerAddress = $this->findAddress($this->customer, $addressData);
        if (!$this->customerAddress) {
            $this->customerAddress = Mage::getModel('customer/address');
            /** @var Mage_Customer_Model_Address $customerAddress */
            $this->customerAddress->setData($addressData)
                    ->setCustomerId($this->customer->getId())
                    ->setIsDefaultBilling('1')
                    ->setIsDefaultShipping('1')
                    ->setSaveInAddressBook('0'); // todo was 1

            $this->customerAddress->save();
        }

        return $this->customerAddress;
    }

    public function normalizeState($state) {
        $state_map = array(
                'AC' => 'Acre',
                'AL' => 'Alagoas',
                'AP' => 'Amapá',
                'AM' => 'Amazonas',
                'BA' => 'Bahia',
                'CE' => 'Ceará',
                'DF' => 'Distrito Federal',
                'ES' => 'Espírito Santo',
                'GO' => 'Goiás',
                'MA' => 'Maranhão',
                'MT' => 'Mato Grosso',
                'MS' => 'Mato Grosso do Sul',
                'MG' => 'Minas Gerais',
                'PA' => 'Pará',
                'PB' => 'Paraíba',
                'PR' => 'Paraná',
                'PE' => 'Pernambuco',
                'PI' => 'Piauí',
                'RJ' => 'Rio de Janeiro',
                'RN' => 'Rio Grande do Norte',
                'RS' => 'Rio Grande do Sul',
                'RO' => 'Rondônia',
                'RR' => 'Rorâima',
                'SC' => 'Santa Catarina',
                'SP' => 'São Paulo',
                'SE' => 'Sergipe',
                'TO' => 'Tocantins'
        );

        $use_full_name = Mage::getStoreConfig('general/aleap_integrator/use_full_state_name');
        if ($use_full_name && $state_map[$state]) {
            return $state_map[$state];
        } else {
            return $state;
        }
    }

    private function splitReceiverName($receiverName, $customer) {
        $result = array($customer->first_name, $customer->last_name);

        if (!empty($receiverName) && $receiverName != trim(implode($result, ' '))) {
            $result = $this->splitName($receiverName);
        }

        if (empty($result[1])) {
            $result = $this->splitName($result[0]);
        }

        return $result;
    }

    private function splitName($fullName) {
        $parts = explode(' ', $fullName);
        $firstName = $parts[0];
        $lastName = implode(' ', array_slice($parts, 1));

        if ($lastName == "") {
            $lastName = 'Sem Sobrenome';
        }

        return array($firstName, $lastName);
    }

    private function buildAddressText($address) {
        $helper = Mage::helper('customer/address');
        $lineCount = $helper->getStreetLines();
        if ($lineCount == 4) {
            $result = $address->street . "\n" . $address->number . "\n" .
                    $address->complement . "\n" . $address->neighborhood;
        } else {
            $result = $address->street . ', ' . $address->number;
            if ($address->complement || $address->neighborhood) {
                $result = $result . "\n";
            }

            if ($address->complement) {
                $result = $result . $address->complement;
            }

            if ($address->neighborhood) {
                if ($address->complement) {
                    $result = $result . ' - ';
                }

                $result = $result . $address->neighborhood;
            }
        }

        return $result;
    }

    private function findAddress($customer, $addressData) {
        $keys = array_keys($addressData);
        $found = null;

        foreach ($customer->getAddresses() as $address) {
            if ($this->cmpkeys($keys, $address, $addressData)) {
                return $address;
            }
        }

        return null;
    }

    private function cmpkeys($keys, $a, $b) {
        foreach ($keys as $key) {
            if ($a[$key] != $b[$key]) {
                return false;
            }
        }

        return true;
    }
}