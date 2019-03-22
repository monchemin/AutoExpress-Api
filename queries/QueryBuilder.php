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

    public static function getInternalRoutes($fromStation=null, $toStation=null, $startDate=null, $endDate=null, $fromHour=null, $toHour=null)
    {
        $whereClause = array();
        $sqlVar = array();
        $between = false;
        $beginWhere = "";

        if(!empty($fromStation)) {
            $whereClause[] = "fromStation.PK = :fromStation";
            $sqlVar[":fromStation"] = $fromStation;
        }
        if(!empty($toStation)) {
            $whereClause[] = "toStation.PK = :toStation";
            $sqlVar[":toStation"] = $toStation;
        }
        if($startDate != null) {
            $beginWhere = " WHERE route.routeDate = :startDate ";
            $sqlVar[":startDate"] = $startDate;
        }else {
            $beginWhere = " WHERE route.routeDate >= :startDate ";
            $sqlVar[":startDate"] = date('Y-m-d'); 
        }
        if(!empty($fromHour) && !empty($toHour)){
            $sqlVar[":fromHour"] = $fromHour;
            $sqlVar[":toHour"] = $toHour;
            $between = true;
        }else if ($fromHour) {
            $whereClause[] = "route.FK_Hour = :fromHour";
            $sqlVar[":fromHour"] = $fromHour;
        }

      $statement = self::commonQuery().$beginWhere;
        if(!empty($whereClause) ) $statement .= " AND " . implode(" AND " , $whereClause);
        if($between) $statement .= " AND pickuphour.displayOrder BETWEEN :fromHour AND :toHour";

        $statement .= " GROUP BY route.PK HAVING remaningPlace > 0";

        return array("sql" => $statement, "var" => $sqlVar);
    }

    public static function getZone($station){
        $statement = "SELECT zone.PK, zone.zoneName
                      FROM zone INNER JOIN station ON zone.PK = station.FK_Zone
                      WHERE station.PK = :stationPK";
        return array("sql" => $statement, "var" => array(":stationPK" => $station));
    }

    public static function getInternalRoutesByZone($fromZone, $toZone, $startDate=null, $endDate=null, $fromHour=null, $toHour=null)
    {
        $whereClause = array();
        $sqlVar = array();
        $between = false;
        $beginWhere = "";

        if(!empty($fromZone)) {
            $whereClause[] = "fromZone.PK = :fromZone";
            $sqlVar[":fromZone"] = $fromZone;
        }
        if(!empty($toZone)) {
            $whereClause[] = "toZone.PK = :toZone";
            $sqlVar[":toZone"] = $toZone;
        }
        if($startDate != null) {
            $beginWhere = " WHERE route.routeDate = :startDate ";
            $sqlVar[":startDate"] = $startDate;
        }else {
            $beginWhere = " WHERE route.routeDate >= :startDate ";
            $sqlVar[":startDate"] = date('Y-m-d'); 
        }
        if(!empty($fromHour) && !empty($toHour)){
            $sqlVar[":fromHour"] = $fromHour;
            $sqlVar[":toHour"] = $toHour;
            $between = true;
        }else if ($fromHour) {
            $whereClause[] = "route.FK_Hour = :fromHour";
            $sqlVar[":fromHour"] = $fromHour;
        }

        $statement = self::commonQuery().$beginWhere;
        if(!empty($whereClause) ) $statement .= " AND " . implode(" AND " , $whereClause);
        if($between) $statement .= " AND pickuphour.displayOrder BETWEEN :fromHour AND :toHour";

        $statement .= " GROUP BY route.PK";
        return array("sql" => $statement, "var" => $sqlVar);
    }

    private static function commonQuery() {
        return "SELECT route.PK, route.routeDate, route.routePrice, route.routePlace - count(reservation.PK) as remaningPlace, pickuphour.hour,
                    fromStation.stationName as fStation, fromZone.zoneName as fZone, toStation.stationName as tStation, toZone.zoneName as tZone
        FROM route
        INNER JOIN station fromStation ON route.FK_DepartureStage = fromStation.PK
        INNER JOIN station toStation ON route.FK_ArrivalStage = toStation.PK
        INNER JOIN zone fromZone ON fromZone.PK = fromStation.FK_Zone
        INNER JOIN zone toZone ON toZone.PK = toStation.FK_Zone
        LEFT JOIN reservation ON route.PK = reservation.FK_Route
        INNER JOIN pickuphour ON route.FK_Hour = pickuphour.PK
       ";
    }

    public static function getRouteStation() {
        return "select station.PK, concat(city.cityName, ' ', zone.zoneName, ' ', station.stationName) as stationName, station.stationAddress
        from station
        inner join zone on station.FK_Zone = zone.PK
        inner join city on zone.FK_City = city.PK
        ";
    }

}