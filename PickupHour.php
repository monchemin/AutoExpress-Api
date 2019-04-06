<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Operations\PickupHourOperation;
use Operations\ZoneOperation;


require_once 'ApiHeader.php';

$pickupHourOperation = new PickupHourOperation($manager);
$operationResult = $pickupHourOperation->process();
echo json_encode($operationResult);

?>