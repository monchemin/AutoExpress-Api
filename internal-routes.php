<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Entities\PickupHours;
use Queries\QueryBuilder;

require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['queries', 'QueryBuilder.php']);

$httpMethod = $_SERVER['REQUEST_METHOD'];
$requestData = json_decode(file_get_contents("php://input"));
$uri = explode('/', $_SERVER['REQUEST_URI']);
//print_r($requestData);
//echo QueryBuilder::getInternalRoutes(1, 2, null, null, 5, 9);echo 
//echo date('Y-m-d');
//if($requestData !== null)
//{
    $requestData = $requestData !== null ? $requestData : new \stdClass;
    $fromStation = property_exists($requestData, "fromStation") ? $requestData->fromStation : 0;
    $toStation = property_exists($requestData, "toStation") ? $requestData->toStation : 0;
    $startDate = property_exists($requestData, "startDate") ? $requestData->startDate : null;
    $endDate = property_exists($requestData, "endDate") ? $requestData->endDate : null;
    $fromHour = property_exists($requestData, "fromHour") ? $requestData->fromHour : 0;
    $toHour = property_exists($requestData, "toHour") ?  $requestData->toHour : 0;
    //echo "startdata".$startDate;
    $query = QueryBuilder::getInternalRoutes($fromStation, $toStation, $startDate, $endDate, $fromHour, $toHour);

   // echo $query['sql'];
    //print_r($query['var']);
    $mainResponse = array();
    $manager->getDataByQuery($query['sql'], $query['var']);
    echo json_encode($manager->operationResult);
   // print_r($manager);
  /*  $mainResponse['maindata'] = $manager->operationResult;
    $fzquery = QueryBuilder::getZone($fromStation);
    $manager->getDataByQuery($fzquery['sql'], $fzquery['var']);
    $fzPK = $manager->operationResult->response != null ? $manager->operationResult->response[0]["PK"] : 0;
    $tzquery = QueryBuilder::getZone($toStation);
    $manager->getDataByQuery($tzquery['sql'], $tzquery['var']);
    $tzPK = $manager->operationResult->response != null ? $manager->operationResult->response[0]["PK"] : 0;;
    if($fzPK != 0 && $tzPK != 0) {
        $zoneQuery = QueryBuilder::getInternalRoutesByZone($fzPK, $tzPK, $startDate, $endDate, $fromHour, $toHour);
        $manager->getDataByQuery($zoneQuery['sql'], $zoneQuery['var']);
        $mainResponse['zonedata'] = $manager->operationResult;
    }

    echo json_encode($mainResponse); */
//}
 function getHourDisplayOrder($fromHour, $manager) {
     $manager->getData(PickupHours::class, array("displayOrder"), array("hour"=>$fromHour));
     $result = $manager->operationResult;
    
 }

?>