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
$pk = intval(trim($uri[count($uri)-1],'/'));

    
    //echo "startdata".$startDate;
    $query = QueryBuilder::commonQuery();
    $query .= "WHERE route.PK = :pk";
    $manager->getDataByQuery($query, array(':pk'=>$pk));
    //echo $query['sql'];
   

    echo json_encode($manager->operationResult);
//}
 function getHourDisplayOrder($fromHour, $manager) {
     $manager->getData(PickupHours::class, array("displayOrder"), array("hour"=>$fromHour));
     $result = $manager->operationResult;
     
 }

?>