<?php

require_once join(DIRECTORY_SEPARATOR, ['factor', 'autoload.php']);
require_once join(DIRECTORY_SEPARATOR, ['factor', 'vendor', 'autoload.php']);
require_once join(DIRECTORY_SEPARATOR, ['factor', 'FactorOperations', 'FactorAnnotations.php']);


//use Factor\FactorOperations\FactorManager;
use FactorOperations\FactorManager;

DEFINE("DEV", true);

if(DEV) {
    DEFINE('ENGINE', 'Mysql');
    DEFINE('HOST', 'localhost');
    DEFINE('DBNAME', 'autoexpress');
    DEFINE('USER', 'root');
    DEFINE('PASSWORD', '');
}
else
{
    DEFINE('ENGINE', 'Mysql');
    DEFINE('HOST', '184.175.102.216');
    DEFINE('DBNAME', 'autoexpress');
    DEFINE('USER', 'autoexpress');
    DEFINE('PASSWORD', 'AutoExpress@2019');
}

$dbConnection = array('dbdriver'    => ENGINE,
                        'host'      => HOST,
                        'dbname'    => DBNAME,
                        'user'      => USER,
                        'password'  => PASSWORD
                      );
$manager = FactorManager::create($dbConnection);
//echo json_encode($manager->managerOperationResult);
return $manager;
?>