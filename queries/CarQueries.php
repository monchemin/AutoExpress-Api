<?php
namespace Queries;

final class CarQueries
{

    public static function registeredCars()
    {
        return "SELECT car.pk as Id, car.registration_number as number, car.year, carmodel.model_name as model, carbrand.brand_name as brand, carcolor.color_name as color  
        FROM car
        inner join carcolor ON carcolor.pk = car.fk_carcolor
        inner join carmodel on carmodel.pk = car.fk_carmodel
        inner JOIN carbrand on carbrand.pk = carmodel.fk_brand
        WHERE car.FK_customer = :customerId";
    }


    public static function create()
    {
        return "INSERT INTO car(registration_number, year, fk_carmodel, fk_carcolor, fk_customer, created_at)
                VALUES(:number, :year, :model, :color, :customer, NOW())";
    }

    public static function brandModel()
    {
        return "
                SELECT carmodel.fk as Id, CONCAT(carbrand.brand_name, ' ', carmodel.model_name) as model 
                FROM carmodel
                INNER JOIN carbrand ON carbrand.pk = carmodel.fk_brand 
                ";

    }

}

?>