<?php
namespace Entities;
use FactorAnnotations\TableName;
use FactorAnnotations\TableColumn;
/**
 * @TableName(value="city")
 */
class Cities {
    /**
     * @TableColumn(columnName="PK", isPK="1")
     */
    public $PK;
    /**
     * @TableColumn(columnName="cityName")
     */
    public $cityName;

}
?>