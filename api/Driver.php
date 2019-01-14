<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Operations\DriverOperation;

require_once 'ApiHeader.php';

$driverOperation = new DriverOperation($manager);
$operationResult = $driverOperation->process();
echo json_encode($operationResult);

?>