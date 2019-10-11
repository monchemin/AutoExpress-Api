<?php

namespace FactorOperations;

/* BaobadManager : classe manager qui gère la communication avec l'interface utilisateur
*   @version 1.0
*   @author : NK
*
*/
require_once 'ManagerOperationResult.php';
use FactorData\FactorMysqlManager;

final class FactorManager {
    
    protected $connexion;
    const insert = 1;
    const update = 2;
    const delete = 3;
    public $operationResult;

    protected function __construct($arrayConfig) {
        $this->connexion = $this -> makeConnexion($arrayConfig);
        $this->retrieveResult();

    }
    private function retrieveResult() {
        $this->operationResult = new ManagerOperationResult();
        $this->operationResult->status = $this->connexion->operationResult()->status;
        $this->operationResult->errorMessage = $this->connexion->operationResult()->errorMessage;
        $this->operationResult->lastIndex = $this->connexion->operationResult()->lastIndex;
        $this->operationResult->response = $this->connexion->operationResult()->resultData;
}
    // make connection with configuration table
    protected function makeConnexion($arrayConfig) {
        switch (strtolower($arrayConfig['dbdriver'])) {
            case  'mysql' :
                return FactorMysqlManager::getConnexion($arrayConfig);
                break;
            default :
                return null;
        }
        
    }
/**
 * information simplify by using class's instance.
 */

    public function insertData($insertObject, $lastInsert=true) {

        $this->executeQuery($insertObject, $lastInsert, self::insert);
    }
  
    public function changeData($insertObject, $lastInsert=false) {

        $this->executeQuery($insertObject, $lastInsert, self::update);
        
    }
  
    public function deleteData($insertObject) {
        $this->executeQuery($insertObject, $lastInsert=false, self::delete);
    }
  
   
    // get records by criteria
    public function getData($strClassName, $fieldList=array(), $whereArray=array(), $orderByArray=array()) {
        //print_r($whereArray);
        $queryAndParams = FactorUtils::makeGetDataQuery($strClassName, $fieldList, $whereArray, $orderByArray);
        //echo $queryAndParams['query'];
        $this->connexion->getData($queryAndParams['query'], $queryAndParams['sqlVars']);
        $this->retrieveResult();
        $queryResults = $this->operationResult->response;
        $bind = FactorUtils::getPropertyBindColumn($strClassName);
        $resultInstance = array();
        if($this->operationResult->status == 200) {
            if ($queryResults !== null) {
                foreach ($queryResults As $row) {
                    $class = new \ReflectionClass($strClassName);
                    $newInstance = $class->newInstance();
                    foreach ($row as $col => $value) {
                        $property = array_search($col, $bind);
                        if ($property !== false) $newInstance->$property = $value;
                    }
                    $resultInstance[] = $newInstance;
                }

                $this->operationResult->response = $resultInstance;
            }
            

        }
       
    }

/**
 * data retrieve by complexe query
 */
    public function getDataByQuery($query, $params) {
        //return new EntityManager($query, $params, $this->connexion);
        $this->connexion->getData($query, $params);
        $this->retrieveResult();
    }

    protected function getObjectVarsValues($insertObject) {
       // FactorUtils::

    }

    protected function executeQuery($insertObject, $lastInsert, $operation) {
        $queryAndParams = array();
        switch ($operation) {
            case self::insert :
                $queryAndParams = FactorUtils::makeInsertQuery($insertObject);
                break;
            case self::update :
                $queryAndParams = FactorUtils::makeUpdateQuery($insertObject);
               break;
            case self::delete :
                $queryAndParams = FactorUtils::makedeleteQuery($insertObject);
                break;
        }
         $this->connexion->modifyData($queryAndParams['query'], $queryAndParams['sqlVars'], $lastInsert);
        $this->retrieveResult();

        
    }

    public function execute($query, $params, $lastInsert) {
        $this->connexion->modifyData($query, $params, $lastInsert);
        $this->retrieveResult();
    }

    // fonction d'entrée pour assurer une seule instance du manager 
    public static function create($arrayConfig) {
        return  ( is_array($arrayConfig) &&  count($arrayConfig) >= 4 ) ?   new FactorManager($arrayConfig) : null; 
        
    }

}
?>