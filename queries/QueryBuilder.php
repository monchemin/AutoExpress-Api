<?php
/**
 * Created by PhpStorm.
 * User: Nyemo
 * Date: 15/01/2019
 * Time: 20:39
 */

namespace Queries;


final class QueryBuilder
{

    public static function getInternalRoutes($fromStation = null, $toStation = null, $startDate = null, $endDate = null, $fromHour = null, $toHour = null)
    {
        $whereClause = array();
        $sqlVar = array();
        $between = false;
        $beginWhere = "";

        if (!empty($fromStation)) {
            $whereClause[] = "fromStation.PK = :fromStation";
            $sqlVar[":fromStation"] = $fromStation;
        }
        if (!empty($toStation)) {
            $whereClause[] = "toStation.PK = :toStation";
            $sqlVar[":toStation"] = $toStation;
        }
        if ($startDate != null) {
            $beginWhere = " WHERE route.routeDate = :startDate ";
            $sqlVar[":startDate"] = $startDate;
        } else {
            $beginWhere = " WHERE route.routeDate >= :startDate ";
            $sqlVar[":startDate"] = date('Y-m-d');
        }
        if (!empty($fromHour) && !empty($toHour)) {
            $sqlVar[":fromHour"] = $fromHour;
            $sqlVar[":toHour"] = $toHour;
            $between = true;
        } else if ($fromHour) {
            $whereClause[] = "route.FK_Hour = :fromHour";
            $sqlVar[":fromHour"] = $fromHour;
        }

        $statement = self::commonQuery() . $beginWhere;
        $statement .= " AND route.deletedAt IS NULL";
        if (!empty($whereClause)) $statement .= " AND " . implode(" AND ", $whereClause);
        if ($between) $statement .= " AND pickuphour.displayOrder BETWEEN :fromHour AND :toHour";

        $statement .= " GROUP BY route.PK HAVING remainingPlace > 0";

        return array("sql" => $statement, "var" => $sqlVar);
    }

    public static function getZone($station)
    {
        $statement = "SELECT zone.PK, zone.zoneName
                      FROM zone INNER JOIN station ON zone.PK = station.FK_Zone
                      WHERE station.PK = :stationPK";
        return array("sql" => $statement, "var" => array(":stationPK" => $station));
    }

    public static function getInternalRoutesByZone($fromZone, $toZone, $startDate = null, $endDate = null, $fromHour = null, $toHour = null)
    {
        $whereClause = array();
        $sqlVar = array();
        $between = false;
        $beginWhere = "";

        if (!empty($fromZone)) {
            $whereClause[] = "fromZone.PK = :fromZone";
            $sqlVar[":fromZone"] = $fromZone;
        }
        if (!empty($toZone)) {
            $whereClause[] = "toZone.PK = :toZone";
            $sqlVar[":toZone"] = $toZone;
        }
        if ($startDate != null) {
            $beginWhere = " WHERE route.routeDate = :startDate ";
            $sqlVar[":startDate"] = $startDate;
        } else {
            $beginWhere = " WHERE route.routeDate >= :startDate ";
            $sqlVar[":startDate"] = date('Y-m-d');
        }
        if (!empty($fromHour) && !empty($toHour)) {
            $sqlVar[":fromHour"] = $fromHour;
            $sqlVar[":toHour"] = $toHour;
            $between = true;
        } else if ($fromHour) {
            $whereClause[] = "route.FK_Hour = :fromHour";
            $sqlVar[":fromHour"] = $fromHour;
        }

        $statement = self::commonQuery() . $beginWhere;
        $statement .= " AND route.deletedAt IS NULL";
        if (!empty($whereClause)) $statement .= " AND " . implode(" AND ", $whereClause);
        if ($between) $statement .= " AND pickuphour.displayOrder BETWEEN :fromHour AND :toHour";

        $statement .= " GROUP BY route.PK";
        return array("sql" => $statement, "var" => $sqlVar);
    }

    public static function commonQuery()
    {
        return "SELECT route.PK as routeId, route.routeDate, route.routePrice, route.routePlace, (route.routePlace - coalesce(sum(reservation.place),0)) as remainingPlace, pickuphour.hour,
                    fromStation.stationName as fStation, fromStation.stationDetail as fStationDetail, fromZone.zoneName as fZone, toStation.stationName as tStation, toStation.stationDetail as tStationDetail, toZone.zoneName as tZone
        FROM route
        INNER JOIN station fromStation ON route.FK_DepartureStage = fromStation.PK
        INNER JOIN station toStation ON route.FK_ArrivalStage = toStation.PK
        INNER JOIN zone fromZone ON fromZone.PK = fromStation.FK_Zone
        INNER JOIN zone toZone ON toZone.PK = toStation.FK_Zone
        INNER JOIN car ON car.PK = route.FK_car
        LEFT JOIN reservation ON route.PK = reservation.FK_Route
        INNER JOIN pickuphour ON route.FK_Hour = pickuphour.PK
       ";
    }

    public static function getRouteStation()
    {
        return "select station.PK as stationId, concat(city.cityName, ' ', zone.zoneName, ' ', station.stationName) as stationName, station.stationAddress
        from station
        inner join zone on station.FK_Zone = zone.PK
        inner join city on zone.FK_City = city.PK
        ";
    }

    public static function getRoutePlace()
    {
        return "SELECT (route.routePlace - sum(coalesce(reservation.place, 0))) as place, route.deletedAt, route.FK_Driver as driverId
        FROM route
        LEFT JOIN reservation ON reservation.FK_Route = route.PK
        WHERE route.PK = :pk
        GROUP BY route.PK
        ";
    }

    public static function commonReservation()
    {
        return "SELECT reservation.PK as reservationId, reservation.place as place, route.PK as routeId, route.routeDate, route.routePrice, 
        pickuphour.hour,
        fromStation.stationName as fStation, fromStation.stationDetail as fStationDetail, fromZone.zoneName as fZone, 
        toStation.stationName as tStation, toStation.stationDetail as tStationDetail, toZone.zoneName as tZone,
        car.registrationNumber, car.year,
        carmodel.modelName, carbrand.brandName, carcolor.colorName,
        customer.firstName as driverFirstName, customer.lastName as driverLastName
            FROM route
            inner JOIN reservation ON route.PK = reservation.FK_Route
            INNER JOIN station fromStation ON route.FK_DepartureStage = fromStation.PK
            INNER JOIN station toStation ON route.FK_ArrivalStage = toStation.PK
            INNER JOIN zone fromZone ON fromZone.PK = fromStation.FK_Zone
            INNER JOIN zone toZone ON toZone.PK = toStation.FK_Zone
            INNER JOIN pickuphour ON route.FK_Hour = pickuphour.PK
            inner join customer on route.FK_Driver = customer.PK
            inner join car on route.FK_car = car.PK
            inner join carcolor on car.FK_carcolor = carcolor.PK
            inner join carmodel on car.FK_carmodel = carmodel.PK
            inner join carbrand on carmodel.FK_brand = carbrand.PK ";
    }

    public static function getReservation()
    {
        return self::commonReservation() .
            "where reservation.PK = :PK";
    }

    public static function allReservations()
    {
        return self::commonReservation() .
            "where reservation.FK_customer = :PK 
                AND route.routeDate >= :date
                AND reservation.deletedAt IS NULL";
    }

    public static function ownerRoutes()
    {
        return self::commonQuery() .
            " WHERE route.routeDate >= :date
               AND  route.FK_Driver = :PK
               AND route.deletedAt IS NULL 
               GROUP BY route.PK 
               ORDER BY route.routeDate";
    }

    public static function deleteRouteReservation()
    {
        return "
        SELECT 
		route.FK_Driver as driver,
		GROUP_CONCAT(reservation.PK) as reservationIds, 
		GROUP_CONCAT(customer.lastName) as names, 
		GROUP_CONCAT(customer.eMail) as mails 
        FROM route 
        INNER JOIN reservation ON reservation.FK_route = route.PK 
        INNER JOIN customer ON reservation.FK_customer = customer.PK 
        WHERE route.PK = :PK 
        AND reservation.deletedAt IS NULL 
        GROUP BY route.PK";
    }

    public static function updateReservations($count) {
        $in = "?";
        if($count > 1) {
           $in = str_repeat("?,", $count-1)."?";
        }

        return "
        UPDATE reservation
        SET deletedAt = NOW()
        WHERE PK IN($in)
        ";
    }

    public static function routeDelete() {
        return "
        UPDATE route
        SET deletedAt = NOW()
        WHERE PK = :PK
        ";
    }

    public static function routeReservations() {
        return "
        SELECT 
            reservation.place, 
	        customer.firstName,
   	        customer.lastName,
   	        customer.phoneNumber,
   	        customer.eMail
        FROM reservation
        INNER JOIN customer ON reservation.FK_customer = customer.PK
        INNER JOIN route ON reservation.FK_route = route.PK
        WHERE route.PK = :PK
        AND   route.FK_driver = :driver
        ";
    }

}