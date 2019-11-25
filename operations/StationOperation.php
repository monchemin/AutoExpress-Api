<?php
namespace Operations;

use Entities\Stations;
use FactorOperations\FactorManager;

require_once join(DIRECTORY_SEPARATOR, ['entities', 'Stations.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);

class stationOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

         ($this->Id != 0) ?  $this->readOne($this->Id) : $this->manager->getData(Stations::class);
        if($this->manager->operationResult->status == 200) {
            $this->operationStatus = true;
        }
    }

    protected function create()
    {

        if($this->requestData != null && property_exists($this->requestData, "stationName")) {
            $station = new Stations();
            $station->stationName = $this->requestData->stationName;
            $station->stationAddress = property_exists($this->requestData, "stationAddress") ? $this->requestData->stationAddress : null;
            $station->stationDetail = property_exists($this->requestData, "stationDetail") ? $this->requestData->stationDetail : null;
            $station->fkZone = property_exists($this->requestData, "fkZone") ? $this->requestData->fkZone : null;
            $this->manager->insertData($station);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if($this->requestData != null && property_exists($this->requestData, "id")) {
            $station = new Stations();
            $station->id = $this->requestData->id;
            if (property_exists($this->requestData, "stationName")) $station->stationName = $this->requestData->stationName;
            if (property_exists($this->requestData, "stationAddress")) $station->stationAddress = $this->requestData->stationAddress;
            if (property_exists($this->requestData, "stationDetail")) $station->stationDetail = $this->requestData->stationDetail;
            if (property_exists($this->requestData, "fkZone")) $station->fkZone = $this->requestData->fkZone;
            $this->manager->changeData($station);
            $this->readOne($station->id);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(Stations::class, array(), array("id" => $pk));
    }

    protected function delete()
    {

            if($this->Id != 0) {
            $station = new Stations();
            $station->id = $this->Id;
            $this->manager->deleteData($station);
                if($this->manager->operationResult->status == 200) {
                    $this->manager->getData(Stations::class);
                    $this->operationStatus = true;
                }
        }
    }

    public function process()
    {

        switch ($this->httpMethod) {
            case "POST" :
                $this->create();
                break;
            case "PUT" :
                $this->update();
                break;
            case "GET" :
                $this->read();
                break;
            case "DELETE" :
                $this->delete();
        }
        return $this->operationResult();

    }
    protected function operationResult()
    {
        return $this->operationStatus ? $this->manager->operationResult : array("status" => "120", "errorMessage"=>"Erreur dans la data");
    }

}
?>