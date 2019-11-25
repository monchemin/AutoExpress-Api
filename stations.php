<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Operations\StationOperation;


require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['operations', 'StationOperation.php']);

$stationOperation = new StationOperation($manager);
$operationResult = $stationOperation->process();
echo json_encode($operationResult);

?>