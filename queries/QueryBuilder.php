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
            $whereClause[] = "fromStation.pk = :fromStation";
            $sqlVar[":fromStation"] = $fromStation;
        }
        if (!empty($toStation)) {
            $whereClause[] = "toStation.pk = :toStation";
            $sqlVar[":toStation"] = $toStation;
        }
        if ($startDate != null) {
            $beginWhere = " WHERE route.route_date = :startDate ";
            $sqlVar[":startDate"] = $startDate;
        } else {
            $beginWhere = " WHERE route.route_date >= :startDate ";
            $sqlVar[":startDate"] = date('Y-m-d');
        }
        if (!empty($fromHour) && !empty($toHour)) {
            $sqlVar[":fromHour"] = $fromHour;
            $sqlVar[":toHour"] = $toHour;
            $between = true;
        } else if ($fromHour) {
            $whereClause[] = "route.fk_hour = :fromHour";
            $sqlVar[":fromHour"] = $fromHour;
        }

        $statement = self::commonQuery() . $beginWhere;
        $statement .= " AND route.deleted_at IS NULL";
        if (!empty($whereClause)) $statement .= " AND " . implode(" AND ", $whereClause);
        if ($between) $statement .= " AND pickuphour.display_order BETWEEN :fromHour AND :toHour";

        $statement .= " GROUP BY route.pk HAVING remainingPlace > 0";

        return array("sql" => $statement, "var" => $sqlVar);
    }

    public static function getZone($station)
    {
        $statement = "SELECT zone.pk, zone.zone_name
                      FROM zone INNER JOIN station ON zone.pk = station.fk_zone
                      WHERE station.pk = :stationPK";
        return array("sql" => $statement, "var" => array(":stationPK" => $station));
    }

    public static function getInternalRoutesByZone($fromZone, $toZone, $startDate = null, $endDate = null, $fromHour = null, $toHour = null)
    {
        $whereClause = array();
        $sqlVar = array();
        $between = false;
        $beginWhere = "";

        if (!empty($fromZone)) {
            $whereClause[] = "fromZone.pk = :fromZone";
            $sqlVar[":fromZone"] = $fromZone;
        }
        if (!empty($toZone)) {
            $whereClause[] = "toZone.pk = :toZone";
            $sqlVar[":toZone"] = $toZone;
        }
        if ($startDate != null) {
            $beginWhere = " WHERE route.route_date = :startDate ";
            $sqlVar[":startDate"] = $startDate;
        } else {
            $beginWhere = " WHERE route.route_date >= :startDate ";
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
        $statement .= " AND route.deleted_at IS NULL AND reservation.deleted_at IS NULL";
        if (!empty($whereClause)) $statement .= " AND " . implode(" AND ", $whereClause);
        if ($between) $statement .= " AND pickuphour.displayOrder BETWEEN :fromHour AND :toHour";

        $statement .= " GROUP BY route.pk";
        return array("sql" => $statement, "var" => $sqlVar);
    }

    public static function commonQuery()
    {
        return "SELECT route.pk as routeId, 
                    route.route_date as routeDate, 
                    route.route_price as routePrice, 
                    route.route_place as routePlace, 
                    (route.route_place - coalesce(sum(reservation.place),0)) as remainingPlace, 
                    pickuphour.hour,
                    fromStation.station_name as fStation, 
                    fromStation.station_detail as fStationDetail, 
                    fromZone.zone_name as fZone, 
                    toStation.station_name as tStation, 
                    toStation.station_detail as tStationDetail, 
                    toZone.zone_name as tZone
                FROM route
                INNER JOIN station fromStation ON route.fk_departure_stage = fromStation.pk
                INNER JOIN station toStation ON route.fk_arrival_stage = toStation.pk
                INNER JOIN zone fromZone ON fromZone.pk = fromStation.fk_zone
                INNER JOIN zone toZone ON toZone.pk = toStation.fk_zone
                INNER JOIN car ON car.pk = route.fk_car
                LEFT JOIN reservation ON route.pk = reservation.fk_route
                    AND reservation.deleted_at IS NULL
                INNER JOIN pickuphour ON route.fk_hour = pickuphour.pk
       ";
    }

    public static function getRouteStation()
    {
        return "SELECT station.pk as stationId, 
                    concat(city.city_name, ' ', zone.zone_name, ' ', station.station_name) as stationName, 
                    station.station_address as stationAddress
                from station
                inner join zone on station.fk_zone = zone.pk
                inner join city on zone.fk_city = city.pk
        ";
    }

    public static function getRoutePlace()
    {
        return "SELECT 
                    (route.route_place - sum(coalesce(reservation.place, 0))) as place, 
                    route.deleted_at as deletedAt, route.fk_driver as driverId
                FROM route
                LEFT JOIN reservation ON reservation.fk_route = route.pk
                    AND reservation.deleted_at IS NULL
                WHERE route.pk = :pk
                AND reservation.deleted_at IS NULL
                GROUP BY route.pk
        ";
    }

    public static function commonReservation()
    {
        return "SELECT 
                    reservation.pk as reservationId, 
                    reservation.place as place, 
                    route.pk as routeId, 
                    route.route_date as routeDate, 
                    route.route_price as routePrice, 
                    pickuphour.hour,
                    fromStation.station_name as fStation, 
                    fromStation.station_detail as fStationDetail, 
                    fromZone.zone_name as fZone, 
                    toStation.station_name as tStation, 
                    toStation.station_detail as tStationDetail, 
                    toZone.zone_name as tZone,
                    car.registration_number as registrationNumber, 
                    car.year,
                    carmodel.model_name as modelName, 
                    carbrand.brand_name as brandName, 
                    carcolor.color_name as colorName,
                    carcolor.color_label as colorLabel,
                    customer.first_name as driverFirstName, 
                    customer.last_name as driverLastName
                FROM route
                inner JOIN reservation ON route.pk = reservation.fk_route
                INNER JOIN station fromStation ON route.fk_departure_stage = fromStation.pk
                INNER JOIN station toStation ON route.fk_arrival_stage = toStation.pk
                INNER JOIN zone fromZone ON fromZone.pk = fromStation.FK_Zone
                INNER JOIN zone toZone ON toZone.pk = toStation.FK_Zone
                INNER JOIN pickuphour ON route.FK_Hour = pickuphour.pk
                inner join customer on route.fk_driver = customer.pk
                inner join car on route.fk_car = car.pk
                inner join carcolor on car.fk_carcolor = carcolor.pk
                inner join carmodel on car.fk_carmodel = carmodel.pk
                inner join carbrand on carmodel.fk_brand = carbrand.pk ";
    }

    public static function getReservation()
    {
        return self::commonReservation() .
            "where reservation.pk = :PK";
    }

    public static function allReservations()
    {
        return self::commonReservation() .
            "where reservation.fk_customer = :PK 
                AND route.route_date >= :date
                AND reservation.deleted_at IS NULL";
    }

    public static function ownerRoutes()
    {
        return self::commonQuery() .
            " WHERE route.route_date >= :date
               AND  route.fk_driver = :PK
               AND route.deleted_at IS NULL
               AND reservation.deleted_at IS NULL  
               GROUP BY route.pk 
               ORDER BY route.route_date";
    }

    public static function deleteRouteReservation()
    {
        return "
        SELECT 
            route.fk_driver as driver,
            GROUP_CONCAT(reservation.pk) as reservationIds, 
            GROUP_CONCAT(customer.last_name) as names, 
            GROUP_CONCAT(customer.e_mail) as mails 
        FROM route 
        INNER JOIN reservation ON reservation.fk_route = route.pk 
        INNER JOIN customer ON reservation.fk_customer = customer.pk 
        WHERE route.pk = :PK 
        AND reservation.deleted_at IS NULL 
        GROUP BY route.pk";
    }

    public static function updateReservations($count)
    {
        $in = "?";
        if ($count > 1) {
            $in = str_repeat("?,", $count - 1) . "?";
        }

        return "
        UPDATE reservation
        SET deleted_at = NOW()
        WHERE pk IN($in)
        ";
    }

    public static function routeDelete()
    {
        return "
        UPDATE route
        SET deleted_at = NOW()
        WHERE pk = :PK
        ";
    }

    public static function routeReservations()
    {
        return "
        SELECT 
            reservation.place, 
	        customer.first_name as firstName,
   	        customer.last_name as lastName,
   	        customer.phone_number as phoneNumber,
   	        customer.e_mail as eMail
        FROM reservation
        INNER JOIN customer ON reservation.fk_customer = customer.pk
        INNER JOIN route ON reservation.fk_route = route.pk
            AND reservation.deleted_at IS NULL
        WHERE route.pk = :PK
        AND   route.fk_driver = :driver
        ";
    }

    public static function passwordRecovery()
    {
        return "
            UPDATE customer
            SET password = :password
            WHERE e_mail = :email
        ";
    }

}