<?php
namespace Operations;

use Entities\Customers;
use FactorOperations\FactorManager;

require_once join(DIRECTORY_SEPARATOR, ['entities', 'Customers.php']);


class CustomerOperation extends OperationBase {

    private $message = "Erreur dans la data";
    private $status = 200;

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
            $customer->firstName = property_exists($this->requestData, "firstName") ? $this->requestData->firstName : null;
            $customer->lastName = property_exists($this->requestData, "lastName") ? $this->requestData->lastName : null;
            $customer->eMail = property_exists($this->requestData, "eMail") ? $this->requestData->eMail : null;
            $customer->phoneNumber = property_exists($this->requestData, "phoneNumber") ? $this->requestData->phoneNumber : null;
            $customer->password = property_exists($this->requestData, "password") ? $this->requestData->customerLogin : $customer->password;
           // $customer->createdAt = property_exists($this->requestData, "customerPassword") ? $this->requestData->customerPassword : null;
            $customer->createdAt = date("Y-m-d H:i:s");
            $this->manager->insertData($customer);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = 1001; 
            return;
        }
        if (!property_exists($this->requestData, "PK")) {
            $this->message = "No Customer provided";
            $this->status = 1002;  
            return;
        }
        
        $this->manager->getData(Customers::class, array("PK"), 
                array("eMail" => $this->requestData->email, "PK"=>$this->requestData->PK));
                if( $this->manager->operationResult->status == 200 
                    && count($this->manager->operationResult->response)==1 
                    && $this->manager->operationResult->response[0]->PK ==  $this->requestData->PK ) {
                               
            $customer = new Customers();
            $customer->PK = $this->requestData->PK;
            if (property_exists($this->requestData, "firstName")) $customer->firstName = $this->requestData->firstName;
            if (property_exists($this->requestData, "lastName")) $customer->lastName = $this->requestData->lastName;
            //if (property_exists($this->requestData, "customerEMailAddress")) $customer->customerEMailAddress = $this->requestData->customerEMailAddress;
            if (property_exists($this->requestData, "phoneNumber")) $customer->phoneNumber = $this->requestData->phoneNumber;
            $this->manager->changeData($customer);
            $this->operationStatus = true;
        } else {
            $this->message = "I'm not able to change your information";
            $this->status = 1003;  
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
        $this->manager->getData(Customers::class, array(), array("eMail" => $this->requestData->checkLogin));
        $loginResult = $this->manager->operationResult;
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
        $response = array();
        if( $this->requestData != null && property_exists($this->requestData, "login")) {
            $this->manager->getData(Customers::class, array("PK", "firstName", "lastName", "eMail", "phoneNumber", "drivingNumber"), 
                    array("eMail" => $this->requestData->login, "password"=>$this->requestData->password)
                );
            if($this->manager->operationResult->status == 200 && count($this->manager->operationResult->response)==1 ) {
                $response['status'] = 200;
                $response['isLog'] = true;
                $response['response'] = $this->manager->operationResult->response;               
            } else {
                //$this->manager->getData(Customers::class, array("PK", "customerFistName", "customerLastName", "customerEMailAddress"), 
                //    array("customerEMailAddress" => $this->requestData->login, "customerPassword"=>$this->requestData->password)
               // );
               // if($this->manager->operationResult->status == 200 && count($this->manager->operationResult->response)==1 ) {
               //     $response['status'] = 200;
                //    $response['isLog'] = true;
               //     $response['response'] = $this->manager->operationResult->response; 
                //} else {
                $response['status'] = 200;
                $response['isLog'] = false;
                //}
            }
        
        } else { $response['status'] = 120;}
        return $response;
    }

    public function changePassword() {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = 1001;  
            return;
        }
        if (!property_exists($this->requestData, "PK")) {
            $this->message = "No Customer provided";
            $this->status = 1002;  
            return;
        }
        
        $this->manager->getData(Customers::class, array("PK"), 
                array("eMail" => $this->requestData->email, "password"=>$this->requestData->oldPassword));
                if( $this->manager->operationResult->status == 200 
                    && count($this->manager->operationResult->response)==1 
                    && $this->manager->operationResult->response[0]->PK ==  $this->requestData->PK ) {
                               
            $customer = new Customers();
            $customer->PK = $this->requestData->PK;
            if (property_exists($this->requestData, "newPassword")) $customer->password = $this->requestData->newPassword;
            $this->manager->changeData($customer);
            $this->operationStatus = true;
        } else {
            $this->message = "I'm not able to change your information";
            $this->status = 1004;  
        }
        return $this->operationResult();
    }

    public function upgradeToDriver(){
        if ($this->requestData == null) {
            $this->message = "No provided data"; 
            return;
        }
        if (!property_exists($this->requestData, "PK")) {
            $this->message = "No Customer provided"; 
            return;
        }
        
        $this->manager->getData(Customers::class, array("PK"), 
                array("eMail" => $this->requestData->email, "password"=>$this->requestData->password));
                if( $this->manager->operationResult->status == 200 
                    && count($this->manager->operationResult->response)==1 
                    && $this->manager->operationResult->response[0]->PK ==  $this->requestData->PK ) {
                               
            $customer = new Customers();
            $customer->PK = $this->requestData->PK;
            if (property_exists($this->requestData, "drivingNumber")) $customer->drivingNumber = $this->requestData->drivingNumber;
            $this->manager->changeData($customer);
            $this->operationStatus = true;
        } else {
            $this->message = "I'm not able to set your driving information"; 
        }
        return $this->operationResult();
    }


    protected function operationResult()
    {
        return $this->operationStatus ? $this->manager->operationResult : array("status" => $this->status, "errorMessage"=>$this->message);
    }
}
?>