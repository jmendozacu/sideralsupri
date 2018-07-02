<?php

class Aleap_Integrator_OrdersController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() {
        $response = $this->getResponse();
        try {
            $request = $this->getRequest();
            $body = $request->getRawBody();
            $orderJson = Zend_Json::decode($body, Zend_Json::TYPE_OBJECT);
            $storeId = $request->getParam('storeId', null);

            $this->registerOrder($orderJson, $storeId, $response);
        } catch (Exception $e) {
            $messages = Array($e->getMessage());
            $this->reportError($response, $messages, $e);
        }
    }

    private function registerOrder($orderJson, $storeId, $response) {
        $order = new Aleap_Integrator_Model_AdminOrder($orderJson, $storeId);
        $magentoOrder = $order->register();

        if ($order->getError()) {
            $error = $order->getError();
            $this->reportError($response, $error['messages'], $error['exception']);
        } else {
            $response->setHttpResponseCode(201);
            $response->setHeader('Content-type', 'application/json');
            $id = $magentoOrder->getId();
            $response->setBody("{ \"id\": ${id}}");
        }
    }

    private function reportError($response, $messages, $exception) {
        $response->setHttpResponseCode(400);
        $response->setHeader('Content-type', 'application/json');
        $body = $this->builErrorJson($messages, $exception);

        $response->setBody($body);
    }

    private function builErrorJson($messages, $exception) {
        $result = Array(
            'messages' => $messages,
            'trace' => "${exception}"
        );

        return json_encode($result);
    }
}

