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

class Buscape_PagamentoDigital_Block_Redirect extends Mage_Core_Block_Abstract
{
    
    protected function _toHtml()
    {
        $standard = Mage::getModel('pagamentodigital/'.$this->getRequest()->getParam("type"));
                
        $form = new Varien_Data_Form();
        
        $form->setAction($standard->getPagamentoDigitalUrl())
            ->setId('pagamentodigital_payment_checkout')
            ->setName('pagamentodigital_payment_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
            
        foreach ($standard->getCheckoutFormFields() as $field => $value)
        {
            $form->addField($field, 'hidden', array('name' => $field, 'value' => $value));
        }

        $html  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $html .= '<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="pt-BR">';
        $html .= '<head>';
        $html .= '<meta http-equiv="Content-Language" content="pt-br" />';
        $html .= '<meta name="language" content="pt-br" />';
        $html .= '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /></head>';
        $html .= '<body>';
        $html .= '<div align="center">';
        $html .= '<font size="4">Sua compra estê em processo de finalização.<br /><br />';
        $html .= ''.$this->__('Aguarde ... você será redirecionado para o Bcash em <span id="tempo">5</span> segundos.</font>');
        $html .= '<div>';
        $html .= $form->toHtml();
        $html .= '<script type="text/javascript">
                    function setTempo(){
                        var tempo = eval(document.getElementById("tempo").innerHTML);
                        if (tempo - 1 < 0){
                            document.getElementById("pagamentodigital_payment_checkout").submit();
                        }else{
                            document.getElementById("tempo").innerHTML = tempo - 1;
                            setTimeout("setTempo()",1000);
                        }

                    }
                    setTimeout("setTempo()",1000);
                  </script>';
        $html .= '</body></html>';

        return utf8_decode($html);
    }
}