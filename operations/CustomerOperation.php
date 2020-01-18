<?php

namespace Operations;

use Entities\Customers;
use FactorOperations\FactorManager;
use Queries\QueryBuilder;
use utils\MailUtils;

require_once join(DIRECTORY_SEPARATOR, ['entities', 'Customers.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'MailUtils.php']);
require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);
require_once join(DIRECTORY_SEPARATOR, ['queries', 'QueryBuilder.php']);


class CustomerOperation extends OperationBase
{

    private $message = "authorisation required";
    private $status = NO_AUTHORIZATION;

    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {

        ($this->Id != 0) ? $this->readOne($this->Id) : $this->manager->getData(Customers::class);
        $this->operationStatus = true;

    }

    protected function create()
    {
        $firstName = property_exists($this->requestData, "firstName") ? $this->requestData->firstName : null;
        $lastName = property_exists($this->requestData, "lastName") ? $this->requestData->lastName : null;
        $mail = property_exists($this->requestData, "eMail") ? $this->requestData->eMail : null;
        $number = property_exists($this->requestData, "phoneNumber") ? $this->requestData->phoneNumber : null;
        $pass = property_exists($this->requestData, "password") ? $this->requestData->password : null;
        $language = property_exists($this->requestData, "language") ? $this->requestData->language : "fr";

        if ($firstName === null || $lastName === null || $mail === null || $number === null || $pass === null) {
            $this->message = "Error in provided Data";
            $this->status = DATA_ERROR;
            return;
        }

        if ($this->loginExists()) {
            $this->message = "Email already exists.";
            $this->status = LOGIN_EXISTS;
            return;

        }
        $customer = new Customers();
        $customer->firstName = $firstName;
        $customer->lastName = $lastName;
        $customer->eMail = $mail;
        $customer->phoneNumber = $number;
        $customer->password = md5($pass);
        $customer->createdAt = date("Y-m-d H:i:s");
        $code = mt_rand(100000, 999999);
        $customer->activationCode = $code;
        $this->manager->insertData($customer);
        if ($this->manager->operationResult->status == 200) {
            $this->operationStatus = true;
            MailUtils::sendActivationMail($mail, $lastName, $code, $language);
        }
    }

    protected function update()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return;
        }
        if (!property_exists($this->requestData, "Id")) {
            $this->message = "No Customer provided";
            $this->status = NO_PROVIDED_CUSTOMER;
            return;
        }

        $customerId = $this->requestData->Id;

        $this->manager->getData(Customers::class, array("Id"),
            array("eMail" => $this->requestData->eMail, "Id" => $customerId));
        if ($this->manager->operationResult->status == 200
            && count($this->manager->operationResult->response) == 1
            && $this->manager->operationResult->response[0]->Id == $customerId) {

            $customer = new Customers();
            $customer->Id = $customerId;
            if (property_exists($this->requestData, "firstName") && !empty($this->requestData)) {
                $customer->firstName = $this->requestData->firstName;
            }
            if (property_exists($this->requestData, "lastName") && !empty($this->lastName)) {
                $customer->lastName = $this->requestData->lastName;
            }
            if (property_exists($this->requestData, "phoneNumber") && !empty($this->phoneNumber)) {
                $customer->phoneNumber = $this->requestData->phoneNumber;
            }
            $this->manager->changeData($customer);
            if ($this->manager->operationResult->status == 200) {
                $this->operationStatus = true;
            } else {
                $this->message = "I'm not able to change your information";
                $this->status = SQL_ERROR;
            }

        } else {
            $this->message = "data error";
            $this->status = DATA_ERROR;
        }

    }

    public function readOne($pk)
    {
        $this->manager->getData(Customers::class, array(), array("Id" => $pk));
        $loginResult = $this->manager->operationResult;
        return ($loginResult->status == 200 && $loginResult->response != null) ? $loginResult->response[0] : null;
    }

    protected function delete()
    {
        if ($this->Id != 0) {
            $customer = new Customers();

            $customer->Id = $this->Id;
           // $this->manager->deleteData($customer);
            $this->operationStatus = true;
        }
    }

    private function loginExists()
    {
        $loginExists = false;
        $this->manager->getData(Customers::class, array(), array("eMail" => $this->requestData->eMail));
        $loginResult = $this->manager->operationResult;
        if ($loginResult->status == 200 && $loginResult->response != null) $loginExists = true;
        return $loginExists;
    }


    public function process()
    {

        switch ($this->httpMethod) {
            case "POST" :
                if ($this->requestData != null && property_exists($this->requestData, "checkLogin")) return $this->loginExists();
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

    public function login()
    {
        $response = array();
        if ($this->requestData != null && property_exists($this->requestData, "login")) {
            $this->manager->getData(Customers::class, array("Id", "firstName", "lastName", "eMail", "phoneNumber", "drivingNumber", "active"),
                array("eMail" => $this->requestData->login, "password" => md5($this->requestData->password))
            );
            if ($this->manager->operationResult->status == 200 && count($this->manager->operationResult->response) == 1) {
                $response['status'] = 200;
                $response['response'] = $this->manager->operationResult->response;
            } else {

                $response['status'] = SQL_ERROR;
            }

        } else {
            $response['status'] = NO_PROVIDED_DATA;
        }
        return $response;
    }

    public function changePassword()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return $this->operationResult();
        }
        $customerId = property_exists($this->requestData, "Id") ? $this->requestData->Id : null;
        $oldPassword = property_exists($this->requestData, "oldPassword") ? $this->requestData->oldPassword : null;
        $newPassword = property_exists($this->requestData, "newPassword") ? $this->requestData->newPassword : null;
        $mail = property_exists($this->requestData, "eMail") ? $this->requestData->eMail : null;
        if (!is_numeric($customerId) || empty($oldPassword) || empty($newPassword) || empty($mail)) {
            $this->message = "Empty Data";
            $this->status = DATA_EMPTY;
            return $this->operationResult();
        }

        $this->manager->getData(Customers::class, array("Id"), array("eMail" => $mail, "password" => $oldPassword));
        $result = $this->manager->operationResult;
        if ($result->status == 200 && count($result->response) == 1 && $result->response[0]->Id == $customerId) {
            $customer = new Customers();
            $customer->Id = $customerId;
            $customer->password = md5($newPassword);
            $this->manager->changeData($customer);
            $this->operationStatus = true;
        } else {
            $this->message = "I'm not able to change your information";
            $this->status = DATA_ERROR;
        }
        return $this->operationResult();
    }

    public function changeMail()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return $this->operationResult();
        }
        $customerId = property_exists($this->requestData, "Id") ? $this->requestData->Id : null;
        $password = property_exists($this->requestData, "password") ? $this->requestData->password : null;
        $oldMail = property_exists($this->requestData, "oldMail") ? $this->requestData->oldMail : null;
        $newMail = property_exists($this->requestData, "newMail") ? $this->requestData->newMail : null;
        $language = property_exists($this->requestData, "language") ? $this->requestData->language : "fr";
        if (!is_numeric($customerId) || empty($password) || empty($oldMail) || empty($newMail)) {
            $this->message = "Data empty";
            $this->status = DATA_EMPTY;
            return $this->operationResult();
        }

        $this->manager->getData(Customers::class, array("Id", "firstName"), array("eMail" => $oldMail, "password" => $password));
        $result = $this->manager->operationResult;
        if ($result->status == 200 && count($result->response) == 1 && $result->response[0]->Id == $customerId) {
            $customer = new Customers();
            $customer->Id = $customerId;
            $customer->eMail = $newMail;
            $customer->active = 0;
            $code = mt_rand(100000, 999999);
            $customer->activationCode = $code;
            $this->manager->changeData($customer);
            $this->operationStatus = true;
            if ($this->manager->operationResult->status == 200) {
                //echo "send mail ". $code;
                MailUtils::sendActivationMail($newMail, $result->response[0]->fisrtName, $code, $language);
            }

        } else {
            $this->message = $result->errorMessage;
            $this->status = DATA_ERROR;
        }
        return $this->operationResult();
    }

    public function upgradeToDriver()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return $this->operationResult();
        }
        $customerId = property_exists($this->requestData, "Id") ? $this->requestData->Id : null;
        $password = property_exists($this->requestData, "password") ? $this->requestData->password : null;
        $mail = property_exists($this->requestData, "eMail") ? $this->requestData->eMail : null;
        $drivingNumber = property_exists($this->requestData, "drivingNumber") ? $this->requestData->drivingNumber : null;

        if (!is_numeric($customerId) || empty($password) || empty($mail) || empty($drivingNumber)) {
            $this->message = "Data empty";
            $this->status = DATA_EMPTY;
            return $this->operationResult();
        }

        $this->manager->getData(Customers::class, array("Id", "active"), array("eMail" => $mail, "password" => $password));
        $result = $this->manager->operationResult;
        if ($result->status == 200 && count($result->response) == 1 && $result->response[0]->Id == $customerId) {
            if ($result->response[0]->active) {
                $customer = new Customers();
                $customer->Id = $this->requestData->Id;
                $customer->drivingNumber = $drivingNumber;
                $this->manager->changeData($customer);
                $this->operationStatus = true;
            } else {
                $this->message = "No active account";
                $this->status = NO_ACTIVE_ACCOUNT;
            }

        } else {
            $this->message = $result->errorMessage;
            $this->status = SQL_ERROR;
        }
        return $this->operationResult();
    }

    public function activateAccount()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return $this->operationResult();
        }
        $customerId = property_exists($this->requestData, "Id") ? $this->requestData->Id : null;
        $password = property_exists($this->requestData, "password") ? $this->requestData->password : null;
        $mail = property_exists($this->requestData, "eMail") ? $this->requestData->eMail : null;
        $code = property_exists($this->requestData, "code") ? $this->requestData->code : null;

        if (!is_numeric($customerId) || empty($password) || empty($mail) || !is_numeric($code)) {
            $this->message = "No Customer provided";
            $this->status = NO_PROVIDED_CUSTOMER;
            return $this->operationResult();
        }

        $this->manager->getData(Customers::class, array("Id", "activationCode"), array("eMail" => $mail, "password" => $password));
        $result = $this->manager->operationResult;
        if ($result->status == 200 && count($result->response) == 1 && $result->response[0]->Id == $customerId && $result->response[0]->activationCode == $code) {
            $customer = new Customers();
            $customer->Id = $customerId;
            $customer->active = true;
            $this->manager->changeData($customer);
            $this->operationStatus = true;

        } else {
            $this->message = "Data Error";
            $this->status = DATA_ERROR;
        }
        return $this->operationResult();
    }

    public function passwordRecovery()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return $this->operationResult();
        }

        $newMail = property_exists($this->requestData, "eMail") ? $this->requestData->eMail : null;
        $language = property_exists($this->requestData, "language") ? $this->requestData->language : "fr";
        if (empty($newMail)) {
            $this->message = "Data empty";
            $this->status = DATA_EMPTY;
            return $this->operationResult();
        }

       $genPass = $this->generateStrongPassword();
        $query = QueryBuilder::passwordRecovery();
        $vars = array(':password' => md5($genPass), ':email' => $newMail);
        $this->manager->execute($query, $vars, false);
        MailUtils::sendPasswordRecoveryMail($newMail, $genPass, $language);
        $this->operationStatus = true;
        return $this->operationResult();
    }

    protected function operationResult()
    {
        return $this->operationStatus ? $this->manager->operationResult : array("status" => $this->status, "errorMessage" => $this->message);
    }

    function generateStrongPassword($length = 6, $add_dashes = false, $available_sets = 'luds')
    {
        $sets = array();
        if(strpos($available_sets, 'l') !== false)
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        if(strpos($available_sets, 'u') !== false)
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        if(strpos($available_sets, 'd') !== false)
            $sets[] = '23456789';
        if(strpos($available_sets, 's') !== false)
            $sets[] = '!@#$%&*?';

        $all = '';
        $password = '';
        foreach($sets as $set)
        {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);
        for($i = 0; $i < $length - count($sets); $i++)
            $password .= $all[array_rand($all)];

        $password = str_shuffle($password);

        if(!$add_dashes)
            return $password;

        $dash_len = floor(sqrt($length));
        $dash_str = '';
        while(strlen($password) > $dash_len)
        {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }
        $dash_str .= $password;
        return $dash_str;
    }
}

?>