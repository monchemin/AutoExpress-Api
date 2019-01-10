<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Operations\CarModelOperation;


require_once 'ApiHeader.php';

$carModelOperation = new CarModelOperation($manager);
$operationResult = $carModelOperation->process();
echo json_encode($operationResult);

?>