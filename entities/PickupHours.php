<?php
namespace Entities;
use FactorAnnotations\TableName;
use FactorAnnotations\TableColumn;
/**
 * @TableName(value="pickuphour")
 */
class PickupHours {
    /**
     * @TableColumn(columnName="PK", isPK="1")
     */
    public $PK;
    /**
     * @TableColumn(columnName="hour")
     */
    public $hour;
    /**
     * @TableColumn(columnName="displayOrder")
     */
    public $displayOrder;
}
?>