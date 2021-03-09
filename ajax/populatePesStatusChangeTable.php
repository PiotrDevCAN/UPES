<?php
use itdq\Trace;
use upes\AllTables;
use upes\ContractTable;
use upes\PesLevelTable;
use itdq\DbTable;
use upes\AccountPersonTable;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

Trace::pageOpening($_SERVER['PHP_SELF']);
ob_start();

$sql = " SELECT * ";
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . AllTables::$PES_STATUS_AUDIT . " as P ";
$sql.= " ORDER BY UPDATED DESC ";


error_log(__FILE__ . __LINE__ . $_SESSION['ssoEmail']);
error_log($sql);


$rs = db2_exec($GLOBALS['conn'], $sql);

if(!$rs){
    DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
}


while(($row=db2_fetch_assoc($rs))==true){
     $data[] = $row;
}

$messages = ob_get_clean();
$Success = empty($messages);

$response = array('data'=>$data,'success'=>$Success,'messages'=>$messages, 'sql'=>$sql);
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);