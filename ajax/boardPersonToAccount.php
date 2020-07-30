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

$loader = new Loader();
$allAccounts = $loader->loadIndexed('ACCOUNT','ACCOUNT_ID',AllTables::$ACCOUNT);


try {
    $accountPersonRecord = new AccountPersonRecord();
    $accountPersonTable = new AccountPersonTable(AllTables::$ACCOUNT_PERSON);
    $accountPersonRecordData = array_map('trim', $_POST);
    $accountPersonRecord->setFromArray($accountPersonRecordData);
    $emailResponse = $accountPersonRecord->sendNotificationToPesTaskid();

    $saveRecord = $accountPersonTable->insert($accountPersonRecord); // Only used to save NEW accountPersonRecords

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    print_r($e->getTrace());
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
if($success){
    $messages = " Person: " . $accountPersonRecordData['FULL_NAME'] . "<br/> Will be PES Cleared for :" . $allAccounts[$accountPersonRecordData['ACCOUNT_ID']] . "<br/>";
    $messages.= $_POST['mode']==FormClass::$modeDEFINE ? "Created" : "Updated" ;
}

$response = array('success'=>$success,'saveResponse' => $saveRecord, 'Messages'=>$messages,'emailResponse'=>$emailResponse);

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);