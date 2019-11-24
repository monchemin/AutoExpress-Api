<?php
namespace Operations;
use Entities\Cities;
use FactorOperations\FactorManager;

require_once join(DIRECTORY_SEPARATOR, ['entities', 'Cities.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);
class CityOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {
        ($this->Id != 0) ?  $this->readOne($this->Id) : $this->manager->getData(Cities::class);
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
        if($this->requestData != null && property_exists($this->requestData, "id")) {
            $city = new Cities();
            $city->id = $this->requestData->id;
            if(property_exists($this->requestData, "cityName")) $city->cityName = $this->requestData->cityName;
            $this->manager->changeData($city);
            $this->readOne($city->id);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(Cities::class, array(), array("pk" => $pk));
    }

    protected function delete()
    {
        if($this->Id != 0) {
            $city = new Cities();
            $city->id = $this->Id;
            $this->manager->deleteData($city);
            if($this->manager->operationResult->status == 200) {
                $this->manager->getData(Cities::class);
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