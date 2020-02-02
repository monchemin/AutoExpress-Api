<?php
namespace Entities;
use FactorAnnotations\TableName;
use FactorAnnotations\TableColumn;
/**
 * @TableName(value="carcolor")
 */
class CarColors {
    /**
     * @TableColumn(columnName="pk", isPK="1")
     */
    public $Id;
    /**
     * @TableColumn(columnName="color_name")
     */
    public $colorName;
    /**
     * @TableColumn(columnName="color_label")
     */
    public $colorLabel;

}
?>