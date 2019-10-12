<?php
namespace api;

use Operations\RouteOperation;

require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['operations', 'RouteOperation.php']);

$routeOperation = new RouteOperation($manager);
$operationResult = $routeOperation->process();
echo json_encode($operationResult);

?>