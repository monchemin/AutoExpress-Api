<?php
namespace api;

use Operations\CarOperation;

require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['operations', 'CarOperation.php']);

$customerOperation = new CarOperation($manager);
$operationResult = $customerOperation->getRegisteredCars();
echo json_encode($operationResult);

?>