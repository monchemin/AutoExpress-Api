<?php
namespace Operations;

use Entities\Users;
use FactorOperations\FactorManager;

require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);

class UserOperation extends OperationBase {

    private $message = "authorisation required";
    private $status = NO_AUTHORIZATION;

    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

       //  ($this->pk != 0) ?  $this->readOne($this->pk) : $this->manager->getData(Users::class);
       // $this->operationStatus = true;

    }

    protected function create()
    {

        if($this->requestData != null) {
            $user = new Users();
            $user->userName = property_exists($this->requestData, "userName") ? $this->requestData->userName : null;
            $user->userLogin = property_exists($this->requestData, "userLogin") ? $this->requestData->userLogin : null;
            $user->userPassword = property_exists($this->requestData, "userPassword") ? $this->requestData->userPassword : null;
            //$user->customerDateCreate = date("Y-m-d H:i:s");
            $this->manager->insertData($user);
            $this->operationStatus = true;
        }
    }

    protected function update()
    {
        if( $this->requestData != null && property_exists($this->requestData, "PK")) {
            $user = new Users();
            $user->PK = $this->requestData->PK;
            if (property_exists($this->requestData, "userName")) $user->userName = $this->requestData->userName;
            if (property_exists($this->requestData, "userPassword")) $user->userPassword = $this->requestData->userPassword;
            $this->manager->changeData($user);
            $this->readOne($user->PK);
            $this->operationStatus = true;
        }

    }
    protected function readOne($pk) {
         $this->manager->getData(Users::class, array(), array("PK" => $pk));
    }

    protected function delete()
    {
        //if($this->requestData != null && property_exists($this->requestData, "PK")) {
            if($this->pk != 0) {
            $user = new Users();

            $user->PK = $this->pk;
            $this->manager->deleteData($user);
            $this->operationStatus = true;
        }
    }
    private function loginExists()
    {
        $loginExists = false;
        $this->manager->getData(Users::class, array(), array("userLogin" => $this->requestData->checkLogin));
        $loginResult = $this->manager->operationResult;
        if($loginResult->status == 200 && $loginResult->response != null) $loginExists = true;
        return array("status" => $loginResult->status, "loginExists" => $loginExists);
    }

    public function login() {
        if( $this->requestData != null && property_exists($this->requestData, "login")) {
            $this->manager->getData(Users::class, array(), 
                    array("userLogin" => $this->requestData->login, "userPassword"=>$this->requestData->password)
                );
            if($this->manager->operationResult->status == 200 && count($this->manager->operationResult->response)==1 ) {
                return array("status"=>200, "isLog"=> true);
            } else {return array("status"=>120, "isLog"=> false);}
        
        }
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
    protected function operationResult()
    {
        return $this->operationStatus ? $this->manager->operationResult : array("status" => $this->status, "errorMessage" => $this->message);
    }
}
?>