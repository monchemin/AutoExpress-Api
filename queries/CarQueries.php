<?
namespace Queries;

final class CarQueries {

    public static function registeredCars() {
        return "SELECT car.PK, car.registrationNumber as number, car.year, carmodel.modelName as model, carbrand.brandName as brand, carcolor.colorName as color  
        FROM car
        inner join carcolor ON carcolor.PK = car.FK_carcolor
        inner join carmodel on carmodel.PK = car.FK_carmodel
        inner JOIN carbrand on carbrand.PK = carmodel.FK_brand
        WHERE car.FK_customer = :PK";
    }


    public static function create() {
        return "INSERT INTO car(registrationNumber, year, FK_carmodel, FK_carcolor, FK_customer, createdAt)
                VALUES(:number, :year, :model, :color, :customer, NOW())";
    }

}
?>