<?php

namespace api;

use Queries\QueryBuilder;

require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['queries', 'QueryBuilder.php']);


$requestData = json_decode(file_get_contents("php://input"));

    $PK = property_exists($requestData, "PK") ?  $requestData->PK : 0;

    $query = QueryBuilder::ownerRoutes();

    $manager->getDataByQuery($query, array(':PK'=>$PK));
    echo json_encode($manager->operationResult);

?>