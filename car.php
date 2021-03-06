<?php
namespace api;

use Operations\CarOperation;

require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['operations', 'CarOperation.php']);

$carOperation = new CarOperation($manager);
$operationResult = $carOperation->getRegisteredCars();
echo json_encode($operationResult);

?>