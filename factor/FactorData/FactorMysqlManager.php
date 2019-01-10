<?php
namespace FactorData;

/*
*/
use PDO;
final class FactorMysqlManager implements IFactorDbManager {

    
    protected $pdo;
    protected $operationResult;
     CONST STATUS_OK = 200;
     CONST STATUS_ERROR = 100;

    protected function __construct($arrayConfig) {
       $this->operationResult = new DataOperationResult();
        try {
            $this->pdo =  new PDO('mysql:host='. $arrayConfig['host'] . ';dbname='. $arrayConfig['dbname'] .';charset=utf8', 
                                    $arrayConfig['user'], 
                                    $arrayConfig['password'], 
                                    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
                                );
            $this->operationResult->status = self::STATUS_OK;
                                
        } catch( \Exception $pdoError)
        {
            $this->operationResult->errorMessage = $pdoError->getMessage();
            $this->operationResult->status = self::STATUS_ERROR;
        }   

    }

    
    public function insertData($queryString, $pamarsArray=null, $lastInsert=false) {
        
        $pdoQuery = $this->pdo->prepare($queryString);
        try {
                $pdoQuery->execute($pamarsArray);
                
                if( $pdoQuery->rowCount() && $lastInsert ){ 
                    //return result as array
                    return  $this->pdo->lastInsertId();
                }
                else {
                    return true;
                }

        } catch(\PDOException $pdoError ) {
            return $pdoError->getMessage();
        }
    }

    public function getData($queryString, $pamarsArray=null) {
        $pdoQuery = $this->pdo->prepare($queryString);
        try {
                $pdoQuery->execute($pamarsArray);
                $this->operationResult->status = self::STATUS_OK;
                if( $pdoQuery->rowCount() ){
                    $this->operationResult->resultData =  $pdoQuery->fetchAll(PDO::FETCH_ASSOC);
                }
                else {
                    $this->operationResult->resultData = array();
                }
            $pdoQuery->closeCursor();
        } catch(\PDOException $pdoError ) {
            $this->operationResult->errorMessage = $pdoError->getMessage();
            $this->operationResult->status = self::STATUS_ERROR;
        }
    }
    

    public function ModifyData($queryString, $pamarsArray=null, $returnLine=false){
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