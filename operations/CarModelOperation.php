<?php
namespace Operations;

use Entities\CarModels;
use FactorOperations\FactorManager;

require_once join(DIRECTORY_SEPARATOR, ['entities', 'CarModels.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);

class CarModelOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

         ($this->Id != 0) ?  $this->readOne($this->Id) : $this->manager->getData(CarModels::class);
        $this->operationStatus = true;
    }

    protected function create()
    {

        if($this->requestData != null && $this->requestData->modelName != null) {
            $carModel = new CarModels();
            $carModel->modelName = $this->requestData->modelName;
            $carModel->fkBrand =property_exists($this->requestData, "fkBrand") ? $this->requestData->fkBrand : null;
            $this->manager->insertData($carModel);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if($this->requestData != null && property_exists($this->requestData, "id")) {
            $carModel = new CarModels();
            $carModel->id = $this->requestData->id;
           if(property_exists($this->requestData, "modelName")) $carModel->modelName = $this->requestData->modelName;
           if(property_exists($this->requestData, "FK_brand")) $carModel->fkBrand = $this->requestData->fkBrand;
            $this->manager->changeData($carModel);
            $this->readOne($carModel->id);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(CarModels::class, array(), array("id" => $pk));
    }

    protected function delete()
    {
        //if($this->requestData != null && property_exists($this->requestData, "PK")) {
            if($this->Id != 0) {
            $carModel = new CarModels();
            //$carModel->modelName = $this->requestData->modelName;
            $carModel->id = $this->Id;
            $this->manager->deleteData($carModel);
                if($this->manager->operationResult->status == 200) {
                    $this->manager->getData(CarModels::class);
                    $this->operationStatus = true;
                }

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
                //$this->read();
        }
        return $this->operationResult();

    }
    protected function operationResult()
    {
        return $this->operationStatus ? $this->manager->operationResult : array("status" => "120", "errorMessage"=>"Erreur dans la data");
    }
}
?>