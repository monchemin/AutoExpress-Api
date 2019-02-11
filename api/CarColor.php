<?php

namespace api;


use Operations\CarColorOperation;


require_once 'ApiHeader.php';

$carColorOperation = new CarColorOperation($manager);
$operationResult = $carColorOperation->process();
echo json_encode($operationResult);

/* if($operationResult !== null ) {
   
}
else echo json_encode(array("error"=>"message")); */
//echo json_encode($carbrandOperation->process($manager));

?>