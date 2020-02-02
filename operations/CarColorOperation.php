<?php
namespace Operations;
use Entities\CarColors;
use FactorOperations\FactorManager;

require_once join(DIRECTORY_SEPARATOR, ['entities', 'CarColors.php']);

class CarColorOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {
        ($this->Id != 0) ?  $this->readOne($this->Id) : $this->manager->getData(CarColors::class);
        $this->operationStatus = true;
    }

    protected function create()
    {
        if($this->requestData != null && $this->requestData->colorName != null) {
            $carColor = new CarColors();
            $carColor->colorName = $this->requestData->colorName;
            $carColor->colorLabel = $this->requestData->colorLabel;
            $this->manager->insertData($carColor);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if($this->requestData != null &&  property_exists($this->requestData, "Id")) {
            $carColor = new CarColors();
            $carColor->Id = $this->requestData->Id;
            if  (property_exists($this->requestData, "colorName") ) $carColor->colorName = $this->requestData->colorName;
            if  (property_exists($this->requestData, "colorLabel") ) $carColor->colorLabel = $this->requestData->colorLabel;
            $this->manager->changeData($carColor);
            //$this->readOne($carColor->PK);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
        $this->manager->getData(CarColors::class, array(), array("Id" => $pk));
    }

    protected function delete()
    {
            if($this->Id != 0 && isset($this->manager)) {
            $carColor = new CarColors();
            $carColor->Id = $this->Id;
            $this->manager->deleteData($carColor);
            if($this->manager->operationResult->status == 200) {
                $this->manager->getData(CarColors::class);
                $this->operationStatus = true;
            }

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