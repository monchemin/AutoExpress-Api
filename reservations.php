<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Operations\ReservationOperation;

require_once 'ApiHeader.php';
require_once join(DIRECTORY_SEPARATOR, ['operations', 'ReservationOperation.php']);
//echo "reser";
$reservationOperation = new ReservationOperation($manager);
$operationResult = $reservationOperation->process();
echo json_encode($operationResult);
//echo json_encode(array("1"=>"un"));

?>