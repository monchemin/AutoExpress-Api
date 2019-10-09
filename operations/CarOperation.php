<?
namespace Operations;

use Entities\Customers;
use FactorOperations\FactorManager;
use Queries\CarQueries;

require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);
require_once join(DIRECTORY_SEPARATOR, ['queries', 'CarQueries.php']);

class CarOperation extends OperationBase {

    private $message = "";
    private $status = 200;

    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    public function getRegisteredCars() {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;  
            return;
        }
        if (!property_exists($this->requestData, "PK")) {
            $this->message = "No Customer provided";
            $this->status = NO_PROVIDED_CUSTOMER;  
            return;
        }

        $query = CarQueries::registredCars();
        $this->manager->getDataByQuery($query, array(':PK'=>$this->requestData->PK));
        $this->operationStatus = true;
        return $this->operationResult();
    }

    protected function operationResult()
    {
        return $this->operationStatus ? $this->manager->operationResult : array("status" => $this->status, "errorMessage"=>$this->message);
    }

   protected function read(){}
     protected function create(){}
     protected function update(){}
     protected function delete(){}
     public function process(){}
}
?>

?>