<?php
namespace Entities;
use FactorAnnotations AS ORM;
/**
 * 
 * @ORM\TableName(value="Stage")
 */
class Stages {
 /**
     * @ORM\TableColumn(columnName="PK", isPK="1")
     */
    public $PK;
    /**
     * @ORM\TableColumn(columnName="stageName")
     */
    public $stageName;

    /**
     * @ORM\TableColumn(columnName="stageAddress")
     */
    public $stageAddress;

    /**
     * @ORM\TableColumn(columnName="FK_Zone")
     */
    public $FK_Zone;

}
?>