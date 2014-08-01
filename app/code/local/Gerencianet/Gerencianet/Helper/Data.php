<?php
/**
 * Módulo de Pagamento Gerencianet para Magento
 * Cobrança Online
 * Helper/Data.php
 *
 * NÃO MODIFIQUE OS ARQUIVOS DESTE MÓDULO PARA O BOM FUNCIONAMENTO DO MESMO
 * Em caso de dúvidas entre em contato com a Gerencianet. Contatos através do site:
 * http://www.gerencianet.com.br
 */

class Gerencianet_Gerencianet_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function getStatusByGerencianet($status){

        $possible_status = array(
            'pago' => 'processing',
            'aguardando' => 'new',
            'vencido' => 'closed',
            'selecionado' => 'payment_review',
            'recusado' => 'canceled',
            'cancelado' => 'canceled',
            'contestado' => 'on_hold',
            'devolvido' => 'closed'
        );

    	return (array_key_exists($status, $possible_status) !== false) ? $possible_status[$status] : false;
    }

}