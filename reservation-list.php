<?php

namespace api;

use Queries\QueryBuilder;

require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['queries', 'QueryBuilder.php']);

$uri = explode('/', $_SERVER['REQUEST_URI']);
$customerId = intval(trim($uri[count($uri)-1],'/'));

$query = QueryBuilder::allReservations();
$manager->getDataByQuery($query, array(":PK" => $customerId, ':date'=> date('Y-m-d')));
$data = $manager->operationResult;
echo json_encode($data);
?>