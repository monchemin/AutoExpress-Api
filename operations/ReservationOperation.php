<?php

namespace Operations;

use Entities\Customers;
use Entities\Reservations;
use FactorOperations\FactorManager;
use Queries\QueryBuilder;
use utils\MailUtils;

require_once join(DIRECTORY_SEPARATOR, ['entities', 'Reservations.php']);
require_once join(DIRECTORY_SEPARATOR, ['queries', 'QueryBuilder.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'DateUtils.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'MailUtils.php']);
require_once 'CustomerOperation.php';


class ReservationOperation extends OperationBase
{

    private $message = "";
    private $status = 200;

    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

        ($this->Id != 0) ? $this->readOne($this->Id) : $this->manager->getData(Reservations::class);
        $this->operationStatus = true;
    }

    protected function create()
    {
        if ($this->requestData == null || !property_exists($this->requestData, "customer")) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return;
        }
        $customerId = $this->requestData->customer;
        $customerOp = new CustomerOperation($this->manager);
        $customer = $customerOp->readOne($customerId);
        if ($customer == null) {
            $this->message = "No Customer";
            $this->status = NO_PROVIDED_CUSTOMER;
            return;
        }
        $isFirstReservation = property_exists($this->requestData, "isFirstReservation") ? $this->requestData->isFirstReservation  : false;
        if (!$isFirstReservation && !$customer->active) {
            $this->message = "No active account";
            $this->status = NO_ACTIVE_ACCOUNT;
            return;
        }

        $routeId = property_exists($this->requestData, "route") ? $this->requestData->route : null;
        $place = property_exists($this->requestData, "place") ? $this->requestData->place : null;
        $language = property_exists($this->requestData, "language") ? $this->requestData->language : "fr";

        if (!is_numeric($customerId) || !is_numeric($routeId) || !is_numeric($place) || $place < 1) {
            $this->message = "Data error";
            $this->status = DATA_ERROR;
            return;
        }
        $reservation = new Reservations();
        $reservation->PK = time() + $this->requestData->FK_Route + $this->requestData->FK_Customer;
        $reservation->reservationDate = date("Y-m-d H:i:s"); //property_exists($this->requestData, "reservationDate") ? $this->requestData->reservationDate : null;
        $reservation->FK_Customer = $customerId;
        $reservation->FK_Route = $routeId;
        $reservation->place = $place;
        if ($this->shouldMakeReservation($routeId, $place, $customerId)) {
            $this->manager->insertData($reservation);

            if ($this->manager->operationResult->status == 200) {
                $query = QueryBuilder::getReservation();
                $this->manager->getDataByQuery($query, array(':PK' => $reservation->PK));

                $this->operationStatus = true;
                MailUtils::sendReservationMail($customer, $this->manager->operationResult->response[0], $language);
            }
        } else {
            $this->message = "Data res error";
            $this->status = DATA_ERROR;
        }


    }

    public function shouldMakeReservation($pk, $place, $customer)
    {
        $ok = false;
        $placeQuery = QueryBuilder::getRoutePlace();

        $this->manager->getDataByQuery($placeQuery, array(":pk" => $pk));
        if ($this->manager->operationResult->status == 200) {
            $remainingPlace = $this->manager->operationResult->response[0]['place'];
            $deleteAt = $this->manager->operationResult->response[0]['deletedAt'];
            $routeDriver = $this->manager->operationResult->response[0]['driverId'];
            if ($place <= $remainingPlace && $deleteAt == null && $routeDriver != $customer) {
                $ok = true;
            }
        }
        return $ok && $this->customerExists($customer);

    }

    protected function update()
    {
        if ($this->requestData != null && property_exists($this->requestData, "PK")) {
            $reservation = new Reservations();
            $reservation->PK = $this->requestData->PK;
            if (property_exists($this->requestData, "reservationDate")) $reservation->reservationDate = $this->requestData->reservationDate;
            if (property_exists($this->requestData, "FK_Customer")) $reservation->FK_Customer = $this->requestData->FK_Customer;
            if (property_exists($this->requestData, "FK_Route")) $reservation->FK_Route = $this->requestData->FK_Route;
            $this->manager->changeData($reservation);
            $this->readOne($reservation->PK);
            $this->operationStatus = true;
        }

    }

    protected function readOne($pk)
    {
        $query = QueryBuilder::getReservation();
        $this->manager->getDataByQuery($query, array(':PK' => $pk));
    }

    protected function delete()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return;
        }
        $customerId = property_exists($this->requestData, "customerId") ? $this->requestData->customerId : null;
        $reservationId = property_exists($this->requestData, "reservationId") ? $this->requestData->reservationId : null;
        if(!is_numeric($customerId) || !is_numeric($reservationId)) {
            $this->message = "Data Error";
            $this->status = DATA_ERROR;
        }
        $this->manager->getData(Reservations::class, array("FK_Customer"), array("PK" => $reservationId));
        $result =  $this->manager->operationResult;
        if($result->status == 200 && count($result->response)==1 && $result->response[0]->FK_Customer ==  $customerId )
        {
            $reservation = new Reservations();
            $reservation->PK = $reservationId;
            $reservation->deletedAt = date("Y-m-d H:i:s");
            $this->manager->changeData($reservation);
            if( $this->manager->operationResult->status == 200) {
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

    private function customerExists($customer)
    {
        $exists = false;
        $this->manager->getData(Customers::class, array(), array("Id" => $customer));
        $loginResult = $this->manager->operationResult;
        if ($loginResult->status == 200 && $loginResult->response != null) $exists = true;
        return $exists;
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

        }
        return $this->operationResult();

    }

    public function operationResult()
    {
        return $this->operationStatus ? $this->manager->operationResult : array("status" => $this->status, "errorMessage" => $this->message);
    }
}

?>