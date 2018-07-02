<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to suporte.developer@buscape-inc.com so we can send you a copy immediately.
 *
 * @category   Buscape
 * @package    Buscape_PagamentoDigital
 * @copyright  Copyright (c) 2010 Buscapé Company (http://www.buscapecompany.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Buscape_PagamentoDigital_StandardController extends Mage_Core_Controller_Front_Action 
{

    /**
     * Order instance
     */
    protected $_order;

    
    public function paymentAction()
    {             
       $this->loadLayout();
       $this->renderLayout();       
    }
    
    public function returnAction()
    {
       $this->loadLayout();
       $this->renderLayout();
    }
    
    public function paymentbackendAction() 
    {
        $this->loadLayout();
        $this->renderLayout();

        $hash = explode("/order/", $this->getRequest()->getOriginalRequest()->getRequestUri());
        $hashdecode = explode(":", Mage::getModel('core/encryption')->decrypt($hash[1]));

        $order = Mage::getModel('sales/order')
                ->getCollection()
                ->addFieldToFilter('increment_id', $hashdecode[0])
                ->addFieldToFilter('quote_id', $hashdecode[1])
                ->getFirstItem();

        if ($order) {
            $session = Mage::getSingleton('checkout/session');
            $session->setLastQuoteId($order->getData('quote_id'));
            $session->setLastOrderId($order->getData('entity_id'));
            $session->setLastSuccessQuoteId($order->getData('quote_id'));
            $session->setLastRealOrderId($order->getData('increment_id'));
            $session->setPagamentodigitalQuoteId($order->getData('quote_id'));
            $this->_redirect('pagamentodigital/standard/payment/type/geral');
        } else {
            Mage::getSingleton('checkout/session')->addError('URL informada é inválida!');
            $this->_redirect('checkout/cart');
        }
    }

    public function errorAction()
    {
       $this->loadLayout();
       $this->renderLayout();
    }
    
    /**
     *  Get order
     *
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder() {
        
        if ($this->_order == null) {
            
        }
        
        return $this->_order;
    }

    protected function _expireAjax() {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1', '403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with pagamento digital standard order transaction information
     *
     * @return Buscape_PagamentoDigital_Model_Api
     */
    public function getApi() 
    {
        return Mage::getSingleton('pagamentodigital/'.$this->getRequest()->getParam("type"));
    }

    /**
     * When a customer chooses Bcash on Checkout/Payment page
     *
     */
    public function redirectAction() 
    {
        /*
         * caso precise para identificar o tipo de modelo.
         * Ex: $this->getResponse()->setBody($this->getLayout()->createBlock('pagamentodigital/redirect_{$type}}')->toHtml());
         */
        
        $type = $this->getRequest()->getParam('type', false);
        
        $session = Mage::getSingleton('checkout/session');

        $session->setPagamentodigitalQuoteId($session->getQuoteId());
        
        $this->getResponse()->setHeader("Content-Type", "text/html; charset=ISO-8859-1", true);

        $this->getResponse()->setBody($this->getLayout()->createBlock('pagamentodigital/redirect')->toHtml());

        $session->unsQuoteId();
    }

    /**
     * When a customer cancel payment from pagamento digital.
     */
    public function cancelAction() 
    {
        
        $session = Mage::getSingleton('checkout/session');

        $session->setQuoteId($session->getPagamentoDigitalQuoteId(true));

        // cancel order
        if ($session->getLastRealOrderId()) {

            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());

            if ($order->getId()) {
                $order->cancel()->save();
            }
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * when pagamento_digital returns
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get validation from the return post.
     */
    public function successAction() 
    {
        $_type = $this->getRequest()->getParam('type', false);
               
        //$token = $this->getApi()->getConfigData('token');
        // Email cadastrado no Bcash
        $email = $this->getApi()->getConfigData('emailID'); 

        // Obtenha seu TOKEN entrando no menu Ferramentas do Pagamento Digital 
        $token = $this->getApi()->getConfigData('token');


        $urlPost = "https://www.bcash.com.br/transacao/consulta/"; 

        $dados_post = $this->getRequest()->getPost();

        $transacaoId = utf8_encode($dados_post['transacao_id']);; 

        $idPedidoPd = utf8_encode($dados_post['pedido']);; 

        $tipoRetorno = 1; 

        $codificacao = 1; 

        ob_start(); 
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $urlPost); 
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, array("id_transacao"=>$transacaoId,"id_pedido"=>$idPedidoPd,"tipo_retorno"=>$tipoRetorno,"codificacao"=>$codificacao)); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Basic ".base64_encode($email. ":".$token))); 
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_exec ($ch); 

        /* XML ou Json de retorno */ 
        $resposta = ob_get_contents(); 

        ob_end_clean(); 

        /* Capturando o http code para tratamento dos erros na requisi��o*/ 
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close($ch); 
        
        $xml = simplexml_load_string($resposta);
        
        if($httpCode != "200"){
            $codigo_erro = $xml->codigo;
            $descricao_erro = $xml->descricao;
            if ($codigo_erro == ''){
                $codigo_erro = '0000000';
            }
            if ($descricao_erro == ''){
                $descricao_erro = 'Erro Desconhecido';
            }
            $this->_redirect('pagamentodigital/standard/error', array('_secure' => true , 'descricao' => urlencode(utf8_encode($descricao_erro)),'codigo' => urlencode($codigo_erro)));
        }else{
            
            $pedidoId = str_replace($this->getApi()->getConfigData('prefixo'),'',$xml->id_pedido);
            
            if (isset($xml->cod_status)) {
                $comment .= " - " . $xml->cod_status;
            }

            if (isset($xml->status)) {
                $comment .= " - " . $xml->status;
            }
            $order = Mage::getModel('sales/order');

            $order->loadByIncrementId($pedidoId);
            
            if ($order->getId()) {
			
                $valor_loja = round(floatval($order->getGrandTotal()),2);
                $valor_bcash = round(floatval($xml->valor_original),2)+round(floatval($xml->desconto_programado),2);
                echo "Valor Loja = " . floatval($order->getGrandTotal()) . "<br>";
                echo "Valor Bcash = " . (floatval($xml->valor_original)+floatval($xml->desconto_programado)) . "<br>";
                echo "Valor Loja (Floor) = " . $valor_loja . "<br>";
                echo "Valor Bcash (Floor) = " . $valor_bcash . "<br>";
				
                if ( $valor_loja != $valor_bcash ) {
                    
                    $frase = 'Total pago ao Bcash é diferente do valor original.';
					echo $frase;
                    $order->addStatusToHistory(
                            $order->getStatus(), //continue setting current order status
                            Mage::helper('pagamentodigital')->__($frase), true
                    );

                    $order->sendOrderUpdateEmail(true, $frase);
                } else {
                    $cod_status = $xml->cod_status;
                    switch ($cod_status){
                        case '1':
                                $order->addStatusToHistory(
                                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, Mage::helper('pagamentodigital')->__('Bcash enviou automaticamente o status: %s', $comment)
                                );
                            break;
                        case '3':
                                $items = $order->getAllItems();

                                $thereIsVirtual = false;

                                foreach ($items as $itemId => $item) {
                                    if ($item["is_virtual"] == "1" || $item["is_downloadable"] == "1") {
                                        $thereIsVirtual = true;
                                    }
                                }

                                // what to do - from admin
                                $toInvoice = $this->getApi()->getConfigData('acaopadraovirtual') == "1" ? true : false;

                                if ($thereIsVirtual && !$toInvoice) {

                                    $frase = 'Bcash - Aprovado. Pagamento (fatura) confirmado automaticamente.';

                                    $order->addStatusToHistory(
                                            $order->getStatus(), //continue setting current order status
                                            Mage::helper('pagamentodigital')->__($frase), true
                                    );

                                    $order->sendOrderUpdateEmail(true, $frase);
                                } else {

                                    if (!$order->canInvoice()) {

                                        //when order cannot create invoice, need to have some logic to take care
                                        $order->addStatusToHistory(
                                            $order->getStatus(), //continue setting current order status
                                            Mage::helper('pagamentodigital')->__('Erro ao criar pagamento (fatura).')
                                        );

                                    } else {

                                        //need to save transaction id
                                        $order->getPayment()->setTransactionId($dados_post['id_transacao']);

                                        //need to convert from order into invoice
                                        $invoice = $order->prepareInvoice();

                                        if ($this->getApi()->canCapture()) {
                                            $invoice->register()->capture();
                                        }

                                        Mage::getModel('core/resource_transaction')
                                                ->addObject($invoice)
                                                ->addObject($invoice->getOrder())
                                                ->save();

                                        $frase = 'Pagamento (fatura) ' . $invoice->getIncrementId() . ' foi criado. Bcash - Aprovado. Confirmado automaticamente o pagamento do pedido.';

                                        if ($thereIsVirtual) {

                                            $order->addStatusToHistory(
                                                $order->getStatus(), Mage::helper('pagamentodigital')->__($frase), true
                                            );

                                        } else {

                                            $order->addStatusToHistory(
                                                'processing', //update order status to processing after creating an invoice
                                                Mage::helper('pagamentodigital')->__($frase), true
                                            );
                                        }

                                        $invoice->sendEmail(true, $frase);
                                    }
                                }
                            break;
                        case '4':
                                $order->addStatusToHistory(
                                    Mage_Sales_Model_Order::STATE_COMPLETE, Mage::helper('pagamentodigital')->__('Bcash enviou automaticamente o status: %s', $comment)
                                );
                            break;
                        case '5':
                                $order->addStatusToHistory(
                                    Mage_Sales_Model_Order::STATE_HOLDED, Mage::helper('pagamentodigital')->__('Bcash enviou automaticamente o status: %s', $comment)
                                );
                            break;
                        case '6':
                                $order->addStatusToHistory(
                                    Mage_Sales_Model_Order::STATE_HOLDED, Mage::helper('pagamentodigital')->__('Bcash enviou automaticamente o status: %s', $comment)
                                );
                            break;
                        case '7':
                                $frase = 'Bcash - Cancelado. Pedido cancelado automaticamente (transação foi cancelada, pagamento foi negado, pagamento foi estornado ou ocorreu um chargeback).';

                                $order->addStatusToHistory(
                                    Mage_Sales_Model_Order::STATE_CANCELED, Mage::helper('pagamentodigital')->__($frase), true
                                );

                                $order->sendOrderUpdateEmail(true, $frase);

                                $order->cancel();
                            break;
                        case '8':
                                $order->addStatusToHistory(
                                    Mage_Sales_Model_Order::STATE_HOLDED, Mage::helper('pagamentodigital')->__('Bcash enviou automaticamente o status: %s', $comment)
                                );
                            break;
                    }
                }
                $order->save();
            }
        }
    }

}