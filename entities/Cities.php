<?php
namespace Entities;
use FactorAnnotations\TableName;
use FactorAnnotations\TableColumn;
/**
 * @TableName(value="city")
 */
class Cities {
    /**
     * @TableColumn(columnName="pk", isPK="1")
     */
    public $id;
    /**
     * @TableColumn(columnName="city_name")
     */
    public $cityName;

}
?>