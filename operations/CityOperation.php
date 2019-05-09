<?php
namespace Operations;
use Entities\Cities;
use FactorOperations\FactorManager;


class CityOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {
        ($this->pk != 0) ?  $this->readOne($this->pk) : $this->manager->getData(Cities::class);
        $this->operationStatus = true;
    }

    protected function create()
    {
        if($this->requestData != null && $this->requestData->cityName != null) {
            $City = new Cities();
            $City->cityName = $this->requestData->cityName;
            $this->manager->insertData($City);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if($this->requestData != null && property_exists($this->requestData, "PK")) {
            $City = new Cities();
            $City->PK = $this->requestData->PK;
            if(property_exists($this->requestData, "cityName")) $City->cityName = $this->requestData->cityName;
            $this->manager->changeData($City);
            $this->readOne($City->PK);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(Cities::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        //if($this->requestData != null && property_exists($this->requestData, "PK")) {
            if($this->pk != 0) {
        $City = new Cities();
        //$City->cityName = property_exists($this->requestData, "cityName") ? $this->requestData->cityName : null;
        $City->PK = $this->pk;
        $this->manager->deleteData($City);
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
        return $this->operationStatus ? $this->manager->operationResult : array("status" => "120", "errorMessage"=>"Erreur dans la data");
    }

}
?>