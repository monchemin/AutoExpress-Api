<?php

namespace Operations;

use Entities\Customers;
use Entities\Routes;
use FactorOperations\FactorManager;
use Queries\QueryBuilder;
use utils\DateUtils;


require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'DateUtils.php']);
require_once join(DIRECTORY_SEPARATOR, ['entities', 'Routes.php']);
require_once join(DIRECTORY_SEPARATOR, ['queries', 'QueryBuilder.php']);

class RouteOperation extends OperationBase
{

    private $message = "";
    private $status = 200;

    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);


    }

    protected function read()
    {

        ($this->pk != 0) ? $this->readOne($this->pk) : $this->manager->getData(Routes::class);
    }

    protected function create()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return $this->operationResult();
        }
        if (!property_exists($this->requestData, "PK") || !$this->customerExists()) {
            $this->message = "No Customer provided";
            $this->status = NO_PROVIDED_CUSTOMER;
            return $this->operationResult();
        }

        $price = $this->requestData->price;
        $place = $this->requestData->place;
        $PK = $this->requestData->PK;
        $departure = $this->requestData->departure;
        $arrival = $this->requestData->arrival;
        $car = $this->requestData->car;
        $hour = $this->requestData->hour;
        $date = $this->requestData->date;

        if (!is_numeric($price) || !is_numeric($place) || !is_numeric($PK) || !DateUtils::validateDate($date) || !is_numeric($departure) || !is_numeric($arrival)
            || !is_numeric($car) || !is_numeric($hour) || $departure == $arrival) {
            $this->message = "Error in provided data";
            $this->status = DATA_ERROR;
            return $this->operationResult();
        }
        try {
            $route = new Routes();
            $route->routeDate = $date;
            $route->routePlace = $place;
            $route->routePrice = $price;
            $route->FK_Driver = $PK;
            $route->FK_Hour = $hour;
            $route->FK_car = $car;
            $route->FK_DepartureStage = $departure;
            $route->FK_ArrivalStage = $arrival;
            $this->manager->insertData($route);

            $this->operationStatus = true;
            if($this->manager->operationResult->lastIndex != null) {
                $query = QueryBuilder::ownerRoutes();
                $this->manager->getDataByQuery($query, array(':PK'=>$PK));
            }
            return $this->operationResult();
        } catch (\Exception $ex) {
            echo "catch";
            $this->message = $ex->getMessage();
            $this->status = DATA_ERROR;
            return $this->operationResult();
        }
    }


    protected function update()
    {
        if ($this->requestData != null && property_exists($this->requestData, "PK")) {
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
        } else {
            $this->operationStatus = 400;
        }

    }

    protected function readOne($pk)
    {
        $this->manager->getData(Routes::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        //if($this->requestData != null && property_exists($this->requestData, "PK")) {
        if ($this->pk != 0) {
            $route = new Routes();

            $route->PK = $this->pk;
            $this->manager->deleteData($route);
        } else {
            $this->operationStatus = 400;
        }
    }

    private function customerExists()
    {
        $exists = false;
        $this->manager->getData(Customers::class, array(), array("PK" => $this->requestData->PK));
        $loginResult = $this->manager->operationResult;
        if ($loginResult->status == 200 && $loginResult->response != null) $exists = true;
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

    protected function operationResult()
    {
        return $this->operationStatus ? $this->manager->operationResult : array("status" => $this->status, "errorMessage" => $this->message);
    }

}

?>