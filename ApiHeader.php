<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:29
 */
namespace  api;

    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: *");
    header("Content-Type: application/json; charset=UTF-8");

$manager = require_once 'boot.php';
if($manager->operationResult->status === 400) {
    http_response_code(400);
    json_encode($manager->operationResult->erroMessage);
}
?>