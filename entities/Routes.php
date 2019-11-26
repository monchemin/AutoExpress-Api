<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="route")
 */
class Routes {
    /**
     * @ORM\TableColumn(columnName="pk", isPK="1")
     */
    public $Id;
    /**
     * @ORM\TableColumn(columnName="route_date")
     */
    public $routeDate;

    /**
     * @ORM\TableColumn(columnName="route_place")
     */
    public $routePlace;

    /**
     * @ORM\TableColumn(columnName="route_price")
     */
    public $routePrice;

    /**
     * @ORM\TableColumn(columnName="fk_hour")
     */
    public $fkHour;

    /**
     * @ORM\TableColumn(columnName="fk_driver")
     */
    public $fkDriver;
    /**
     * @ORM\TableColumn(columnName="fk_departure_stage")
     */
    public $fkDepartureStage;
    /**
     * @ORM\TableColumn(columnName="fk_arrival_stage")
     */
    public $fkArrivalStage;

    /**
     * @ORM\TableColumn(columnName="fk_car")
     */
    public $fkCar;

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