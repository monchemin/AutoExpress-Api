<?php
namespace FactorAnnotations;
use Doctrine\Common\Annotations;
/**
 * @Annotation 
 */
 class TableName {
    public $value;

}

/**
 * @Annotation 
 */
 class TableColumn  {
    public $columnName;
    public $isPK;
}
?>