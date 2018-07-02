<?PHP

/**
 * Script manutenção magento
 *
 * 
 @version    1.0
 * 
 @author     Mucci Estúdio <giaco@mucciestudio.com.br>
 * @copyright  Copyright (c) 2016 Mucci Estúdio. 
 *@link       http://www.mucciestudio.com.br Mucci Estúdio
 
 */


switch ($_GET['comando']) {
    
    case 'logs':
        limpar_log_tables();       
        break;
    
    case 'pastas':        
        limpar_log_diretorios();        
        break;        
}

function limpar_log_tables()
{
    
    $xml = simplexml_load_file('./app/etc/local.xml', NULL, LIBXML_NOCDATA);
     
    if (is_object($xml)) {        
        $db['host'] = $xml->global->resources->default_setup->connection->host;        
        $db['name'] = $xml->global->resources->default_setup->connection->dbname;        
        $db['user'] = $xml->global->resources->default_setup->connection->username;        
        $db['pass'] = $xml->global->resources->default_setup->connection->password;       
        $db['pref'] = $xml->global->resources->db->table_prefix;
        
        
        
        $tables = array(
            'aw_core_logger',            
            'dataflow_batch_export',            
            'dataflow_batch_import',            
            'log_customer',            
            'log_quote',            
            'log_summary',            
            'log_summary_type',            
            'log_url',            
            'log_url_info',            
            'log_visitor',            
            'log_visitor_info',            
            'log_visitor_online',            
            'index_event',            
            'report_event',            
            'report_viewed_product_index',            
            'report_compared_product_index',
            'catalog_compare_item',            
            'catalogindex_aggregation',            
            'catalogindex_aggregation_tag',            
            'catalogindex_aggregation_to_tag'
        );
        
             
        mysql_connect($db['host'], $db['user'], $db['pass']) or die(mysql_error());     
        
        mysql_select_db($db['name']) or die(mysql_error());          
        
        foreach ($tables as $table) {   
	        @mysql_query('TRUNCATE `' . $db['pref'] . $table . '`');
        }
        
        
    } else {      
	      
        exit('Não foi possivel carregar o arquivo local.xml');
        
    }
    
    
}



function limpar_log_diretorios()
{
    
    
    $dirs = array(
        'instalarmodulos/.cache/',        
        'instalarmodulos/pearlib/cache/*',        
        'instalarmodulos/pearlib/download/*',        
        'media/css/',        
        'media/css_secure/',        
        'media/import/',        
        'media/js/',        
        'var/cache/',        
        'var/locks/',        
        'var/log/',        
        'var/report/',        
        'var/session/',        
        'var/tmp/'
    );
    
    
    foreach ($dirs as $dir) {        
        
        exec('rm -rf ' . $dir);       
        
    }   
    
}