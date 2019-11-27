<?php
namespace Operations;

use Entities\PickupHours;
use FactorOperations\FactorManager;

require_once join(DIRECTORY_SEPARATOR, ['entities', 'PickupHours.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);
class PickupHourOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

         ($this->Id != 0) ?  $this->readOne($this->Id) : $this->manager->getData(PickupHours::class, array(), array(), array("displayOrder"));
        if($this->manager->operationResult->status == 200) {
            $this->operationStatus = true;
        }
    }

    protected function create()
    {

        if($this->requestData != null) {
            $pickupHour = new PickupHours();
            $pickupHour->hour = property_exists($this->requestData, "hour") ? $this->requestData->hour : null;
            $pickupHour->displayOrder = property_exists($this->requestData, "displayOrder") ? $this->requestData->displayOrder : null;
            $this->manager->insertData($pickupHour);
            if($this->manager->operationResult->status == 200) {
                $this->operationStatus = true;
            }

        }
    }

    protected function update()
    {
        if($this->requestData != null && property_exists($this->requestData, "id")) {
            $pickupHour = new PickupHours();
            $pickupHour->Id = $this->requestData->id;
            if (property_exists($this->requestData, "hour")) $pickupHour->hour = $this->requestData->hour;
            if (property_exists($this->requestData, "displayOrder")) $pickupHour->displayOrder = $this->requestData->displayOrder;
            $this->manager->changeData($pickupHour);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(PickupHours::class, array(), array("id" => $pk));
    }

    protected function delete()
    {
            if($this->Id != 0) {
            $pickupHour = new PickupHours();
            $pickupHour->Id = $this->Id;
            $this->manager->deleteData($pickupHour);
            if($this->manager->operationResult->status == 200) {
                $this->manager->getData(PickupHours::class, array(), array(), array("displayOrder"));
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