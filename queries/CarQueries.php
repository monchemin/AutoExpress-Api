<?
namespace Queries;

final class CarQueries {

    public static function registredCars() {
        return "SELECT car.PK, car.registrationNumber as number, car.year, carmodel.modelName as model, carbrand.brandName as brand, carcolor.colorName as color  
        FROM car
        inner join carcolor ON carcolor.PK = car.FK_carcolor
        inner join carmodel on carmodel.PK = car.FK_carmodel
        inner JOIN carbrand on carbrand.PK = carmodel.FK_brand
        WHERE car.FK_customer = :PK";
    }
}
?>