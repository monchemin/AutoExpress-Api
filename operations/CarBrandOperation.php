<?php
namespace Operations;
use Entities\CarBrands;
use FactorOperations\FactorManager;


class CarBrandOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {
        ($this->pk != 0) ?  $this->readOne($this->pk) : $this->manager->getData(CarBrands::class);
        $this->operationStatus = true;
    }

    protected function create()
    {
        if($this->requestData != null && $this->requestData->brandName != null) {
            $carBrand = new Carbrands();
            $carBrand->brandName = $this->requestData->brandName;
            $this->manager->insertData($carBrand);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if($this->requestData != null && property_exists($this->requestData, "PK")) {
            $carBrand = new Carbrands();
            $carBrand->PK = $this->requestData->PK;
            if (property_exists($this->requestData, "PK") )$carBrand->brandName = $this->requestData->brandName;
            $this->manager->changeData($carBrand);
            $this->readOne($carBrand->PK);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(CarBrands::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        if($this->requestData != null && property_exists($this->requestData, "PK")) {
        $carBrand = new Carbrands();
        //$carBrand->brandName = property_exists($this->requestData, "brandName") ? $this->requestData->brandName : null;
        $carBrand->PK = $this->requestData->PK;
        $this->manager->deleteData($carBrand);
        $this->operationStatus = true;
        }
    }

    public function process()
    {


        switch ($this->httpMethod) {
            case "POST" :
                $this->create();
                $this->read();
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