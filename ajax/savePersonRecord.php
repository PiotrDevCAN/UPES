<?php

use itdq\DbTable;
use itdq\FormClass;
use itdq\Trace;
use upes\AllTables;
use upes\PersonTable;
use upes\PersonRecord;



Trace::pageOpening($_SERVER['PHP_SELF']);

set_time_limit(0);
ob_start();


try {
    $personRecord = new PersonRecord();
    $personTable = new PersonTable(AllTables::$PERSON);
    $personRecordRecordData = array_map('trim', $_POST);

    $personRecordRecordData['UPES_REF'] = $_POST['mode']==FormClass::$modeDEFINE ? null : $personRecordRecordData['UPES_REF'];

    $personRecord->setFromArray($personRecordRecordData);

    $saveRecord = $_POST['mode']==FormClass::$modeDEFINE ? $personTable->insert($personRecord) : $personTable->update($personRecord, false, false);
    $upesRef  = $_POST['mode']==FormClass::$modeDEFINE ? $personTable->lastId() : $personRecordRecordData['UPES_REF'];

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    print_r($e->getTrace());
}

$messages = ob_get_clean();
$success = empty($messages);
if($success){
    $messages = " Person: " . $personRecordData['EMAIL_ADDRESS'] . "<br/> uPES Ref:" . $upesRef . "<br/>";
    $messages.= $_POST['mode']==FormClass::$modeDEFINE ? "Created" : "Updated" ;
}

$response = array('success'=>$success,'upesref' => $upesRef, 'saveResponse' => $saveRecord, 'Messages'=>$messages);

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);