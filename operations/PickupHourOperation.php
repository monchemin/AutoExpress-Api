<?php
namespace Operations;

use Entities\PickupHours;
use FactorOperations\FactorManager;


class PickupHourOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

         ($this->pk != 0) ?  $this->readOne($this->pk) : $this->manager->getData(PickupHours::class, array(), array(), array("displayOrder"));
        $this->operationStatus = true;
    }

    protected function create()
    {

        if($this->requestData != null) {
            $pickupHour = new PickupHours();
            $pickupHour->hour = property_exists($this->requestData, "hour") ? $this->requestData->hour : null;
            $pickupHour->displayOrder = property_exists($this->requestData, "displayOrder") ? $this->requestData->displayOrder : null;
            $this->manager->insertData($pickupHour);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if($this->requestData != null && property_exists($this->requestData, "PK")) {
            $pickupHour = new PickupHours();
            $pickupHour->PK = $this->requestData->PK;
            if (property_exists($this->requestData, "hour")) $pickupHour->hour = $this->requestData->hour;
            if (property_exists($this->requestData, "displayOrder")) $pickupHour->displayOrder = $this->requestData->displayOrder;
            $this->manager->changeData($pickupHour);
            //$this->readOne($pickupHour->PK);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(PickupHours::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        //if($this->requestData != null && property_exists($this->requestData, "PK")) {
            if($this->pk != 0) {
            $pickupHour = new PickupHours();
            $pickupHour->PK = $this->pk;
            $this->manager->deleteData($pickupHour);
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
        if($data->response != null) {
        foreach($data->response as $value) {
           //echo $value->PK;
             $picker[] = array('value'=>$value->PK, 'label'=>$value->hour);
        }
        $data->picker = $picker;
        
    }
    return $data;
}

}
?>