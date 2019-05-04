<?php
namespace Operations;

use Entities\Customers;
use Entities\Drivers;
use FactorOperations\FactorManager;


class DriverOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

         ($this->pk != 0) ?  $this->readOne($this->pk) : $this->manager->getData(Drivers::class);
        $this->operationStatus = true;
    }

    protected function create()
    {

        if($this->requestData != null && property_exists($this->requestData, "PK") && $this->customerExists()) {
            $driver = new Drivers();
            $driver->PK = $this->requestData->PK;
            $driver->carRegistrationNumber = property_exists($this->requestData, "carRegistrationNumber") ? $this->requestData->carRegistrationNumber : null;
            $driver->drivingPermitNumber = property_exists($this->requestData, "drivingPermitNumber") ? $this->requestData->drivingPermitNumber : null;
            $driver->carYear = property_exists($this->requestData, "carYear") ? $this->requestData->carYear : null;
            $driver->FK_carcolor = property_exists($this->requestData, "FK_carcolor") ? $this->requestData->FK_carcolor : null;
            $driver->FK_carmodel = property_exists($this->requestData, "FK_carmodel") ? $this->requestData->FK_carmodel : null;
            $driver->driverDateCreate = date("Y-m-d H:i:s");
            $this->manager->insertData($driver);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if( $this->requestData != null && property_exists($this->requestData, "PK")) {
            $driver = new Drivers();
            $driver->PK = $this->requestData->PK;
            if (property_exists($this->requestData, "carRegistrationNumber")) $driver->carRegistrationNumber = $this->requestData->carRegistrationNumber;
            if (property_exists($this->requestData, "drivingPermitNumber")) $driver->drivingPermitNumber = $this->requestData->drivingPermitNumber;
            if (property_exists($this->requestData, "carYear")) $driver->carYear = $this->requestData->carYear;
            if (property_exists($this->requestData, "FK_carcolor")) $driver->FK_carcolor = $this->requestData->FK_carcolor;
            if (property_exists($this->requestData, "FK_carmodel")) $driver->FK_carmodel = $this->requestData->FK_carmodel;
            $this->manager->changeData($driver);
            $this->readOne($driver->PK);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(Drivers::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        //if($this->requestData != null && property_exists($this->requestData, "PK")) {
            if($this->pk != 0) {
            $driver = new Drivers();

            $driver->PK = $this->pk;
            $this->manager->deleteData($driver);
            $this->operationStatus = true;
        }
    }
    private function customerExists()
    {
        $exists = false;
        $this->manager->getData(Customers::class, array(), array("PK" => $this->requestData->PK));
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
    protected function operationResult()
    {
        return $this->operationStatus ? $this->manager->operationResult : array("status" => "120", "errorMessage"=>"Erreur dans la data");
    }
}
?>