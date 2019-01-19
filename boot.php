<?php

require_once join(DIRECTORY_SEPARATOR, ['factor', 'autoload.php']);
require_once join(DIRECTORY_SEPARATOR, ['factor', 'vendor', 'autoload.php']);
require_once join(DIRECTORY_SEPARATOR, ['factor', 'FactorOperations', 'FactorAnnotations.php']);


//use Factor\FactorOperations\FactorManager;
use FactorOperations\FactorManager;


$dbConnection = array('dbdriver'    => 'Mysql',
                        'host'      => 'localhost',
                        'dbname'    => 'autoexpress',
                        'user'      => 'root',
                        'password'  => ''
                      );
$manager = FactorManager::create($dbConnection);
return $manager;
?>