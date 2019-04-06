<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Operations\CarBrandOperation;


require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['operations', 'CarBrandOperation.php']);

$carbrandOperation = new CarBrandOperation($manager);
$operationResult = $carbrandOperation->process();
echo "code: ".$operationResult['code'];
http_response_code($operationResult['code']);
echo json_encode($operationResult);
?>