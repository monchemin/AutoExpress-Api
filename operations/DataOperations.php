<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 15/01/2019
 * Time: 20:39
 */

namespace Operations;


final class DataOperations
{

    public static function getRoutes($fromStage, $toStage, $startDate, $endDate, $startHour, $endHour)
    {
        /*
         * SELECT route.PK, from_stage.stageName as fromS, to_stage.stageName as toStage, route.routePrice, route.routePlace - count(reservation.PK), pickuphour.hour
FROM route
INNER JOIN stage from_stage ON route.FK_DepartureStage = from_stage.PK
INNER JOIN stage to_stage ON route.FK_ArrivalStage = to_stage.PK
INNER JOIN reservation ON route.PK = reservation.FK_Route
INNER JOIN pickuphour ON route.FK_Hour = pickuphour.PK
GROUP BY route.PK
         */
    }
}