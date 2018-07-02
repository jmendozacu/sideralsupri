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
 * @copyright  Copyright (c) 2010 BuscapÃ© Company (http://www.buscapecompany.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Buscape_PagamentoDigital_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    const PAYMENT_TYPE_AUTH = 'AUTHORIZATION';
    const PAYMENT_TYPE_SALE = 'SALE';

    protected $_allowCurrencyCode = array('BRL');
    
    /**
     * Availability options
     */
  

    /**
     * Can be edit order (renew order)
     *
     * @return bool
     */
    public function canEdit()
    {
        return false;
    }
    
    /**
     *  Return Order Place Redirect URL
     *
     *  @return	  string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('pagamentodigital/redirect');
    }
    
     /**
     * Get pagamentodigital session namespace
     *
     * @return Buscape_PagamentoDigital_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('pagamentodigital/session');
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return false;
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock($_formBlockType, $name)
            ->setMethod('pagamentodigital')
            ->setPayment($this->getPayment())
            ->setTemplate('buscape/pagamentodigital/form.phtml');
        return $block;
    }

    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if (!$currency_code){
            $session = Mage::getSingleton('adminhtml/session_quote');
            $currency_code = $session->getQuote()->getBaseCurrencyCode();            
        } 
        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('pagamentodigital')->__('A moeda selecionada ('.$currency_code.') não é compatível com o Bcash'));
        }
        return $this;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
       return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {
        return $this;
    }

    public function canCapture()
    {
        return true;
    }

    public function getNumEndereco($endereco) 
    {
        $numEndereco = '';

        $posSeparador = $this->getPosSeparador($endereco, false);
        if ($posSeparador !== false)
                    $numEndereco = trim(substr($endereco, $posSeparador + 1));

        $posComplemento = $this->getPosSeparador($numEndereco, true);
        
        if ($posComplemento !== false)
            $numEndereco = trim(substr($numEndereco, 0, $posComplemento));

        if ($numEndereco == '')
            $numEndereco = '?';

        return($numEndereco);
    }

    public function getPosSeparador($endereco, $procuraEspaco = false) 
    {
            $posSeparador = strpos($endereco, ',');
            if ($posSeparador === false)
                    $posSeparador = strpos($endereco, '-');

            if ($procuraEspaco)
                    if ($posSeparador === false)
                            $posSeparador = strrpos($endereco, ' ');

            return($posSeparador);
    }

    public function getCheckoutFormFields() 
    {
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        
        if (!$orderIncrementId) {
            $quoteidbackend = $this->getCheckout()->getData('pagamentodigital_quote_id');
            $order = Mage::getModel('sales/order')->loadByAttribute('quote_id', $quoteidbackend);
            $orderIncrementId = $order->getData('increment_id');
        }
        else {
            $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        }
        
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        $isOrderVirtual = $order->getIsVirtual();
        $a = $isOrderVirtual ? $order->getBillingAddress() : $order->getShippingAddress();
        $currency_code = $order->getBaseCurrencyCode();

        list($items, $totals, $discountAmount, $shippingAmount) = Mage::helper('pagamentodigital')->prepareLineItems($order, false, false);

        $postal_code = trim(str_replace("-", "", $a->getPostcode()));
        
        $payment_type = $order->getPayment()->getData('cc_type');
		
	$shipping_description = $order->getData('shipping_description');
        
        if (Mage::getStoreConfig('dev/buscape_connect/pdidplataforma')) {
            
            $pdidplataforma = Mage::getStoreConfig('dev/buscape_connect/pdidplataforma');
            
        } else {
            
            $pdidplataforma = '461';
            
        }
        
        $cpf = '';

        switch(true) {
            case !is_null($order->getData('customer_taxvat')):
                $cpf = $order->getData('customer_taxvat');
            break;
            case !is_null($order->getCpfCnpj()):
                $cpf = $order->getCpfCnpj();
            break;
        }

        function onlyNum($str) {
            return preg_replace("/[^0-9]/", "", $str);
        }

        $cpf = onlyNum($cpf);

        function limpa_string($var, $enc = 'UTF-8')
        {
            $acentos = array(
                'a' => '/À|Á|Â|Ã|Ä|Å/',
                'a' => '/à|á|â|ã|ä|å/',
                'c' => '/Ç/',
                'c' => '/ç/',
                'e' => '/È|É|Ê|Ë/',
                'e' => '/è|é|ê|ë/',
                'i' => '/Ì|Í|Î|Ï/',
                'i' => '/ì|í|î|ï/',
                'n' => '/Ñ/',
                'n' => '/ñ/',
                'o' => '/Ò|Ó|Ô|Õ|Ö/',
                'o' => '/ò|ó|ô|õ|ö/',
                'u' => '/Ù|Ú|Û|Ü/',
                'u' => '/ù|ú|û|ü/',
                'y' => '/Ý/',
                'y' => '/ý|ÿ/',
                'a.' => '/ª/',
                'o.' => '/º/'
            );

            $var = preg_replace($acentos, array_keys($acentos), $var);

            $var = strtolower($var);

            $var = str_replace(" ", "_", $var);

            return $var;
        }

        $uf = $order->getBillingAddress()->getRegion();

        if(strlen($uf) !="2"){

            $uf = limpa_string($uf);

            $_state_sigla = array(
                'acre' => 'AC',
                'alagoas' => 'AL',
                'amapa' => 'AP',
                'amazonas' => 'AM',
                'bahia' => 'BA',
                'ceara' => 'CE',
                'distrito_federal' => 'DF',
                'espirito_santo' => 'ES',
                'goias' => 'GO',
                'maranhao' => 'MA',
                'mato_grosso' => 'MT',
                'mato_grosso_do_sul' => 'MS',
                'minas_gerais' => 'MG',
                'para' => 'PA',
                'paraiba' => 'PB',
                'parana' => 'PR',
                'pernambuco' => 'PE',
                'piaui' => 'PI',
                'rio_de_janeiro' => 'RJ',
                'rio_grande_do_norte' => 'RN',
                'rio_grande_do_sul' => 'RS',
                'rondonia' => 'RO',
                'roraima' => 'RR',
                'santa_catarina' => 'SC',
                'sao_paulo' => 'SP',
                'sergipe' => 'SE',
                'tocatins' => 'TO'
            );

            $uf = $_state_sigla[$uf];
        }

        $numEndereco = onlyNum($a->getStreet(1));

        $endereco = $a->getStreet(1);

        $endereco = str_replace($numEndereco, "", $endereco);

        $endereco = str_replace(",", "", $endereco);

        $endereco = $endereco. ", ". $numEndereco;

        $valorFrete = number_format($order->getBaseShippingAmount(), 2, ".", "");

        $sArr = array(
            'email_loja'        => $this->getConfigData('emailID'),
            'tipo_integracao'   => "PAD",
            'id_pedido'         => $this->getConfigData('prefixo').$orderIncrementId,
            'nome'              => $a->getFirstname() . ' ' . str_replace("(pj)", "", $a->getLastname()),
            'cep'               => $postal_code,
            'endereco'       	=> $endereco,
            //'complemento'     => "",//$a->getStreet(3),
            //'bairro'          => $a->getStreet(2),
            'cidade'            => $a->getCity(),
            'estado'            => $uf,
            'pais'              => $a->getCountry(),
            'telefone'          => substr(str_replace(" ","",str_replace("(","",str_replace(")","",str_replace("-","",$a->getTelephone())))),0,2) . substr(str_replace(" ","",str_replace("-","",$a->getTelephone())),-8),
            'email'             => $a->getEmail(),
            'meio_pagamento'    => $payment_type,
            //'tipo_frete'     => $shipping_description,
            'id_plataforma'   => $pdidplataforma,
            'cpf'               => $cpf,
            'frete'             => $valorFrete
        );
        
        if ($items) {
            $i = 1;
            foreach($items as $item) {
            	if ($item->getAmount() > 0) {

                    $valorProduto = sprintf('%.2f',$item->getAmount());

                    $sArr = array_merge($sArr, array(
                        'produto_descricao_'.$i => $item->getName(),
                        'produto_codigo_'.$i    => $item->getId(),
                        'produto_qtde_'.$i   	=> $item->getQty(),
                        'produto_valor_'.$i  	=> $valorProduto,
                    ));
            	}
		// @todo caso utilize imposto
            	$i++;
            }
            $sArr["desconto"] = is_numeric( $discountAmount ) ? sprintf('%.2f',$discountAmount) : 0;
            $sArr["acrescimo"] = is_numeric( $order->getData("base_tax_amount") ) ? sprintf('%.2f',$order->getData("base_tax_amount")) : 0;
        }        
        
        $totalArr = $order->getBaseGrandTotal();

        $shipping = sprintf('%.2f',$shippingAmount);

    	$sArr = array_merge($sArr, array('frete' => $shipping));

        $sArr = array_merge($sArr, array('url_retorno' => Mage::getUrl('pagamentodigital/standard/return', array('_secure' => true, '_nosid' =>true))));

        if ($this->getConfigData('retorno') == '1') 
        {            
            $sArr = array_merge($sArr, array('url_aviso' => Mage::getUrl('pagamentodigital/standard/success', array('_secure' => true, '_nosid' =>true, 'type' => $this->_standardType))));
        }            
        
        $sArr = array_merge($sArr, array('redirect' => 'true'));
        $sArr = array_merge($sArr, array('redirect_time' => '10'));
       
        $sReq = '';
        
        $rArr = array();
        
        foreach ($sArr as $k=>$v) {
            $value =  str_replace("&","and",$v);
            $rArr[$k] =  $value;
            $sReq .= '&'.$k.'='.$value;
        }

        if ($this->getDebug() && $sReq) {
            $sReq = substr($sReq, 1);
            $debug = Mage::getModel('pagamentodigital/api_debug')
                    ->setApiEndpoint($this->getPagamentoDigitalUrl())
                    ->setRequestBody($sReq)
                    ->save();
        }
        
        if ($this->getConfigData('validacaoDados') == '1') {
            $form_key = Mage::getSingleton('core/session')->getFormKey();
            $rArrAux = $rArr;
            $rArrAux = array_merge($rArrAux, array('form_key' => $form_key));
            ksort($rArrAux);
            $parametros = http_build_query($rArrAux).$this->getConfigData('token');
            $hash = md5($parametros);
            $rArr = array_merge($rArr, array('hash' => $hash));
        }
        
        return $rArr;
    }

    public function getPagamentoDigitalUrl()
    {
         $url = 'https://www.bcash.com.br/checkout/pay/';
         
         return $url;
    }

    public function getDebug()
    {
        return Mage::getStoreConfig('pagamentodigital/wps/debug_flag');
    }
}