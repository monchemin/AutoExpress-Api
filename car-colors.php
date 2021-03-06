<?php

namespace api;


use Operations\CarColorOperation;


require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['operations', 'CarColorOperation.php']);

$carColorOperation = new CarColorOperation($manager);
$operationResult = $carColorOperation->process();
echo json_encode($operationResult);
?>