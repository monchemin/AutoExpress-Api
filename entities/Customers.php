<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="customer")
 */
class Customers {
 /**
     * @ORM\TableColumn(columnName="PK", isPK="1")
     */
    public $PK;
    /**
     * @ORM\TableColumn(columnName="firstName")
     */
    public $firstName;

    /**
     * @ORM\TableColumn(columnName="lastName")
     */
    public $lastName;
    /**
     * @ORM\TableColumn(columnName="phoneNumber")
     */
    public $phoneNumber;

    /**
     * @ORM\TableColumn(columnName="eMail")
     */
    public $eMail;
    /**
     * @ORM\TableColumn(columnName="password")
     */
    public $password;

    /**
     * @ORM\TableColumn(columnName="createdAt")
     */
    public $createdAt;
    /**
     * @ORM\TableColumn(columnName="drivingNumber")
     */
    public $drivingNumber;

}
?>