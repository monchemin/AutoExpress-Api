<?php
namespace Operations;

use Entities\Customers;
use Entities\Drivers;
use Entities\Routes;
use FactorOperations\FactorManager;


class RouteOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

         ($this->pk != 0) ?  $this->readOne($this->pk) : $this->manager->getData(Routes::class);
    }

    protected function create()
    {

        if($this->requestData != null && property_exists($this->requestData, "FK_Driver")){

        if( $this->customerExists()) {
            $route = new Routes();
            //$route->PK = $this->requestData->PK;
            $route->routeDate = property_exists($this->requestData, "routeDate") ? $this->requestData->routeDate : null;
            $route->routePlace = property_exists($this->requestData, "routePlace") ? $this->requestData->routePlace : null;
            $route->routePrice = property_exists($this->requestData, "routePrice") ? $this->requestData->routePrice : null;
            $route->FK_Driver = property_exists($this->requestData, "FK_Driver") ? $this->requestData->FK_Driver : null;
            $route->FK_Hour = property_exists($this->requestData, "FK_Hour") ? $this->requestData->FK_Hour : null;
            $route->FK_DepartureStage = property_exists($this->requestData, "FK_DepartureStage") ? $this->requestData->FK_DepartureStage : null;
            $route->FK_ArrivalStage = property_exists($this->requestData, "FK_ArrivalStage") ? $this->requestData->FK_ArrivalStage : null;
            $this->manager->insertData($route);
        } else {$this->operationStatus = 403;}
    } else {$this->operationStatus = 400;}

    }

    protected function update()
    {
        if( $this->requestData != null && property_exists($this->requestData, "PK")) {
            $route = new Routes();
            $route->PK = $this->requestData->PK;
            if (property_exists($this->requestData, "routeDate")) $route->routeDate = $this->requestData->routeDate;
            if (property_exists($this->requestData, "routePlace")) $route->routePlace = $this->requestData->routePlace;
            if (property_exists($this->requestData, "routePrice")) $route->routePrice = $this->requestData->routePrice;
            if (property_exists($this->requestData, "FK_Driver")) $route->FK_Driver = $this->requestData->FK_Driver;
            if (property_exists($this->requestData, "FK_Hour")) $route->FK_Hour = $this->requestData->FK_Hour;
            if (property_exists($this->requestData, "FK_DepartureStage")) $route->FK_DepartureStage = $this->requestData->FK_DepartureStage;
            if (property_exists($this->requestData, "FK_ArrivalStage")) $route->FK_ArrivalStage = $this->requestData->FK_ArrivalStage;
            $this->manager->changeData($route);
            $this->readOne($route->PK);
            $this->operationStatus = 200;
        } else { $this->operationStatus = 400;}

    }
    protected function readOne($pk) {
         $this->manager->getData(Routes::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        //if($this->requestData != null && property_exists($this->requestData, "PK")) {
            if($this->pk != 0) {
            $route = new Routes();

            $route->PK = $this->pk;
            $this->manager->deleteData($route);
        } else {$this->operationStatus = 400;}
    }
    private function customerExists()
    {
        $exists = false;
        $this->manager->getData(Drivers::class, array(), array("PK" => $this->requestData->FK_Driver));
        $loginResult = $this->manager->operationResult;
        if($loginResult->status == 200 && $loginResult->response != null) $exists = true;
        return $exists;
    }

    public function process()
    {
        
        switch ($this->httpMethod) {
            case "POST" :
                //if($this->requestData != null && property_exists($this->requestData, "PK")) return $this->customerExists();
                $this->create();
                //$this->read();
                break;
            case "PUT" :
                $this->update();
                break;
            case "GET" :
                $this->read();
                break;
            case "DELETE" :
                $this->delete();
                $this->read();
        }
        return $this->operationResult();

    }
   
}
?>