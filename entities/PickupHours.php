<?php
namespace Entities;
use FactorAnnotations\TableName;
use FactorAnnotations\TableColumn;
/**
 * @TableName(value="pickuphour")
 */
class PickupHours {
    /**
     * @TableColumn(columnName="pk", isPK="1")
     */
    public $id;
    /**
     * @TableColumn(columnName="hour")
     */
    public $hour;
    /**
     * @TableColumn(columnName="display_order")
     */
    public $displayOrder;
}
?>