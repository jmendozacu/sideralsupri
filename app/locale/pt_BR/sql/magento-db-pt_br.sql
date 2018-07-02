/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


/*
*
* Tradução Banco de Dados
*
*/

--
-- `cms_page`
--

LOCK TABLES `cms_page` WRITE;
/*!40000 ALTER TABLE `cms_page` DISABLE KEYS */;
UPDATE `cms_page` SET `title`='Sobre Nós' WHERE `title`='About Us';
UPDATE `cms_page` SET `title`='Atendimento ao cliente' WHERE `title`='Customer Service';
UPDATE `cms_page` SET `title`='Ativar Cookies' WHERE `title`='Enable Cookies';
UPDATE `cms_page` SET `title`='Página Inicial' WHERE `title`='Home page';
UPDATE `cms_page` SET `title`='404 Não Encontrado' WHERE `title`='404 Not Found 1';
/*!40000 ALTER TABLE `cms_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- `cms_block`
--
LOCK TABLES `cms_block` WRITE;
/*!40000 ALTER TABLE `cms_block` DISABLE KEYS */;
UPDATE `cms_block` SET `content` = '<ul> <li><a class="snap_shots" href="{{store direct_url="quem-somos"}}">Quem Somos</a></li> <li><a class="snap_shots" href="{{store direct_url="blog"}}">Blog</a></li> <li class="last"><a class="snap_shots" href="{{store direct_url="atendimento-cliente"}}">Atendimento ao cliente</a></li> </ul>' WHERE `cms_block`.`block_id` =5 LIMIT 1 ;
/*!40000 ALTER TABLE `cms_block` ENABLE KEYS */;
UNLOCK TABLES;

--
-- `eav_attribute`
--

-- SELECT frontend_label FROM `eav_attribute` GROUP BY frontend_label ORDER BY frontend_label DESC LIMIT 0 , 300

LOCK TABLES `eav_attribute` WRITE;
/*!40000 ALTER TABLE `eav_attribute` DISABLE KEYS */;
UPDATE `eav_attribute` SET `frontend_label` = 'CEP' WHERE `frontend_label` = 'Zip/Postal Code';
UPDATE `eav_attribute` SET `frontend_label` = 'Peso' WHERE `frontend_label` = 'Weight';
UPDATE `eav_attribute` SET `frontend_label` = 'Visibilidade' WHERE `frontend_label` = 'Visibility';
UPDATE `eav_attribute` SET `frontend_label` = 'URL chave' WHERE `frontend_label` = 'URL key';
UPDATE `eav_attribute` SET `frontend_label` = 'Usuário do Twitter' WHERE `frontend_label` = 'Twitter Username';
UPDATE `eav_attribute` SET `frontend_label` = 'Senha do Twitter' WHERE `frontend_label` = 'Twitter Password';
UPDATE `eav_attribute` SET `frontend_label` = 'Nível de preço' WHERE `frontend_label` = 'Tier Price';
UPDATE `eav_attribute` SET `frontend_label` = 'Rótulo da Miniatura' WHERE `frontend_label` = 'Thumbnail Label';
UPDATE `eav_attribute` SET `frontend_label` = 'Miniatura' WHERE `frontend_label` = 'Thumbnail';
UPDATE `eav_attribute` SET `frontend_label` = 'Telefone' WHERE `frontend_label` = 'Telephone';
UPDATE `eav_attribute` SET `frontend_label` = 'Classe fiscal' WHERE `frontend_label` = 'Tax Class';
UPDATE `eav_attribute` SET `frontend_label` = 'Sufixo' WHERE `frontend_label` = 'Suffix';
UPDATE `eav_attribute` SET `frontend_label` = 'Endereço' WHERE `frontend_label` = 'Street Address';
UPDATE `eav_attribute` SET `frontend_label` = 'Estado/Província' WHERE `frontend_label` = 'State/Province';
UPDATE `eav_attribute` SET `frontend_label` = 'Preço especial para Data' WHERE `frontend_label` = 'Special Price To Date';
UPDATE `eav_attribute` SET `frontend_label` = 'Preço especial da Data' WHERE `frontend_label` = 'Special Price From Date';
UPDATE `eav_attribute` SET `frontend_label` = 'Preço especial' WHERE `frontend_label` = 'Special Price';
UPDATE `eav_attribute` SET `frontend_label` = 'Rótulo da imagem pequena' WHERE `frontend_label` = 'Small Image Label';
UPDATE `eav_attribute` SET `frontend_label` = 'Imagem pequena' WHERE `frontend_label` = 'Small Image';
UPDATE `eav_attribute` SET `frontend_label` = 'Código' WHERE `frontend_label` = 'SKU';
UPDATE `eav_attribute` SET `frontend_label` = 'Breve descrição' WHERE `frontend_label` = 'Short Description';
UPDATE `eav_attribute` SET `frontend_label` = 'Tipo da sapato' WHERE `frontend_label` = 'Shoe Type';
UPDATE `eav_attribute` SET `frontend_label` = 'Tamanho do Calçado' WHERE `frontend_label` = 'Shoe Size';
UPDATE `eav_attribute` SET `frontend_label` = 'Tamanho da camisa' WHERE `frontend_label` = 'Shirt Size';
UPDATE `eav_attribute` SET `frontend_label` = 'Expedição' WHERE `frontend_label` = 'Shipment';
UPDATE `eav_attribute` SET `frontend_label` = 'Forma' WHERE `frontend_label` = 'shape';
UPDATE `eav_attribute` SET `frontend_label` = 'Setar produtos sendo novo para para Data' WHERE `frontend_label` = 'Set Product as New to Date';
UPDATE `eav_attribute` SET `frontend_label` = 'Setar produtos sendo novo para da Data' WHERE `frontend_label` = 'Set Product as New from Date';
UPDATE `eav_attribute` SET `frontend_label` = 'Tamanho do ecrã' WHERE `frontend_label` = 'Screensize';
UPDATE `eav_attribute` SET `frontend_label` = 'Amostras título' WHERE `frontend_label` = 'Samples title';
UPDATE `eav_attribute` SET `frontend_label` = 'Sala' WHERE `frontend_label` = 'Room';
UPDATE `eav_attribute` SET `frontend_label` = 'Tempo de Resposta' WHERE `frontend_label` = 'Response Time';
UPDATE `eav_attribute` SET `frontend_label` = 'Tamanho da RAM' WHERE `frontend_label` = 'RAM SIze';
UPDATE `eav_attribute` SET `frontend_label` = 'Processador' WHERE `frontend_label` = 'Processor';
UPDATE `eav_attribute` SET `frontend_label` = 'Preço Vista' WHERE `frontend_label` = 'Price View';
UPDATE `eav_attribute` SET `frontend_label` = 'Preço' WHERE `frontend_label` = 'Price';
UPDATE `eav_attribute` SET `frontend_label` = 'Prefixo' WHERE `frontend_label` = 'Prefix';
UPDATE `eav_attribute` SET `frontend_label` = 'Posição' WHERE `frontend_label` = 'Position';
UPDATE `eav_attribute` SET `frontend_label` = 'Forma de Pagamento' WHERE `frontend_label` = 'Payment Method';
UPDATE `eav_attribute` SET `frontend_label` = 'Caminho' WHERE `frontend_label` = 'Path';
UPDATE `eav_attribute` SET `frontend_label` = 'Título da página' WHERE `frontend_label` = 'Page Title';
UPDATE `eav_attribute` SET `frontend_label` = 'Layout de página' WHERE `frontend_label` = 'Page Layout';
UPDATE `eav_attribute` SET `frontend_label` = 'Nome' WHERE `frontend_label` = 'Name';
UPDATE `eav_attribute` SET `frontend_label` = 'Modelo' WHERE `frontend_label` = 'Model';
UPDATE `eav_attribute` SET `frontend_label` = 'Preço Mínimo' WHERE `frontend_label` = 'Minimal Price';
UPDATE `eav_attribute` SET `frontend_label` = 'Nome do Meio/Inicial' WHERE `frontend_label` = 'Middle Name/Initial';
UPDATE `eav_attribute` SET `frontend_label` = 'Memória' WHERE `frontend_label` = 'Memory';
UPDATE `eav_attribute` SET `frontend_label` = 'Galeria de media' WHERE `frontend_label` = 'Media Gallery';
UPDATE `eav_attribute` SET `frontend_label` = 'Resolução Máxima' WHERE `frontend_label` = 'Max Resolution';
UPDATE `eav_attribute` SET `frontend_label` = 'Fabricante' WHERE `frontend_label` = 'Manufacturer';
UPDATE `eav_attribute` SET `frontend_label` = 'Título de Links' WHERE `frontend_label` = 'Links title';
UPDATE `eav_attribute` SET `frontend_label` = 'Links podem ser adquiridos separadamente' WHERE `frontend_label` = 'Links can be purchased separately';
UPDATE `eav_attribute` SET `frontend_label` = 'Nível' WHERE `frontend_label` = 'Level';
UPDATE `eav_attribute` SET `frontend_label` = 'Sobrenome' WHERE `frontend_label` = 'Last Name';
UPDATE `eav_attribute` SET `frontend_label` = 'É um produto disponível para compra com o Google Checkout ' WHERE `frontend_label` = 'Is product available for purchase with Google Checkout';
UPDATE `eav_attribute` SET `frontend_label` = 'É confirmado' WHERE `frontend_label` = 'Is confirmed';
UPDATE `eav_attribute` SET `frontend_label` = 'É a âncora' WHERE `frontend_label` = 'Is Anchor';
UPDATE `eav_attribute` SET `frontend_label` = 'É ativo' WHERE `frontend_label` = 'Is Active';
UPDATE `eav_attribute` SET `frontend_label` = 'Em Profundidade' WHERE `frontend_label` = 'In Depth';
UPDATE `eav_attribute` SET `frontend_label` = 'Rótulo da imagem' WHERE `frontend_label` = 'Image Label';
UPDATE `eav_attribute` SET `frontend_label` = 'Galeria de imagem' WHERE `frontend_label` = 'Image Gallery';
UPDATE `eav_attribute` SET `frontend_label` = 'Imagem' WHERE `frontend_label` = 'Image';
UPDATE `eav_attribute` SET `frontend_label` = 'Gênero' WHERE `frontend_label` = 'Gender';
UPDATE `eav_attribute` SET `frontend_label` = 'Nome' WHERE `frontend_label` = 'First Name';
UPDATE `eav_attribute` SET `frontend_label` = 'Acabamento' WHERE `frontend_label` = 'Finish';
UPDATE `eav_attribute` SET `frontend_label` = 'Produtos em destaque' WHERE `frontend_label` = 'Featured product';
UPDATE `eav_attribute` SET `frontend_label` = 'Exibir produtos em' WHERE `frontend_label` = 'Display product options in';
UPDATE `eav_attribute` SET `frontend_label` = 'Modo de exibição' WHERE `frontend_label` = 'Display Mode';
UPDATE `eav_attribute` SET `frontend_label` = 'Desative os métodos de pagamento para este produto' WHERE `frontend_label` = 'Disable payment methods for this product';
UPDATE `eav_attribute` SET `frontend_label` = 'Dimensões' WHERE `frontend_label` = 'Dimensions';
UPDATE `eav_attribute` SET `frontend_label` = 'Descrição' WHERE `frontend_label` = 'Description';
UPDATE `eav_attribute` SET `frontend_label` = 'Endereço de entrega padrão' WHERE `frontend_label` = 'Default Shipping Address';
UPDATE `eav_attribute` SET `frontend_label` = 'Padrão de classificação do produto por anúncio' WHERE `frontend_label` = 'Default Product Listing Sort by';
UPDATE `eav_attribute` SET `frontend_label` = 'Endereço de cobrança padrão' WHERE `frontend_label` = 'Default Billing Address';
UPDATE `eav_attribute` SET `frontend_label` = 'Data de Nascimento' WHERE `frontend_label` = 'Date Of Birth';
UPDATE `eav_attribute` SET `frontend_label` = 'Comentário do Cliente para o pedido' WHERE `frontend_label` = 'Customer Order Comment';
UPDATE `eav_attribute` SET `frontend_label` = 'Cliente do Grupo' WHERE `frontend_label` = 'Customer Group';
UPDATE `eav_attribute` SET `frontend_label` = 'Comentário do cliente' WHERE `frontend_label` = 'Customer Comment';
UPDATE `eav_attribute` SET `frontend_label` = 'Atualização de layout personalizado' WHERE `frontend_label` = 'Custom Layout Update';
UPDATE `eav_attribute` SET `frontend_label` = 'Design personalizado' WHERE `frontend_label` = 'Custom Design';
UPDATE `eav_attribute` SET `frontend_label` = 'Criado em de' WHERE `frontend_label` = 'Created From';
UPDATE `eav_attribute` SET `frontend_label` = 'Criado em' WHERE `frontend_label` = 'Created At';
UPDATE `eav_attribute` SET `frontend_label` = 'Criar em' WHERE `frontend_label` = 'Create In';
UPDATE `eav_attribute` SET `frontend_label` = 'Velocidade da CPU' WHERE `frontend_label` = 'CPU Speed';
UPDATE `eav_attribute` SET `frontend_label` = 'País de origem' WHERE `frontend_label` = 'Country of Origin';
UPDATE `eav_attribute` SET `frontend_label` = 'País' WHERE `frontend_label` = 'Country';
UPDATE `eav_attribute` SET `frontend_label` = 'Custo' WHERE `frontend_label` = 'Cost';
UPDATE `eav_attribute` SET `frontend_label` = 'Relação de contraste' WHERE `frontend_label` = 'Contrast Ratio';
UPDATE `eav_attribute` SET `frontend_label` = 'Empresa' WHERE `frontend_label` = 'Company';
UPDATE `eav_attribute` SET `frontend_label` = 'Cor' WHERE `frontend_label` = 'Color';
UPDATE `eav_attribute` SET `frontend_label` = 'Bloco do CMS' WHERE `frontend_label` = 'CMS Block';
UPDATE `eav_attribute` SET `frontend_label` = 'Cidade' WHERE `frontend_label` = 'City';
UPDATE `eav_attribute` SET `frontend_label` = 'Marca' WHERE `frontend_label` = 'Brand';
UPDATE `eav_attribute` SET `frontend_label` = 'Base Imagem' WHERE `frontend_label` = 'Base Image';
UPDATE `eav_attribute` SET `frontend_label` = 'Ordenar por Produto disponível' WHERE `frontend_label` = 'Available Product Listing Sort by';
UPDATE `eav_attribute` SET `frontend_label` = 'Associado ao site' WHERE `frontend_label` = 'Associate to Website';
UPDATE `eav_attribute` SET `frontend_label` = 'Para aplicar' WHERE `frontend_label` = 'Apply To';
UPDATE `eav_attribute` SET `frontend_label` = 'Permitir Mensagem de Presente' WHERE `frontend_label` = 'Allow Gift Message';
UPDATE `eav_attribute` SET `frontend_label` = 'Ativo para' WHERE `frontend_label` = 'Active To';
UPDATE `eav_attribute` SET `frontend_label` = 'Ativo de' WHERE `frontend_label` = 'Active From';
UPDATE `eav_attribute` SET `frontend_label` = 'Informação de ativação' WHERE `frontend_label` = 'Activation Information';
/*!40000 ALTER TABLE `eav_attribute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- `eav_attribute_option_value`
--

LOCK TABLES `eav_attribute_option_value` WRITE;
/*!40000 ALTER TABLE `eav_attribute_option_value` DISABLE KEYS */;
UPDATE `eav_attribute_option_value` SET `value` = 'Masculino' WHERE `value` = 'Male';
UPDATE `eav_attribute_option_value` SET `value` = 'Feminino' WHERE `value` = 'Female';
/*!40000 ALTER TABLE `eav_attribute_option_value` ENABLE KEYS */;
UNLOCK TABLES;

/* customer_group */
UPDATE `customer_group` SET `customer_group_code`='Visitante' WHERE `customer_group_code`='NOT LOGGED IN';
UPDATE `customer_group` SET `customer_group_code`='Comum' WHERE `customer_group_code`='General';
UPDATE `customer_group` SET `customer_group_code`='Atacado' WHERE `customer_group_code`='Wholesale';
UPDATE `customer_group` SET `customer_group_code`='Revenda' WHERE `customer_group_code`='Retailer';
 
/* dataflow_profile */
UPDATE `dataflow_profile` SET `name`='Exportar Todos Produtos' WHERE `name`='Export All Products';
UPDATE `dataflow_profile` SET `name`='Exportar Estoque Produtos' WHERE `name`='Export Product Stocks';
UPDATE `dataflow_profile` SET `name`='Importar Todos Produtos' WHERE `name`='Import All Products';
UPDATE `dataflow_profile` SET `name`='Importar Estoque Produtos' WHERE `name`='Import Product Stocks';
UPDATE `dataflow_profile` SET `name`='Exportar Clientes' WHERE `name`='Export Customers';
UPDATE `dataflow_profile` SET `name`='Importar Clientes' WHERE `name`='Import Customers';

/* eav_form_fieldset_label */
UPDATE `eav_form_fieldset_label` SET `label`='Informações Pessoais' WHERE `label`='Personal Information';
UPDATE `eav_form_fieldset_label` SET `label`='Informações de Conta' WHERE `label`='Account Information';
UPDATE `eav_form_fieldset_label` SET `label`='Informações de Contato' WHERE `label`='Contact Information';
UPDATE `eav_form_fieldset_label` SET `label`='Endereços' WHERE `label`='Address';
UPDATE `eav_form_fieldset_label` SET `label`='Informações de Endereços' WHERE `label`='Address Information';
 
/* poll */
UPDATE `poll` SET `poll_title`='Qual sua cor favorita' WHERE `poll_title`='What is your favorite color';
 
/* poll_answer */
UPDATE `poll_answer` SET `answer_title`='Verde' WHERE `answer_title`='Green';
UPDATE `poll_answer` SET `answer_title`='Vermelho' WHERE `answer_title`='Red';
UPDATE `poll_answer` SET `answer_title`='Preto' WHERE `answer_title`='Black';
UPDATE `poll_answer` SET `answer_title`='Magenta' WHERE `answer_title`='Magenta';
 
/* rating */
UPDATE `rating` SET `rating_code`='Qualidade' WHERE `rating_code`='Quality';
UPDATE `rating` SET `rating_code`='Pontuação' WHERE `rating_code`='Value';
UPDATE `rating` SET `rating_code`='Preço' WHERE `rating_code`='Price';



/*
*
* As ocorrencias abaixo comentadas não devem ser alteradas pois as referencias nativas é usadas nos código fontes 
*
*/

/* review_status */
/* UPDATE `review_status` SET `status_code`='Aprovado' WHERE `status_code`='Approved';
UPDATE `review_status` SET `status_code`='Pendente' WHERE `status_code`='Pending';
UPDATE `review_status` SET `status_code`='Reprovado' WHERE `status_code`='Not Approved'; */

/* sales_order_status */
/*UPDATE `sales_order_status` SET `label`='Cancelado' WHERE `label`='Canceled';
UPDATE `sales_order_status` SET `label`='Fechado' WHERE `label`='Closed';
UPDATE `sales_order_status` SET `label`='Completo' WHERE `label`='Complete';
UPDATE `sales_order_status` SET `label`='Suspeita de Fraude' WHERE `label`='Suspected Fraud';
UPDATE `sales_order_status` SET `label`='Segurado' WHERE `label`='On Hold';
UPDATE `sales_order_status` SET `label`='Análise de Pagamento' WHERE `label`='Payment Review';
UPDATE `sales_order_status` SET `label`='Pendente' WHERE `label`='Pending';
UPDATE `sales_order_status` SET `label`='Pagamento Pendente' WHERE `label`='Pending Payment';
UPDATE `sales_order_status` SET `label`='PayPal Pendente' WHERE `label`='Pending PayPal';
UPDATE `sales_order_status` SET `label`='Processando' WHERE `label`='Processing';*/



/*
*
* O Script abaixo tem a função de adicionar o suporte a estados do brasil no Magento
* Obs. Percebe que é informado uma sequencia numérica em "INSERT INTO `directory_country_region_name`", baseado em uma instalação crua do Magento, sugiro analisar o seu caso e efetuar as devidas correções
*
*/

/* directory_country_region */
INSERT INTO `directory_country_region` (`country_id`, `code`, `default_name`) VALUES
	('BR', 'AC', 'Acre'),
	('BR', 'AL', 'Alagoas'),
	('BR', 'AP', 'Amapá'),
	('BR', 'AM', 'Amazonas'),
	('BR', 'BA', 'Bahia'),
	('BR', 'CE', 'Ceará'),
	('BR', 'ES', 'Espírito Santo'),
	('BR', 'GO', 'Goiás'),
	('BR', 'MA', 'Maranhão'),
	('BR', 'MT', 'Mato Grosso'),
	('BR', 'MS', 'Mato Grosso do Sul'),
	('BR', 'MG', 'Minas Gerais'),
	('BR', 'PA', 'Pará'),
	('BR', 'PB', 'Paraíba'),
	('BR', 'PR', 'Paraná'),
	('BR', 'PE', 'Pernambuco'),
	('BR', 'PI', 'Piauí'),
	('BR', 'RJ', 'Rio de Janeiro'),
	('BR', 'RN', 'Rio Grande do Norte'),
	('BR', 'RS', 'Rio Grande do Sul'),
	('BR', 'RO', 'Rondônia'),
	('BR', 'RR', 'Roraima'),
	('BR', 'SC', 'Santa Catarina'),
	('BR', 'SP', 'São Paulo'),
	('BR', 'SE', 'Sergipe'),
	('BR', 'TO', 'Tocantins'),
	('BR', 'DF', 'Distrito Federal');

/* directory_country_region_name */
INSERT INTO `directory_country_region_name` (`locale`, `region_id`, `name`) VALUES
	('pt_BR', 485, 'Acre'),
	('pt_BR', 486, 'Alagoas'),
	('pt_BR', 487, 'Amapá'),
	('pt_BR', 488, 'Amazonas'),
	('pt_BR', 489, 'Bahia'),
	('pt_BR', 490, 'Ceará'),
	('pt_BR', 491, 'Espírito Santo'),
	('pt_BR', 492, 'Goiás'),
	('pt_BR', 493, 'Maranhão'),
	('pt_BR', 494, 'Mato Grosso'),
	('pt_BR', 495, 'Mato Grosso do Sul'),
	('pt_BR', 496, 'Minas Gerais'),
	('pt_BR', 497, 'Pará'),
	('pt_BR', 498, 'Paraíba'),
	('pt_BR', 499, 'Paraná'),
	('pt_BR', 500, 'Pernambuco'),
	('pt_BR', 501, 'Piauí'),
	('pt_BR', 502, 'Rio de Janeiro'),
	('pt_BR', 503, 'Rio Grande do Norte'),
	('pt_BR', 504, 'Rio Grande do Sul'),
	('pt_BR', 505, 'Rondônia'),
	('pt_BR', 506, 'Roraima'),
	('pt_BR', 507, 'Santa Catarina'),
	('pt_BR', 508, 'São Paulo'),
	('pt_BR', 509, 'Sergipe'),
	('pt_BR', 510, 'Tocantins'),
	('pt_BR', 511, 'Distrito Federal');

