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

$upesref = $_POST['UPES_REF'];

try {
    $accountPersonRecord = new AccountPersonRecord();
    $accountPersonTable = new AccountPersonTable(AllTables::$ACCOUNT_PERSON);
    $accountPersonRecordData = array_map('trim', $_POST);
    $accountPersonRecord->setFromArray(array('ACCOUNT_ID'=>$accountPersonRecordData['ACCOUNT_ID'], 'UPES_REF'=>$accountPersonRecordData['UPES_REF'],'PES_LEVEL'=>$accountPersonRecordData['PES_LEVEL'],'COUNTRY_OF_RESIDENCE'=>$accountPersonRecordData['COUNTRY_OF_RESIDENCE']));
    $updateRecord = $accountPersonTable->update($accountPersonRecord,false, false); // Only used to save NEW accountPersonRecords
    $accountPersonTable->setPesRescheckDate($accountPersonRecordData['UPES_REF'],$accountPersonRecordData['ACCOUNT_ID'],$_SESSION['ssoEmail']);
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    print_r($e->getTrace());
}

$messages = ob_get_clean();
$success = empty($messages);

$response = array('success'=>$success,'upesref' => $upesref, 'updateResponse' => $updateRecord, 'Messages'=>$messages);

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);