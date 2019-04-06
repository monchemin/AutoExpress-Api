<?php
namespace api;


use Operations\UserOperation;

require_once 'ApiHeader.php';

$userOperation = new UserOperation($manager);
$operationResult = $userOperation->login();
http_response_code($operationResult['code']);
echo json_encode($operationResult);

?>