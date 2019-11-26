<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="reservation")
 */
class Reservations {
    /**
     * @ORM\TableColumn(columnName="pk", isPK="1")
     */
    public $id;

    /**
     * @ORM\TableColumn(columnName="reservation_date")
     */
    public $reservationDate;

    /**
     * @ORM\TableColumn(columnName="fk_route")
     */
    public $fkRoute;

    /**
     * @ORM\TableColumn(columnName="fk_customer")
     */
    public $fkCustomer;

     /**
     * @ORM\TableColumn(columnName="place")
     */
    public $place;

    /**
     * @ORM\TableColumn(columnName="deleted_at")
     */
    public $deletedAt;
}
?>