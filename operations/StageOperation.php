<?php
namespace Operations;

use Entities\Stages;
use FactorOperations\FactorManager;


class StageOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

         ($this->pk != 0) ?  $this->readOne($this->pk) : $this->manager->getData(Stages::class);
        $this->operationStatus = true;
    }

    protected function create()
    {

        if($this->requestData != null && property_exists($this->requestData, "stageName")) {
            $stage = new Stages();
            $stage->stageName = $this->requestData->stageName;
            $stage->stageAddress = property_exists($this->requestData, "stageAddress") ? $this->requestData->stageAddress : null;
            $stage->FK_Zone = property_exists($this->requestData, "FK_Zone") ? $this->requestData->FK_Zone : null;
            $this->manager->insertData($stage);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if($this->requestData != null && property_exists($this->requestData, "PK")) {
            $stage = new Stages();
            $stage->PK = $this->requestData->PK;
            if (property_exists($this->requestData, "stageName")) $stage->stageName = $this->requestData->stageName;
            if (property_exists($this->requestData, "stageAddress")) $stage->stageAddress = $this->requestData->stageAddress;
            if (property_exists($this->requestData, "FK_Zone")) $stage->FK_Zone = $this->requestData->FK_Zone;
            $this->manager->changeData($stage);
            $this->readOne($stage->PK);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(Stages::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        if($this->requestData != null && property_exists($this->requestData, "PK")) {
            $stage = new Stages();
            $stage->stageName = $this->requestData->stageName;
            $stage->PK = $this->requestData->PK;
            $this->manager->deleteData($stage);
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
        return $this->operationStatus ? $this->manager->managerOperationResult : array("status" => "120", "errorMessage"=>"Erreur dans la data");
    }
}
?>