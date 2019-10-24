<?php
namespace api;


use Operations\CustomerOperation;

require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['operations', 'CustomerOperation.php']);

$customerOperation = new CustomerOperation($manager);
$operationResult = $customerOperation->activateAccount();
echo json_encode($operationResult);

?>