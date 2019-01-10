<?php
namespace Entities;
use FactorAnnotations\TableName;
use FactorAnnotations\TableColumn;
/**
 * @TableName(value="carcolor")
 */
class CarColors {
    /**
     * @TableColumn(columnName="PK", isPK="1")
     */
    public $PK;
    /**
     * @TableColumn(columnName="colorName")
     */
    public $colorName;

}
?>