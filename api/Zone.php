<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Operations\ZoneOperation;


require_once 'ApiHeader.php';

$zoneOperation = new ZoneOperation($manager);
$operationResult = $zoneOperation->process();
echo json_encode($operationResult);

?>