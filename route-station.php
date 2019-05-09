<?php

namespace api;

use Queries\QueryBuilder;

require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['queries', 'QueryBuilder.php']);

$query = QueryBuilder::getRouteStation();
$manager->getDataByQuery($query, array());
$data = $manager->operationResult;
$picker = array();
  
    foreach($data->response as $value) {
         $picker[] = array('value'=>$value['PK'], 'label'=>$value['stationName']);
    }
    $data->picker = $picker;
    echo json_encode($data);
?>