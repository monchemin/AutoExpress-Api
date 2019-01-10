<?php
/*
* Interface de gestion des bases
*/
namespace FactorData;

interface IFactorDbManager {
    //insertion
    public function InsertData($queryString, $pamarsArray=null, $lastInsert=false);
    // recuperation
    public function getData($queryString, $pamarsArray=null); 
    // mise a jour et suppression
    public function ModifyData($queryString, $pamarsArray=null, $returnLine=false);
   
    //recuperation des informations sur une table
    public function getTableInfo(); 
           
}
?>