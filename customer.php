<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Operations\CustomerOperation;

require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['operations', 'CustomerOperation.php']);

$customerOperation = new CustomerOperation($manager);
$operationResult = $customerOperation->process();
echo json_encode($operationResult);

?>