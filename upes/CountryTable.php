<?php
namespace upes;

use itdq\DbTable;
use itdq\Loader;

/*
 *
 * CREATE TABLE "UPES".COUNTRY  ( COUNTRY CHAR(50) NOT NULL ,INTERNATIONAL CHAR(3) )
 * CREATE UNIQUE INDEX "UPES"."CountryIx" ON "UPES_DEV"."COUNTRY" ("COUNTRY" ASC);
 *
 */



class CountryTable extends DbTable
{

    function returnAsArray($predicate=null,$withButtons=true){
        $sql  = " SELECT '' as ACTION, C.* ";
        $sql .= " FROM  " . $_SESSION['Db2Schema'] . "." . $this->tableName. " as C ";
        $sql .= " WHERE 1=1 " ;
        $sql .= !empty($predicate) ? " AND  $predicate " : null ;

        $resultSet = $this->execute($sql);
        $resultSet ? null : die("SQL Failed");
        $allData = null;

        while(($row = db2_fetch_assoc($resultSet))==true){
            $testJson = json_encode($row);
            if(!$testJson){
                die('Failed JSON Encode');
                break; // It's got invalid chars in it that will be a problem later.
            }
            if($withButtons){
                $this->addGlyphicons($row);
            }
            $allData[]  = $row;
        }
        return $allData ;
    }


    function addGlyphicons(&$row){
        $data = array();
        $country       = trim($row['COUNTRY']);
        $international = trim($row['INTERNATIONAL']);

        $data['display'] = "<button type='button' class='btn btn-primary btn-xs editCountry ' aria-label='Left Align' data-country='" . $country . "'  data-toggle='tooltip' title='Edit Country' >
              <span class='glyphicon glyphicon-edit editCountryName'  aria-hidden='true' data-country='" . $country . "'  data-international='" . $international . "'   ></span>
              </button>&nbsp;" . $country;
        $data['sort'] = $country;
        $row['COUNTRY'] = $data;
    }

}