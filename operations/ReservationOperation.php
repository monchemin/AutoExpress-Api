<?php
namespace Operations;

use Entities\Customers;
use Entities\Reservations;
use FactorOperations\FactorManager;
use Queries\QueryBuilder;

require_once join(DIRECTORY_SEPARATOR, ['entities', 'Reservations.php']);
require_once join(DIRECTORY_SEPARATOR, ['queries', 'QueryBuilder.php']);


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
        if($this->requestData !== null ) {
            $reservation = new Reservations();
            $reservation->PK = time() + $this->requestData->FK_Route + $this->requestData->FK_Customer;
            $reservation->reservationDate = date("Y-m-d H:i:s"); //property_exists($this->requestData, "reservationDate") ? $this->requestData->reservationDate : null;
            $reservation->FK_Customer = property_exists($this->requestData, "FK_Customer") ? $this->requestData->FK_Customer : null;
            $reservation->FK_Route = property_exists($this->requestData, "FK_Route") ? $this->requestData->FK_Route : null;
            $reservation->place = property_exists($this->requestData, "place") ? $this->requestData->place : null;
            if ($this->customerExists(property_exists($this->requestData, "FK_Customer")) && $this->shouldMakeReservation($reservation->FK_Route, $reservation->place, $reservation->FK_Customer ))
            {
                $this->manager->insertData($reservation);
            
                if($this->manager->operationResult->status == 200) 
                {
                   $query = QueryBuilder::getReservation();
                   $this->manager->getDataByQuery($query, array(':PK'=>$reservation->PK));
                  
                    $this->operationStatus = true;
                }
            }
            
            
        }
    }
    public function shouldMakeReservation($pk, $place, $customer) {
        $ok = false;
        $placeQuery = QueryBuilder::getRoutePlace();
       
        $this->manager->getDataByQuery($placeQuery, array(":pk"=>$pk));
        if($this->manager->operationResult->status == 200) {
            //print_r($this->manager->operationResult);
           $rplace = $this->manager->operationResult->response[0]['place'];
           if ($place <= $rplace) {$ok = true; }
        }
        return $ok && $this->customerExists($customer);

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
         //$this->manager->getData(Reservations::class, array(), array("PK" => $pk));
         $query = QueryBuilder::getReservation();
         $this->manager->getDataByQuery($query, array(':PK'=>$pk));
    }

    protected function delete()
    {
        //if($this->requestData != null && property_exists($this->requestData, "PK")) {
            if($this->pk != 0) {
            $reservation = new Reservations();

            $reservation->PK = $this->pk;
            $this->manager->deleteData($reservation);
            $this->operationStatus = true;
        }
    }
    private function customerExists($customer)
    {
        $exists = false;
        $this->manager->getData(Customers::class, array(), array("PK" => $customer));
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
               
        }
        return $this->operationResult();

    }
    public function operationResult()
    {
        //return array("status" => "120", "errorMessage"=>"Erreur dans la data");

        return $this->operationStatus ? $this->manager->operationResult : array("status" => "120", "errorMessage"=>"Erreur dans la data");
    }
}
?>