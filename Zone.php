<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Operations\QueryBuilder;
use Operations\ZoneOperation;


require_once 'ApiHeader.php';

$zoneOperation = new ZoneOperation($manager);
$operationResult = $zoneOperation->process();
//echo QueryBuilder::getInternalRoutes(1, 2, null, null, 5, 9);
echo json_encode($operationResult);

?>