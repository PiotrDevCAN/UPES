<?php
namespace upes;

use itdq\DbTable;
use itdq\Loader;





class PersonTable extends DbTable
{

    function returnAsArray($predicate=null,$withButtons=true){
        $sql  = " SELECT '' as ACTION, P.* ";
        $sql .= " FROM  " . $_SESSION['Db2Schema'] . "." . $this->tableName. " as P ";
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
//         $accountId = trim($row['ACCOUNT_ID']);
//         $account   = trim($row['ACCOUNT']);

//         $row['ACTION'] = "<button type='button' class='btn btn-primary btn-xs editAccountName ' aria-label='Left Align' data-accountid='" .$accountId . "' data-account='" . $account . "'  data-toggle='tooltip' title='Edit Account Name' >
//               <span class='glyphicon glyphicon-edit editAccountName'  aria-hidden='true' data-accountid='" .$accountId . "' data-account='" . $account . "'   ></span>
//               </button>";
//         $row['ACTION'].= "&nbsp;";
//         $row['ACTION'].= "<button type='button' class='btn btn-warning btn-xs deleteAccount ' aria-label='Left Align' data-accountid='" .$accountId . "' data-account='" . $account . "'   data-toggle='tooltip' title='Delete Account'>
//               <span class='glyphicon glyphicon-trash deleteAccount' aria-hidden='true' data-accountid='" .$accountId . "' data-account='" . $account . "' ></span>
//               </button>";
    }

    static function prepareJsonKnownEmailLookup(){
        $loader = new Loader();
        return $loader->loadIndexed('EMAIL_ADDRESS','UPES_REF',AllTables::$PERSON);
    }



}

