<?php
namespace Test;


use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="users")
 */
class User
{
   
    /**
     * @ORM\TableColumn(columnName="login", isPK="1")
     */
    public $login;
    /**
     * @ORM\TableColumn(columnName="password")
     */
    public $password;
    /**
     * @ORM\TableColumn(columnName="role")
     */
    public $role;

    

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }
    public function __toString()
    {
        $format = "User (id: %s, firstname: %s, lastname: %s, role: %s)\n";
        return sprintf($format, $this->login,  $this->role);
    }
}
?>