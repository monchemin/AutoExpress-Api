<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:28
 */
namespace api;


use Operations\StageOperation;


require_once 'ApiHeader.php';

$stageOperation = new StageOperation($manager);
$operationResult = $stageOperation->process();
echo json_encode($operationResult);

?>