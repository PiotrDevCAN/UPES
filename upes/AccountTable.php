<?php
namespace upes;

use itdq\DbTable;
use itdq\Loader;


/*
 * CREATE TABLE UPES_DEV.ACCOUNT ( ACCOUNT_ID INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1000 INCREMENT BY 10 NO CYCLE ), ACCOUNT CHAR(125) NOT NULL ) IN USERSPACE1;
 * CREATE TABLE UPES_UT.ACCOUNT ( ACCOUNT_ID INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1000 INCREMENT BY 10 NO CYCLE ), ACCOUNT CHAR(125) NOT NULL  ) IN USERSPACE1;
 * CREATE TABLE UPES.ACCOUNT ( ACCOUNT_ID INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 1000 INCREMENT BY 10 NO CYCLE ), ACCOUNT CHAR(125) NOT NULL  ) IN USERSPACE1;
 *
 * ALTER TABLE UPES_DEV.ACCOUNT ADD CONSTRAINT ACC_ID_PK PRIMARY KEY (ACCOUNT_ID )  ENFORCED;
 * ALTER TABLE UPES_UT.ACCOUNT ADD CONSTRAINT ACC_ID_PK PRIMARY KEY (ACCOUNT_ID )  ENFORCED;
 * ALTER TABLE UPES.ACCOUNT ADD CONSTRAINT ACC_ID_PK PRIMARY KEY (ACCOUNT_ID )  ENFORCED;
 *
 * ALTER TABLE "UPES_DEV"."ACCOUNT" ADD CONSTRAINT "ACC_UNIQUE" UNIQUE ("ACCOUNT" ) ENFORCED;
 * ALTER TABLE "UPES_UT"."ACCOUNT" ADD CONSTRAINT "ACC_UNIQUE" UNIQUE ("ACCOUNT" ) ENFORCED;
 * ALTER TABLE "UPES"."ACCOUNT" ADD CONSTRAINT "ACC_UNIQUE" UNIQUE ("ACCOUNT" ) ENFORCED;
 *
 * ALTER TABLE "UPES_DEV"."ACCOUNT" ADD COLUMN "TASKID" CHAR(20);*
 *
 *
 */
class AccountTable extends DbTable
{

    function returnAsArray($predicate=null,$withButtons=true){
        $sql  = " SELECT '' as ACTION, A.* ";
        $sql .= " FROM  " . $GLOBALS['Db2Schema'] . "." . $this->tableName. " as A ";
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
            $trimmedRow = array_map('trim',$row);
            if($withButtons){
                $this->addGlyphicons($trimmedRow);
            }
            $allData[]  = $trimmedRow;
        }
        return $allData ;
    }


    function addGlyphicons(&$row){
        $accountId = $row['ACCOUNT_ID'];
        $account   = $row['ACCOUNT'];
        $taskid    = $row['TASKID'];
        $accountType    = $row['ACCOUNT_TYPE'];

        $row['ACTION'] = "<button type='button' class='btn btn-primary btn-xs editAccountName ' aria-label='Left Align' data-accountid='" .$accountId . "' data-account='" . $account . "'data-taskid='" . $taskid . "' data-accounttype='" . $accountType . "' data-toggle='tooltip' title='Edit Account Name' >
              <span class='glyphicon glyphicon-edit editAccountName'  aria-hidden='true' data-accountid='" .$accountId . "' data-account='" . $account . "'data-taskid='" . $taskid . "' data-accounttype='" . $accountType . "'   ></span>
              </button>";
        $row['ACTION'].= "&nbsp;";
        $row['ACTION'].= "<button type='button' class='btn btn-warning btn-xs deleteAccount ' aria-label='Left Align' data-accountid='" .$accountId . "' data-account='" . $account . "'data-taskid='" . $taskid . "' data-accounttype='" . $accountType . "' data-toggle='tooltip' title='Delete Account'>
              <span class='glyphicon glyphicon-trash deleteAccount' aria-hidden='true' data-accountid='" .$accountId . "' data-account='" . $account . "'data-taskid='" . $taskid . "' data-accounttype='" . $accountType . "' ></span>
              </button>";
    }

    static function prepareJsonAccountIdLookup(){
        $loader = new Loader();
        return $loader->loadIndexed('ACCOUNT','ACCOUNT_ID',AllTables::$ACCOUNT);
    }


    static function getAccountNameFromId($accountId){
        $sql = " SELECT ACCOUNT FROM " . $GLOBALS['Db2Schema'] . "." . \upes\AllTables::$ACCOUNT . " WHERE ACCOUNT_ID='" . db2_escape_string($accountId) . "' ";
        $rs = db2_exec($GLOBALS['conn'], $sql);
        $row = db2_fetch_assoc($rs);
        return trim($row['ACCOUNT']);
    }
}

