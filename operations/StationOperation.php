<?php
namespace Operations;

use Entities\Stations;
use FactorOperations\FactorManager;


class stationOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

         ($this->pk != 0) ?  $this->readOne($this->pk) : $this->manager->getData(Stations::class);
        $this->operationStatus = true;
    }

    protected function create()
    {

        if($this->requestData != null && property_exists($this->requestData, "stationName")) {
            $station = new Stations();
            $station->stationName = $this->requestData->stationName;
            $station->stationAddress = property_exists($this->requestData, "stationAddress") ? $this->requestData->stationAddress : null;
            $station->FK_Zone = property_exists($this->requestData, "FK_Zone") ? $this->requestData->FK_Zone : null;
            $this->manager->insertData($station);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if($this->requestData != null && property_exists($this->requestData, "PK")) {
            $station = new Stations();
            $station->PK = $this->requestData->PK;
            if (property_exists($this->requestData, "stationName")) $station->stationName = $this->requestData->stationName;
            if (property_exists($this->requestData, "stationAddress")) $station->stationAddress = $this->requestData->stationAddress;
            if (property_exists($this->requestData, "FK_Zone")) $station->FK_Zone = $this->requestData->FK_Zone;
            $this->manager->changeData($station);
            $this->readOne($station->PK);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(Stations::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        //if($this->requestData != null && property_exists($this->requestData, "PK")) {
            if($this->pk != 0) {
            $station = new Stations();
            //$station->stationName = $this->requestData->stationName;
            $station->PK = $this->pk;
            $this->manager->deleteData($station);
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
        return $this->operationStatus ? $this->picker($this->manager->managerOperationResult) : array("status" => "120", "errorMessage"=>"Erreur dans la data");
    }

    protected function picker($data) {
        $picker = array();
        foreach($data->response as $value) {
           //echo $value->PK;
             $picker[] = array('value'=>$value->PK, 'label'=>$value->stationName);
        }
        $data->picker = $picker;
        return $data;
    }
}
?>