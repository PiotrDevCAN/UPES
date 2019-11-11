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

$sql = " SELECT '' AS ACTION, P.EMAIL_ADDRESS, P.FULL_NAME, A.ACCOUNT, PL.PES_LEVEL, PL.PES_LEVEL_DESCRIPTION, AP.PES_STATUS,AP.PES_CLEARED_DATE, AP.ACCOUNT_ID, AP.UPES_REF ";
$sql.= " FROM " . $_SESSION['Db2Schema'] . "." . AllTables::$PERSON . " as P ";
$sql.= " LEFT JOIN  " . $_SESSION['Db2Schema'] . "." . AllTables::$ACCOUNT_PERSON . " as AP ";
$sql.= " ON P.UPES_REF = AP.UPES_REF ";
$sql.= " LEFT JOIN  " . $_SESSION['Db2Schema'] . "." . AllTables::$ACCOUNT . " as A ";
$sql.= " ON AP.ACCOUNT_ID = A.ACCOUNT_ID ";
$sql.= " LEFT JOIN  " . $_SESSION['Db2Schema'] . "." . AllTables::$PES_LEVELS . " as PL ";
$sql.= " ON AP.PES_LEVEL = PL.PES_LEVEL_REF ";
$sql.= " WHERE A.ACCOUNT is not null and AP.UPES_REF is not null ";
$sql.= $_SESSION['isPesTeam'] ? null : " AND (AP.PES_REQUESTOR='" . db2_escape_string($_SESSION['ssoEmail']) . "' OR P.EMAIL_ADDRESS = '" . db2_escape_string($_SESSION['ssoEmail']) . "' ) ";


$rs = db2_exec($_SESSION['conn'], $sql);

if(!$rs){
    DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
}


while(($row=db2_fetch_assoc($rs))==true){
    $rowWithActionButtons = AccountPersonTable::addButtonsForPeopleReport(array_map('trim', $row));
    $data[] = $rowWithActionButtons;
}

$messages = ob_get_clean();
$Success = empty($messages);

$response = array('data'=>$data,'success'=>$Success,'messages'=>$messages, 'sql'=>$sql);
echo json_encode($response);
Trace::pageLoadComplete($_SERVER['PHP_SELF']);