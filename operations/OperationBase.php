<?php
namespace Operations;
use FactorOperations\FactorManager;

abstract class OperationBase
{
    protected $httpMethod;
    protected $requestData;
    protected $manager;
    protected $pk;
    protected $operationStatus = false;
    abstract protected function read();
    abstract protected function create();
    abstract protected function update();
    abstract protected function delete();
    abstract public function process();
    abstract protected function operationResult();
    function __construct(FactorManager $manager)
    {
        $this->httpMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestData = json_decode(file_get_contents("php://input"));
        $uri = explode('/', $_SERVER['REQUEST_URI']);
        $this->pk = intval(trim($uri[count($uri)-1],'/'));
        $this->manager = $manager;

    }
}

?>