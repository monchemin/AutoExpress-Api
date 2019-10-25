<?php
namespace Operations;
use FactorOperations\FactorManager;

abstract class OperationBase
{
    protected $httpMethod;
    protected $requestData;
    protected $manager;
    protected $Id;
    protected $operationStatus = null;
    abstract protected function read();
    abstract protected function create();
    abstract protected function update();
    abstract protected function delete();
    abstract public function process();
    
    function __construct(FactorManager $manager)
    {
        $this->httpMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestData = json_decode(file_get_contents("php://input"));
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        $this->Id = intval(trim($uri[count($uri)-1],'/'));
        $this->manager = $manager;

    }
    protected function operationResult()
    {

        $httpResponse = array(
            'code' => $this->operationStatus !== null ? $this->operationStatus : $this->manager->operationResult->status,
            'data' => $this->operationStatus !== null ? array('message' => "missing processing information") : $this->manager->operationResult
        );

        return $httpResponse; //$this->operationStatus ? $this->manager->operationResult : array("status" => "120", "errorMessage"=>"Erreur dans la data");
    }
}

?>