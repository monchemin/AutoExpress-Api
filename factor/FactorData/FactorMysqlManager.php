<?php
namespace FactorData;

/*
*/
require_once 'IFactorDbManager.php';
require_once 'DataOperationResult.php';
use PDO;
final class FactorMysqlManager implements IFactorDbManager {

    
    protected $pdo;
    protected $operationResult;
     CONST STATUS_OK = 200;
     CONST STATUS_ERROR = 2002;
     CONST NO_DATA = 202;

    protected function __construct($arrayConfig) {
       $this->operationResult = new DataOperationResult();
        try {
            $this->pdo =  new PDO('mysql:host='. $arrayConfig['host'] . ';dbname='. $arrayConfig['dbname'] .';charset=utf8', 
                                    $arrayConfig['user'], 
                                    $arrayConfig['password'], 
                                    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                                    PDO::ATTR_PERSISTENT => true)
                                );
            $this->operationResult->status = self::STATUS_OK;
                                
        } catch( \Exception $pdoError)
        {
            $this->operationResult->errorMessage = $pdoError->getMessage();
            print($pdoError->getMessage());
            $this->operationResult->status = self::STATUS_ERROR;
        }   

    }

    
    public function insertData($queryString, $pamarsArray=null, $lastInsert=false) {
        
        $pdoQuery = $this->pdo->prepare($queryString);
        try {
                $pdoQuery->execute($pamarsArray);
                
                if( $pdoQuery->rowCount() && $lastInsert ){ 
                    //return result as array
                    $this->operationResult->status = self::STATUS_OK;
                    return  $this->pdo->lastInsertId();
                }
                else {
                    $this->operationResult->status = self::STATUS_OK;
                    return true;
                }

        } catch(\PDOException $pdoError ) {
            return $pdoError->getMessage();
        }
    }

    public function getData($queryString, $pamarsArray=null) {
        $this->operationResult = new DataOperationResult();
        
        try {
                $pdoQuery = $this->pdo->prepare($queryString);
                $pdoQuery->execute($pamarsArray);
                $result =  $pdoQuery->fetchAll(PDO::FETCH_ASSOC);
                if(!empty($result) ){
                    $this->operationResult->resultData =  $result;
                    $this->operationResult->status = self::STATUS_OK;
                }
                else {
                    $this->operationResult->resultData = array();
                    $this->operationResult->status = self::NO_DATA;
                }
            $pdoQuery->closeCursor();
        } catch(\PDOException $pdoError ) {
            $this->operationResult->errorMessage = $pdoError->getMessage();
            $this->operationResult->status = self::STATUS_ERROR;
        }
    }
    

    public function ModifyData($queryString, $pamarsArray=null, $returnLine=false){
        $this->operationResult = new DataOperationResult();
        $pdoQuery = $this->pdo->prepare($queryString);
        try {
                $pdoQuery->execute($pamarsArray);
                $this->operationResult->status = self::STATUS_OK;
                if( $pdoQuery->rowCount() && $returnLine) {

                        $this->operationResult->lastIndex = $this->pdo->lastInsertId();
                }
        } catch(\PDOException $pdoError ) {
            $this->operationResult->errorMessage = $pdoError->getMessage();
            $this->operationResult->status = self::STATUS_ERROR;
        }
    }

    

    public function getTableInfo() {
        
    }

    public function operationResult(){
        return $this->operationResult;
    }

    public static function getConnexion($arrayConfig) {
        return new FactorMysqlManager($arrayConfig);

    }


}

?>