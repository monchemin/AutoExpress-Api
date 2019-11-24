<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="carmodel")
 */
class CarModels {
 /**
     * @ORM\TableColumn(columnName="pk", isPK="1")
     */
    public $id;
    /**
     * @ORM\TableColumn(columnName="model_name")
     */
    public $modelName;

    /**
     * @ORM\TableColumn(columnName="fk_brand")
     */
    public $fkBrand;

}
?>