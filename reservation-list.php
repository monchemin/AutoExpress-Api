<?php

namespace api;

use Queries\QueryBuilder;

require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['queries', 'QueryBuilder.php']);

$httpMethod = $_SERVER['REQUEST_METHOD'];
$requestData = json_decode(file_get_contents("php://input"));
$uri = explode('/', $_SERVER['REQUEST_URI']);
$customerId = intval(trim($uri[count($uri)-1],'/'));

$query = QueryBuilder::allReservations();
$manager->getDataByQuery($query, array("PK" => $customerId));
$data = $manager->operationResult;
echo json_encode($data);
?>