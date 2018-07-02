<?php

class Aleap_Integrator_Model_Customer {
    private $customer;
    private $customerAddress;

    function __construct($order)
    {
        $this->order = $order;
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
     * @return Aleap_Integrator_Model_Customer
     */
    public static function createFromOrder($aleapOrder)
    {
        $customer = new Aleap_Integrator_Model_Customer($aleapOrder);
        $customer->create();

        return $customer;
    }

    private function create()
    {
        $this->customer = Mage::getModel('customer/customer');
        $this->customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $names = empty($this->order->customer->last_name) ?
                $this->splitName($this->order->customer->first_name) :
                Array($this->order->customer->first_name, $this->order->customer->last_name);
        $email = $this->order->customer->email;
        $this->customer->loadByEmail($email);
        if (!$this->customer->getId()) {
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
                'region' => $address->state, // region id?!
                'postcode' => $address->postal_code,
                'country_id' => 'BR',
                'telephone' => $this->order->customer->phone
        );

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