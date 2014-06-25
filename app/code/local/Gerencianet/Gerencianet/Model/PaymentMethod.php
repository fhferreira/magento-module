<?php
/**
* Módulo de Pagamento Gerencianet para Magento
* Cobrança Online
* Model/PaymentMethod.php
*
* NÃO MODIFIQUE OS ARQUIVOS DESTE MÓDULO PARA O BOM FUNCIONAMENTO DO MESMO
* Em caso de dúvidas entre em contato com a Gerencianet. Contatos através do site:
* http://www.gerencianet.com.br
*/

class Gerencianet_Gerencianet_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract{

    const API_URL = 'https://go.gerencianet.com.br/api/pagamento/json';
    const NOTIFICATION_URL = 'https://go.gerencianet.com.br/api/notificacao/json';

    protected $_code = 'gerencianet';
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = false;
    protected $_canVoid = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = true;
    private $order;
    private $helper;
    private $address;

    function __construct(){
        $checkout = Mage::getSingleton('checkout/session');
        $this->order = Mage::getModel('sales/order')->load($checkout->getLastOrderId());
        $this->helper = Mage::getSingleton('Gerencianet_Gerencianet_Helper_Data');
    }

    function makeRequest($url, $data){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        return $result;
    }

    public function getPaymentUrl(){

        $data['token'] = Mage::getStoreConfig('payment/gerencianet/accesstoken');
        $data['dados']['itens'] = $this->getItens();
        $data['dados']['retorno'] = array(
            'identificador' => $this->getOrderId(),
            'url' => Mage::getStoreConfig('payment/gerencianet/returnurl'),
            'urlNotificacao' => Mage::helper("core/url")->getHomeUrl().'gerencianet/payment/notification'
        );
        $data['dados']['cliente'] = $this->getClient();
        $data['dados']['frete'] = $this->getDelivery();
        $data['dados']['desconto'] = $this->getDiscount();

        $data['dados'] = json_encode($data['dados']);
        $resposta = json_decode($this->makeRequest(self::API_URL, $data), true);

        if($resposta["status"] == '2'){
            return $resposta['resposta']['link'];
        }else{
            throw new Exception("Erro ao gerar cobrança");
        }
    }

    public function getOrderPlaceRedirectUrl(){
        return Mage::getUrl($this->getCode() . '/payment/request');
    }

    public function sendNotificationUrl($notification){

        if($notification["notificacao"]){
            $data["token"] = Mage::getStoreConfig('payment/gerencianet/accesstoken');
            $data["dados"]["notificacao"] = $notification["notificacao"];

            $data['dados'] = json_encode($data['dados']);
            $resposta = $this->makeRequest(self::NOTIFICATION_URL, $data);
            return json_decode($resposta, true);
        }else{
            throw new Exception("Nenhum código de notificação disponível.");
        }
    }

    public function updateOrderStatus($charge){

        $obj = Mage::getModel('sales/order')->load($charge['identificador']);

        try {

            $mage_state = $this->helper->getStatusByGerencianet($charge['status']);

            if($mage_state){
                $obj->setState($mage_state, true);
            }
            $obj->save();

            $history = $obj->addStatusHistoryComment('', false);
            $history->setIsCustomerNotified(false);

            return "200";
            
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    private function getItens(){

        $Itens = $this->order->getAllVisibleItems();
        $GerencianetItens = array();

        foreach ($Itens as $item) {
            $GerencianetItens[] = array(
                'itemDescricao' => $item->getName(),
                'itemValor' => (int)($item->getPrice()*100),
                'itemQuantidade' => (int)$item->getQtyOrdered()
            );
        }
        return $GerencianetItens;
    }

    private function getDelivery(){
        return (int)($this->order->getShippingAmount()*100);
    }

    private function getDiscount(){
        return (int)($this->order->getDiscountAmount()*100);
    }

    private function getClient(){

        $this->address = $this->order->getShippingAddress()->getData();
        $client = array();

        /* cpf - optional */
        $cpf = Mage::getModel('customer/customer')->load($this->order["customer_id"])->getTaxvat();
        if($cpf){
            $client['cpf'] = $cpf;
        }

        /* dob - optional */
        $dob = Mage::getModel('customer/customer')->load($this->address["customer_id"])->getDob();
        if($dob){
            $formatted_dob = date("Y-m-d", strtotime($dob));
            $client['nascimento'] = $formatted_dob;
        }

        $client['email'] = $this->order['customer_email'];
        $client['nome'] = $this->order['customer_firstname']." ".$this->order['customer_lastname'];
        $client['logradouro'] = $this->order->getShippingAddress()->getStreet(1);
        $client['numero'] = $this->order->getShippingAddress()->getStreet(2);
        $client['complemento'] = $this->order->getShippingAddress()->getStreet(3);
        $client['bairro'] = $this->order->getShippingAddress()->getStreet(4);
        $client['cep'] = $this->address['postcode'];
        $client['estado'] = $this->address['region'];
        $client['cidade'] = $this->address['city'];
        $client['celular'] = $this->order->getShippingAddress()->getTelephone();

        return $client;
    }

    private function getOrderId(){
        return $this->order->getId();
    }

}
?>