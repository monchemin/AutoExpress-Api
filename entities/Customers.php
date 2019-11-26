<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="customer")
 */
class Customers {
 /**
     * @ORM\TableColumn(columnName="pk", isPK="1")
     */
    public $Id;
    /**
     * @ORM\TableColumn(columnName="first_name")
     */
    public $firstName;

    /**
     * @ORM\TableColumn(columnName="last_name")
     */
    public $lastName;
    /**
     * @ORM\TableColumn(columnName="phone_number")
     */
    public $phoneNumber;

    /**
     * @ORM\TableColumn(columnName="e_mail")
     */
    public $eMail;
    /**
     * @ORM\TableColumn(columnName="password")
     */
    public $password;

    /**
     * @ORM\TableColumn(columnName="created_at")
     */
    public $createdAt;
    /**
     * @ORM\TableColumn(columnName="driving_number")
     */
    public $drivingNumber;

    /**
     * @ORM\TableColumn(columnName="activation_code")
     */
    public $activationCode;

    /**
     * @ORM\TableColumn(columnName="active")
     */
    public $active;
}
?>