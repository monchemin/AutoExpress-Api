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
     * @ORM\TableColumn(columnName="customerFistName")
     */
    public $customerFistName;

    /**
     * @ORM\TableColumn(columnName="customerLastName")
     */
    public $customerLastName;
    /**
     * @ORM\TableColumn(columnName="customerPhoneNumber")
     */
    public $customerPhoneNumber;

    /**
     * @ORM\TableColumn(columnName="customerEMailAddress")
     */
    public $customerEMailAddress;
    /**
     * @ORM\TableColumn(columnName="customerLogin")
     */
    public $customerLogin;

    /**
     * @ORM\TableColumn(columnName="customerPassword")
     */
    public $customerPassword;

}
?>