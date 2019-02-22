<?php
namespace Operations;

use Entities\Customers;
use FactorOperations\FactorManager;


class CustomerOperation extends OperationBase {


    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

         ($this->pk != 0) ?  $this->readOne($this->pk) : $this->manager->getData(Customers::class);
        $this->operationStatus = true;
    }

    protected function create()
    {

        if($this->requestData != null) {
            $customer = new Customers();
            $customer->customerFistName = property_exists($this->requestData, "customerFistName") ? $this->requestData->customerFistName : null;
            $customer->customerLastName = property_exists($this->requestData, "customerLastName") ? $this->requestData->customerLastName : null;
            $customer->customerEMailAddress = property_exists($this->requestData, "customerEMailAddress") ? $this->requestData->customerEMailAddress : null;
            $customer->customerPhoneNumber = property_exists($this->requestData, "customerPhoneNumber") ? $this->requestData->customerPhoneNumber : null;
            $customer->customerLogin = property_exists($this->requestData, "customerLogin") ? $this->requestData->customerLogin : null;
            $customer->customerPassword = property_exists($this->requestData, "customerPassword") ? $this->requestData->customerPassword : null;
            $customer->customerDateCreate = date("Y-m-d H:i:s");
            $this->manager->insertData($customer);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if( $this->requestData != null && property_exists($this->requestData, "PK")) {
            $customer = new Customers();
            $customer->PK = $this->requestData->PK;
            if (property_exists($this->requestData, "customerFistName")) $customer->customerFistName = $this->requestData->customerFistName;
            if (property_exists($this->requestData, "customerLastName")) $customer->customerLastName = $this->requestData->customerLastName;
            if (property_exists($this->requestData, "customerEMailAddress")) $customer->customerEMailAddress = $this->requestData->customerEMailAddress;
            if (property_exists($this->requestData, "customerPhoneNumber")) $customer->customerPhoneNumber = $this->requestData->customerPhoneNumber;
            if (property_exists($this->requestData, "customerPassword")) $customer->customerPassword = $this->requestData->customerPassword;
            $this->manager->changeData($customer);
            $this->readOne($customer->PK);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(Customers::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        //if($this->requestData != null && property_exists($this->requestData, "PK")) {
            if($this->pk != 0) {
            $customer = new Customers();

            $customer->PK = $this->pk;
            $this->manager->deleteData($customer);
            $this->operationStatus = true;
        }
    }
    private function loginExists()
    {
        $loginExists = false;
        $this->manager->getData(Customers::class, array(), array("customerLogin" => $this->requestData->checkLogin));
        $loginResult = $this->manager->managerOperationResult;
        if($loginResult->status == 200 && $loginResult->response != null) $loginExists = true;
        return array("status" => $loginResult->status, "loginExists" => $loginExists);
    }

    public function process()
    {
        
        switch ($this->httpMethod) {
            case "POST" :
                if($this->requestData != null && property_exists($this->requestData, "checkLogin")) return $this->loginExists();
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
    public function login() {
        if( $this->requestData != null && property_exists($this->requestData, "login")) {
            $this->manager->getData(Customers::class, array(), 
                    array("customerLogin" => $this->requestData->login, "customerPassword"=>$this->requestData->password)
                );
            if($this->manager->managerOperationResult->status == 200 && count($this->manager->managerOperationResult->response)==1 ) {
                return array("status"=>200, "isLog"=> true);
            } else {return array("status"=>120, "isLog"=> false);}
        
        }
    }
    protected function operationResult()
    {
        return $this->operationStatus ? $this->manager->managerOperationResult : array("status" => "120", "errorMessage"=>"Erreur dans la data");
    }
}
?>