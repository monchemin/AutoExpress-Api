<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="user")
 */
class Users {
 /**
     * @ORM\TableColumn(columnName="pk", isPK="1")
     */
    public $userId;
    /**
     * @ORM\TableColumn(columnName="user_name")
     */
    public $userName;

    /**
     * @ORM\TableColumn(columnName="user_login")
     */
    public $userLogin;
    /**
     * @ORM\TableColumn(columnName="user_password")
     */
    public $userPassword;

    /**
     * @ORM\TableColumn(columnName="created_at")
     */
    public $createdAt;

    /**
     * @ORM\TableColumn(columnName="deleted_at")
     */
    public $deletedAt;

}
?>