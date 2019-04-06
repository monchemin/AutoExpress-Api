<?php
namespace api;


use Operations\CustomerOperation;

require_once 'ApiHeader.php';

$customerOperation = new CustomerOperation($manager);
$operationResult = $customerOperation->login();
echo json_encode($operationResult);

?>