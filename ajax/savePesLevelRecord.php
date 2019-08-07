<?php

use itdq\DbTable;
use itdq\FormClass;
use itdq\Trace;
use upes\ContractTable;
use upes\AllTables;
use upes\ContractRecord;
use upes\PesLevelRecord;
use upes\PesLevelTable;



Trace::pageOpening($_SERVER['PHP_SELF']);

set_time_limit(0);
ob_start();


try {
    $pesLevelRecord = new PesLevelRecord();
    $pesLevelRecordTable = new PesLevelTable(AllTables::$PES_LEVELS);
    $pesLevelRecordData = array_map('trim', $_POST);

    $pesLevelRecordData['PES_LEVEL_REF'] = $_POST['mode']==FormClass::$modeDEFINE ? null : $pesLevelRecordData['PES_LEVEL_REF'];

    $pesLevelRecord->setFromArray($pesLevelRecordData);

    $saveRecord = $_POST['mode']==FormClass::$modeDEFINE ? $pesLevelRecordTable->insert($pesLevelRecord) : $pesLevelRecordTable->update($pesLevelRecord);

    $pesLevelRef  = $_POST['mode']==FormClass::$modeDEFINE ? $pesLevelRecordTable->lastId() : $pesLevelRecordData['PES_LEVEL_REF'];

} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    print_r($e->getTrace());
}

$messages = ob_get_clean();
$success = empty($messages);
if($success){
    $messages = " Account: " . $pesLevelRecordData['ACCOUNT'] . "<br/>Pes Level: " . $pesLevelRecordData['PES_LEVEL'] . "<br/>Pes Level Ref:" . $pesLevelRef . "<br/>";
    $messages.= $_POST['mode']==FormClass::$modeDEFINE ? "Created" : "Updated" ;
}

$response = array('success'=>$success,'peslevelref' => $pesLevelRef, 'saveResponse' => $saveRecord, 'Messages'=>$messages, 'pesLevelRecord'=>print_r($pesLevelRecord,true));

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);