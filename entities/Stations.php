<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="station")
 */
class Stations {
 /**
     * @ORM\TableColumn(columnName="pk", isPK="1")
     */
    public $id;
    /**
     * @ORM\TableColumn(columnName="station_name")
     */
    public $stationName;

    /**
     * @ORM\TableColumn(columnName="station_address")
     */
    public $stationAddress;

    /**
     * @ORM\TableColumn(columnName="fk_zone")
     */
    public $fkZone;

    /**
     * @ORM\TableColumn(columnName="station_detail")
     */
    public $stationDetail;

}
?>