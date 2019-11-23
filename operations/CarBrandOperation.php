<?php
namespace Operations;
use Entities\CarBrands;
use FactorOperations\FactorManager;

require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);
require_once join(DIRECTORY_SEPARATOR, ['entities', 'CarBrands.php']);

class CarBrandOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {
        ($this->Id != 0) ?  $this->readOne($this->Id) : $this->manager->getData(CarBrands::class);
    }

    protected function create()
    {
        if($this->requestData != null && $this->requestData->brandName != null) {
            $carBrand = new Carbrands();
            $carBrand->brandName = $this->requestData->brandName;
            $this->manager->insertData($carBrand);
        }
        else {$this->operationStatus = 400;}
    }

    protected function update()
    {
        if($this->requestData != null && property_exists($this->requestData, "id")) {
            $carBrand = new Carbrands();
            $carBrand->id = $this->requestData->id;
            if (property_exists($this->requestData, "id") )$carBrand->brandName = $this->requestData->brandName;
            $this->manager->changeData($carBrand);
            $this->readOne($carBrand->id);
        } else {$this->operationStatus = 400;}

    }
    protected function readOne($pk) {
         $this->manager->getData(CarBrands::class, array(), array("id" => $pk));
    }

    protected function delete()
    {
        if($this->Id != 0) {
            $carBrand = new Carbrands();
            $carBrand->id = $this->Id;
            $this->manager->deleteData($carBrand);
            $this->manager->getData(CarBrands::class);
        } else {$this->operationStatus = 400;}
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
    

}
?>