<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="station")
 */
class Stations {
 /**
     * @ORM\TableColumn(columnName="PK", isPK="1")
     */
    public $PK;
    /**
     * @ORM\TableColumn(columnName="stationName")
     */
    public $stationName;

    /**
     * @ORM\TableColumn(columnName="stationAddress")
     */
    public $stationAddress;

    /**
     * @ORM\TableColumn(columnName="FK_Zone")
     */
    public $FK_Zone;

    /**
     * @ORM\TableColumn(columnName="stationDetail")
     */
    public $stationDetail;

}
?>