<?php
namespace Operations;
use Entities\CarColors;
use FactorOperations\FactorManager;


class CarColorOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {
        ($this->pk != 0) ?  $this->readOne($this->pk) : $this->manager->getData(CarColors::class);
        $this->operationStatus = true;
    }

    protected function create()
    {
        if($this->requestData != null && $this->requestData->colorName != null) {
            $carColor = new CarColors();
            $carColor->colorName = $this->requestData->colorName;
            $this->manager->insertData($carColor);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if($this->requestData != null &&  property_exists($this->requestData, "PK")) {
            $carColor = new CarColors();
            $carColor->PK = $this->requestData->PK;
            if  (property_exists($this->requestData, "colorName") ) $carColor->colorName = $this->requestData->colorName;
            $this->manager->changeData($carColor);
            //$this->readOne($carColor->PK);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
        $this->manager->getData(CarColors::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        if($this->requestData != null && $this->requestData->PK != null) {
            $carColor = new CarColors();
            $carColor->colorName = $this->requestData->colorName;
            $carColor->PK = $this->requestData->PK;
            $this->manager->deteleData($carColor);
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