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
$installer = $this;

$lista = array();
$lista[1]['Nome'] = 'Acre';
$lista[1]['UF'] = 'AC';

$lista[2]['Nome'] = 'Alagoas';
$lista[2]['UF'] = 'AL';

$lista[3]['Nome'] = 'Amapá';
$lista[3]['UF'] = 'AP';

$lista[4]['Nome'] = 'Amazonas';
$lista[4]['UF'] = 'AM';

$lista[5]['Nome'] = 'Bahia';
$lista[5]['UF'] = 'BA';

$lista[6]['Nome'] = 'Ceará';
$lista[6]['UF'] = 'CE';

$lista[7]['Nome'] = 'Distrito Federal';
$lista[7]['UF'] = 'DF';

$lista[8]['Nome'] = 'Espírito Santo';
$lista[8]['UF'] = 'ES';

$lista[9]['Nome'] = 'Goiás';
$lista[9]['UF'] = 'GO';

$lista[10]['Nome'] = 'Maranhão';
$lista[10]['UF'] = 'MA';

$lista[11]['Nome'] = 'Mato Grosso';
$lista[11]['UF'] = 'MT';

$lista[12]['Nome'] = 'Mato Grosso do Sul';
$lista[12]['UF'] = 'MS';

$lista[13]['Nome'] = 'Minas Gerais';
$lista[13]['UF'] = 'MG';

$lista[14]['Nome'] = 'Pará';
$lista[14]['UF'] = 'PA';

$lista[15]['Nome'] = 'Paraíba';
$lista[15]['UF'] = 'PB';

$lista[16]['Nome'] = 'Paraná';
$lista[16]['UF'] = 'PR';

$lista[17]['Nome'] = 'Pernambuco';
$lista[17]['UF'] = 'PE';

$lista[18]['Nome'] = 'Piauí';
$lista[18]['UF'] = 'PI';

$lista[19]['Nome'] = 'Rio de Janeiro';
$lista[19]['UF'] = 'RJ';

$lista[20]['Nome'] = 'Rio Grando do Norte';
$lista[20]['UF'] = 'RN';

$lista[21]['Nome'] = 'Rio Grando do Sul';
$lista[21]['UF'] = 'RS';

$lista[22]['Nome'] = 'Rondônia';
$lista[22]['UF'] = 'RO';

$lista[23]['Nome'] = 'Roraima';
$lista[23]['UF'] = 'RR';

$lista[24]['Nome'] = 'Santa Catarina';
$lista[24]['UF'] = 'SC';

$lista[25]['Nome'] = 'São Paulo';
$lista[25]['UF'] = 'SP';

$lista[26]['Nome'] = 'Sergipe';
$lista[26]['UF'] = 'SE';

$lista[27]['Nome'] = 'Tocantins';
$lista[27]['UF'] = 'TO';

$installer->startSetup();

for ($i = 1; $i <= 27; $i++) {
    $query = "INSERT INTO directory_country_region (`country_id`, `code`, `default_name`) SELECT 'BR','{$lista[$i]['UF']}', '{$lista[$i]['Nome']}' FROM DUAL WHERE NOT EXISTS
        (SELECT * FROM directory_country_region
        WHERE code='{$lista[$i]['UF']}' && country_id='BR');";

    $queryEn = "INSERT INTO directory_country_region_name (`locale`, `region_id`, `name`) SELECT 'en_US', LAST_INSERT_ID(), '{$lista[$i]['Nome']}' FROM DUAL WHERE NOT EXISTS
        (SELECT * FROM directory_country_region_name
        WHERE name='{$lista[$i]['Nome']}');";

    $queryPT_BR = "INSERT INTO directory_country_region_name (`locale`, `region_id`, `name`) SELECT 'pt_BR', LAST_INSERT_ID(), '{$lista[$i]['Nome']}' FROM DUAL WHERE NOT EXISTS
        (SELECT * FROM directory_country_region_name
        WHERE name='{$lista[$i]['Nome']}' && locale = 'pt_BR');";

    $installer->run($query);
    $installer->run($queryEn);
    $installer->run($queryPT_BR);


    Mage::log($query, null, query . php, true);
    Mage::log($queryEn, null, query . php, true);
    Mage::log($queryPT_BR, null, query . php, true);
}




$installer->endSetup();