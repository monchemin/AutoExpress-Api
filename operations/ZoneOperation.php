<?php
namespace Operations;

use Entities\Zones;
use FactorOperations\FactorManager;


class ZoneOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

         ($this->pk != 0) ?  $this->readOne($this->pk) : $this->manager->getData(Zones::class);
        $this->operationStatus = true;
    }

    protected function create()
    {

        if($this->requestData != null && property_exists($this->requestData, "zoneName")) {
            $zone = new Zones();
            $zone->zoneName = $this->requestData->zoneName;
            $zone->FK_City = property_exists($this->requestData, "FK_City") ? $this->requestData->FK_City : null;
            $this->manager->insertData($zone);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if($this->requestData != null && property_exists($this->requestData, "PK")) {
            $zone = new Zones();
            $zone->PK = $this->requestData->PK;
            if(property_exists($this->requestData, "zoneName")) $zone->zoneName = $this->requestData->zoneName;
           if(property_exists($this->requestData, "FK_City")) $zone->FK_City = $this->requestData->FK_City;
            $this->manager->changeData($zone);
            $this->readOne($zone->PK);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(Zones::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        //if($this->requestData != null && property_exists($this->requestData, "PK")) {
            if($this->pk != 0) {
            $zone = new Zones();
            //$zone->zoneName = $this->requestData->zoneName;
            $zone->PK = $this->pk;
            $this->manager->deleteData($zone);
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
        return $this->operationStatus ? $this->picker($this->manager->operationResult) : array("status" => "120", "errorMessage"=>"Erreur dans la data");
    }

    protected function picker($data) {
        $picker = array();
        foreach($data->response as $value) {
           //echo $value->PK;
             $picker[] = array('value'=>$value->PK, 'label'=>$value->zoneName);
        }
        $data->picker = $picker;
        return $data;
    }
}
?>