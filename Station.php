<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Entities\Stations;
use Operations\StationOperation;


require_once 'ApiHeader.php';

$stationOperation = new StationOperation($manager);
$operationResult = $stationOperation->process();
echo json_encode($operationResult);

?>