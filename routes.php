<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Operations\RouteOperation;



require_once 'ApiHeader.php';

$routeOperation = new RouteOperation($manager);
$operationResult = $routeOperation->process();
http_response_code($operationResult['code']);
echo json_encode($operationResult['data']);

?>