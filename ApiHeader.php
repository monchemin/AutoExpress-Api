<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 23/12/2018
 * Time: 22:29
 */
namespace  api;
//error_reporting(E_ERROR | E_PARSE);
   /* header("Access-Control-Allow-Origin: http://localhost:3000");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: *");
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: token, Content-Type');
        header('Access-Control-Max-Age: 1728000');
        header('Content-Length: 0');
        header('Content-Type: text/plain');
        die();
    }

    header('Access-Control-Allow-Origin: *');
   
    header("Content-Type: application/json; charset=UTF-8");
  */ 
  /* 
if($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
      // Tell the Client we support invocations from arunranga.com and 
      // that this preflight holds good for only 20 days
    
      if($_SERVER['HTTP_ORIGIN'] == "http//test.toncopilote.com") {
        header('Access-Control-Allow-Origin: http://test.toncopilote.com');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: X-PINGARUNER');
        header('Access-Control-Max-Age: 1728000');
        header("Content-Length: 0");
        header("Content-Type: text/plain");
        //exit(0);
      } else {
        header("HTTP/1.1 403 Access Forbidden");
        header("Content-Type: text/plain");
        echo "You cannot repeat this request";
      }
    
    } else {
      // Handle POST by first getting the XML POST blob, 
      // and then doing something to it, and then sending results to the client
     
      if($_SERVER['HTTP_ORIGIN'] == "http://test.toncopilote.com") {
        $manager = require_once 'boot.php';
        if($manager->operationResult->status === 400) {
                 http_response_code(400);
                json_encode($manager->operationResult->erroMessage);
            }
             
        header('Access-Control-Allow-Origin: http://test.toncopilote.com');
        header("Content-Type: application/json; charset=UTF-8");
       
      } else {
        die("Request not Allow from your origin");
      }
    } */
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
    header("Content-Type: application/json; charset=UTF-8");
    $manager = require_once 'boot.php';
    if($manager->operationResult->status === 400) {
             http_response_code(400);
            json_encode($manager->operationResult->erroMessage);
        } 
    ?>