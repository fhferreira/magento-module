<?php
/**
 * Módulo de Pagamento Gerencianet para Magento
 * Cobrança Online
 * controllers/PaymentController.php
 *
 * NÃO MODIFIQUE OS ARQUIVOS DESTE MÓDULO PARA O BOM FUNCIONAMENTO DO MESMO
 * Em caso de dúvidas entre em contato com a Gerencianet. Contatos através do site:
 * http://www.gerencianet.com.br
 */

class GerenciaNet_Gerencianet_PaymentController extends Mage_Core_Controller_Front_Action {

	public function requestAction(){

		$this->loadLayout();
		$this->getLayout()->getBlock('root')->setTemplate('gerencianet/request.phtml');
		$this->renderLayout();

		try {
			$GerencianetModel = Mage::getSingleton('Gerencianet_Gerencianet_Model_PaymentMethod');
			$this->_redirectUrl($GerencianetModel->getPaymentUrl());         
        } catch (Exception $ex) {
            Mage::log($ex->getMessage());
            Mage::getSingleton('core/session')->addError('Desculpe, infelizmente, houve um erro durante o checkout.');
            $this->_redirectUrl(Mage::getUrl() . 'checkout/onepage');
        }
	}

	public function notificationAction(){

		try {	
			$GerencianetModel = Mage::getSingleton('Gerencianet_Gerencianet_Model_PaymentMethod');

			$data = $GerencianetModel->sendNotificationUrl($_POST);
			if($data['status'] == '2' && isset($charge['identificador'])){
				$order = $GerencianetModel->updateOrderStatus($data['resposta']);
			}

        } catch (Exception $ex) {
            Mage::log($ex->getMessage());
            Mage::getSingleton('core/session')->addError('Desculpe, infelizmente, houve um erro durante o checkout.');
        }

	}

}