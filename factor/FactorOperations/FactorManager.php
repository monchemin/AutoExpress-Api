<?php

namespace FactorOperations;

/* BaobadManager : classe manager qui gère la communication avec l'interface utilisateur
*   @version 1.0
*   @author : NK
*
*/
use FactorData\FactorMysqlManager;

final class FactorManager {
    
    protected $connexion;
    const insert = 1;
    const update = 2;
    const delete = 3;
    public $managerOperationResult;

    protected function __construct($arrayConfig) {
        $this->connexion = $this -> makeConnexion($arrayConfig);
        $this->retrieveResult();

    }
    private function retrieveResult() {
        $this->managerOperationResult = new ManagerOperationResult();
        $this->managerOperationResult->status = $this->connexion->operationResult()->status;
        $this->managerOperationResult->errorMessage = $this->connexion->operationResult()->errorMessage;
        $this->managerOperationResult->lastIndex = $this->connexion->operationResult()->lastIndex;
        $this->managerOperationResult->resultData = $this->connexion->operationResult()->resultData;
}
    // choisi la connexion par rapport au driver spécifier dans le tableau de configuration
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
        $queryAndParams = FactorUtils::makeGetDataQuery($strClassName, $fieldList, $whereArray, $orderByArray);
        $this->connexion->getData($queryAndParams['query'], $queryAndParams['sqlVars']);
        $this->retrieveResult();
        $queryResults = $this->managerOperationResult->resultData;
        $bind = FactorUtils::getPropertyBindColumn($strClassName);
        $resultInstance = array();
        if($this->managerOperationResult->status == 200) {
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

                $this->managerOperationResult->resultData = $resultInstance;
            }
            else $this->managerOperationResult->status = 110;

        }
    }

/**
 * data retrieve by complexe query
 */
    public function getDataByQuery($query, $params) {
        return new EntityManager($query, $params, $this->connexion);
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


    // fonction d'entrée pour assurer une seule instance du manager 
    public static function create($arrayConfig) {
        return  ( is_array($arrayConfig) &&  count($arrayConfig) >= 4 ) ?   new FactorManager($arrayConfig) : null; 
        
    }

}
?>