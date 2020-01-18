<?php

require_once join(DIRECTORY_SEPARATOR, ['factor', 'autoload.php']);
require_once join(DIRECTORY_SEPARATOR, ['factor', 'vendor', 'autoload.php']);
require_once join(DIRECTORY_SEPARATOR, ['factor', 'FactorOperations', 'FactorAnnotations.php']);
require_once join(DIRECTORY_SEPARATOR, ['factor', 'FactorOperations', 'FactorManager.php']);
require_once join(DIRECTORY_SEPARATOR, ['factor', 'FactorOperations', 'FactorUtils.php']);
require_once join(DIRECTORY_SEPARATOR, ['factor', 'FactorData', 'FactorMysqlManager.php']);
require_once join(DIRECTORY_SEPARATOR, ['factor', 'FactorData', 'IFactorDbManager.php']);
require_once join(DIRECTORY_SEPARATOR, ['operations', 'OperationBase.php']);
require_once join(DIRECTORY_SEPARATOR, ['..', 'profile.php']);


use FactorOperations\FactorManager;

$dbConnection = array('dbdriver'    => ENGINE,
                        'host'      => HOST,
                        'dbname'    => DBNAME,
                        'user'      => USER,
                        'password'  => PASSWORD,
                        'unix_socket' => '/tmp/mysql.sock'
                      );
$manager = FactorManager::create($dbConnection);

return $manager;
?>