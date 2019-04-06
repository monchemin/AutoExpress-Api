<?php
/**
 * class of generic functions
 */
namespace FactorOperations;
error_reporting(E_ERROR | E_PARSE);

use Doctrine\Common\Annotations\AnnotationReader;
use FactorAnnotations\TableColumn;
use FactorAnnotations\TableName;
use Entities;



class FactorUtils {
    /*
     @get params and values as array
     @ return insert query
     */
    public static function makeInsertQuery($insertObject) {
        self::loadFile($strClassName);
        $classProperties = get_class_vars(get_class($insertObject));
        $tableName = self::getTableName(get_class($insertObject)); 
        $queryValues = array(); //query values as pdo statement (:columnName)
        $queryVars = array(); // query var (:columnName => value)
        $tableColumns = array();
        foreach($classProperties as $property => $value) {
            // if key has annotation, tableColumn takes annotation value
            $tableColumns[] = $property;
            $tableColumn = self::getTableColumn(get_class($insertObject), $property);
            $var = ":" . $tableColumn;
            $queryValues[] = $var;
            $queryVars[$var] = $insertObject->$property; 
        }
       
        $statement = " INSERT INTO " 
                    . $tableName . "(" 
                    . implode("," , $tableColumns)
                    . ") VALUES (" . implode("," , $queryValues)
                    . ")";
                   
        return array("query" => $statement, "sqlVars" => $queryVars);
    }
    /**
     * prepare update query string
     * @input(updateObjet : object to update, 
     *          paramsArray : list of modified column and new value,
     *          filters : update's conditions
     *          )
     * @return : query string
     */
    public static function makeUpdateQuery($updateObject, $filters=null) {
        self::loadFile(get_class($updateObject));
        $classProperties = get_class_vars(get_class($updateObject));
        
        $tableName = self::getTableName(get_class($updateObject));
        $queryValues = array(); //query values as pdo statement (:columnName)
        $modificationColumns = array(); // query var (:columnName => value)
        $whereClause = array();

        foreach($classProperties as $property=>$value) {
            
            $tableColumn = self::getTableColumn(get_class($updateObject), $property);

            if( self::isPrimaryKey($updateObject, $property) ) {
               
                $var = ":" . $tableColumn;
                $whereClause[] = $tableColumn . "=". $var;
                $queryValues[$var] = $updateObject->$property;
            }
            if($updateObject->$property != null) {
                $var = ":" . $tableColumn;
                $modificationColumns[] = $tableColumn . "=" .  $var;
                $queryVars[$var] = $updateObject->$property;
            }

        }
        

        $statement = " UPDATE " 
            . $tableName . " SET " 
            . implode("," , $modificationColumns)
            . " WHERE " . implode("AND " , $whereClause);
          
        return array("query" => $statement, "sqlVars" => $queryVars);
        
    }

    public static function makeDeleteQuery($deleteObject, $filters=null) {
        $tableName = self::getTableName(get_class($deleteObject)); 
        $whereClause = array();
        $queryVars = array();
        if($filters == null) {
            $classProperties = get_class_vars(get_class($deleteObject)); 
            foreach($classProperties as $property => $value) {
                // if key has annotation, tableColumn takes annotation value
                if( self::isPrimaryKey($deleteObject, $property) ) {
                    $tableColumn = self::getTableColumn(get_class($deleteObject), $property);
                    $var = ":" . $tableColumn;
                    $whereClause[] = $tableColumn . "=" .  $var;
                    $queryValues[$var] = $deleteObject->$property;
                }
            }
        } else 
        {
            foreach($filters as $field => $value) {
                $tableColumn = self::getTableColumn(get_class($deleteObject), $field);
                $var = ":" . $tableColumn;
                $whereClause[] = $tableColumn . "=" .  $var;
                $queryValues[$var] = $deleteObject->$field;
            }

        }

        $statement = " DELETE FROM " 
            . $tableName  
            . " WHERE " . implode("AND " , $whereClause);

        return array("query" => $statement, "sqlVars" => $queryValues);
    }


    public static function makeGetDataQuery($strClassName, $fieldList, $whereArray, $orderByArray) {
        self::loadFile($strClassName);
        $filters = array();
        $whereClause = array();
        $orderBy = array();
        $queryValues = array();
        $classProperties = get_class_vars($strClassName);
        foreach($classProperties as $property=> $value) {
            $tableColumn = self::getTableColumn($strClassName, $property) !==null ? self::getTableColumn($strClassName, $property) : $property;
            if( in_array($property, $fieldList) ) $filters[] = $tableColumn;
            if( in_array($property, $orderByArray) ) $orderBy[] = $tableColumn;
            if( array_key_exists($property, $whereArray) ) {
                $var = ":".$tableColumn;
                $whereClause[] = $tableColumn."=".$var;
                $queryValues[$var] = $whereArray[$property];

            }
            
        }

        $select = empty($filters) ? " * " : implode(", ", $filters);
        $statement = "SELECT ". $select . " FROM " 
                        . self::getTableName($strClassName);
        if(!empty($whereClause) ) $statement .= " WHERE " . implode(" AND " , $whereClause);
        if(!empty($orderBy) )  $statement .= " ORDER BY ". implode(',', $orderBy);
                        
        return array("query" => $statement, "sqlVars" => $queryValues);
    }

    

    protected static function getTableName($strClassName) {
        self::loadFile($strClassName);
        $reflectedClass = new \ReflectionClass($strClassName);
        $classAnnotations = self::reader()->getClassAnnotations($reflectedClass);
        return $classAnnotations[0]->value;
    } 

    protected static function getTableColumn($tableInstance, $instanceField) {
        
        $reflectedAttr = new \ReflectionProperty($tableInstance, $instanceField);
        return   $reflectedAttr !== null ? self::reader()->getPropertyAnnotation($reflectedAttr, 'FactorAnnotations\TableColumn')->columnName : $instanceField;

    }

    protected static function isPrimaryKey($tableInstance, $instanceField) {
        $reflectedAttr = new \ReflectionProperty($tableInstance, $instanceField);
        return   $reflectedAttr !== null && self::reader()->getPropertyAnnotation($reflectedAttr, 'FactorAnnotations\TableColumn')->isPK !==null ? true : false;
    }

    protected static function reader () {
        return new AnnotationReader();
    }

    public static function getPropertyBindColumn($strClassName) {
        self::loadFile($strClassName);
        $classProperties = get_class_vars($strClassName);
        $bindArray = array();
        foreach($classProperties as $property => $value) {
            $tableColumn = self::getTableColumn($strClassName, $property) !==null ? self::getTableColumn($strClassName, $property) : $property;
            $bindArray[$property] = $tableColumn;
        }
        return $bindArray;

    }
    private static function loadFile($className) {
        $fileArray = explode("\\", $className);
        $searchFile = $fileArray[count($fileArray)-1].".php";
        require_once join(DIRECTORY_SEPARATOR, ['entities', $searchFile]);
    }
}
?>