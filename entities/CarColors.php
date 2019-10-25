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
    public $Id;
    /**
     * @TableColumn(columnName="colorName")
     */
    public $colorName;

}
?>