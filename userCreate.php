<?php 
 
    use Test\User;
    use Entities\CarBrand;
  
    $manager = require_once 'boot.php';
    $fistUser = new User();

    $fistUser->setLogin("fafa");
    $fistUser->setPassword("licorne010le");
    $fistUser->setRole("AD");
    //$manager->insertData($fistUser);
    $cb = new CarBrand();
    $cb->brandName = "pontiac";
//$manager->insertData($cb);
   
    echo  json_encode($manager->getData(CarBrand::class));
?>