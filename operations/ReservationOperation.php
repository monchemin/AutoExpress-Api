<?php
namespace Operations;

use Entities\Customers;
use Entities\Reservations;
use FactorOperations\FactorManager;


class ReservationOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

         ($this->pk != 0) ?  $this->readOne($this->pk) : $this->manager->getData(Reservations::class);
        $this->operationStatus = true;
    }

    protected function create()
    {

        if($this->requestData != null) {
            $reservation = new Reservations();
            $reservation->reservationDate = date("Y-m-d H:i:s"); //property_exists($this->requestData, "reservationDate") ? $this->requestData->reservationDate : null;
            $reservation->FK_Customer = property_exists($this->requestData, "reservationDate") ? $this->requestData->FK_Customer : null;
            $reservation->FK_Route = property_exists($this->requestData, "FK_Route") ? $this->requestData->FK_Route : null;
            $this->manager->insertData($reservation);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if( $this->requestData != null && property_exists($this->requestData, "PK")) {
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
    protected function readOne($pk) {
         $this->manager->getData(Reservations::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        if($this->requestData != null && property_exists($this->requestData, "PK")) {
            $reservation = new Reservations();

            $reservation->PK = $this->requestData->PK;
            $this->manager->deleteData($reservation);
            $this->operationStatus = true;
        }
    }
    private function customerExists()
    {
        $exists = false;
        $this->manager->getData(Customers::class, array(), array("PK" => $this->requestData->PK));
        $loginResult = $this->manager->managerOperationResult;
        if($loginResult->status == 200 && $loginResult->resultData != null) $exists = true;
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
        return $this->operationStatus ? $this->manager->managerOperationResult : array("status" => "120", "errorMessage"=>"Erreur dans la data");
    }
}
?>