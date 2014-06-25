Gerencianet Módulo Magento
===============

Módulo de integração da Gerencianet com o Magento


Instalação
===============

Faça o download do arquivo compactado referente ao módulo da Gerencianet para Magento;
Substitua a pasta "app" do seu projeto Magento pelas pastas extraídas do arquivo compactado*;
Defina as permissões 755 e 644 para as pastas code e etc, respectivamente;
Atualize o cache da sua loja acessando Sistema > Gerenciador de Cache > Atualizar Cache

Configuração
================

**Token de Acesso**

Neste campo o integrador deve informar o token de integração gerado na conta Gerencianet. Para gerar um token, basta logar na conta Gerencianet, acessar o menu Desenvolvedores > Token de Integração e clicar em "Gerar novo Token".

**Url de Redirecionamento**

Neste campo o integrador deve informar a url para a qual o cliente deverá ser redirecionado após a finalização da compra. Muitos integradores utilizam a página inicial da loja ou uma página específica de agradecimento pela compra.

Notificação
=================

O módulo já vem preparado para receber as notificações do sistema Gerencianet automaticamente, ou seja, não é necessário cadastrar nenhuma url para que o Magento entenda quando uma cobrança sofreu uma alteração de status na Gerencianet.

Tela de Pagamento
=================

A Tela de Pagamento da Gerencianet necessita de algumas informações que não estão presentes na configuração padrão do Magento, como obrigatoriedade de CPF e Data de Nascimento, e campos separados para informações de endereço.

Para que as informações entre as duas plataformas sejam passadas da forma correta, é preciso realizar as seguintes alterações na loja Magento:*

**Obrigatoriedade de Campos**

Acesse Sistema > Configuração > Configurações (Clientes) > Opções de Nome e Endereço, selecione a opção obrigatório para os campos "Exibir Data de Nascimento" e "Exibir CPF/CNPJ" e clique em **Salvar**.

**Separação dos campos de endereço**

Acesse Sistema > Configuração > Configurações (Clientes) > Opções de Nome e Endereço, digite 4 para a opção "Número de Linhas p/ Endereço". Isso possibilitará a utilização de 4 linhas para informar o endereço. O módulo está preparado para receber a primeira linha como sendo a rua, a segunda linha como sendo o número, a terceira linha como complemento e a última como bairro.  

    O módulo funcionará mesmo se essas duas configurações não forem feitas. O que ocorrerá neste caso, é que o cliente precisará informar ou corrigir seus dados pessoais na tela de pagamento da Gerencianet.
