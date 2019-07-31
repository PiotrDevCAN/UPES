<?php
namespace upes;

use itdq\DbTable;
use itdq\Loader;





class PersonTable extends DbTable
{

    function returnAsArray($predicate=null,$withButtons=true){
        $sql  = " SELECT '' as ACTION, P.*, PL.PES_LEVEL as PES_LEVEL_TXT, PL.PES_LEVEL_DESCRIPTION ";
        $sql .= " FROM  " . $_SESSION['Db2Schema'] . "." . $this->tableName. " as P ";
        $sql .= " LEFT JOIN " .  $_SESSION['Db2Schema'] . "." . AllTables::$PES_LEVELS . " as PL ";
        $sql .= " ON P.PES_LEVEL = PL.PES_LEVEL_REF ";
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
        $row['PES_LEVEL'] = $row['PES_LEVEL_TXT'];
        unset($row['PES_LEVEL_TXT']);
        unset($row['PES_LEVEL_DESCRIPTION']);

    }

    static function prepareJsonKnownEmailLookup(){
        $allEmail =  array();

        $sql = " Select distinct lower(EMAIL_ADDRESS) as EMAIL_ADDRESS from " . $_SESSION['Db2Schema'] . "." . AllTables::$PERSON;
        $rs = db2_exec($_SESSION['conn'], $sql);

        while(($row=db2_fetch_assoc($rs))==true){
            $allEmail[] = trim($row['EMAIL_ADDRESS']);
        }
        return $allEmail;
    }

    static function prepareJsonUpesrefToNameMapping(){
        $allNames =  array();

        $sql = " Select distinct UPES_REF, FULL_NAME from " . $_SESSION['Db2Schema'] . "." . AllTables::$PERSON;
        $rs = db2_exec($_SESSION['conn'], $sql);

        while(($row=db2_fetch_assoc($rs))==true){
            $allNames[$row['UPES_REF']] = trim($row['FULL_NAME']);
        }
        return $allNames;
    }


}
