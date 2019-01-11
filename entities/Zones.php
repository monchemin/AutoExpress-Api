<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="Zone")
 */
class Zones {
 /**
     * @ORM\TableColumn(columnName="PK", isPK="1")
     */
    public $PK;
    /**
     * @ORM\TableColumn(columnName="zoneName")
     */
    public $zoneName;

    /**
     * @ORM\TableColumn(columnName="FK_City")
     */
    public $FK_City;

}
?>