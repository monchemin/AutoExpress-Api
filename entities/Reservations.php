<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="reservation")
 */
class Reservations {
    /**
     * @ORM\TableColumn(columnName="PK", isPK="1")
     */
    public $PK;

    /**
     * @ORM\TableColumn(columnName="reservationDate")
     */
    public $reservationDate;

    /**
     * @ORM\TableColumn(columnName="FK_Route")
     */
    public $FK_Route;

    /**
     * @ORM\TableColumn(columnName="FK_Customer")
     */
    public $FK_Customer;

     /**
     * @ORM\TableColumn(columnName="place")
     */
    public $place;

    /**
     * @ORM\TableColumn(columnName="cancelled")
     */
    public $cancelled;
}
?>