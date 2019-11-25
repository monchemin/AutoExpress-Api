<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="zone")
 */
class Zones {
 /**
     * @ORM\TableColumn(columnName="pk", isPK="1")
     */
    public $id;
    /**
     * @ORM\TableColumn(columnName="zone_name")
     */
    public $zoneName;

    /**
     * @ORM\TableColumn(columnName="fk_city")
     */
    public $fkCity;

}
?>