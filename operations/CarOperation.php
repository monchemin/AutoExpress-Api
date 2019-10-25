<?

namespace Operations;

use FactorOperations\FactorManager;
use Queries\CarQueries;

require_once join(DIRECTORY_SEPARATOR, ['utils', 'errorCode.php']);
require_once join(DIRECTORY_SEPARATOR, ['queries', 'CarQueries.php']);
require_once 'CustomerOperation.php';

class CarOperation extends OperationBase
{

    private $message = "";
    private $status = 200;

    function __construct(FactorManager $manager)
    {
        parent::__construct($manager);

    }

    protected function read()
    {
    }

    protected function create()
    {
    }

    protected function update()
    {
    }

    protected function delete()
    {
    }

    public function process()
    {
    }

    public function getRegisteredCars()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return $this->operationResult();
        }
        if (!property_exists($this->requestData, "customerId")) {
            $this->message = "No Customer provided";
            $this->status = NO_PROVIDED_CUSTOMER;
            return $this->operationResult();
        }

        $query = CarQueries::registeredCars();
        $this->manager->getDataByQuery($query, array(':customerId' => $this->requestData->customerId));
        $this->operationStatus = true;
        return $this->operationResult();
    }

    public function createCar()
    {
        if ($this->requestData == null) {
            $this->message = "No provided data";
            $this->status = NO_PROVIDED_DATA;
            return $this->operationResult();
        }
        $year = $this->requestData->year;
        $number = $this->requestData->number;
        $customerId = $this->requestData->customerId;
        $model = $this->requestData->model;
        $color = $this->requestData->color;

        if (!is_numeric($year) || !is_numeric($customerId) || empty($number) || !is_numeric($model) || !is_numeric($color) || getDate()['year'] < $year) {
            $this->message = "Error in provided data";
            $this->status = DATA_ERROR;
            return $this->operationResult();
        }

        $customerOp = new CustomerOperation($this->manager);
        $customer = $customerOp->readOne($customerId);
        if($customer == null) {
            $this->message = "No Customer";
            $this->status = NO_PROVIDED_CUSTOMER;
            return $this->operationResult();
        }

        if(!$customer->active) {
            $this->message = "No active account";
            $this->status = NO_ACTIVE_ACCOUNT;
            return $this->operationResult();
        }

        if($customer->drivingNumber == null) {
            $this->message = "Your Account is not driving account";
            $this->status = NO_DRIVING_ACCOUNT;
            return $this->operationResult();
        }

        $query = CarQueries::create();
        $params = array(':year' => $year, ':number' => $number, ':customer' => $customerId, ':model' => $model, ':color' => $color);
        $this->manager->execute($query, $params, false);
        $result = $this->manager->operationResult;
        if ($result->status == 200) {
           return $this->getRegisteredCars();
        } else {
            $this->message = $result->errorMessage;
            $this->status = SQL_ERROR;
            return $this->operationResult();
        }
    }

    protected function operationResult()
    {
        return $this->operationStatus ? $this->manager->operationResult : array("status" => $this->status, "errorMessage" => $this->message);
    }


}

?>