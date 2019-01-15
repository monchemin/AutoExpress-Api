<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="route")
 */
class Routes {
    /**
     * @ORM\TableColumn(columnName="PK", isPK="1")
     */
    public $PK;
    /**
     * @ORM\TableColumn(columnName="routeDate")
     */
    public $routeDate;

    /**
     * @ORM\TableColumn(columnName="routePlace")
     */
    public $routePlace;

    /**
     * @ORM\TableColumn(columnName="routePrice")
     */
    public $routePrice;

    /**
     * @ORM\TableColumn(columnName="FK_Hour")
     */
    public $FK_Hour;

    /**
     * @ORM\TableColumn(columnName="FK_Driver")
     */
    public $FK_Driver;
    /**
     * @ORM\TableColumn(columnName="FK_DepartureStage")
     */
    public $FK_DepartureStage;
    /**
     * @ORM\TableColumn(columnName="FK_ArrivalStage")
     */
    public $FK_ArrivalStage;

}
?>