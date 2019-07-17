<?php

use itdq\DbTable;
use itdq\FormClass;
use itdq\Trace;
use upes\AccountRecord;
use upes\AccountTable;
use upes\AllTables;



Trace::pageOpening($_SERVER['PHP_SELF']);

set_time_limit(0);
ob_start();


try {
    $accountRecord = new AccountRecord();
    $accountTable = new AccountTable(AllTables::$ACCOUNT);
    $accountRecordData = array_map('trim', $_POST);

    $accountRecordData['ACCOUNT_ID'] = $_POST['mode']==FormClass::$modeDEFINE ? null : $accountRecordData['ACCOUNT_ID'];

    $accountRecord->setFromArray($accountRecordData);

    $saveRecord = $_POST['mode']==FormClass::$modeDEFINE ? $accountTable->insert($accountRecord) : $accountTable->update($accountRecord);
    $accountId  = $_POST['mode']==FormClass::$modeDEFINE ? $accountTable->lastId() : $accountRecordData['ACCOUNT_ID'];

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    print_r($e->getTrace());
}

$messages = ob_get_clean();
$success = empty($messages);
if($success){
    $messages = " Account: " . $accountRecordData['ACCOUNT'] . "<br/>Account Id:" . $accountId . "<br/>";
    $messages.= $_POST['mode']==FormClass::$modeDEFINE ? "Created" : "Updated" ;
}

$response = array('success'=>$success,'accountId' => $accountId, 'saveResponse' => $saveRecord, 'Messages'=>$messages);

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);