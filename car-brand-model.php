<?php

namespace api;

use Queries\CarQueries;


require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['queries', 'CarQueries.php']);


$query = CarQueries::brandModel();
$manager->getDataByQuery($query, array());
$data = $manager->operationResult;
echo json_encode($data);
?>