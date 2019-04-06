<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Operations\UserOperation;


require_once 'ApiHeader.php';

$userOperation = new UserOperation($manager);
$operationResult = $userOperation->process();
echo json_encode($operationResult);

?>