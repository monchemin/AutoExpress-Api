<?php

namespace api;

use Queries\QueryBuilder;

require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['queries', 'QueryBuilder.php']);

$query = QueryBuilder::getRouteStation();
$manager->getDataByQuery($query, array());
$data = $manager->operationResult;
echo json_encode($data);
?>