<?php
use itdq\Trace;
use upes\AllTables;
use itdq\DbTable;

Trace::pageOpening($_SERVER['PHP_SELF']);
set_time_limit(0);
ob_start();

if(empty($_POST['accountid'])){
    $response = array('success'=>false,'accountId' => null, 'Messages'=>"Delete not performed<br/>No Parms passed in");
    ob_clean();
    echo json_encode($response);
    return false;
}


$sql = " DELETE FROM " . $_SESSION['Db2Schema'] . "." . AllTables::$ACCOUNT ;
$sql.= " WHeRE ACCOUNT_ID='" . db2_escape_string($_POST['accountid']) . "' ";

$rs = db2_exec($_SESSION['conn'], $sql);

if(!$rs){
    DbTable::displayErrorMessage($rs, __CLASS__, __FILE__, $sql);
}

$messages = ob_get_clean();
$success = $rs && empty($messages);

$response = array('success'=>$success,'accountId' => $accountId, 'Messages'=>$messages);

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);