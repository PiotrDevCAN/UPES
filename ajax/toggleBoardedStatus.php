<?php

use itdq\DbTable;
use itdq\FormClass;
use itdq\Trace;
use itdq\Loader;
use upes\AllTables;
use upes\PersonTable;
use upes\PersonRecord;
use upes\AccountPersonRecord;
use upes\AccountPersonTable;

Trace::pageOpening($_SERVER['PHP_SELF']);

set_time_limit(0);
ob_start();

try {
    $pesTracker = new AccountPersonTable(AllTables::$ACCOUNT_PERSON);
    
    $_POST['boarded']=='yes' ?  AccountPersonTable::offboardFromAccount($_POST['accountId'], $_POST['upesRef']) : AccountPersonTable::reboardToAccount($_POST['accountId'], $_POST['upesRef']); 
    $comment = $_POST['boarded']=='yes' ? "Offboarded by " .  $_SESSION['ssoEmail'] : " Re-boarded by " . $_SESSION['ssoEmail'] ;
    $pesTracker->savePesComment( $_POST['upesRef'], $_POST['accountId'], $comment); 
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    print_r($e->getTrace());
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array('success'=>$success, 'Messages'=>$messages);

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);