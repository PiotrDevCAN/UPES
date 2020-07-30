<?php

use itdq\DbTable;
use itdq\FormClass;
use itdq\Trace;
use upes\ContractTable;
use upes\AllTables;
use upes\ContractRecord;



Trace::pageOpening($_SERVER['PHP_SELF']);

set_time_limit(0);
ob_start();


try {
    $contractRecord = new ContractRecord();
    $contractTable = new ContractTable(AllTables::$CONTRACT);
    $contractRecordData = array_map('trim', $_POST);

    $contractRecordData['CONTRACT_ID'] = $_POST['mode']==FormClass::$modeDEFINE ? null : $contractRecordData['CONTRACT_ID'];

    $contractRecord->setFromArray($contractRecordData);

    $saveRecord = $_POST['mode']==FormClass::$modeDEFINE ? $contractTable->insert($contractRecord) : $contractTable->update($contractRecord);
    $contractId  = $_POST['mode']==FormClass::$modeDEFINE ? $contractTable->lastId() : $contractRecordData['ACCOUNT_ID'];

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    print_r($e->getTrace());
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
if($success){
    $messages = " Account: " . $contractRecordData['ACCOUNT'] . "<br/>Contract: " . $contractRecordData['CONTRACT'] . "<br/>Contract Id:" . $contractId . "<br/>";
    $messages.= $_POST['mode']==FormClass::$modeDEFINE ? "Created" : "Updated" ;
}

$response = array('success'=>$success,'contractId' => $contractId, 'saveResponse' => $saveRecord, 'Messages'=>$messages);

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);