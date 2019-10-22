<?php
namespace Operations;

use Entities\Customers;
use FactorOperations\FactorManager;
use utils\MailUtils;

require_once join(DIRECTORY_SEPARATOR, ['entities', 'Customers.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'MailUtils.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);


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
        $firstName = property_exists($this->requestData, "firstName") ? $this->requestData->firstName : null;
        $lastName =  property_exists($this->requestData, "lastName") ? $this->requestData->lastName : null;
        $mail = property_exists($this->requestData, "eMail") ? $this->requestData->eMail : null;
        $number = property_exists($this->requestData, "phoneNumber") ? $this->requestData->phoneNumber : null;
        $pass = property_exists($this->requestData, "password") ? $this->requestData->password : null;

        if($firstName === null || $lastName === null || $mail === null || $number === null || $pass === null) {
            $this->message = "Error in provided Data";
            $this->status = DATA_ERROR;
            return;
        }

        if($this->loginExists()) {
            $this->message = "Email already exists.";
            $this->status = LOGIN_EXISTS;
            return;

        }

        if($this->requestData != null) {
            $customer = new Customers();
            $customer->firstName = $firstName;
            $customer->lastName = $lastName;
            $customer->eMail = $mail;
            $customer->phoneNumber = $number;
            $customer->password = $pass;
           // $customer->createdAt = property_exists($this->requestData, "customerPassword") ? $this->requestData->customerPassword : null;
            $customer->createdAt = date("Y-m-d H:i:s");
            $code = mt_rand(100000,999999);
            $customer->activationCode = $code;
            $this->manager->insertData($customer);
            $this->operationStatus = true;
            MailUtils::sendActivationMail($mail, $lastName, $code, "en");
        }
    }

    protected function update()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return;
        }
        if (!property_exists($this->requestData, "PK")) {
            $this->message = "No Customer provided";
            $this->status = NO_PROVIDED_CUSTOMER;
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
            if (property_exists($this->requestData, "phoneNumber")) $customer->phoneNumber = $this->requestData->phoneNumber;
            $this->manager->changeData($customer);
            $this->operationStatus = true;
        } else {
            $this->message = "I'm not able to change your information";
            $this->status = DATA_ERROR;
        } 

    }
    public function readOne($pk) {
        $this->manager->getData(Customers::class, array(), array("PK" => $pk));
        $loginResult = $this->manager->operationResult;
        return  ($loginResult->status == 200 && $loginResult->response != null) ? $loginResult->response[0] : null;
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
        $this->manager->getData(Customers::class, array(), array("eMail" => $this->requestData->eMail));
        $loginResult = $this->manager->operationResult;
        if($loginResult->status == 200 && $loginResult->response != null) $loginExists = true;
        return $loginExists;
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
            $this->manager->getData(Customers::class, array("PK", "firstName", "lastName", "eMail", "phoneNumber", "drivingNumber", "active"),
                    array("eMail" => $this->requestData->login, "password"=>$this->requestData->password)
                );
            if($this->manager->operationResult->status == 200 && count($this->manager->operationResult->response)==1 ) {
                $response['status'] = 200;
                $response['isLog'] = true;
                $response['response'] = $this->manager->operationResult->response;               
            } else {

                $response['status'] = 200;
                $response['isLog'] = false;

            }
        
        } else { $response['status'] = 120;}
        return $response;
    }

    public function changePassword() {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return $this->operationResult();
        }
        $customerId = property_exists($this->requestData, "PK") ? $this->requestData->PK : null;
        $oldPassword = property_exists($this->requestData, "oldPassword") ? $this->requestData->oldPassword : null;
        $newPassword = property_exists($this->requestData, "newPassword") ? $this->requestData->newPassword : null;
        $mail = property_exists($this->requestData, "newPassword") ? $this->requestData->email : null;
        if (!is_numeric($customerId) || empty($oldPassword) || empty($newPassword) || empty($mail)) {
            $this->message = "Data Error";
            $this->status = DATA_ERROR;
            return $this->operationResult();
        }
        
        $this->manager->getData(Customers::class, array("PK"), array("eMail" => $mail, "password"=>$oldPassword));
        $result =  $this->manager->operationResult;
        if($result->status == 200 && count($result->response)==1 && $result->response[0]->PK ==  $customerId ) {
            $customer = new Customers();
            $customer->PK = $customerId;
            $customer->password = $newPassword;
            $this->manager->changeData($customer);
            $this->operationStatus = true;
        } else {
            $this->message = "I'm not able to change your information";
            $this->status = DATA_ERROR;
        }
        return $this->operationResult();
    }

    public function changeMail() {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return $this->operationResult();
        }
        $customerId = property_exists($this->requestData, "PK") ? $this->requestData->PK : null;
        $password = property_exists($this->requestData, "password") ? $this->requestData->password : null;
        $oldMail = property_exists($this->requestData, "oldMail") ? $this->requestData->oldMail : null;
        $newMail = property_exists($this->requestData, "newMail") ? $this->requestData->newMail : null;
        if (!is_numeric($customerId) || empty($password) || empty($oldMail) || empty($newMail)) {
            $this->message = "No Customer provided";
            $this->status = NO_PROVIDED_CUSTOMER;
            return $this->operationResult();
        }

        $this->manager->getData(Customers::class, array("PK", "firstName"), array("eMail" => $oldMail, "password"=>$password));
        $result =  $this->manager->operationResult;
        if($result->status == 200 && count($result->response)==1 && $result->response[0]->PK ==  $customerId ) {
            $customer = new Customers();
            $customer->PK = $customerId;
            $customer->eMail = $newMail;
            $customer->active = false;
            $code = mt_rand(100000,999999);
            $customer->activationCode = $code;
            $this->manager->changeData($customer);
            $this->operationStatus = true;
            if($this->manager->operationResult->status == 200) {
                MailUtils::sendActivationMail($newMail, $result->response[0]->fisrtName, $code, "en");
            }

        } else {
            $this->message = "I'm not able to change your information";
            $this->status = DATA_ERROR;
        }
        return $this->operationResult();
    }

    public function upgradeToDriver(){
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return $this->operationResult();
        }
        $customerId = property_exists($this->requestData, "PK") ? $this->requestData->PK : null;
        $password = property_exists($this->requestData, "password") ? $this->requestData->password : null;
        $mail = property_exists($this->requestData, "eMail") ? $this->requestData->eMail : null;
        $drivingNumber = property_exists($this->requestData, "drivingNumber") ? $this->requestData->drivingNumber : null;

        if (!is_numeric($customerId) || empty($password) || empty($mail) || empty($drivingNumber)) {
            $this->message = "No Customer provided";
            $this->status = NO_PROVIDED_CUSTOMER;
            return $this->operationResult();
        }
        
        $this->manager->getData(Customers::class, array("PK", "activate"), array("eMail" => $mail, "password"=>$password));
        $result =  $this->manager->operationResult;
        if( $result->status == 200 && count($result->response)==1 && $result->response[0]->PK ==  $customerId) {
            if($result->response[0]->activate) {
                $customer = new Customers();
                $customer->PK = $this->requestData->PK;
                $customer->drivingNumber = $drivingNumber;
                $this->manager->changeData($customer);
                $this->operationStatus = true;
            } else {
                $this->message = "No active account";
                $this->status = NO_ACTIVE_ACCOUNT;
            }

        } else {
            $this->message = "Data Error";
            $this->status = DATA_ERROR;
        }
        return $this->operationResult();
    }

    public function activateAccount(){
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return $this->operationResult();
        }
        $customerId = property_exists($this->requestData, "PK") ? $this->requestData->PK : null;
        $password = property_exists($this->requestData, "password") ? $this->requestData->password : null;
        $mail = property_exists($this->requestData, "eMail") ? $this->requestData->eMail : null;
        $code = property_exists($this->requestData, "code") ? $this->requestData->code : null;

        if (!is_numeric($customerId) || empty($password) || empty($mail) || !is_numeric($code)) {
            $this->message = "No Customer provided";
            $this->status = NO_PROVIDED_CUSTOMER;
            return $this->operationResult();
        }

        $this->manager->getData(Customers::class, array("PK", "activationCode"), array("eMail" => $mail, "password"=>$password));
        $result =  $this->manager->operationResult;
        if( $result->status == 200 && count($result->response)==1 && $result->response[0]->PK ==  $customerId && $result->response[0]->activationCode == $code) {
                $customer = new Customers();
                $customer->PK = $customerId;
                $customer->active = true;
                $this->manager->changeData($customer);
                $this->operationStatus = true;

        } else {
            $this->message = "Data Error";
            $this->status = DATA_ERROR;
        }
        return $this->operationResult();
    }

    protected function operationResult()
    {
        return $this->operationStatus ? $this->manager->operationResult : array("status" => $this->status, "errorMessage"=>$this->message);
    }
}
?>