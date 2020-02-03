<?php

namespace Operations;

use Entities\CarColors;
use FactorOperations\FactorManager;

require_once join(DIRECTORY_SEPARATOR, ['entities', 'CarColors.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);

class CarColorOperation extends OperationBase
{

    private $message = "";
    private $status = 200;

    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

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
        }
        return $this->operationResult();

    }

    protected function create()
    {
        if ($this->requestData != null && $this->requestData->colorName != null) {
            $carColor = new CarColors();
            $carColor->colorName = $this->requestData->colorName;
            $carColor->colorLabel = $this->requestData->colorLabel;
            $this->manager->insertData($carColor);
            $this->operationStatus = true;
        } else {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
        }
    }

    protected function read()
    {
        ($this->Id != 0) ? $this->readOne($this->Id) : $this->manager->getData(CarColors::class);
        $this->operationStatus = true;
    }

    protected function readOne($pk)
    {
        $this->manager->getData(CarColors::class, array(), array("Id" => $pk));
    }

    protected function update()
    {
        if ($this->requestData != null && property_exists($this->requestData, "Id")) {
            $carColor = new CarColors();
            $carColor->Id = $this->requestData->Id;
            if (property_exists($this->requestData, "colorName")) $carColor->colorName = $this->requestData->colorName;
            if (property_exists($this->requestData, "colorLabel")) $carColor->colorLabel = $this->requestData->colorLabel;
            $this->manager->changeData($carColor);
            $this->operationStatus = true;
        } else {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
        }

    }

    protected function delete()
    {
        if ($this->Id != 0 && isset($this->manager)) {
            $carColor = new CarColors();
            $carColor->Id = $this->Id;
            $this->manager->deleteData($carColor);
            if ($this->manager->operationResult->status == 200) {
                $this->manager->getData(CarColors::class);
                $this->operationStatus = true;
            } else {
                $this->message = $this->manager->operationResult->errorMessage;
                $this->status = SQL_ERROR;
            }
        } else {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
        }
    }

    protected function operationResult()
    {
        return $this->operationStatus ? $this->manager->operationResult : array("status" => $this->status, "errorMessage" => $this->message);
    }
}
?>