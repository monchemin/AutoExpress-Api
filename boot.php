<?php

require_once join(DIRECTORY_SEPARATOR, ['factor', 'autoload.php']);
require_once join(DIRECTORY_SEPARATOR, ['factor', 'vendor', 'autoload.php']);
require_once join(DIRECTORY_SEPARATOR, ['factor', 'FactorOperations', 'FactorAnnotations.php']);

//echo join(DIRECTORY_SEPARATOR, ['factor', 'FactorOperations', 'FactorAnnotations.php']);
//use Factor\FactorOperations\FactorManager;
use FactorOperations\FactorManager;

DEFINE("DEV", FALSE);

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
    DEFINE('HOST', 'mysql1-p2.ezhostingserver.com'); //216.15.188.161 mysql1-p2.ezhostingserver.com
    DEFINE('DBNAME', 'autoexpress');
    DEFINE('USER', 'autoexpress');
    DEFINE('PASSWORD', 'Autoexpress@123');
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