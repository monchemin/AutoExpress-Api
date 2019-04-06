<?php

namespace api;


use Operations\CarColorOperation;


require_once 'ApiHeader.php';

$carColorOperation = new CarColorOperation($manager);
$operationResult = $carColorOperation->process();
http_response_code($operationResult['code']);
echo json_encode($operationResult);
?>