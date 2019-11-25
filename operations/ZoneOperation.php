<?php
namespace Operations;

use Entities\Zones;
use FactorOperations\FactorManager;

require_once join(DIRECTORY_SEPARATOR, ['entities', 'Zones.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);
class ZoneOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

         ($this->Id != 0) ?  $this->readOne($this->Id) : $this->manager->getData(Zones::class);
        $this->operationStatus = true;
    }

    protected function create()
    {

        if($this->requestData != null && property_exists($this->requestData, "zoneName")) {
            $zone = new Zones();
            $zone->zoneName = $this->requestData->zoneName;
            $zone->fkCity = property_exists($this->requestData, "fkCity") ? $this->requestData->fkCity : null;
            $this->manager->insertData($zone);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if($this->requestData != null && property_exists($this->requestData, "id")) {
            $zone = new Zones();
            $zone->id = $this->requestData->id;
            if(property_exists($this->requestData, "zoneName")) $zone->zoneName = $this->requestData->zoneName;
           if(property_exists($this->requestData, "fkCity")) $zone->fkCity = $this->requestData->fkCity;
            $this->manager->changeData($zone);
            $this->readOne($zone->id);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(Zones::class, array(), array("id" => $pk));
    }

    protected function delete()
    {

            if($this->Id != 0) {
            $zone = new Zones();
            $zone->id = $this->Id;
            $this->manager->deleteData($zone);
                if($this->manager->operationResult->status == 200) {
                    $this->manager->getData(Zones::class);
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