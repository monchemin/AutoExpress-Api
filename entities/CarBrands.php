<?php
namespace Entities;
use FactorAnnotations\TableName;
use FactorAnnotations\TableColumn;
/**
 * @TableName(value="carbrand")
 */
class CarBrands {
    /**
     * @TableColumn(columnName="pk", isPK="1")
     */
    public $id;
    /**
     * @TableColumn(columnName="brand_name")
     */
    public $brandName;

}
?>