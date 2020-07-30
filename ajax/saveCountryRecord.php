<?php

use itdq\DbTable;
use itdq\FormClass;
use itdq\Trace;
use upes\CountryRecord;
use upes\CountryTable;
use upes\AllTables;



Trace::pageOpening($_SERVER['PHP_SELF']);

set_time_limit(0);
ob_start();


try {
    $countryRecord = new CountryRecord();
    $countryTable = new CountryTable(AllTables::$COUNTRY);
    $countryRecordRecordData = array_map('trim', $_POST);

    $countryRecord->setFromArray($countryRecordRecordData);

    $saveRecord = $_POST['mode']==FormClass::$modeDEFINE ? $countryTable->insert($countryRecord) : $countryTable->update($countryRecord);

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    print_r($e->getTrace());
}

$messages = ob_get_clean();
$success = empty($messages);
if($success){
    $messages = " Country: " . $countryRecordRecordData['COUNTRY'];
    $messages.= $_POST['mode']==FormClass::$modeDEFINE ? "Created" : "Updated" ;
}

ob_start();
$countryRecord->iterateVisible();
$record = ob_get_clean();
ob_start();


$response = array('success'=>$success,'saveResponse' => $saveRecord, 'Messages'=>$messages,'record'=>$record);

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);