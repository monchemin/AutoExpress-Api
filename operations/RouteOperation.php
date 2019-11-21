<?php

namespace Operations;

use Entities\Customers;
use Entities\Routes;
use FactorOperations\FactorManager;
use Queries\QueryBuilder;
use utils\DateUtils;
use utils\MailUtils;


require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'DateUtils.php']);
require_once join(DIRECTORY_SEPARATOR, ['entities', 'Routes.php']);
require_once join(DIRECTORY_SEPARATOR, ['queries', 'QueryBuilder.php']);
require_once 'CustomerOperation.php';

class RouteOperation extends OperationBase
{

    private $message = "authorisation required";
    private $status = NO_AUTHORIZATION;
    private static $PLACE_LIMIT = 3;

    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);


    }

    protected function read()
    {

        // ($this->pk != 0) ? $this->readOne($this->pk) : $this->manager->getData(Routes::class);
    }

    protected function create()
    {
        if ($this->requestData == null || !property_exists($this->requestData, "customerId") || !is_numeric($this->requestData->customerId)) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return $this->operationResult();
        }

        $customerId = $this->requestData->customerId;
        $customerOp = new CustomerOperation($this->manager);
        $customer = $customerOp->readOne($customerId);
        if ($customer == null) {
            $this->message = "No Customer";
            $this->status = NO_PROVIDED_CUSTOMER;
            return $this->operationResult();
        }

        if (!$customer->active) {
            $this->message = "No active account";
            $this->status = NO_ACTIVE_ACCOUNT;
            return $this->operationResult();
        }

        if ($customer->drivingNumber == null) {
            $this->message = "Your Account is not driving account";
            $this->status = NO_DRIVING_ACCOUNT;
            return $this->operationResult();
        }

        $price = $this->requestData->price;
        $place = $this->requestData->place;
        $departure = $this->requestData->departure;
        $arrival = $this->requestData->arrival;
        $car = $this->requestData->car;
        $hour = $this->requestData->hour;
        $date = $this->requestData->date;

        if (!is_numeric($price) || !is_numeric($place) || ($place > self::$PLACE_LIMIT) || !DateUtils::isValidDate($date) || !is_numeric($departure) || !is_numeric($arrival)
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
            $route->FK_Driver = $customerId;
            $route->FK_Hour = $hour;
            $route->FK_car = $car;
            $route->FK_DepartureStage = $departure;
            $route->FK_ArrivalStage = $arrival;
            $route->createdAt = date("Y-m-d H:i:s");
            $this->manager->insertData($route);

            $this->operationStatus = true;
            if ($this->manager->operationResult->lastIndex != null) {
                $query = QueryBuilder::ownerRoutes();
                $this->manager->getDataByQuery($query, array(':PK' => $customerId, ':date' => date("Y-m-d")));
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
        $this->manager->getData(Routes::class, array(), array('PK' => $pk));
    }

    protected function delete()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return;
        }
        $customerId = property_exists($this->requestData, 'customerId') ? $this->requestData->customerId : null;
        $routeId = property_exists($this->requestData, 'routeId') ? $this->requestData->routeId : null;
        $language = property_exists($this->requestData, "language") ? $this->requestData->language : "fr";
        if (!is_numeric($customerId) || !is_numeric($routeId)) {
            $this->message = "Data Error";
            $this->status = DATA_ERROR;
        }

        $query = QueryBuilder::deleteRouteReservation();

        $this->manager->getDataByQuery($query, array(':PK' => $routeId));
        $result = $this->manager->operationResult;
        if ($result->status == 200 && count($result->response) == 1 && $result->response[0]['driver'] == $customerId) {
            $reservationIds = explode(',', $result->response[0]['reservationIds']);
            $reservationArray = array(
                'query' => QueryBuilder::updateReservations(count($reservationIds)),
                'vars' => $reservationIds
            );
            $routeArray = array(
                'query' => QueryBuilder::routeDelete(),
                "vars" => array(':PK' => $routeId)
            );
            $this->manager->doIntransaction(array($reservationArray, $routeArray));
            if ($this->manager->operationResult->status == 200) {
                $this->operationStatus = true;
                MailUtils::sendRouteDeleted(explode(',', $result->response[0]['mails']), $language);

            } else {
                $this->message = "Data Error";
                $this->status = SQL_ERROR;
            }

        } else {

            $this->manager->getData(Routes::class, array('FK_driver'), array('PK' => $routeId));
            $result = $this->manager->operationResult;
            if ($result->status == 200 && count($result->response) == 1 && $result->response[0]->FK_Driver == $customerId) {
                $route = new Routes();
                $route->PK = $routeId;
                $route->deletedAt = date("Y-m-d H:i:s");
                $this->manager->changeData($route);
                if ($this->manager->operationResult->status == 200) {
                    $this->operationStatus = true;
                } else {
                    $this->message = "Data Error";
                    $this->status = SQL_ERROR;
                }
            } else {
                $this->message = "Error in provided data";
                $this->status = DATA_ERROR;
            }
        }
    }

    public function routeReservations()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return;
        }
        $customerId = property_exists($this->requestData, 'customerId') ? $this->requestData->customerId : null;
        $routeId = property_exists($this->requestData, 'routeId') ? $this->requestData->routeId : null;
        if (!is_numeric($customerId) || !is_numeric($routeId)) {
            $this->message = "Data Error";
            $this->status = DATA_ERROR;
            return;
        }

        $query = QueryBuilder::routeReservations();
        $this->manager->getDataByQuery($query, array(':PK' => $routeId, ':driver' => $customerId));
        $this->operationStatus = true;
    }

    private function getCustomer()
    {
        $this->manager->getData(Customers::class, array(), array("PK" => $this->requestData->PK));
        $loginResult = $this->manager->operationResult;
        return ($loginResult->status == 200 && $loginResult->response != null) ? $loginResult->response[0] : null;
    }

    public function process()
    {
        switch ($this->httpMethod) {
            case "POST" :
                $this->create();
                break;
            case "PUT" :
                $this->update();
                break;
            case "GET" :
                $this->read();
                break;
            case "DELETE" :
                $this->delete();
            // $this->read();
        }
        return $this->operationResult();

    }

    public function operationResult()
    {
        return $this->operationStatus ? $this->manager->operationResult : array("status" => $this->status, "errorMessage" => $this->message);
    }

}
?>