<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="carModel")
 */
class CarModels {
 /**
     * @ORM\TableColumn(columnName="PK", isPK="1")
     */
    public $PK;
    /**
     * @ORM\TableColumn(columnName="modelName")
     */
    public $modelName;

    /**
     * @ORM\TableColumn(columnName="FK_brand")
     */
    public $FK_brand;

}
?>