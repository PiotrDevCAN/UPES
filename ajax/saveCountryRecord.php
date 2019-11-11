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

    $international = isset($_POST['INTERNATIONAL']) ? CountryRecord::INTERNATIONAL_YES : CountryRecord::INTERNATIONAL_NO;
    $countryRecord->setFromArray(array('COUNTRY'=>$_POST['COUNTRY'],'INTERNATIONAL'=>$international));

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

$response = array('success'=>$success,'saveResponse' => $saveRecord, 'Messages'=>$messages);

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);