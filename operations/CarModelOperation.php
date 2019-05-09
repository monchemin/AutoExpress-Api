<?php
namespace Operations;

use Entities\CarModels;
use FactorOperations\FactorManager;


class CarModelOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

         ($this->pk != 0) ?  $this->readOne($this->pk) : $this->manager->getData(CarModels::class);
        $this->operationStatus = true;
    }

    protected function create()
    {

        if($this->requestData != null && $this->requestData->modelName != null) {
            $carModel = new CarModels();
            $carModel->modelName = $this->requestData->modelName;
            $carModel->FK_brand =property_exists($this->requestData, "FK_brand") ? $this->requestData->FK_brand : null;
            $this->manager->insertData($carModel);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if($this->requestData != null && property_exists($this->requestData, "PK")) {
            $carModel = new CarModels();
            $carModel->PK = $this->requestData->PK;
           if(property_exists($this->requestData, "modelName")) $carModel->modelName = $this->requestData->modelName;
           if(property_exists($this->requestData, "FK_brand")) $carModel->FK_brand = $this->requestData->FK_brand;
            $this->manager->changeData($carModel);
            $this->readOne($carModel->PK);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(CarModels::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        //if($this->requestData != null && property_exists($this->requestData, "PK")) {
            if($this->pk != 0) {
            $carModel = new CarModels();
            //$carModel->modelName = $this->requestData->modelName;
            $carModel->PK = $this->pk;
            $this->manager->deleteData($carModel);
            $this->operationStatus = true;
        }
    }

    public function process()
    {


        switch ($this->httpMethod) {
            case "POST" :
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