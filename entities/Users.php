<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="user")
 */
class Users {
 /**
     * @ORM\TableColumn(columnName="PK", isPK="1")
     */
    public $PK;
    /**
     * @ORM\TableColumn(columnName="userName")
     */
    public $userName;

    /**
     * @ORM\TableColumn(columnName="userLogin")
     */
    public $userLogin;
    /**
     * @ORM\TableColumn(columnName="userPassword")
     */
    public $userPassword;

}
?>