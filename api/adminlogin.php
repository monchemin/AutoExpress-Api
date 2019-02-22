<?php
namespace api;


use Operations\UserOperation;

require_once 'ApiHeader.php';

$userOperation = new UserOperation($manager);
$operationResult = $userOperation->login();
echo json_encode($operationResult);

?>