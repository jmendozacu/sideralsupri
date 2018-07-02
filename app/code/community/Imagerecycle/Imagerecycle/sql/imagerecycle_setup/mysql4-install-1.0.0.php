<?php

$installer = $this;
$installer->startSetup();
$installer->run("
    CREATE TABLE IF NOT EXISTS `{$installer->getTable('imagerecycle/images')}` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
	   `file` varchar(250) NOT NULL,
	   `md5` varchar(32) NOT NULL,
	   `extension` VARCHAR(5) NOT NULL,
	   `api_id` int(11) NOT NULL,
	   `size_before` int(11) NOT NULL,
	   `size_after` int(11) NOT NULL,	   
	   `date` datetime NOT NULL,
	   PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();