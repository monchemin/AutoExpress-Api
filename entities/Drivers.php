<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="driver")
 */
class Drivers {
    /**
     * @ORM\TableColumn(columnName="PK", isPK="1")
     */
    public $PK;

    /**
     * @ORM\TableColumn(columnName="drivingPermitNumber")
     */
    public $drivingPermitNumber;

    /**
     * @ORM\TableColumn(columnName="carRegistrationNumber")
     */
    public $carRegistrationNumber;

    /**
     * @ORM\TableColumn(columnName="carYear")
     */
    public $carYear;

    /**
     * @ORM\TableColumn(columnName="FK_carmodel")
     */
    public $FK_carmodel;

    /**
     * @ORM\TableColumn(columnName="FK_carcolor")
     */
    public $FK_carcolor;
    /**
     * @ORM\TableColumn(columnName="driverDateCreate")
     */
    public $driverDateCreate;


}
?>