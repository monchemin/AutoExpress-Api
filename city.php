<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;



use Operations\CityOperation;


require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['operations', 'CityOperation.php']);

$cityOperation = new CityOperation($manager);
$operationResult = $cityOperation->process();
//http_response_code($operationResult['code']);
echo json_encode($operationResult);
?>