<?php

namespace api;

use Queries\QueryBuilder;

require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['queries', 'QueryBuilder.php']);


    $requestData = json_decode(file_get_contents("php://input"));

    $customerId = property_exists($requestData, "customerId") ?  $requestData->customerId : 0;

    $query = QueryBuilder::ownerRoutes();

    $manager->getDataByQuery($query, array(':PK'=>$customerId, ':date' => date('Y-m-d')));
    echo json_encode($manager->operationResult);

?>