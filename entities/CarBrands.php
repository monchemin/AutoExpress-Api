<?php
namespace Entities;
use FactorAnnotations\TableName;
use FactorAnnotations\TableColumn;
/**
 * @TableName(value="carBrand")
 */
class CarBrands {
    /**
     * @TableColumn(columnName="PK", isPK="1")
     */
    public $PK;
    /**
     * @TableColumn(columnName="brandName")
     */
    public $brandName;

}
?>